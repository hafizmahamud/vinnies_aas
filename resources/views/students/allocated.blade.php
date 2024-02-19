@extends('layouts.app')

@section('title')
    Students successfully allocated
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Students successfully allocated</h1>
                <p>Thank you, {{ $students }} student{{ $students > 0 ? 's' : '' }} have been successfully allocated to {{ $donations }} donation{{ $donations > 0 ? 's' : '' }}.</p>
                <p>{{ $excluded }} donation{{ $excluded > 1 ? 's' : '' }} with special requirements were excluded from the allocation pool. Please allocate them separately using the custom allocation</p>
                <p>To print the certificates please go into the Donations section to select desired certificates to be printed.</p>
                <br>
                <a href="{{ route('donations.list') }}" class="btn btn-primary">Go to Manage Donations</a>
            </div>
        </div>

    </div>
@stop
