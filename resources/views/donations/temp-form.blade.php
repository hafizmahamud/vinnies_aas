<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
            <label for="state">Donation State/Territory <sup class="text-danger">*</sup></label>
            {!! Form::select('state', Helper::getUserStates(), null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'state']) !!}

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('received_at') ? ' has-error' : '' }}">
            <label for="class">Donation Received Date/Time <sup class="text-danger">*</sup></label>
            {!! Form::text('received_at', null, ['class' => 'form-control datetimepicker', 'id' => 'received_at']) !!}

            @if ($errors->has('received_at'))
                <span class="help-block">
                   {{ $errors->first('received_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('amount') ? ' has-error' : '' }}">
            <label for="amount">Donation Amount <sup class="text-danger">*</sup></label>


                {!! Form::text('amount', null, ['class' => 'form-control', 'id' => 'amount']) !!}

            @if ($errors->has('amount'))
                <span class="help-block">
                   {{ $errors->first('amount') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('special_allocation_required') ? ' has-error' : '' }}">
            <label for="class">Special Allocation Required <sup class="text-danger">*</sup></label>
            {!! Form::select('special_allocation_required', ['1' => 'Yes', '0' => 'No'], optional($donation)->special_allocation_required ? 1 : 0, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'special_allocation_required']) !!}

            @if ($errors->has('special_allocation_required'))
                <span class="help-block">
                   {{ $errors->first('special_allocation_required') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('certificate_needed') ? ' has-error' : '' }}">
            <label for="certificate_needed">Donor requires certificate <sup class="text-danger">*</sup></label>
            {!! Form::select('certificate_needed', ['1' => 'Yes', '0' => 'No'], optional($donation)->certificate_needed ? 1 : 0, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'certificate_needed']) !!}

            @if ($errors->has('certificate_needed'))
                <span class="help-block">
                   {{ $errors->first('certificate_needed') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('name_on_certificate') ? ' has-error' : '' }}">
            <label for="name_on_certificate">Donor Certificate name to be printed <sup class="text-danger">*</sup></label>
            {!! Form::text('name_on_certificate', null, ['class' => 'form-control', 'id' => 'name_on_certificate']) !!}

            @if ($errors->has('name_on_certificate'))
                <span class="help-block">
                   {{ $errors->first('name_on_certificate') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('online_donation') ? ' has-error' : '' }}">
            <label for="online_donation">Online Donation</label>
            {!! Form::select('online_donation', ['1' => 'Yes', '0' => 'No'], $donation->online_donation == true ? 1 : (is_null($donation->online_donation) ? '' : 0), ['placeholder' => '', 'class' => 'form-control', 'id' => 'online_donation']) !!}

            @if ($errors->has('online_donation'))
                <span class="help-block">
                   {{ $errors->first('online_donation') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('special_allocation_details') ? ' has-error' : '' }}">
            <label for="special_allocation_details">Special Allocation Details</label>
            {!! Form::text('special_allocation_details', null, ['class' => 'form-control', 'id' => 'special_allocation_details']) !!}

            @if ($errors->has('special_allocation_details'))
                <span class="help-block">
                   {{ $errors->first('special_allocation_details') }}
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('contact_address') ? ' has-error' : '' }}">
            <label for="contact_address">Address</label>
            {!! Form::text('contact_address', null, ['class' => 'form-control', 'id' => 'contact_address']) !!}

            @if ($errors->has('contact_address'))
                <span class="help-block">
                   {{ $errors->first('contact_address') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_suburb') ? ' has-error' : '' }}">
            <label for="contact_suburb">City/Suburb</label>
            {!! Form::text('contact_suburb', null, ['class' => 'form-control', 'id' => 'contact_suburb']) !!}

            @if ($errors->has('contact_suburb'))
                <span class="help-block">
                   {{ $errors->first('contact_suburb') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_postcode') ? ' has-error' : '' }}">
            <label for="contact_postcode">Post Code</label>
            {!! Form::text('contact_postcode', null, ['class' => 'form-control', 'id' => 'contact_postcode']) !!}

            @if ($errors->has('contact_postcode'))
                <span class="help-block">
                   {{ $errors->first('contact_postcode') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_email') ? ' has-error' : '' }}">
            <label for="contact_email">Email</label>
            {!! Form::text('contact_email', null, ['class' => 'form-control', 'id' => 'contact_email']) !!}

            @if ($errors->has('contact_email'))
                <span class="help-block">
                   {{ $errors->first('contact_email') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
            <label for="contact_phone">Phone</label>
            {!! Form::text('contact_phone', null, ['class' => 'form-control', 'id' => 'contact_phone']) !!}

            @if ($errors->has('contact_phone'))
                <span class="help-block">
                   {{ $errors->first('contact_phone') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_mobile') ? ' has-error' : '' }}">
            <label for="contact_mobile">Mobile</label>
            {!! Form::text('contact_mobile', null, ['class' => 'form-control', 'id' => 'contact_mobile']) !!}

            @if ($errors->has('contact_mobile'))
                <span class="help-block">
                   {{ $errors->first('contact_mobile') }}
                </span>
            @endif
        </div>
    </div>
</div>
