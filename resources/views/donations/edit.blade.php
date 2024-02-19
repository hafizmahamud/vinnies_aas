@extends('layouts.app')

@section('title')
    Edit Donation
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
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit Donation</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <hr>
                <div class="row well">
                    <div class="col-sm-4">
                        <p><strong>Donation ID: {{ $donation->id }}</strong></p>
                        <p><strong>Donation Status: </strong>
                            @if ($donation->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Added on: </strong>{{ $donation->created_at->format(config('vinnies.date_format')) }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p><strong>Student Payment Value at Creation Time: </strong>${{ $donation->sponsorship_value }}</p>
                        <p><strong>Total Number of Student Payments: </strong>{{ $donation->total_sponsorships }}</p>
                        <p><strong>No of allocated Student Payments: </strong>{{ $donation->sponsorships->count() }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p><strong>Donor desires certificate: </strong>{{ $donation->certificate_needed ? 'Yes' : 'No' }}</p>
                        <p><strong>Certificate already printed: </strong>{{ $donation->is_printed ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        {!! Form::model($donation, ['route' => ['donations.edit', $donation->id], 'class' => 'form js-form', 'method' => 'patch']) !!}
            @include('donations.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit donation changes" data-text-progress="Submitting...">Submit donation changes</button>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-sm-12">
                    <h5>Allocated Students</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Student ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Assistance Year</th>
                                <th>Class</th>
                                <th>Country</th>
                                <th>Age</th>
                                <th>Gender</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($donation->sponsorships->count())
                                @foreach ($donation->sponsorships as $key => $sponsorship)
                                    <tr>
                                        <td><a href="{{ route('students.edit', ['student' => $sponsorship->student->id]) }}">{{ $key + 1 }}</a></td>
                                        <td><a href="{{ route('students.edit', ['student' => $sponsorship->student->id]) }}">{{ $sponsorship->student->id }}</a></td>
                                        <td>{{ $sponsorship->student->first_name }}</td>
                                        <td>{{ $sponsorship->student->last_name }}</td>
                                        <td>{{ $sponsorship->student->assistance_year }}</td>
                                        <td>{{ $sponsorship->student->class }}</td>
                                        <td>{{ $sponsorship->student->country }}</td>
                                        <td>{{ $sponsorship->student->age ? $sponsorship->student->age : 'N/A' }}</td>
                                        <td>{{ $sponsorship->student->gender }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">
                                        No students available
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            @unless ($donation->isFullyAllocated() || $donation->isLesserThanSponsorshipValue())
                <div class="row">
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modal-students">Custom student allocation</button>
                    </div>
                </div>
            @endunless
        </form>

        @include('partials.modal.students')
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/donation.js') }}"></script>
@stop
