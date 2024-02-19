{!! Form::open(['route' => 'galleries.upload', 'class' => 'form js-gallery-upload-form']) !!}
    {{ csrf_field() }}
    <label for="gallery_file">Gallery File <sup class="text-danger">*</sup> <span class="font-weight-normal text-warning">(allowed file formats: doc/docx, ppt/pptx, pdf, zip, jpg, jpeg, png, bmp)</span></label>

    <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
        @route('galleries.create')
            <p class="mt-1"><span class="js-file-selected text-warning" data-default="No file selected yet.">No file selected yet.</span></p>
        @else
            <p class="mt-1"><span class="js-file-selected text-warning" data-default="No file selected yet.">Uploaded file: <a href="{{ $gallery->url }}" target="_blank" rel="noopener noreferrer">{{ basename($gallery->file) }} ({{ $gallery->size }})</a></span></p>
        @endif

        <input type="file" name="gallery_file" id="gallery_file" class="hidden js-input-file" accept=".doc,.docx,.ppt,.pptx,.pdf,.zip,.jpg,.jpeg,.png,.bmp">
        <p></p>
    </div>

    @if ($errors->has('gallery_file'))
        <span class="help-block">
           {{ $errors->first('gallery_file') }}
        </span>
    @endif
    <div class="row">
        <div class="col-sm-3">
            <label for="gallery_file" class="btn btn-block btn-primary">Browse for your file</label>
        </div>
        <div class="col-sm-3">
            <button type="submit" class="btn btn-block btn-warning" data-text-default="Upload the Selected File" data-text-progress="Uploading...">Upload the Selected File</button>
        </div>
    </div>
</form>
