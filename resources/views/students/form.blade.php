<div class="row">
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
            <label for="assistance_year">Assistance Year <sup class="text-danger">*</sup></label>
            @if (!empty($student) && $student->is_allocated)
                {!! Form::text('assistance_year', null, ['class' => 'form-control', 'id' => 'assistance_year', 'readonly' => 'readonly']) !!}
            @else
                {!! Form::text('assistance_year', null, ['class' => 'form-control', 'id' => 'assistance_year']) !!}
            @endif

            @if ($errors->has('assistance_year'))
                <span class="help-block">
                   {{ $errors->first('assistance_year') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('class') ? ' has-error' : '' }}">
            <label for="class">Student Class <sup class="text-danger">*</sup></label>
            {!! Form::text('class', null, ['class' => 'form-control', 'id' => 'class']) !!}

            @if ($errors->has('class'))
                <span class="help-block">
                   {{ $errors->first('class') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
            <label for="country">Student Country <sup class="text-danger">*</sup></label>
            {!! Form::text('country', null, ['class' => 'form-control', 'id' => 'country']) !!}

            @if ($errors->has('country'))
                <span class="help-block">
                   {{ $errors->first('country') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('age') ? ' has-error' : '' }}">
            <label for="age">Student Age</label>
            {!! Form::text('age', null, ['class' => 'form-control', 'id' => 'age']) !!}

            @if ($errors->has('age'))
                <span class="help-block">
                   {{ $errors->first('age') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
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
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
            <label for="last_name">Last Name</label>
            {!! Form::text('last_name', null, ['class' => 'form-control', 'id' => 'last_name']) !!}

            @if ($errors->has('last_name'))
                <span class="help-block">
                   {{ $errors->first('last_name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('education_sector') ? ' has-error' : '' }}">
            <label for="education_sector">Education Sector <sup class="text-danger">*</sup></label>
            {!! Form::select('education_sector', Helper::getEducationSectors(), null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'education_sector']) !!}

            @if ($errors->has('education_sector'))
                <span class="help-block">
                   {{ $errors->first('education_sector') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
            <label for="gender">Student Gender</label>
            {!! Form::select('gender', Helper::getGenders(), empty($student) ? 'N/A' : null, ['class' => 'form-control', 'id' => 'gender']) !!}

            @if ($errors->has('gender'))
                <span class="help-block">
                   {{ $errors->first('gender') }}
                </span>
            @endif
        </div>
    </div>
</div>
