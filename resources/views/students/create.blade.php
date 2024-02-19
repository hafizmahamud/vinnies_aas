@extends('layouts.app')

@section('title')
    Add Student
@stop

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Add Student</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <p><em><strong>NB:</strong> If the Student does not have the name split in Firstname and Lastname please input the full name in the Firstname field.</em></p>
            </div>
        </div>

        {!! Form::open(['route' => 'students.create', 'class' => 'form js-form', 'data-redirect' => route('students.list'), 'data-reset' => 1]) !!}
            @include('students.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Add Student to System" data-text-progress="Adding...">Add Student to System</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/student.js') }}"></script>
@stop
