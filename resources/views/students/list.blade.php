@extends('layouts.app')

@section('title')
    Manage Students
@stop

@section('prejs')
    <script>
        window.columnConfig = [
            null,
            {
                data: 'id',
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="/students/edit/' + data + '">' + data + '</a>';
                }
            },
            {
                data: 'first_name',
                className: 'text-center'
            },
            {
                data: 'last_name',
                className: 'text-center'
            },
            {
                data: 'is_allocated',
                className: 'text-center'
            },
            {
                data: 'assistance_year',
                className: 'text-center'
            },
            {
                data: 'class',
                className: 'text-center'
            },
            {
                data: 'country',
                className: 'text-center'
            },
            {
                data: 'age',
                className: 'text-center'
            },
            {
                data: 'gender',
                className: 'text-center'
            },
        ];
    </script>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Students</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>This allows you to check the allocated and unallocated students, add new ones via the import facility as well as add one by one.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download AAS database guide in PDF format.</a></p>
                <div class="section clearfix">
                    <h2 class="section-title">Actions</h2>
                    <a href="{{ route('students.create') }}" class="btn btn-warning">Add Student</a>
                    <a href="{{ route('students.import') }}" class="btn btn-default">Import Students</a>
                    <a href="#" class="btn btn-danger pull-right hidden js-btn-action js-btn-student-allocate" data-text-default="Allocate selected students" data-text-progress="Allocating...">Allocate selected students</a>
                </div>
                <div class="section">
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="is_allocated">Allocation Status</label>
                            <select name="is_allocated" id="is_allocated" class="form-control">
                                <option value="all">All Students</option>
                                <option value="1">Allocated Students</option>
                                <option value="0">Unallocated Students</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assistance_year">Assistance Year</label>
                            <select name="assistance_year" id="assistance_year" class="form-control">
                                <option value="all">All Years</option>
                                @foreach (range(date('Y'), config('vinnies.start_year')) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="has_age">Age provided</label>
                            <select name="has_age" id="has_age" class="form-control">
                                <option value="all">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">N/A</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="age_from">Age from</label>
                            <input type="text" id="age_from" class="form-control" name="age_from" style="width:50px;">
                            <span class="ml-1">to</span>
                            <input type="text" id="age_to" class="form-control ml-1" name="age_to" style="width:50px;">
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="all">All</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="N/A">N/A</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="student-search">Search students</label>
                        <input type="text" class="form-control js-table-search-input" id="student-search" placeholder="Search">
                        <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                {{ $students->links('pagination.basic') }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped js-table" data-total="{{ $students->total() }}" data-url="{{ route('students.datatables') }}" data-page-length="{{ $per_page }}" data-order-col="1" data-order-type="desc">
                    <thead>
                        <tr>
                            <th data-orderable="false"><input type="checkbox" class="js-select-all"></th>
                            <th class="text-center" data-name="id">Student ID</th>
                            <th class="text-center" data-name="first_name">First Name</th>
                            <th class="text-center" data-name="last_name">Last Name</th>
                            <th class="text-center" data-name="is_allocated">Allocated</th>
                            <th class="text-center" data-name="assistance_year">Assistance Year</th>
                            <th class="text-center" data-name="class">Class</th>
                            <th class="text-center" data-name="country">Country</th>
                            <th class="text-center" data-name="age">Age</th>
                            <th class="text-center" data-name="gender">Gender</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-6">
                        <a href="#" class="btn btn-danger hidden js-btn-action js-btn-student-delete" data-text-default="Delete selected unallocated yet students" data-text-progress="Deleting...">Delete selected unallocated yet students</a>
                    </div>
                    <div class="col-sm-12 text-center">
                        {{ $students->links('pagination.table') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/student.js') }}"></script>
@stop
