@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="intro">
                <h1 class="page-title text-center">Assist a Student Dashboard</h1>
                <p>Welcome to the new Assist a Student web application that allows you to easily maintain and manage the students database and donations.</p>
                <p>Please take time to read the guide below.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <strong><a href="{{ Helper::getDocUrl('guide') }}">Download AAS database guide in PDF format.</a></strong></p>
            </div>
            <ul class="cta list-inline text-center">
                <li class="cta-item">
                    <a href="{{ route('galleries.index') }}">
                        <img src="{{ asset('assets/img/bg-cta-6.jpg') }}" alt="Students Gallery" class="cta-img">
                        <span class="cta-link text-center">Students Gallery</span>
                    </a>
                </li>
                <li class="cta-item">
                    <a href="{{ route('stats.index') }}">
                        <img src="{{ asset('assets/img/bg-cta-1.jpg') }}" alt="Stats" class="cta-img">
                        <span class="cta-link text-center">Stats</span>
                    </a>
                </li>
                <li class="cta-item">
                    <a href="{{ route('students.list') }}">
                        <img src="{{ asset('assets/img/bg-cta-2.jpg') }}" alt="Manage Students" class="cta-img">
                        <span class="cta-link text-center">Manage Students</span>
                    </a>
                </li>
                <li class="cta-item">
                    <a href="{{ route('donations.list') }}">
                        <img src="{{ asset('assets/img/bg-cta-3.jpg') }}" alt="Manage Donations" class="cta-img">
                        <span class="cta-link text-center">Manage Donations</span>
                    </a>
                </li>
                <li class="cta-item">
                    <a href="{{ route('users.list') }}">
                        <img src="{{ asset('assets/img/bg-cta-4.jpg') }}" alt="Manage Users" class="cta-img">
                        <span class="cta-link text-center">Manage Users</span>
                    </a>
                </li>
                <li class="cta-item">
                    <a href="{{ route('reports.index') }}">
                        <img src="{{ asset('assets/img/bg-cta-5.jpg') }}" alt="Reports" class="cta-img">
                        <span class="cta-link text-center">Reports</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
