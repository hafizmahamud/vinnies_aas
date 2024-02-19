@extends('layouts.app')

@section('title')
    Import Students
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
                <h1 class="page-title text-center">Import Students</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>In here you can upload the file containing new students.</p>
                <p>Please make sure that the csv file you are going to upload is based on the standard format containing the standard fields required.</p>
                <p>You can download the Students CSV Template by <a href="{{ Helper::getDocUrl('students.template') }}">clicking here</a>. An example of an import file can also be downloaded <a href="{{ Helper::getDocUrl('students.example') }}">here</a>.</p>
                <p>We recommend that before uploading any new Students file, you check the Student list to see if they have not been uploaded already.</p>
                <p>Please as well make sure you input the correct Assistance Year field below.</p>
                <p class="text-danger">** Please note that starting 1 Feb 2018 the CSV template for Students has been modified to include new fields. Updated user guide explains the changes. Please download updated versions.</p>
            </div>
        </div>
        <div class="row import-section">
            <div class="col-sm-4 col-sm-offset-1">
                <form action="{{ route('students.import') }}" method="post" class="form form-import js-form">
                    <div class="form-group">
                        <label for="assistance_year">Assistance Year <sup class="text-danger">*</sup></label>
                        <select name="assistance_year" id="assistance_year" class="form-control">
                            @foreach (range(date('Y'), config('vinnies.start_year')) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
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
<script src="{{ Helper::asset('assets/js/student.js') }}"></script>
@stop
