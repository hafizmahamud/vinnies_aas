{{ Form::hidden('file', null, ['id' => 'file']) }}

<div class="row">
    <div class="col-sm-3">
        <div class="form-group">
            <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }}">
                <label for="class">Album Year <sup class="text-danger">*</sup></label>
                {!! Form::select('year', Helper::getGalleryYears(), null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'year']) !!}

                @if ($errors->has('year'))
                    <span class="help-block">
                       {{ $errors->first('year') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="form-group">
            <div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
                <label for="class">Country <sup class="text-danger">*</sup></label>
                {!! Form::select('country', Helper::getGalleryCountries(), null, ['placeholder' => 'Please select', 'class' => 'form-control', 'id' => 'country']) !!}

                @if ($errors->has('country'))
                    <span class="help-block">
                       {{ $errors->first('country') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
            <label for="description">Gallery Item Description <sup class="text-danger">*</sup> <span class="font-weight-normal text-warning">(appears under the image/thumbnail)</span></label>
            {!! Form::textarea('description', null, ['class' => 'form-control', 'id' => 'description']) !!}

            @if ($errors->has('description'))
                <span class="help-block">
                   {{ $errors->first('description') }}
                </span>
            @endif
        </div>
    </div>
</div>
