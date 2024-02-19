<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
            <label for="state">User State/Territory <sup class="text-danger">*</sup></label>
            {!! Form::select('state', Helper::getStates(), null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'state']) !!}

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
            <label for="first_name">First Name <sup class="text-danger">*</sup></label>
            {!! Form::text('first_name', null, ['class' => 'form-control', 'id' => 'first_name']) !!}

            @if ($errors->has('first_name'))
                <span class="help-block">
                   {{ $errors->first('first_name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
            <label for="last_name">Last Name <sup class="text-danger">*</sup></label>
            {!! Form::text('last_name', null, ['class' => 'form-control', 'id' => 'last_name']) !!}

            @if ($errors->has('last_name'))
                <span class="help-block">
                   {{ $errors->first('last_name') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('has_accepted_conditions') ? ' has-error' : '' }}">
            <label for="has_accepted_conditions">User Agreed to Conditions of Use? <sup class="text-danger">*</sup></label>
            {!! Form::select('has_accepted_conditions', ['0' => 'No', '1' => 'Yes'], null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'has_accepted_conditions']) !!}

            @if ($errors->has('has_accepted_conditions'))
                <span class="help-block">
                   {{ $errors->first('has_accepted_conditions') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('conditions_accepted_at') ? ' has-error' : '' }}">
            <label for="conditions_accepted_at">User Agreed to Conditions of Use Date</label>
            {!! Form::text('conditions_accepted_at', optional(empty($user) ? null : $user->conditions_accepted_at)->format(config('vinnies.date_only_format')), ['class' => 'form-control datepicker', 'id' => 'conditions_accepted_at']) !!}
            <!-- {!! Form::text('conditions_accepted_at', null, ['class' => 'form-control datepicker', 'id' => 'conditions_accepted_at']) !!} -->

            @if ($errors->has('conditions_accepted_at'))
                <span class="help-block">
                   {{ $errors->first('conditions_accepted_at') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
            <label for="role">User Role <sup class="text-danger">*</sup></label>
            {!! Form::select('role', Access::getRoles()->toArray(), (empty($user) ? null : $selectedRole), ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'role']) !!}

            @if ($errors->has('role'))
                <span class="help-block">
                   {{ $errors->first('role') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('branch_display') ? ' has-error' : '' }}">
            <label for="branch_display">Branch display in header <sup class="text-danger">*</sup></label>
            {!! Form::text('branch_display', null, ['class' => 'form-control', 'id' => 'branch_display']) !!}

            @if ($errors->has('branch_display'))
                <span class="help-block">
                   {{ $errors->first('branch_display') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">Email Address <sup class="text-danger">*</sup></label>
            {!! Form::email('email', null, ['class' => 'form-control', 'id' => 'email', 'autocomplete' => 'new-password']) !!}

            @if ($errors->has('email'))
                <span class="help-block">
                   {{ $errors->first('email') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    @if (Route::currentRouteName() !== 'users.create')
        <div class="col-sm-4">
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password">Password <sup class="text-danger">*</sup></label>
                <div class="input-group has-icon">
                    {!! Form::password('password', ['class' => 'form-control', 'id' => 'password', 'autocomplete' => 'new-password']) !!}
                    <span class="input-group-addon js-toggle-password" data-target="#password"><i class="fa fa-eye" aria-hidden="true"></i></span>
                </div>

                @if (!empty($user))
                    <span class="help-block">Leave blank to keep using current password.</span>
                @endif

                @if ($errors->has('password'))
                    <span class="help-block">
                       {{ $errors->first('password') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="col-sm-3">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-primary btn-block js-generate-password" data-target="#password">Generate password</button>
        </div>
    @endif
</div>
