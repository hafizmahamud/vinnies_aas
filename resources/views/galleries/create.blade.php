@extends('layouts.app')

@section('title')
    Add Gallery Item
@stop

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Add Gallery Item</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
            </div>
        </div>

        @include('galleries.form-upload')

        <hr>

        {!! Form::open(['route' => 'galleries.index', 'class' => 'form js-gallery-form', 'data-reset' => 1]) !!}
            {{ csrf_field() }}
            @include('galleries.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Add Gallery Item" data-text-progress="Adding..." disabled>Add Gallery Item</button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/galleries.js') }}"></script>
@stop
