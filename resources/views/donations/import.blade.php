@extends('layouts.app')

@section('title')
    Import Donations
@stop

@section('prejs')
    <script>
        (function ($) {
            window.confirmCb = function () {
                var $form = $('.form-import');

                $form.find('[name="force"]').val(1);
                $form.submit();
                $form.find('[name="force"]').val(0);
            };
        })(jQuery);
    </script>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Import Donations</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>In here you can upload the file containing new donations ONLY from your state or territory.</p>
                <p>Please make sure that the csv file you are going to upload is based on the standard format containing the standard fields required.</p>
                <p>You can download the Donations CSV Template by <a href="{{ Helper::getDocUrl('donations.template') }}">clicking here</a>. An example of an import file can also be downloaded <a href="{{ Helper::getDocUrl('donations.example') }}">here</a>.</p>
                <p>We recommend that before uploading any new donations file, you check the donations list to see if the donations have not been uploaded already.</p>
                <p class="text-danger">** Please note that starting 29 Sept 2020 the CSV template for Donations has been modified to include new fields. Updated user guide explains the changes. Please download updated versions.</p>
            </div>
        </div>
        <div class="row import-section">
            <div class="col-sm-4 col-sm-offset-1">
                <form action="{{ route('donations.import') }}" method="post" class="form form-import js-form">
                    <div class="form-group">
                        <label for="state">Donation State/Territory <sup class="text-danger">*</sup></label>
                        <select name="state" id="state" class="form-control">
                            @foreach (Helper::getUserStates() as $key => $state)
                                @role('Full Admin')
                                    <option value="{{ $key }}" {{ $key == 'national' ? ' selected' : '' }}>{{ $state }}</option>
                                @else
                                    <option value="{{ $key }}">{{ $state }}</option>
                                @endrole
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="csv" class="btn btn-block btn-primary">Browse for CSV file</label>
                        <input type="file" name="csv" id="csv" class="hidden js-input-file" accept=".csv">
                        <p></p>
                        <p>File selected: <span class="js-file-selected">-</span></p>
                        <input type="hidden" name="force" value="0">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block btn-warning" data-text-default="Upload the selected file above" data-text-progress="Processing...">Upload the selected file above</button>
                    </div>
                </form>
            </div>
            <div class="col-sm-5 col-sm-offset-1">
                <img src="{{ asset('assets/img/bg-import.png') }}">
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/donation.js') }}"></script>
@stop
