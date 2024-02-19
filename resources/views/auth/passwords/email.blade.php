@extends('layouts.app')

@section('title')
    Reset Password
@endsection

@section('content')
<div class="container">

    <div class="row">
        <div class="col-sm-6 col-sm-offset-2">
            <div class="panel panel-login panel-login-lg">
                <div class="panel-heading text-center">
                    <a href="{{ route('home') }}" class="login-logo"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Vinnies"></a>
                    Reset Password
                </div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email">{{ __('E-Mail Address') }}</label>

                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="help-block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group is-last text-center">
                            <button type="submit" class="btn btn-warning text-uppercase">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
