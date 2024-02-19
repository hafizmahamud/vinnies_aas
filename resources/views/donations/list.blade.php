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
                        return '<a href="/donations/edit/' + data + '">' + data + '</a>';
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
                data: 'special_allocation_required',
                className: 'text-center'
            },
            {
                data: 'special_allocation_details',
                className: 'text-center'
            },
            {
                data: 'name_on_certificate',
                className: 'text-center'
            },
            {
                data: 'uploaded_at',
                className: 'text-center'
            },
            {
              data: 'approved_at',
              className: 'text-center'
            },
            {
              data: 'received_at',
              className: 'text-center'
            },
            {
                data: 'amount',
                className: 'text-center'
            },
            {
                data: 'total_sponsorships',
                className: 'text-center'
            },
            {
                data: 'allocated_sponsorships',
                className: 'text-center'
            },
            {
                data: 'certificate_needed',
                className: 'text-center'
            },
            {
                data: 'is_printed',
                className: 'text-center'
            },
        ];
    </script>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Donations</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>This allows you to check all the donations and their allocation status, add new donations one by one or via the import facility. As well this allows the National Office to print certificates.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download AAS database guide in PDF format.</a></p>
                <div class="section clearfix">
                    <h2 class="section-title">Actions</h2>
                    <a href="{{ route('donations.create') }}" class="btn btn-warning">Add Donations</a>
                    <a href="{{ route('donations.import-list') }}" class="btn btn-default">Import Donations</a>
                    @if ($can_set_amount)
                        <a href="{{ route('donations.settings') }}" class="btn btn-danger">Amount Setting</a>
                    @endif

                    @if ($can_edit)
                        <a href="#" class="btn btn-danger pull-right hidden js-btn-action js-btn-donation-generate" data-text-default="Generate certificates for selected donations" data-text-progress="Generating...">Generate certificates for selected donations</a>
                    @endif
                </div>
                <div class="section">
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <select name="donation_status" id="donation_status" class="form-control">
                                <option value="all">All Donations</option>
                                <option value="full">Fully Allocated Donations</option>
                                <option value="partial">Partially Allocated Donations</option>
                                <option value="unallocated">Unallocated Donations</option>
                                <option value="less">Smaller than Student Payments Value</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="certificate_needed">Desires Certificate</label>
                            <select name="certificate_needed" id="certificate_needed" class="form-control">
                                <option value="all">All</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_printed">Certificate printed</label>
                            <select name="is_printed" id="is_printed" class="form-control">
                                <option value="all">All</option>
                                <option value="1">Already printed</option>
                                <option value="0">Not printed yet</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="special_allocation_required">Special allocation required</label>
                            <select name="special_allocation_required" id="special_allocation_required" class="form-control">
                                <option value="all">All</option>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                    </form>
                </div>
            </div>
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
                <table class="table table-striped js-table" data-total="{{ $donations->total() }}" data-url="{{ route('donations.datatables') }}" data-page-length="{{ $per_page }}" data-order-col="1" data-order-type="desc">
                    <thead>
                        <tr>
                            <th data-orderable="false"><input type="checkbox" class="js-select-all"></th>
                            <th class="text-center" data-name="id">Donation ID</th>
                            <th class="text-center" data-name="state">Donation State</th>
                            <th class="text-center" data-name="special_allocation_required">Special Allocation Required</th>
                            <th class="text-center" data-name="special_allocation_details">Special Allocation Details</th>
                            <th class="text-center" data-name="name_on_certificate">Donor Certificate Name</th>
                            <th class="text-center" data-name="uploaded_at">Uploaded Date</th>
                            <th class="text-center" data-name="approved_at">Approved Date</th>
                            <th class="text-center" data-name="received_at">Received Date</th>
                            <th class="text-center" data-name="amount">Donation Amount</th>
                            <th class="text-center" data-name="total_sponsorships">Total Student Payments</th>
                            <th class="text-center" data-name="allocated_sponsorships">Allocated Student Payments</th>
                            <th class="text-center" data-name="certificate_needed">Desires Certificate/Letter</th>
                            <th class="text-center" data-name="is_printed">Certificate/Letter Printed</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-6">
                        @if ($can_edit)
                            <a href="#" class="btn btn-danger hidden js-btn-action js-btn-donation-delete" data-text-default="Delete selected unallocated yet donations" data-text-progress="Deleting...">Delete selected unallocated yet donations</a>
                        @endif
                    </div>
                    <div class="col-sm-12 text-center">
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
