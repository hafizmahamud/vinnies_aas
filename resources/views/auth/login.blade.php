@extends('layouts.app')

@section('title')
    Login
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <div class="panel panel-login">
                <div class="panel-heading text-center">
                    <a href="{{ route('home') }}" class="login-logo"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Vinnies"></a>
                    Assist a Student Program
                </div>
                <div class="panel-body">
                    <form role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email">Email <sup class="text-danger">*</sup></label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                   {{ $errors->first('email') }}
                                </span>
                            @endif
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password">Password <sup class="text-danger">*</sup></label>
                            <input id="password" type="password" class="form-control" name="password" required>

                            @if (Route::has('password.request'))
                                <small><a href="{{ route('password.request') }}" style="text-decoration:none;" class="mt-1">Forgot your password?</a></small>
                            @endif

                            @if ($errors->has('password'))
                                <span class="help-block">
                                   {{ $errors->first('password') }}
                                </span>
                            @endif
                        </div>

                        <input type="hidden" name="remember" value="1">

                        <div class="form-group{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }}">
                            {!! NoCaptcha::renderJs() !!}
                            {!! NoCaptcha::display() !!}
                            @if ($errors->has('g-recaptcha-response'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group is-last text-center">
                            <button type="submit" class="btn btn-warning text-uppercase">
                                Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
