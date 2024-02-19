@extends('layouts.app')

@section('title')
    Stats
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Stats</h1>
            </div>
        </div>
        <h3 class="stats-title text-center">Students</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Total Students</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['students']['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Allocated Students</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['students']['allocated'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Unallocated Students</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['students']['unallocated'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <h3 class="stats-title text-center">Donations</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Total Donations</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['donations']['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Fully Allocated Donations</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['donations']['fully_allocated'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Partially Allocated Donations</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['donations']['partially_allocated'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4 col-sm-offset-2">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Donations Less than Student Payments</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['donations']['lesser_than_sponsorship_value'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Unallocated Donations</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['donations']['unallocated'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <h3 class="stats-title text-center">Student Payments</h3>
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Total Student Payments</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['sponsorships']['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Allocated Student Payments</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['sponsorships']['allocated'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="panel panel-default panel-stats text-center">
                    <div class="panel-heading">
                        <h3 class="panel-title">Unallocated Student Payments</h3>
                    </div>
                    <div class="panel-body">
                        <span class="text-large">{{ $stats['sponsorships']['unallocated'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
