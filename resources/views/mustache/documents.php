<script id="mustache-documents" type="x-tmpl-mustache">
    {{#has_documents}}
        <table class="table-unstyled">
            <thead>
                <tr>
                    <th class="first text-center">ID</th>
                    <th class="text-center">Filename</th>
                    <th class="text-center">Type</th>
                    <th class="text-center">Date Uploaded</th>
                    <th style="width:25px" class="text-center">Size</th>
                    <th class="text-center">By User</th>
                    <th>Comments</th>
                    <th style="width:110px">Actions</th>
                </tr>
            </thead>
            <tbody>
                {{#documents}}
                    <tr>
                        <td class="first text-center"><a href="{{{url}}}" target="_blank">{{id}}</a></td>
                        <td class="text-center"><a href="{{{url}}}" target="_blank">{{filename}}</a></td>
                        <td class="text-center">{{type}}</td>
                        <td class="text-center">{{date}}</td>
                        <td class="text-center">{{size}}</td>
                        <td class="text-center">{{user}}</td>

                        <td>{{excerpt}}</td>
                        <td class="actions">
                            <a class="text-success js-popover" href="#" data-toggle="tooltip" data-html="true" data-placement="top" title="{{{comments}}}"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                            {{#can_edit_documents}}
                                <a class="text-warning" href="#" data-toggle="modal" data-target="#modal-edit-document" data-edit-url="{{{edit_url}}}" data-id="{{id}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                            {{/can_edit_documents}}
                            {{#can_delete_documents}}
                                <a class="text-danger js-delete-document" href="#" data-delete-url="{{{delete_url}}}"><i class="fa fa-times" aria-hidden="true"></i></a>
                            {{/can_delete_documents}}
                        </td>
                    </tr>
                {{/documents}}
            </tbody>
        </table>
    {{/has_documents}}
    {{^documents}}
        {{#can_create_documents}}
            <p class="text-warning">No Documents added yet. Click on the button below to add Documents.</p>
        {{/can_create_documents}}
        {{^can_create_documents}}
            <p class="text-warning">No Documents added yet.</p>
        {{/can_create_documents}}
    {{/documents}}

    {{#can_create_documents}}
        <div class="row">
            <div class="col-sm-2">
                <a href="#" class="btn btn-primary btn-block mt-1" data-toggle="modal" data-target="#modal-create-document">Add Document</a>
            </div>
        </div>
    {{/can_create_documents}}
</script>

