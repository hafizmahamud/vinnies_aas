/* global bootbox */

'use strict';

(function ($) {
    var $btnAllocate  = $('.js-btn-student-allocate');
    var $btnDelete    = $('.js-btn-student-delete');
    var forceDelete   = 0;
    var forceAllocate = 0;

    $(document).ready(function () {
        $btnDelete.on('click', function (e) {
            e.preventDefault();

            var data = {
                students: window.selectedRows,
                force: forceDelete
            };

            $btnDelete.text($btnDelete.data('text-progress'));

            $.post('/students/deactivate', data, function (resp) {
                forceDelete = 0;

                window.showAlert(resp.msg, 'success');
                $('.js-table').find('tr.success').hide();
                $('.js-table').DataTable().ajax.reload();

                window.selectedRows = [];
            }).fail(function (xhr) {
                var resp = JSON.parse(xhr.responseText);

                window.showDialog(resp.msg, resp.confirm, function () {
                    forceDelete = 1;
                    $btnDelete.click();
                });
            }).always(function () {
                $btnDelete.text($btnDelete.data('text-default'));
            });
        });

        $btnAllocate.on('click', function (e) {
            e.preventDefault();

            var data = {
                students: window.selectedRows,
                force: forceAllocate
            };

            $btnAllocate.text($btnAllocate.data('text-progress'));

            $.post('/students/allocate', data, function (resp) {
                forceAllocate = 0;
                window.showAlert(resp.msg, 'success');

                $('.js-table').find('tr.success').click();
                window.selectedRows = [];

                if ($.trim(resp.redirect) !== '') {
                    setTimeout(function () {
                        window.location.href = resp.redirect;
                    }, 1000);
                }
            }).fail(function (xhr) {
                var resp = JSON.parse(xhr.responseText);

                window.showDialog(resp.msg, resp.confirm, function () {
                    forceAllocate = 1;
                    $btnAllocate.click();
                });
            }).always(function () {
                $btnAllocate.text($btnAllocate.data('text-default'));
            });
        });
    });
})(jQuery);
