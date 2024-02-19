<div class="form-group">
    <div class="row">
        <div class="col-sm-8">
            <label for="type">Document Type <sup class="text-danger">*</sup></label>
            <select name="type" class="form-control">
                @foreach (Helper::getDocumentTypesOption() as $type => $type_name)
                    <option value="{{ $type }}">{{ $type_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="type">Comments</label>
    <textarea name="comments" class="form-control form-control-text-danger js-stretch"></textarea>
</div>

<input type="hidden" name="documentable_id" value="">
<input type="hidden" name="documentable_type" value="">
