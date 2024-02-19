@extends('layouts.app')

@section('title')
    Two-Factor Authentication
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <h1 class="page-title text-center">Two-Factor Authentication</h1>
            <p>Please provide two-factor authentication code before continuing.</p>
            <form method="POST" action="{{ route('2fa.verify') }}">
                @csrf

                <div class="form-group{{ $errors->has('one_time_password') ? ' has-error' : '' }}">
                    <input name="one_time_password" class="form-control" required autocomplete="new-password">

                    @foreach ($errors->all() as $message)
                        <span class="help-block">
                            {{ $message }}
                        </span>
                    @endforeach
                </div>

                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-block btn-lg btn-primary">{{ __('Continue') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
