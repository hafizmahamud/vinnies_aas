<div class="modal fade" id="modal-edit-document" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Document</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => '#', 'class' => 'js-modal-form', 'method' => 'patch', 'files' => true]) !!}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="document2" class="btn btn-block btn-primary">Browse</label>
                                <input type="file" name="document2" id="document2" class="hidden js-input-file">
                            </div>
                            <input name="file_check" id="file_check" class="hidden">
                            <div class="col-sm-6">
                                <p style="margin-top: 6px;">File selected: <span class="js-file-selected">-</span></p>
                            </div>
                        </div>
                    </div>

                    @include('modals.document.modal-form')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Uploading..." data-text-default="Upload the selected file">Upload the selected file</button>
            </div>
        </div>
    </div>
</div>
