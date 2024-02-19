<?php

namespace App\Http\Controllers;

use App\Vinnies\Import\Students as StudentsImport;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Vinnies\Allocator;
use App\Vinnies\Helper;
use App\Donation;
use App\Student;
use Hashids;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.students');

        $per_page = config('vinnies.pagination.students');
        $students = Student::where('is_active', 1)->paginate($per_page);

        return view('students.list')->with(compact('students', 'per_page'));
    }

    public function showCreateForm()
    {
        $this->authorize('create.students');

        $years = range(date('Y'), config('vinnies.start_year'));

        return view('students.create')->with(compact('years'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.students');

        // Validate this
        $this->validate($request, $this->rules());

        $data    = $request->only(['first_name', 'last_name', 'assistance_year', 'class', 'country', 'education_sector', 'age', 'gender']);
        $msg     = 'New student has been successfully created';
        $student = Student::create($data);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(Student $student)
    {
        $this->authorize('update.students');

        $years = range(date('Y'), config('vinnies.start_year'));

        return view('students.edit')->with(compact('student', 'years'));
    }

    public function edit(Request $request, Student $student)
    {
        $this->authorize('update.students');

        $this->validate($request, $this->rules());

        $keys = ['first_name', 'last_name', 'class', 'country', 'education_sector', 'age', 'gender'];

        // Only unallocated student can be edited for their assistance year
        if (!$student->is_allocated) {
            $keys[] = 'assistance_year';
        }

        $data = $request->only($keys);
        $msg  = 'Student has been successfully edited';

        $student->update($data);

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
        $this->authorize('update.students');
        return view('students.import');
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.students');

        $students = Student::where('is_active', 1);
        $students = $this->sortModelFromRequest($students, $request);

        if (!empty($filters = $request->get('filters'))) {
            if ($filters['assistance_year'] !== 'all') {
                $students->where('assistance_year', $filters['assistance_year']);
            }

            if ($filters['is_allocated'] !== 'all') {
                $students->where('is_allocated', $filters['is_allocated']);
            }

            if ($filters['has_age'] !== 'all') {
                switch ($filters['has_age']) {
                    case 'yes':
                        $students->whereNotNull('age');
                        break;

                    case 'no':
                        $students->whereNull('age');
                        break;
                }
            }

            if ($filters['gender'] !== 'all') {
                $students->where('gender', $filters['gender']);
            }

            if (!empty($filters['age_from'])) {
                $students->where('age', '>=', (int) $filters['age_from']);
            }

            if (!empty($filters['age_to'])) {
                $students->where('age', '<=', (int) $filters['age_to']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $students->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('class', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('country', 'LIKE', '%' . $keyword . '%');
            });
        }

        $students = $students->paginate(config('vinnies.pagination.students'));
        $data     = $this->getDatatableBaseData($students, $request);

        foreach ($students as $student) {
            $data['data'][] = [
                'id'              => $student->id,
                'first_name'      => $student->first_name,
                'last_name'       => $student->last_name,
                'assistance_year' => $student->assistance_year,
                'is_allocated'    => ($student->is_allocated ? 'Yes' : 'No'),
                'class'           => $student->class,
                'country'         => $student->country,
                'age'             => $student->age ?? 'N/A',
                'gender'          => $student->gender ?? 'N/A',
                'DT_RowId'        => 'row_' . $student->id
            ];
        }

        return $data;
    }

    public function import(Request $request)
    {
        $this->authorize('update.students');

        $this->validate($request, [
            'assistance_year' => 'required|digits:4',
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

        $import = new StudentsImport($csv);
        $error  = [
            'type'    => 'dialog',
            'confirm' => false,
            'msg'     => ''
        ];

        if (!$import->validHeaders()) {
            $error['msg'] = 'The uploaded CSV does not match the Student template. Please review the CSV file.';

            return response()->json($error, 400);
        }

        if (!$import->validAssistanceYear($request->get('assistance_year')) && !$force) {
            $error['confirm'] = true;
            $error['msg']     = 'The assistance year does not match. Please confirm upload or cancel.';

            return response()->json($error, 400);
        }

        if (!$import->validAge()) {
            $error['msg'] = 'The Age field at row ' . $import->invalidRow . ' cannot be correctly interpreted. Please set to a valid integer or leave empty.';

            return response()->json($error, 400);
        }

        if (!$import->validGender()) {
            $error['msg'] = 'The Gender field at row ' . $import->invalidRow . ' cannot be correctly interpreted. Please set to "Male" or "Female" or leave empty.';

            return response()->json($error, 400);
        }

        if (!$import->validEducationSector()) {
            $error['msg'] = 'The Education Sector field at row ' . $import->invalidRow . ' cannot be correctly interpreted. Please set to "Primary", "Secondary" or "Tertiary" or leave empty.';

            return response()->json($error, 400);
        }

        // All validation passes
        $result = $import->import();

        if ($result) {
            $msg = sprintf(
                'Successfully uploaded %d student%s',
                $result['count'],
                ($result['count'] > 1 ? 's' : '')
            );

            return response()->json(['msg' => $msg]);
        } else {
            $error['msg']     = 'Failed to import students data into database';

            return response()->json($error, 400);
        }
    }

    private function rules()
    {
        return [
            'assistance_year' => 'required|digits:4',
            'class'           => 'required',
            'country'         => 'required',
            'first_name'      => 'required',
            'education_sector' => [
                'required',
                Rule::in(array_keys(Helper::getEducationSectors()))
            ],
            'age'    => 'sometimes|nullable|integer',
            'gender' => [
                'required',
                Rule::in(array_keys(Helper::getGenders()))
            ]
        ];
    }

    public function deactivate(Request $request)
    {
        $this->authorize('update.students');

        $students = $request->get('students');
        $force    = (bool) $request->get('force');

        if (empty($students)) {
            return response()->json([
                'msg' => 'Invalid students supplied.'
            ], 400);
        }

        $students = Helper::getRowIds($students);

        // Check for allocated students
        foreach ($students as $student_id) {
            if (Student::find($student_id)->is_allocated) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => 'One or more selected students is already allocated. Please review your selection.'
                ], 400);
            }
        }

        if (!$force) {
            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'You have selected %s Student%s for DELETION. Please confirm',
                    count($students),
                    (count($students) > 1 ? 's' : '')
                )
            ], 400);
        }

        Student::whereIn('id', $students)->update(['is_active' => 0]);

        return response()->json([
            'msg' => 'Selected students have been successfully deleted.'
        ]);
    }

    public function allocate(Request $request)
    {
        $this->authorize('update.students');

        $students = $request->get('students');
        $force    = (bool) $request->get('force');

        if (empty($students)) {
            return response()->json([
                'msg' => 'Invalid students supplied.'
            ], 400);
        }

        $students = Helper::getRowIds($students);

        // Check for allocated students
        foreach ($students as $student_id) {
            $student = Student::find($student_id);
            if ($student->is_allocated) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => 'One or more selected students is already allocated. Please review your selection.'
                ], 400);
            }

            if (($student->age || $student->gender != 'N/A') && !$force) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => true,
                    'msg'     => 'One or more students selected for random allocation have Age or Gender provided. These students are usually used for specific donations. To override and confirm anyway click the button "CONFIRM".',
                ], 400);
            }
        }

        // Check if enough sponsorships
        $allocator    = new Allocator($students);
        $sponsorships = $allocator->getAvailableSponsorships();

        if (count($students) > $sponsorships && !$force) {
            if ($sponsorships == 0) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => sprintf(
                        'There are not enough unallocated Student Payments available. You have selected %s Student%s and there are 0 Student Payments available',
                        count($students),
                        (count($students) > 1 ? 's' : '')
                    )
                ], 400);
            }

            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'There are not enough unallocated Student Payments available. You have selected %s Student%s and there are only %s Student Payment%s available. Please review your selection or confirm below the allocation for only %s student%s.',
                    count($students),
                    (count($students) > 1 ? 's' : ''),
                    $sponsorships,
                    ($sponsorships > 1 ? 's' : ''),
                    $sponsorships,
                    ($sponsorships > 1 ? 's' : '')
                )
            ], 400);
        }

        if (!$force) {
            return response()->json([
                'type'    => 'dialog',
                'confirm' => true,
                'msg'     => sprintf(
                    'You have selected %s Student%s for allocation. Please confirm.',
                    count($students),
                    (count($students) > 1 ? 's' : '')
                )
            ], 400);
        }

        $result = $allocator->allocate();

        return response()->json([
            'msg' => sprintf(
                '%s student%s have been successfully allocated to %s donation%s.',
                $result['students'],
                ($result['students'] > 1 ? 's' : ''),
                $result['donations'],
                ($result['donations'] > 1 ? 's' : '')
            ),
            'redirect' => route('students.allocated', ['hash' => Hashids::encode($result['students'], $result['donations'])])
        ]);
    }

    public function allocated($hash)
    {
        $result    = Hashids::decode($hash);
        $students  = $result[0];
        $donations = $result[1];
        $excluded  = Donation::where('special_allocation_required', 1)
            ->where('total_sponsorships', '>', 0)
            ->where('allocated_sponsorships', 0)
            ->count();

        return view('students.allocated')->with(compact('students', 'donations', 'excluded'));
    }
}
