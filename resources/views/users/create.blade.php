@extends('layouts.app')

@section('title')
    Add User
@stop

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Add User</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
            </div>
        </div>

        {!! Form::open(['route' => 'users.create', 'class' => 'form js-form', 'data-redirect' => route('users.list'), 'data-reset' => 1]) !!}
            @include('users.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Add User to System" data-text-progress="Adding...">Add User to System</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/user.js') }}"></script>
@stop
