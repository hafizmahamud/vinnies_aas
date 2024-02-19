@extends('layouts.app')

@section('title')
    Certificates PDF generation and Download
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Certificates PDF generation and Download</h1>

                @if ($count['donations'] == 0)
                    <p>No donations selected are fully allocated donations or donations less than student payment value.</p>
                    <br>
                    <a href="{{ route('donations.list') }}" class="btn btn-primary">Back to Manage Donations</a>
                @else
                    <p>You have selected {{ $count['donations'] }} certificate{{ $count['donations'] > 1 ? 's' : '' }} corresponding to {{ $count['donations'] }} donation{{ $count['donations'] > 1 ? 's' : '' }} assigned to a total of {{ $count['students'] }} student{{ $count['students'] > 1 ? 's' : '' }}.</p>
                    <p>Please click below to generate and download a single PDF file that comprises all {{ $count['donations'] }} certificate{{ $count['donations'] > 1 ? 's' : '' }}.</p>
                    <br>
                    <a href="{{ route('certificates.download', ['hash' => $hash]) }}" class="btn btn-primary">Generate and Download the PDF file</a>
                @endif
            </div>
        </div>

    </div>
@stop
