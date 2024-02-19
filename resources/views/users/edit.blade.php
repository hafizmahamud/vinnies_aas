@extends('layouts.app')

@section('title')
    Edit User
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit User</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <hr>
                <div class="row well">
                    <div class="col-sm-6">
                        <p><strong>User Status: </strong>
                            @if ($user->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>User Last Login: </strong>{{ $user->getLastLoginDt() ? $user->getLastLoginDt()->format(config('vinnies.date_format')) : 'Never' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>MFA Status: </strong>
                            @if ($user->hasGoogle2FAEnabled())
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        @if ($user->hasGoogle2FAEnabled())
                            <form action="{{ route('2fa.admin.reset', $user) }}" method="post" >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-danger">Disable two-factor authentication</button>
                            </form>
                        @endif
                    </div>
                </div>
                <hr>
            </div>
        </div>

        {!! Form::model($user, ['route' => ['users.edit', $user->id], 'class' => 'form js-form', 'method' => 'patch']) !!}
            @include('users.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit user changes" data-text-progress="Submitting...">Submit user changes</button>
                </div>

                <div class="col-sm-3 col-sm-offset-6 text-right">
                    @if (!$user->is_active)
                        <button type="button" class="btn btn-danger js-btn-user-reactivate" data-user-id="{{ $user->id }}" data-text-default="Reactivate User" data-text-progress="Reactivating...">Reactivate User</button>
                    @else
                        <button type="button" class="btn btn-danger js-btn-user-deactivate" data-user-id="{{ $user->id }}" data-text-default="Deactivate user" data-text-progress="Deactivating...">Deactivate User</button>
                    @endif
                </div>
                <div class="col-sm-6 col-sm-offset-6 text-right mt-2">
                    @if ($user->has_accepted_terms == "1")
                        <button type="button" class="btn btn-danger js-btn-user-signtos" data-user-id="{{ $user->id }}" data-text-default="Re-sign Terms of Use" data-text-progress="Re-sign Term of Use...">Re-sign Terms of Use</button>
                    @else
                        <button disabled type="button" class="btn btn-danger" data-user-id="{{ $user->id }}" data-text-default="Waiting for user to Sign Terms of Use">Waiting for user to Sign Terms of Use</button>
                    @endif
                </div>

              </div>
            </div>
        </form>
        <hr>

        <div class="container">
        <h4 class="form-heading">User Documents</h4>
        
        <div class="section-documents" data-id="{{ $user->id }}" data-type="User"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading...</div>
        
        @include('modals.document.create')
        @include('modals.document.edit')

        @include('mustache.documents')

        <p>
        <p>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/user.js') }}"></script>
@stop
