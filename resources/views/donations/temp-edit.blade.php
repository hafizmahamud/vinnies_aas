@extends('layouts.app')

@section('title')
    Edit Donation
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit Imported Donation</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <hr>
                <div class="row well">
                    <div class="col-sm-4">
                        <p><strong>Temporary ID: {{ $donation->id }}</strong></p>
                        <p><strong>Donation Status: </strong>
                            @if ($donation->is_active)
                                <span class="text-success">New Import</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>Added on: </strong>{{ $donation->created_at->format(config('vinnies.date_format')) }}</p>
                    </div>
                    <div class="col-sm-4">
                        <p><strong>Donor desires certificate: </strong>{{ $donation->certificate_needed ? 'Yes' : 'No' }}</p>
                        <p><strong>Certificate already printed: </strong>{{ $donation->is_printed ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
                <hr>
            </div>
        </div>

        {!! Form::model($donation, ['route' => ['donations.import-edit', $donation->id], 'class' => 'form js-form', 'method' => 'patch']) !!}
            @include('donations.temp-form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit donation changes" data-text-progress="Submitting...">Submit donation changes</button>
                </div>
            </div>



        </form>

    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/donation.js') }}"></script>
@stop
