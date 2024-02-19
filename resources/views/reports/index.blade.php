@extends('layouts.app')

@section('title')
    Reports
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Reports</h1>
                <p>In here you can generate a report showing the number of Allocated Students per Donation State per County for a selected period of time based on the "Donation Received" Date.</p>
                <p>Select "ALL STATES" for a full report or an individual state from the selector below.</p>
                <p class="text-warning">Please select the date range at all times.</p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="list-unstyled">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <hr>
                <p><strong>Allocated Students per State per Country Report</strong>
                <div class="row well">
                  <form class="form form-reports" action="{{ route('reports.index') }}" method="post">
                      {{ csrf_field() }}
                      <div class="row">
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="state">State/Territory <sup class="text-danger">*</sup></label>
                                  <select name="state" id="state" class="form-control">
                                      @role('Full Admin')
                                          <option value="all">All States</option>
                                      @endrole
                                      @if ($currentUser->hasRole('Donations Uploader') || $currentUser->hasRole('Reports Viewer'))
                                          @if ($currentUser->state === 'national')
                                              <option value="all">All States</option>
                                          @endif
                                      @endif
                                      @foreach (Helper::getStates() as $key => $state)
                                          @role('Full Admin')
                                              <option value="{{ $key }}">{{ $state }}</option>
                                          @endrole
                                          @if ($currentUser->hasRole('Donations Uploader') || $currentUser->hasRole('Reports Viewer'))
                                              @if ($currentUser->state === 'national')
                                                  <option value="{{ $key }}">{{ $state }}</option>
                                              @elseif ($currentUser->state === $key)
                                                  <option value="{{ $key }}">{{ $state }}</option>
                                              @endif
                                          @endif
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="received_at_from">From Donation Received Date <sup class="text-danger">*</sup></label>
                                  {!! Form::text('received_at_from', null, ['class' => 'form-control datepicker', 'id' => 'received_at_from']) !!}
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="received_at_to">To Donation Received Date <sup class="text-danger">*</sup></label>
                                  {!! Form::text('received_at_to', null, ['class' => 'form-control datepicker', 'id' => 'received_at_to']) !!}
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label>&nbsp;</label>
                                  <button type="submit" class="btn btn-block btn-warning">Export</button>
                              </div>
                          </div>
                      </div>
                  </form>
                </div>
                <p><strong>Donations Received Report</strong>
                <div class="row well">
                  <form class="form form-reports" action="{{ route('reports.donations-export') }}" method="post">
                      {{ csrf_field() }}
                      <div class="row">
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="state">State/Territory <sup class="text-danger">*</sup></label>
                                  <select name="state" id="state" class="form-control">
                                      @role('Full Admin')
                                          <option value="all">All States</option>
                                      @endrole
                                      @if ($currentUser->hasRole('Donations Uploader') || $currentUser->hasRole('Reports Viewer'))
                                          @if ($currentUser->state === 'national')
                                              <option value="all">All States</option>
                                          @endif
                                      @endif
                                      @foreach (Helper::getStates() as $key => $state)
                                          @role('Full Admin')
                                              <option value="{{ $key }}">{{ $state }}</option>
                                          @endrole
                                          @if ($currentUser->hasRole('Donations Uploader') || $currentUser->hasRole('Reports Viewer'))
                                              @if ($currentUser->state === 'national')
                                                  <option value="{{ $key }}">{{ $state }}</option>
                                              @elseif ($currentUser->state === $key)
                                                  <option value="{{ $key }}">{{ $state }}</option>
                                              @endif
                                          @endif
                                      @endforeach
                                  </select>
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="received_at_from">From Donation Received Date <sup class="text-danger">*</sup></label>
                                  {!! Form::text('received_at_from', null, ['class' => 'form-control datepicker', 'id' => 'received_at_from']) !!}
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label for="received_at_to">To Donation Received Date <sup class="text-danger">*</sup></label>
                                  {!! Form::text('received_at_to', null, ['class' => 'form-control datepicker', 'id' => 'received_at_to']) !!}
                              </div>
                          </div>
                          <div class="col-sm-3">
                              <div class="form-group">
                                  <label>&nbsp;</label>
                                  <button type="submit" class="btn btn-block btn-warning">Export</button>
                              </div>
                          </div>
                      </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
@stop
