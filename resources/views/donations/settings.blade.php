@extends('layouts.app')

@section('title')
    Donation Settings
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Donation Settings</h1>
                @include('flash::message')
                @include('partials.js-alert')
            </div>
        </div>

        {!! Form::model(null, ['route' => 'donations.settings', 'class' => 'form js-form', 'method' => 'patch']) !!}
          <div>
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
                  <label for="amount">amount Amount <sup class="text-danger">*</sup></label>
                  {!! Form::text('amount', $treshold, ['class' => 'form-control', 'id' => 'amount']) !!}

                  @if ($errors->has('amount'))
                    <span class="help-block">
                      {{ $errors->first('amount') }}
                    </span>
                  @endif
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-3">
                <button type="submit" class="btn btn-warning" data-text-default="Submit donation changes" data-text-progress="Submitting...">Submit donation changes</button>
              </div>
            </div>
          </div>
        </form>

    </div>
@stop
