/* global bootbox */

'use strict';

(function ($) {
    var $btnGenerate  = $('.js-btn-donation-generate');
    var $btnDelete    = $('.js-btn-donation-delete');
    var $btnImportDelete    = $('.js-btn-donation-import-delete');
    var forceDelete   = 0;
    var forceImportDelete   = 0;
    var forceGenerate = 0;
    var $dataTable;

    var changeSelectAllCheckbox = function () {
        var rowLength  = $('#modal-students .js-table').find('tbody tr').length;
        var cbLength   = $('#modal-students .js-table').find('tbody input[type="checkbox"]').filter(':checked').length;
        var checkedAll = false;

        if (rowLength === cbLength && rowLength !== 0) {
            checkedAll = true;
        }

        $('#modal-students .js-select-all').prop('checked', checkedAll);
    };

    var selectRow = function (el) {
        $(el).toggleClass('success');
        $(el).find('input[type="checkbox"]').prop('checked', $(el).hasClass('success'));

        changeSelectAllCheckbox();
    };

    $(document).ready(function () {
        $btnDelete.on('click', function (e) {
            e.preventDefault();

            var data = {
                donations: window.selectedRows,
                force: forceDelete
            };

            $btnDelete.text($btnDelete.data('text-progress'));

            $.post('/donations/deactivate', data, function (resp) {
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

        $btnImportDelete.on('click', function (e) {
            e.preventDefault();

            var data = {
                donations: window.selectedRows,
                force: forceImportDelete
            };

            $btnImportDelete.text($btnImportDelete.data('text-progress'));

            $.post('/donations/import/delete', data, function (resp) {
                forceImportDelete = 0;

                window.showAlert(resp.msg, 'success');
                $('.js-table').find('tr.success').hide();
                $('.js-table').DataTable().ajax.reload();

                window.selectedRows = [];
            }).fail(function (xhr) {
                var resp = JSON.parse(xhr.responseText);

                window.showDialog(resp.msg, resp.confirm, function () {
                    forceImportDelete = 1;
                    $btnImportDelete.click();
                });
            }).always(function () {
                $btnImportDelete.text($btnImportDelete.data('text-default'));
            });
        });

        $btnGenerate.on('click', function (e) {
            e.preventDefault();

            var data = {
                donations: window.selectedRows,
                force: forceGenerate
            };

            $btnGenerate.text($btnGenerate.data('text-progress'));

            $.post('/donations/generate', data, function (resp) {
                forceGenerate = 0;

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
                    forceGenerate = 1;
                    $btnGenerate.click();
                });
            }).always(function () {
                $btnGenerate.text($btnGenerate.data('text-default'));
            });
        });

        $('#modal-students').on('hidden.bs.modal', function () {
            if ($dataTable) {
                $dataTable.draw();
            }
        });

        $('#modal-students').on('shown.bs.modal', function () {
            window.selectedRows = [];

            var $table = $('#modal-students .js-modal-table');
            $dataTable = $table.DataTable({
                paging: false,
                searching: false,
                info: false,
                serverSide: true,
                processing: true,
                pageLength: $table.data('page-length'),
                retrieve: true,
                searchDelay: 250,
                order: [[ $table.data('order-col'), $table.data('order-type') ]],
                ajax: {
                    url: $table.data('url'),
                    data: function (d) {
                        d.search.value = $('#modal-students .js-table-search-input').val();
                        d.length       = $table.data('page-length');
                        d.page         = (d.start / d.length) + 1;

                        var $filter = $('#modal-students .js-modal-table-filter');

                        if ($filter.length) {
                            d.filters = {};

                            $filter.find('input, select, textarea').each(function () {
                                var $el = $(this);
                                var name = $el.attr('name');
                                var val;

                                switch ($el.prop('nodeName').toLowerCase()) {
                                    default:
                                        val = $el.val();
                                        break;
                                }

                                d.filters[name] = val;
                            });
                        }
                    }
                },
                columnDefs: [
                    {
                        render: function (data, type, row) {
                            return '<input type="checkbox" name="student_ids[]" value="' + row.id + '">';
                        },
                        targets: 0
                    }
                ],
                columns: window.columnConfig,
                drawCallback: function (settings) {
                    var pagination = settings.json.pagination;
                    var $pBasic = $('#modal-students .pagination-basic');
                    var $pTable = $('#modal-students .pagination-table');
                    var html = [];
                    var $btn = $('#modal-students .js-modal-table-filter').find('[type="submit"]');

                    $btn.text($btn.data('text-default'));

                    if ($pBasic.length) {
                        $pBasic.find('.page-status').text(pagination.current + ' of ' + pagination.last);
                        $pBasic.find('.page-total').text(pagination.total + ' item' + (pagination.total > 1 ? 's' : ''));
                    }

                    if ($pTable.length) {
                        for (var i = pagination.first; i <= pagination.last; i++) {
                            if (i === pagination.current) {
                                html.push('<li class="active"><span>' + i + '</span></li>');
                            } else {
                                html.push('<li><a href="#" class="js-table-pager" data-page="' + (i - 1) + '">' + i + '</a></li>');
                            }
                        }

                        $pTable.empty().html(html.join(''));
                    }

                    changeSelectAllCheckbox();
                }
            });

            $('#modal-students .js-modal-table tbody').off('click').on('click', 'tr', function () {
                selectRow(this);
            });

            var throttledSearch = $.fn.dataTable.util.throttle(
                function () {
                    $dataTable.search(this.value).draw();
                },
                250
            );

            $(document).on('click', '#modal-students .js-table-pager', function (e) {
                e.preventDefault();
                $dataTable.page($(this).data('page')).draw('page');
            });

            $('#modal-students .js-table-search-input').off('keyup').on('keyup', throttledSearch);

            $('#modal-students .js-table-search-form').off('submit').on('submit', function (e) {
                e.preventDefault();
                $('#modal-students .js-table-search-input').trigger('keyup');
            });

            $('#modal-students .js-select-all').off('change').on('change', function () {
                var isChecked = $(this).is(':checked');

                if (isChecked) {
                    $table.find('tr').not('.success').click();
                } else {
                    $table.find('tr').filter('.success').click();
                }
            });

            $('#modal-students .js-modal-table-filter').off('submit').on('submit', function (e) {
                e.preventDefault();

                var $btn = $(this).find('[type="submit"]');

                $btn.text($btn.data('text-progress'));
                $('#modal-students .js-table-search-input').trigger('keyup');
            });

            $('#modal-students .btn-submit-modal-form').on('click', function (e) {
                e.preventDefault();
                $('#modal-students .js-modal-form').submit();
            });

            $('#modal-students .js-modal-form').ajaxForm({
                clearForm: false,
                resetForm: false,
                beforeSubmit: function () {
                    var $btn = $('#modal-students .btn-submit-modal-form');
                    $btn.text($btn.data('text-progress'));
                },
                success: function (responseText, statusText, xhr, $form) {
                    $('[data-error="student_ids"]').empty();

                    setTimeout(function () {
                        window.location.href = responseText.redirect;
                    }, 1000);
                },
                error: function (xhr, status, error) {
                    var errors = JSON.parse(xhr.responseText);

                    if (errors.hasOwnProperty('type')) {
                        if (errors.type === 'dialog') {
                            window.showDialog(errors.msg, true, function () {
                                $('#modal-students [name="force"]').val(1);
                                $('#modal-students .js-modal-form').submit();
                            });
                        }

                        if (errors.type === 'alert') {
                            $('#modal-students [name="force"]').val(0);
                            window.showDialog(errors.msg, false);
                        }
                    } else {
                        $('[data-error="student_ids"]').text(errors.errors[Object.keys(errors.errors)[0]][0]);
                    }
                },
                complete: function () {
                    var $btn = $('#modal-students .btn-submit-modal-form');
                    $btn.text($btn.data('text-default'));
                }
            });
        });
    });
})(jQuery);
