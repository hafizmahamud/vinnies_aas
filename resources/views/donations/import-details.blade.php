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
                    @if ($can_edit && !$file->is_approved)
                        return '<a href="/donations/import/edit/' + data + '">' + data + '</a>';
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
              data: 'received_at',
              className: 'text-center'
            },
            {
                data: 'name_on_certificate',
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
                data: 'amount',
                className: 'text-center'
            },
            // {
            //     data: 'total_sponsorships',
            //     className: 'text-center'
            // },
            {
                data: 'certificate_needed',
                className: 'text-center'
            },
            {
                data: 'address',
                className: 'text-center'
            },
            {
                data: 'contact',
                className: 'text-center'
            },
            {
                data: 'online_donation',
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
                <p>In here you can verify the Imported Donation and make the approval.</p>
            </div>
        </div>
        <div class="section clearfix">
            <h2 class="section-title">Actions</h2>

            @if (!$file->is_approved)
                <a href="{{ route('donations.import-approve', $file) }}" class="btn btn-warning">Approve Donation Import</a>
            @else
                <a href="#" class="btn btn-success" disabled>Donations Approved</a>
            @endif

            @if (!$file->is_approved)
                <a href="{{ route('donations.import-delete', $file->id) }}" id="delete" class="btn btn-danger delete">Delete Donation Import</a>
            @endif

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
                <table class="table table-striped js-table" data-total="{{ $donations->total() }}" data-url="{{ route('donations.import-details-datatables',$file->id) }}" data-page-length="{{ $per_page }}" data-order-col="1" data-order-type="desc">
                    <thead>
                        <tr>
                            <th data-orderable="false"></th>
                            <th class="text-center" data-name="id">Temporary ID</th>
                            <th class="text-center" data-name="state">Donation State</th>
                            <th class="text-center" data-name="received_at">Received Date</th>
                            <th class="text-center" data-name="name_on_certificate">Donor Certificate Name</th>
                            <th class="text-center" data-name="special_allocation_required">Special Allocation Required</th>
                            <th class="text-center" data-name="special_allocation_details">Special Allocation Details</th>
                            <th class="text-center" data-name="amount">Donation Amount</th>
                            <!-- <th class="text-center" data-name="total_sponsorships">Total Student Payments</th> -->
                            <th class="text-center" data-name="certificate_needed">Desires Certificate/Letter</th>
                            <th class="text-center" data-name="address">Address</th>
                            <th class="text-center" data-name="contact">Contact</th>
                            <th class="text-center" data-name="online_donation">Online Donation</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-6 text-right">
                        {{ $donations->links('pagination.table') }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            File : <a href="{{ route('donations.download', Hashids::encode([$file->id])) }}"> {{ $file->file }} </a>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/donation.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.delete').click(function(e) {
            if(!confirm('Are you sure you want to delete <?php echo $file->file;?>?')) {
                e.preventDefault();
            }
        });
    });
</script>
@stop
