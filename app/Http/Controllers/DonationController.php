<?php

namespace App\Http\Controllers;

use App\Vinnies\Import\Donations as DonationsImport;
use Illuminate\Support\Facades\Storage;
use App\DonationsImportApprovalList;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\DonationsFileImport;
use App\Vinnies\Helper;
use App\Certificate;
use App\Sponsorship;
use Carbon\Carbon;
use App\Donation;
use App\Treshold;
use App\Student;
use Hashids;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\DB as FacadesDB;
use Throwable;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function getBaseModel()
    {
        $user      = Auth::user();
        $donations = Donation::where('donations.is_active', 1);

        if ($user->state !== 'national') {
            $donations->where('state', $user->state);
        }

        return $donations;
    }

    private function getBaseImportModel()
    {
        $user      = Auth::user();
        $donations = DonationsFileImport::where('donations_file_import.deleted_at', NULL)
                                    ->where('donations_file_import.approved_at', NULL);

        return $donations;
    }


    private function getBaseImportDetailsModel($t)
    {
        $user      = Auth::user();
        $donations = DonationsImportApprovalList::where('donations_import_approval_list.donations_file_import_id', $t);

        return $donations;
    }


    public function list(Request $request)
    {
        $this->authorize('read.donations');

        $per_page        = config('vinnies.pagination.donations');
        $can_edit        = Auth::user()->hasPermissionTo('create.certificates');
        $can_set_amount  = Auth::user()->hasPermissionTo('update.settings');
        $donations       = $this->getBaseModel();
        $donations       = $donations->paginate($per_page);

        return view('donations.list')->with(compact('donations', 'per_page', 'can_edit', 'can_set_amount'));
    }

    public function showCreateForm()
    {
        $this->authorize('create.donations');

        $donation = new Donation;

        return view('donations.create')->with(compact('donation'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.donations');

        // Validate this
        $this->validate($request, $this->rules());

        $user = Auth::user();
        $data = $request->only([
                            'id',
                            'state',
                            'amount',
                            'certificate_needed',
                            'name_on_certificate',
                            'special_allocation_required',
                            'special_allocation_details',
                            'contact_address',
                            'contact_suburb',
                            'contact_postcode',
                            'contact_email',
                            'contact_phone',
                            'contact_mobile',
                            'online_donation',
                          ]);

        $data['sponsorship_value']  = Treshold::current()->amount;
        $data['received_at']        = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('received_at') . ':00');
        $data['total_sponsorships'] = floor($data['amount'] / $data['sponsorship_value']);

        if ($user->state !== 'national') {
            $data['state'] = $user->state;
        }

        // Check for duplicate first
        $is_exists = Donation::where('name_on_certificate', $data['name_on_certificate'])
            ->where('received_at', $data['received_at'])
            ->where('is_active', 1)
            ->where('amount', $data['amount'])->first();

        if ($is_exists) {
            $error  = [
                'type'    => 'dialog',
                'confirm' => false,
                'msg'     => 'The Donation seem to have been previously uploaded Please review.'
            ];

            if ($request->ajax()) {
                return response()->json($error, 400);
            } else {
                flash($error['msg'])->error()->important();

                return redirect()->back();
            }
        }

        $msg      = 'New donation has been successfully created';
        $donation = Donation::create($data);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(Donation $donation)
    {
        $this->authorize('update.donations');

        return view('donations.edit')->with(compact('donation'));
    }

    public function edit(Request $request, Donation $donation)
    {
        $this->authorize('update.donations');

        $this->validate($request, $this->rules());

        $keys = [
                  'state',
                  'certificate_needed',
                  'name_on_certificate',
                  'special_allocation_required',
                  'special_allocation_details',
                  'contact_address',
                  'contact_suburb',
                  'contact_postcode',
                  'contact_email',
                  'contact_phone',
                  'contact_mobile',
                  'online_donation',
                ];

        // Only unallocated donation can be edited for their amount
        if (!$donation->sponsorships->count()) {
            $keys[] = 'amount';
        }

        $data = $request->only($keys);

        // Fix amount error
        if ($donation->sponsorships->count()) {
            $data['amount'] = $donation->amount;
        }

        $data['sponsorship_value']  = Treshold::current()->amount;
        $data['received_at']        = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('received_at') . ':00');
        $data['total_sponsorships'] = floor($data['amount'] / $data['sponsorship_value']);

        $msg  = 'Donation has been successfully edited';

        $donation->update($data);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showTempEditForm(DonationsImportApprovalList $donation)
    {
        $this->authorize('update.donations');

        return view('donations.temp-edit')->with(compact('donation'));
    }

    public function tempEdit(Request $request, DonationsImportApprovalList $donation)
    {
        $this->authorize('update.donations');

        $this->validate($request, $this->rules());

        $keys = [
                  'state',
                  'certificate_needed',
                  'name_on_certificate',
                  'special_allocation_required',
                  'special_allocation_details',
                  'contact_address',
                  'contact_suburb',
                  'contact_postcode',
                  'contact_email',
                  'contact_phone',
                  'contact_mobile',
                  'online_donation',
                ];



        $data = $request->only($keys);


        $data['sponsorship_value']  = Treshold::current()->amount;
        $data['received_at']        = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('received_at') . ':00');
        //$data['total_sponsorships'] = floor($data['amount'] / $data['sponsorship_value']);

        $msg  = 'Donation has been successfully edited';

        $donation->update($data);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showImportForm()
    {
        $this->authorize('update.donations');

        $states = Helper::getStates();

        return view('donations.import')->with(compact('states'));
    }

    public function showImportList()
    {
        $this->authorize('update.donations');

        $states = Helper::getStates();

        $per_page        = config('vinnies.pagination.donations');
        $can_edit        = Auth::user()->hasPermissionTo('create.certificates');
        $can_set_amount  = Auth::user()->hasPermissionTo('update.settings');
        $donations       = $this->getBaseImportModel();

        $donations       = $donations->paginate(30);

        return view('donations.import-list')->with(compact('states', 'donations', 'per_page', 'can_edit', 'can_set_amount'));
    }

    public function showImportDetails()
    {
        $this->authorize('update.donations');

        $states = Helper::getStates();

        $per_page        = config('vinnies.pagination.donations');
        $can_edit        = Auth::user()->hasPermissionTo('create.certificates');
        $can_set_amount  = Auth::user()->hasPermissionTo('update.settings');
        $donations       = $this->getBaseImportDetailsModel();
        $donations       = $donations->paginate($per_page);

        return view('donations.import-details')->with(compact('states', 'donations', 'per_page', 'can_edit', 'can_set_amount'));
    }

    public function showImportDetailsForm(DonationsFileImport $file)
    {
        $this->authorize('update.donations');
        $this->file_id = $file->id;

        //$donations = DonationsImportApprovalList::where('donations_file_import_id', $this->file_id)->first();
        //var_dump($donations);
        //return view('donations.import-details')->with(compact('file', 'donations'));


        $states = Helper::getStates();

        $per_page        = config('vinnies.pagination.donations');
        $can_edit        = Auth::user()->hasPermissionTo('create.certificates');
        $can_set_amount  = Auth::user()->hasPermissionTo('update.settings');
        $donations       = $this->getBaseImportDetailsModel($file->id);
        $donations       = $donations->paginate($per_page);

        return view('donations.import-details')->with(compact('file', 'states', 'donations', 'per_page', 'can_edit', 'can_set_amount'));

    }

    public function datatables(Request $request)
    {
        $this->authorize('read.donations');

        $donations = $this->getBaseModel();
        $donations = $this->sortModelFromRequest($donations, $request);

        if (!empty($filters = $request->get('filters'))) {
            if ($filters['donation_status'] !== 'all') {
                switch ($filters['donation_status']) {
                    case 'full':
                        $donations->whereColumn('allocated_sponsorships', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'partial':
                        $donations->whereColumn('allocated_sponsorships', '<', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'unallocated':
                        $donations->where('amount', '>=', DB::raw('sponsorship_value'));
                        $donations->where('allocated_sponsorships', 0);
                        break;

                    case 'less':
                        $donations->where('amount', '<', DB::raw('sponsorship_value'));
                        break;
                }
            }

            if ($filters['certificate_needed'] !== 'all') {
                $donations->where('certificate_needed', $filters['certificate_needed']);
            }

            if ($filters['is_printed'] !== 'all') {
                $donations->where('is_printed', $filters['is_printed']);
            }

            if ($filters['special_allocation_required'] !== 'all') {
                $donations->where('special_allocation_required', $filters['special_allocation_required']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $donations->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('name_on_certificate', 'LIKE', '%' . $keyword . '%');

                if ($state = Helper::getStateKeyByName($keyword)) {
                    $query->orWhere('state', 'LIKE', '%' . $state . '%');
                }
            });
        }

        $donations = $donations->paginate(config('vinnies.pagination.donations'));
        $data      = $this->getDatatableBaseData($donations, $request);

        foreach ($donations as $donation) {
            $data['data'][] = [
                'id'                          => $donation->id,
                'state'                       => Helper::getStateNameByKey($donation->state),
                'special_allocation_required' => $donation->special_allocation_required ? 'Yes' : 'No',
                'special_allocation_details'  => empty($donation->special_allocation_details) ? 'N/A' : $donation->special_allocation_details,
                'name_on_certificate'         => $donation->name_on_certificate,
                'received_at'                 => $donation->received_at,
                'uploaded_at'                 => $donation->fileImport && $donation->fileImport->approved_at ? $donation->fileImport->created_at->format(config('vinnies.datetime_format')) : '-',
                'approved_at'                 => $donation->fileImport && $donation->fileImport->approved_at ? $donation->fileImport->approved_at->format(config('vinnies.datetime_format')) : '-',
                'amount'                      => $donation->amount,
                'total_sponsorships'          => $donation->total_sponsorships,
                'allocated_sponsorships'      => $donation->sponsorships->count(),
                'certificate_needed'          => ((bool) $donation->certificate_needed ? 'Yes' : 'No'),
                'is_printed'                  => ((bool) $donation->is_printed ? 'Yes' : 'No'),
                'DT_RowId'                    => 'row_' . $donation->id
            ];
        }

        return $data;
    }

    public function importDatatables(Request $request)
    {
        $this->authorize('read.donations');

        $donations = $this->getBaseImportModel();
        $donations = $this->sortModelFromRequest($donations, $request);


        if (!empty($filters = $request->get('filters'))) {
            if ($filters['donation_status'] !== 'all') {
                switch ($filters['donation_status']) {
                    case 'full':
                        $donations->whereColumn('allocated_sponsorships', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'partial':
                        $donations->whereColumn('allocated_sponsorships', '<', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'unallocated':
                        $donations->where('amount', '>=', DB::raw('sponsorship_value'));
                        $donations->where('allocated_sponsorships', 0);
                        break;

                    case 'less':
                        $donations->where('amount', '<', DB::raw('sponsorship_value'));
                        break;
                }
            }

            if ($filters['certificate_needed'] !== 'all') {
                $donations->where('certificate_needed', $filters['certificate_needed']);
            }

            if ($filters['is_printed'] !== 'all') {
                $donations->where('is_printed', $filters['is_printed']);
            }

            if ($filters['special_allocation_required'] !== 'all') {
                $donations->where('special_allocation_required', $filters['special_allocation_required']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $donations->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('name_on_certificate', 'LIKE', '%' . $keyword . '%');

                if ($state = Helper::getStateKeyByName($keyword)) {
                    $query->orWhere('state', 'LIKE', '%' . $state . '%');
                }
            });
        }

        $donations = $donations->paginate(config('vinnies.pagination.donations'));
        $data      = $this->getDatatableBaseData($donations, $request);

        foreach ($donations as $donation) {
            $data['data'][] = [
                'id'            => $donation->id,
                'file'          => $donation->file,
                'state'         => strtoupper($donation->state),
                'is_approved'   => $donation->is_approved ? 'Approved' : 'Waiting for Approval',
                'approved_at'   => empty($donation->approved_at) ? '-' : $donation->approved_at->format(config('vinnies.datetime_format')),
                'created_at'    => empty($donation->created_at) ? '-' : $donation->created_at->format(config('vinnies.datetime_format')),
                'DT_RowId'      => 'row_' . $donation->id
            ];
        }


        return $data;
    }

    public function importDetailsDatatables(Request $request)
    {
        $this->authorize('read.donations');
        $get_id = $request->all();
        $file_id = array_keys($get_id,NULL);

        $donations = $this->getBaseImportDetailsModel($file_id);
        $donations = $this->sortModelFromRequest($donations, $request);

        if (!empty($filters = $request->get('filters'))) {
            if ($filters['donation_status'] !== 'all') {
                switch ($filters['donation_status']) {
                    case 'full':
                        $donations->whereColumn('allocated_sponsorships', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'partial':
                        $donations->whereColumn('allocated_sponsorships', '<', 'total_sponsorships');
                        $donations->where('allocated_sponsorships', '>', 0);
                        break;

                    case 'unallocated':
                        $donations->where('amount', '>=', DB::raw('sponsorship_value'));
                        $donations->where('allocated_sponsorships', 0);
                        break;

                    case 'less':
                        $donations->where('amount', '<', DB::raw('sponsorship_value'));
                        break;
                }
            }

            if ($filters['certificate_needed'] !== 'all') {
                $donations->where('certificate_needed', $filters['certificate_needed']);
            }

            if ($filters['is_printed'] !== 'all') {
                $donations->where('is_printed', $filters['is_printed']);
            }

            if ($filters['special_allocation_required'] !== 'all') {
                $donations->where('special_allocation_required', $filters['special_allocation_required']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $donations->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('name_on_certificate', 'LIKE', '%' . $keyword . '%');

                if ($state = Helper::getStateKeyByName($keyword)) {
                    $query->orWhere('state', 'LIKE', '%' . $state . '%');
                }
            });
        }

        $donations = $donations->paginate(config('vinnies.pagination.donations'));
        $data      = $this->getDatatableBaseData($donations, $request);

        foreach ($donations as $donation) {
            $data['data'][] = [
                'id'                          => $donation->id,
                'state'                       => Helper::getStateNameByKey($donation->state),
                'special_allocation_required' => $donation->special_allocation_required ? 'Yes' : 'No',
                'special_allocation_details'  => empty($donation->special_allocation_details) ? 'N/A' : $donation->special_allocation_details,
                'name_on_certificate'         => $donation->name_on_certificate,
                'received_at'                 => $donation->received_at,
                'amount'                      => $donation->amount,
                // 'total_sponsorships'          => $donation->total_sponsorships,
                'certificate_needed'          => ((bool) $donation->certificate_needed ? 'Yes' : 'No'),
                // 'is_printed'                  => ((bool) $donation->is_printed ? 'Yes' : 'No'),
                'address'                     => "$donation->contact_address, <br> $donation->contact_postcode $donation->contact_suburb",
                'contact'                     => "Email : $donation->contact_email<br>Phone : $donation->contact_phone<br>Mobile : $donation->contact_phone",
                // 'contact_phone'               => $donation->contact_phone,
                // 'contact_mobile'              => $donation->contact_mobile,
                'online_donation'              => ((bool) $donation->online_donation ? 'Yes' : 'No'),
                'DT_RowId'                    => 'row_' . $donation->id
            ];
        }

        return $data;
    }

    public function import(Request $request)
    {
        FacadesDB::beginTransaction();
        try {
            $this->authorize('update.donations');

            $this->validate($request, [
                'state' => [
                    'required',
                    Rule::in(array_keys(Helper::getStates()))
                ],
                'csv' => 'required|file|mimes:csv,txt'
            ]);


            $csv   = $request->file('csv');
            $force = (bool) $request->get('force');

            // Check if file uploaded successfully
            if (!$csv->isValid()) {
                return response()->json([
                    'csv' => ['Failed to upload csv, please try again']
                ], 400);
            }

            $import = new DonationsImport($csv);
            $error  = [
                'type'    => 'dialog',
                'confirm' => false,
                'msg'     => ''
            ];

            if ($import->hasDuplicateFile($request->file('csv')->getClientOriginalName())) {
                $error['msg'] = 'The uploaded CSV contains File Name that seem to have been previously uploaded. Please review the CSV file name.';

                return response()->json($error, 400);
            }

            if (!$import->validHeaders()) {
                $error['msg'] = 'The uploaded CSV does not match the Donations template. Please review the CSV file.';

                return response()->json($error, 400);
            }

            if (!$import->validState($request->get('state'))) {
                $error['msg'] = 'The Donation State does not match.';

                return response()->json($error, 400);
            }

            if (!$import->validSpecialDetailsRequired()) {
                $error['msg'] = 'The value of the Special allocation required field at row ' . $import->invalidRow . ' cannot be correctly interpreted. Please set to "Yes", "No" or leave empty.';

                return response()->json($error, 400);
            }

            if (!$import->validSpecialDetails()) {
                $error['msg'] = 'Please provide at least 3 characters in Special allocation Details at row ' . $import->invalidRow . '. The respective donation has the Special allocation required field = "Yes".';

                return response()->json($error, 400);
            }

            if ($import->hasDuplicates()) {
                $error['msg'] = 'The uploaded CSV contains Donations that seem to have been previously uploaded. Please review the CSV file.';

                return response()->json($error, 400);
            }

            if (!$import->validAddress()) {
                $error['msg'] = 'The value of the Address required field at row ' . $import->invalidRow . ' cannot be empty.';

                return response()->json($error, 400);
            }

            if (!$import->validName()) {
                $error['msg'] = 'The value of the Donor Name on Certificate required field at row ' . $import->invalidRow . ' cannot be empty.';

                return response()->json($error, 400);
            }

            // All validation passes
            // create file import
            $user      = Auth::user();

            $file = new DonationsFileImport;
            $file->file    = $request->file('csv')->getClientOriginalName();
            $file->user_id = $user->id;
            $file->state   = $request->get('state');
            $file->save();

            $path = Storage::putFile('import/donations/' . $file->id, $csv);
            $file->path = $path;
            $file->save();

            $result = $import->import($file->id);

            FacadesDB::commit();
            
            if ($result) {
                $msg = sprintf(
                    'Successfully uploaded %d donation%s',
                    $result['count'],
                    ($result['count'] > 1 ? 's' : '')
                );

                return response()->json([
                    'msg'      => $msg,
                    'redirect' => route('donations.import-list'),
                ]);
                //return response()->json(['msg' => $msg]);
            } else {
                $error['msg']     = 'Failed to import donations data into database';

                return response()->json($error, 400);
            }
        }catch (Exception $e) {
            FacadesDB::rollBack();
            $error['msg']     = 'Failed to import donations data into database. ' . $e->getMessage();

            return response()->json($error, 400);
        } catch (Throwable $e) {
            FacadesDB::rollBack();
            $error['msg']     = 'Failed to import donations data into database. ' . $e->getMessage();

            return response()->json($error, 400);
        }
    }

    private function rules()
    {
        $rules = [
            'state' => [
                'required',
                Rule::in(array_keys(Helper::getStates()))
            ],
            'received_at'                 => 'required|date_format:d/m/Y H:i',
            'amount'                      => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'certificate_needed'          => 'required|boolean',
            'name_on_certificate'         => 'required',
            'special_allocation_required' => 'required|boolean',
            'special_allocation_details'  => 'required_if:special_allocation_required,1|min:3',
            'contact_address'             => '',
            'contact_suburb'              => '',
            'contact_postcode'            => 'sometimes|nullable|numeric',
            'contact_email'               => 'sometimes|nullable|email',
            'contact_phone'               => 'sometimes|nullable|numeric',
            'contact_mobile'              => 'sometimes|nullable|numeric',
            //'online_donation'             => 'required|boolean',
        ];

        if (! (bool) request()->get('special_allocation_required')) {
            $rules['special_allocation_details'] = 'sometimes|nullable';
        }

        return $rules;
    }

    public function deactivate(Request $request)
    {
        $this->authorize('create.certificates');

        $donations = $request->get('donations');
        $force     = (bool) $request->get('force');

        if (empty($donations)) {
            return response()->json([
                'msg' => 'Invalid donations supplied.'
            ], 400);
        }

        $donations = Helper::getRowIds($donations);

        // Check for unallocated donations
        foreach ($donations as $donation_id) {
            $donation = Donation::find($donation_id);
            if (!$donation->isNotAllocated() && !$donation->isLesserThanSponsorshipValue()) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => 'One or more selected Donation is already allocated. Please review your selection.'
                ], 400);
            }
        }

        if (!$force) {
            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'You have selected %s Donation%s for DELETION. Please confirm',
                    count($donations),
                    (count($donations) > 1 ? 's' : '')
                )
            ], 400);
        }

        Donation::whereIn('id', $donations)->update(['is_active' => 0]);

        return response()->json([
            'msg' => 'Selected donations have been successfully deleted.'
        ]);
    }

    public function generate(Request $request)
    {
        $this->authorize('create.certificates');

        $donations = $request->get('donations');
        $force     = (bool) $request->get('force');

        if (empty($donations)) {
            return response()->json([
                'msg' => 'Invalid donations supplied.'
            ], 400);
        }

        $donations = Helper::getRowIds($donations);

        // Only allow fully allocated or less than sponsorship donations
        foreach ($donations as $donation_id) {
            $donation = Donation::find($donation_id);

            if ($force) {
                continue;
            }

            if ($donation->isLesserThanSponsorshipValue()) {
                continue;
            }

            if ($donation->isFullyAllocated()) {
                continue;
            }

            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'One or more selected Donations is Partially allocated or Unallocated. Please review your selection or Confirm certificates generation for only those Donations that are fully allocated or Smaller than $%s.',
                    $donation->sponsorship_value
                )
            ], 400);
        }

        // Save to db, then redirect
        $certificate = new Certificate();
        $certificate->data = serialize($donations);
        $certificate->save();

        return response()->json([
            'msg' => 'Redirecting...',
            'redirect' => route('certificates.index', ['hash' => Hashids::encode($certificate->id)])
        ]);
    }

    public function students(Request $request, Donation $donation)
    {
        $this->authorize('update.donations');

        $student_ids = $request->validate([
            'student_ids' => 'required',
            'student_ids.*' => 'required|integer|exists:students,id',
        ]);

        $remaining   = $donation->total_sponsorships - $donation->sponsorships->count();
        $student_ids = array_unique($student_ids['student_ids']);

        if (count($student_ids) != $remaining && ! (bool) $request->get('force')) {
            return response()->json([
                'type' => 'dialog',
                'msg'  => sprintf(
                    'You have selected %s student%s and this donation has %s Student payment%s available. Please confirm to proceed or cancel allocation.',
                    count($student_ids),
                    count($student_ids) > 1 ? 's' : '',
                    $remaining,
                    $remaining > 1 ? 's' : ''
                )
            ], 422);
        }

        foreach ($student_ids as $student_id) {
            $student = Student::find($student_id);

            if ($student->is_allocated) {
                return response()->json([
                    'type' => 'alert',
                    'msg'  => 'One or more selected students is already allocated. Please review your selection.',
                ], 422);
            }
        }

        $allocated = 0;

        foreach ($student_ids as $student_id) {
            if ($allocated >= $remaining) {
                break;
            }

            $sponsorship = new Sponsorship;
            $student     = Student::find($student_id);

            $sponsorship->donation_id = $donation->id;
            $sponsorship->student_id  = $student->id;

            $result = $sponsorship->save();

            if ($result) {
                DB::transaction(function () use ($student, $donation) {
                    $donation->allocated_sponsorships = $donation->allocated_sponsorships + 1;
                    $donation->save();

                    $student->is_allocated = true;
                    $student->save();
                });
            }

            $allocated++;
        }

        return response()->json([
            'msg'      => 'Redirecting...',
            'redirect' => route('donations.edit', $donation),
        ]);
    }

    public function showSettings()
    {
        $this->authorize('update.settings');

        $treshold  = Treshold::current()->amount;

        return view('donations.settings')->with(compact('treshold'));
    }

    public function updateSettings(Request $request)
    {
        $this->authorize('update.donations');

        $data = $request->only(['amount']);

        //var_dump($data);

        $this->validate($request, ['amount' => 'required|regex:/^\d*(\.\d{1,2})?$/']);


        $treshold = Treshold::current()->amount;
        if ($request->amount != $treshold) {
            $new_treshold = Treshold::create($data);
        }

        $msg  = 'Donation settings has been successfully edited';

        //$donation->update($data);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function importApprove(DonationsFileImport $file)
    {
        $this->authorize('update.donations');

        $file->is_approved = true;
        $file->approved_at = Carbon::now();
        $file->updated_at = Carbon::now();
        $file->save();

        $donations = DonationsImportApprovalList::where('donations_import_approval_list.donations_file_import_id', $file->id)->get();

        $i = 0;
        foreach ($donations as $donation) {
            $data[$i]['name_on_certificate'] = $donation->name_on_certificate;
            $data[$i]['state'] = $donation->state;
            $data[$i]['sponsorship_value'] = $donation->sponsorship_value;
            $data[$i]['amount'] = $donation->amount;
            $data[$i]['total_sponsorships'] = $donation->total_sponsorships;
            $data[$i]['certificate_needed'] = $donation->certificate_needed;
            $data[$i]['received_at'] = $donation->received_at;
            $data[$i]['special_allocation_required'] = $donation->special_allocation_required;
            $data[$i]['special_allocation_details'] = $donation->special_allocation_details;
            $data[$i]['contact_address'] = $donation->contact_address;
            $data[$i]['contact_suburb'] = $donation->contact_suburb;
            $data[$i]['contact_postcode'] = $donation->contact_postcode;
            $data[$i]['contact_email'] = $donation->contact_email;
            $data[$i]['contact_mobile'] = $donation->contact_mobile;
            $data[$i]['online_donation'] = $donation->online_donation;
            $data[$i]['donations_file_import_id'] = $donation->donations_file_import_id;
            $data[$i]['created_at'] = $donation->created_at;
            $data[$i]['updated_at'] = $donation->updated_at;

            $i++;
        }

        $result = Donation::insert($data);

        flash('Donations Import approved successfully.')->success()->important();

        return redirect()->route('donations.import-list');
    }

    /**
     * soft delete import file
     *
     * @return void
     */
    public function deleteImport($id)
    {
        $this->authorize('update.donations');

        DonationsFileImport::find($id)->delete();

        flash('Donations Import has been deleted successfully.')->success()->important();

        return redirect()->route('donations.import-list');
    }

    public function bulkDeleteImport(Request $request)
    {
        $this->authorize('update.donations');

        $donations = $request->get('donations');
        $force     = (bool) $request->get('force');

        if (empty($donations)) {
            return response()->json([
                'msg' => 'Invalid donations supplied.'
            ], 400);
        }

        $donations = Helper::getRowIds($donations);

        if (!$force) {
            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'You have selected %s Donation%s for DELETION. Please confirm',
                    count($donations),
                    (count($donations) > 1 ? 's' : '')
                )
            ], 400);
        }

        DonationsFileImport::whereIn('id', $donations)->delete();

        return response()->json([
            'msg' => 'Selected donations have been successfully deleted.'
        ]);
    }

    public function download($hash)
    {
        $this->authorize('read.donations');

        $document_id = Hashids::decode($hash);

        if (empty($document_id)) {
            abort(403);
        }

        $document = DonationsFileImport::find($document_id[0]);

        if (!$document) {
            abort(403);
        }

        return response()->download(storage_path('app/' . $document->path), $document->file);
    }


}
