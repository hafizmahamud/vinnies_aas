<?php

namespace App\Http\Controllers;

use Hashids;
use App\Gallery;
use App\Vinnies\Helper;
use ZipStream\ZipStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;
use Spatie\Activitylog\Models\Activity;
use App\User;
use Illuminate\Support\Facades\Log;

class GalleryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('read.galleries');

        $galleries = $this->getGalleriesFromRequest($request);

        return view('galleries.index')->with(compact('galleries'));
    }

    public function admin(Request $request)
    {
        $this->authorize('update.galleries');

        $galleries = $this->getGalleriesFromRequest($request);

        return view('galleries.index')->with(compact('galleries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create.galleries');

        $gallery = new Gallery;

        return view('galleries.create')->with(compact('gallery'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create.galleries');

        $request->validate(Gallery::rules());

        $data = $request->only([
            'file',
            'year',
            'country',
            'description',
        ]);

        $data['updated_by'] = Auth::id();

        $gallery  = Gallery::create($data);
        $msg      = 'New gallery item has been successfully created';
        $redirect = route('galleries.edit', $gallery);

        if ($request->ajax()) {
            return response()->json([
                'redirect' => $redirect,
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->route($redirect);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Gallery $gallery)
    {
        $this->authorize('update.galleries');

        return view('galleries.edit')->with(compact('gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Gallery $gallery)
    {
        $this->authorize('update.galleries');

        $request->validate(Gallery::rules());

        $data = $request->only([
            'file',
            'year',
            'country',
            'description',
        ]);

        $data['updated_by'] = Auth::id();

        $gallery  = $gallery->update($data);
        $msg      = 'Gallery item has been successfully updated';

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gallery $gallery)
    {
        $this->authorize('delete.galleries');
    }

    public function upload(Request $request)
    {
        $this->authorize('update.galleries');

        $request->validate([
            'gallery_file' => 'required|file|mimes:doc,docx,ppt,pptx,pdf,zip,jpg,jpeg,png,bmp',
        ]);

        $dir  = Hashids::encode(Auth::id());
        $name = $this->fixDuplicate($request->file('gallery_file')->getClientOriginalName(), 'public' . DIRECTORY_SEPARATOR . $dir);
        $path = $request->file('gallery_file')->storeAs($dir, $name, 'public');

        return response()->json([
            'name' => $name,
            'path' => $path,
            'url'  => url('storage/' . $path),
            'size' => Helper::formatFileSize(File::size(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $path))),
        ]);
    }

    private function fixDuplicate($filename, $directory)
    {
        if (Storage::exists($directory . DIRECTORY_SEPARATOR . $filename)) {
            $pathInfo  = pathinfo($directory . DIRECTORY_SEPARATOR . $filename);
            $extension = isset($pathInfo['extension']) ? ('.' . $pathInfo['extension']) : '';

            if (preg_match('/(.*?)(\d+)$/', $pathInfo['filename'], $match)) {
                $base   = $match[1];
                $number = intVal($match[2]);
            } else {
                $base   = $pathInfo['filename'];
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

    private function getGalleriesFromRequest(Request $request)
    {
        $galleries = Gallery::whereNotNull('id');

        if ($year = $request->get('year')) {
            $galleries->where('year', $year);
        }

        if ($country = $request->get('country')) {
            $galleries->where('country', $country);
        }

        if ($keyword = $request->get('keyword')) {
            $galleries->where(function ($query) use ($keyword) {
                return $query->where('description', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('country', 'LIKE', '%' . $keyword . '%');
            });
        }

        switch ($request->get('sort')) {
            case 'year_country':
                $galleries
                    ->orderBy(DB::raw('FIELD(country, "Generic")'), 'asc')
                    ->orderBy('country', 'asc')
                    ->orderBy('created_at', 'desc');
                break;

            case 'oldest':
                $galleries->orderBy('created_at', 'asc');
                break;

            default:
                $galleries->latest();
                break;
        }

        $galleries = $galleries->get();
        $galleries = $galleries->groupBy('year')->sortKeysDesc();

        return $galleries;
    }

    public function download(Gallery $gallery, User $user)
    {
        Log::channel('changelog')->info('app-update', [
            'causer_id' => Auth::check() ? Auth::user()->id : null,
            'model'   => 'App\\Gallery',
            'action'  => 'DOWNLOAD',
            'activity'  => [
                    'new'     => $gallery->getAttributes(),
                    'changed' => $gallery->getChanges(),
                ]
        ]);

        // activity()
        // ->causedBy($user->id)
        // ->withProperties(['key' => 'User '. $user->firstname . $user->lastname . 'download the ' .$gallery->file])
        // ->log('exported');
   
        // $lastActivity = Activity::all()->last(); //returns the last logged activity
   
        // $lastActivity->getExtraProperty('key'); //returns 'value' 

        // $lastActivity->where('properties->key', 'value')->get();

        return response()->download(
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $gallery->file)
        );
    }

    public function generateBulkDownloadUrl(Request $request)
    {
        return response()->json(
            route(
                'galleries.bulk-download',
                Hashids::encode($request->get('ids'))
            )
        );
    }

    public function bulkDownload($hash)
    {
        $this->authorize('read.galleries');

        $ids = Hashids::decode($hash);
        $galleries = Gallery::whereIn('id', $ids)->get();

        $zip = new \ZipArchive();
        $zip_name = 'gallery-' . time() . '.zip'; // Zip name
        $zip->open($zip_name,  \ZipArchive::CREATE  | \ZipArchive::OVERWRITE);
            foreach ($galleries as $gallery) {
            $path = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
                
                if(file_exists($path)){
                    // $zip->addFile(storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $gallery->file));
                    $zip->addFile($path . $gallery->file);
                }
                else{
                    echo "file does not exist";
                }
            }
        $zip->close();

        Log::channel('changelog')->info('app-update', [
            'causer_id' => Auth::check() ? Auth::user()->id : null,
            'model'   => 'App\\Gallery',
            'action'  => 'DOWNLOAD',
            'activity'  => [
                    'new'     => $zip_name,
                    'changed' => $zip_name,
                ]
        ]);

        return response()->download($zip_name);
    }

    public function bulkDelete(Request $request)
    {
        $this->authorize('delete.galleries');

        $ids = explode(',', $request->get('ids'));

        Gallery::whereIn('id', $ids)->delete();

        flash('Selected gallery ' . str_plural('item', count($ids)) . ' have been successfully deleted')->success();

        return redirect()->route('galleries.admin');
    }
}
