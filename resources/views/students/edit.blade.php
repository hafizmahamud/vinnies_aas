@extends('layouts.app')

@section('title')
    Edit Student
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit Student</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <p><em><strong>NB:</strong> For already allocated students the Assistance Year field cannot be modified.</em></p>
                <hr>
                <div class="row well">
                    <div class="col-sm-6">
                        <p><strong>Student ID: {{ $student->id }}</strong></p>
                        <p><strong>Student Status: </strong>
                            @if ($student->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Added on: </strong>{{ $student->created_at->format(config('vinnies.date_format')) }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>Is Allocated: </strong>{{ $student->is_allocated ? 'Yes' : 'No' }}</p>
                        <p><strong>Allocated Donation ID: </strong>{{ $student->is_allocated ? $student->sponsorship->donation_id : '-' }}</p>
                        <p><strong>Allocated Student Payment ID: </strong>{{ $student->is_allocated ? $student->sponsorship->id : '-' }}</p>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        {!! Form::model($student, ['route' => ['students.edit', $student->id], 'class' => 'form js-form', 'method' => 'patch']) !!}
            @include('students.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit student changes" data-text-progress="Submitting...">Submit student changes</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/user.js') }}"></script>
@stop
