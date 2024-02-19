@extends('layouts.app')

@section('title')
    Reset Password
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-login">
                <div class="panel-heading text-center">
                    <a href="{{ route('home') }}" class="login-logo"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Vinnies"></a>
                    Reset Password
                </div>
                <div class="panel-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email">{{ __('E-Mail Address') }}</label>

                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="help-block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">{{ __('Password') }}</label>

                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                            <small class="text-warning">The password must be at least 8 characters and contain at least one uppercase, one lowercase letter, one number and one symbol.</small>
                            @error('password')
                                <span class="help-block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <div class="form-group is-last text-center">
                            <button type="submit" class="btn btn-warning text-uppercase">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
