@extends('layouts.app')

@section('title')
    Manage Users
@stop

@section('prejs')
    <script>
        window.columnConfig = [
            null,
            {
                data: 'id',
                className: 'text-center',
                render: function (data, type, row) {
                    return '<a href="/users/edit/' + data + '">' + data + '</a>';
                }
            },
            {
                data: 'state',
                className: 'text-center'
            },
            {
                data: 'first_name',
                className: 'text-center'
            },
            {
                data: 'last_name',
                className: 'text-center'
            },
            {
                data: 'role',
                className: 'text-center'
            },
            {
                data: 'mfa',
                className: 'text-center'
            },
            {
                data: 'last_login',
                className: 'text-center'
            },
            {
                data: 'email',
                className: 'text-center'
            },
        ];
    </script>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Users</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>This allows you to add, remove and edit existing users and their roles.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download AAS database guide in PDF format.</a></p>
                <div class="section">
                    <h2 class="section-title">Actions</h2>
                    <a href="{{ route('users.create') }}" class="btn btn-warning">Add User</a>
                    <a href="#" class="btn btn-danger hidden js-btn-action js-btn-user-multi-deactivate" data-text-default="Deactivate Selected User(s)" data-text-progress="Deactivating...">Deactivate Selected User(s)</a>
                </div>
                <div class="section">
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="is_allocated">User Active?</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All</option>
                                <option value="active" selected>Active</option>
                                <option value="not-active">Deactivated</option>
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
                        <label class="sr-only" for="user-search">Search users</label>
                        <input type="text" class="form-control js-table-search-input" id="user-search" placeholder="Search">
                        <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                {{ $users->links('pagination.basic') }}
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped js-table" data-total="{{ $users->total() }}" data-url="{{ route('users.datatables') }}" data-page-length="{{ $per_page }}" data-order-col="1" data-order-type="asc">
                    <thead>
                        <tr>
                            <th data-orderable="false"><input type="checkbox" class="js-select-all"></th>
                            <th class="text-center" data-name="id">User ID</th>
                            <th class="text-center" data-name="state">State</th>
                            <th class="text-center" data-name="first_name">First Name</th>
                            <th class="text-center" data-name="last_name">Last Name</th>
                            <th class="text-center" data-orderable="false">Role</th>
                            <th class="text-center" data-orderable="false">MFA Status</th>
                            <th class="text-center" data-name="last_login">Last Login</th>
                            <th class="text-center" data-name="email">Email</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        {{ $users->links('pagination.table') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/user.js') }}"></script>
@stop
