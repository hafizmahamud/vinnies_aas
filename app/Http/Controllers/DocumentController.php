<?php

namespace App\Http\Controllers;

use Auth;
use File;
use Hashids;
use Storage;
use App\Project;
use App\Document;
use App\Twinning;
use App\User;
use Carbon\Carbon;
use App\Beneficiary;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.documents');

        $data = [];
        $user = Auth::user();
        $id   = $request->get('id');

        switch ($request->get('type')) {
            case 'User':
                $documents = User::find($id)->documents;
                break;
        }

        $data['documents'] = $documents->map(function ($document) {
            return [
                'id'           => $document->id,
                'filename'     => basename($document->path),
                'size'         => Helper::formatFileSize(File::size(storage_path('app/' . $document->path))),
                'url'          => route('documents.download', Hashids::encode([$document->id])),
                'edit_url'     => route('documents.edit', $document),
                'delete_url'   => route('documents.delete', $document),
                'date'         => $document->created_at->format(config('vinnies.date_format')),
                'type'         => array_get(Helper::getDocumentTypes(), $document->type),
                'user'         => optional($document->user)->first_name . ' ' . optional($document->user)->last_name,
                'excerpt'      => str_limit($document->comments, 30),
                'comments'     => nl2br($document->comments),
                'comments_raw' => $document->comments,
            ];
        });

        $data['has_documents']        = $documents->isNotEmpty();
        $data['can_create_documents'] = $user->hasPermissionTo('create.documents');
        $data['can_edit_documents']   = $user->hasPermissionTo('update.documents');
        $data['can_delete_documents'] = $user->hasPermissionTo('delete.documents');

        return $data;
    }

    public function download($hash)
    {
        $this->authorize('read.documents');

        $document_id = Hashids::decode($hash);

        if (empty($document_id)) {
            abort(403);
        }

        $document = Document::find($document_id[0]);

        if (!$document) {
            abort(403);
        }

        return response()->download(storage_path('app/' . $document->path));
    }

    public function create(Request $request)
    {
        $this->authorize('create.documents');

        $data = $this->prepare($request->validate($this->rules()));

        if(!Storage::exists($data['path'])) { // check if file exist/upload success (rm-11505)
            $request->validate(
               [
                  'file_check' => 'required',
               ],
               [
                  'file_check.required'=> 'File upload failed. Please try again!' // custom message
               ]
            );
        }

        $document = Document::create($data);

        $this->updateParentUpdatedBy('App\\' . $request->get('documentable_type'), $request->get('documentable_id'));
       
        return $document;
        
    }

    private function prepare($data)
    {
        if (!empty($data['document2'])) {
            $data['document'] = $data['document2'];

            unset($data['document2']);
        }

        if (!empty($data['document'])) {
            $directory    = $data['documentable_type'] . '/' . $data['documentable_id'];
            $filename     =  $this->fixDuplicate($data['document']->getClientOriginalName(), $directory);
            $data['path'] = $data['document']->storeAs($directory, $filename);
        }

        $data['user_id'] = Auth::id();
        $data['documentable_type'] = 'App\\' . $data['documentable_type'];

        return $data;
    }

    public function delete(Request $request, Document $document)
    {
        $this->authorize('delete.documents');

        Storage::delete($document->path);

        if (Storage::exists($document->path)) { // check file is deleted (rm-11505)
            return response()->json([
                'msg' => 'Failed to delete Document',
            ]);
        }

        $this->updateParentUpdatedBy($document->documentable_type, $document->documentable_id);
        $document->delete();

        return response()->json([
            'msg' => 'Document successfully deleted',
        ]);
    }

    public function edit(Request $request, Document $document)
    {
        $rules = $this->rules();

        if (!empty($request->file('document2'))) {
            $rules['document2'] = $rules['document'];
            unset($rules['document']);
        }

        $data = $this->prepare($request->validate($rules));

        if (isset($data['path'])) {  // check if file exist/upload success (rm-11505)
            if (!Storage::exists($data['path'])) {
                $request->validate(
                  [
                     'file_check' => 'required',
                  ],
                  [
                     'file_check.required'=> 'File upload failed. Please try again!' // custom message
                  ]
                );
             }
        }

        $document->update($data);

        $this->updateParentUpdatedBy('App\\' . $request->get('documentable_type'), $request->get('documentable_id'));

        return $document;
    }

    private function rules()
    {
        return [
            'documentable_type' => 'required',
            'documentable_id'   => 'required|integer',
            'type'              => 'required',
            'document'          => 'file|max:10240|mimes:' . config('vinnies.filetypes'),
            'comments'          => 'max:200',
        ];
    }

    private function fixDuplicate($filename, $directory)
    {
        if (Storage::exists($directory . DIRECTORY_SEPARATOR . $filename)) {
            $pathInfo  = pathinfo($directory . DIRECTORY_SEPARATOR . $filename);
            $extension = isset($pathInfo['extension']) ? ('.' . $pathInfo['extension']) : '';

            if (preg_match('/(.*?)(\d+)$/', $pathInfo['filename'], $match)) {
                $base = $match[1];
                $number = intVal($match[2]);
            } else {
                $base = $pathInfo['filename'];
                $number = 0;
            }

            // Choose a name with an incremented number until a file with that name
            // doesn't exist
            do {
                $filename = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $base . ++$number . $extension;
            } while (Storage::exists($filename));
        }

        return basename($filename);
    }

    private function updateParentUpdatedBy($type, $id)
    {
        if (strpos($type, 'NewRemittance') !== false) {
            return;
        }

        if ($type === 'App\LocalConference' || $type === 'App\Beneficiary') {
            $model = (new $type)->where('id', $id)->withTrashed()->first();
        }  else {
            $model = (new $type)->where('id', $id)->first();
        }

        $model->updated_at = Carbon::now();
        $model->save();
    }
}
