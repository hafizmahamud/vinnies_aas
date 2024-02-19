@extends('layouts.app')

@section('title')
    Edit Gallery Item
@stop

@section('content')
    <div class="container">

        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('galleries.admin') }}">&larr; Go back to the Gallery Administration</a>
            </div>
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit Gallery Item</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
            </div>
        </div>

        <div class="row mt-1">
            <div class="col-sm-6">
                <p><strong>Gallery Item ID:</strong> {{ $gallery->id }}</p>
            </div>
            <div class="col-sm-6 text-right">
                <p>Last Updated on: <strong>{{ $gallery->updated_at->format('d/m/Y') }}</strong> at <strong>{{ $gallery->updated_at->format('H:i') }}</strong> by <strong>{{ $gallery->updated_by->getFullname() }}</strong>.</p>
            </div>
        </div>

        @include('galleries.form-upload')

        <hr>

        {!! Form::model($gallery, ['route' => ['galleries.update', $gallery], 'class' => 'form js-gallery-form']) !!}
            {{ csrf_field() }}
            {{ method_field('PATCH') }}

            @include('galleries.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit Gallery Item Changes" data-text-progress="Submitting...">Submit Gallery Item Changes</button>
                </div>
                <div class="col-sm-3 col-sm-offset-6 text-right">
                    <a href="#" class="btn btn-danger js-delete-gallery">Delete Gallery Item</a>
                </div>
            </div>
        </form>
    </div>

    <form action="{{ route('galleries.admin') }}" class="delete-galleries-form" method="post" style="display:none;">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}

        <input type="hidden" name="ids" value="{{ $gallery->id }}">
    </form>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/galleries.js') }}"></script>
@stop
