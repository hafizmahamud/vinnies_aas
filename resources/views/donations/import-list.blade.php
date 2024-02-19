@extends('layouts.app')

@section('title')
    Manage Donations
@stop

@section('prejs')
    <script>
        window.columnConfig = [
            null,
            {
                data: 'id',
                className: 'text-center',
                render: function (data, type, row) {
                    @if ($can_edit)
                        // return '<a href="/donations/import-details/' + data + '">' + data + '</a>';
                        return '<a href="/donations/import/' + data + '">' + data + '</a>';

                    @else
                        return data;
                    @endif
                }
            },
            {
                data: 'state',
                className: 'text-center'
            },
            {
                data: 'file',
                className: 'text-center'
            },
            {
                data: 'is_approved',
                className: 'text-center'
            },
            {
                data: 'created_at',
                className: 'text-center'
            },
            {
                data: 'approved_at',
                className: 'text-center'
            },
        ];
    </script>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Import Donations List</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>This allows you to check all the imported donations and their approved status and upload new import donation files.</p>
            </div>
        </div>
        <div class="section clearfix">
            <h2 class="section-title">Actions</h2>
            <a href="{{ route('donations.import') }}" class="btn btn-warning">Import a new File</a>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="donation-search">Search donations</label>
                        <input type="text" class="form-control js-table-search-input" id="donation-search" placeholder="Search">
                        <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                {{ $donations->links('pagination.basic') }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped js-table" data-total="{{ $donations->total() }}" data-url="{{ route('donations.import-datatables') }}" data-page-length="{{ $per_page }}" data-order-col="4" data-order-type="desc">
                    <thead>
                        <tr>
                            <th data-orderable="false"><input type="checkbox" class="js-select-all"></th>
                            <th class="text-center" data-name="id">File ID</th>
                            <th class="text-center" data-name="state">State</th>
                            <th class="text-center" data-name="file">File Name</th>
                            <th class="text-center" data-name="is_approved">Status</th>
                            <th class="text-center" data-name="created_at">Uploaded Date</th>
                            <th class="text-center" data-name="approved_at">Approved Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-6">
                        @if ($can_edit)
                            <a href="#" class="btn btn-danger js-btn-donation-import-delete" data-text-default="Delete" data-text-progress="Deleting...">Delete</a>
                        @endif
                    </div>
                    <div class="col-sm-6 text-right">
                        {{ $donations->links('pagination.table') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/donation.js') }}"></script>
@stop
