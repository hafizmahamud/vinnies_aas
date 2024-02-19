/* global columnConfig:true */
/* global bootbox */
/* global rome */
/* global Mustache */
/* global autosize */
/* global documentable_type */
/* global documentable_id */
/* global meta_url */
/* eslint-disable camelcase */

'use strict';

(function ($) {
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    window.selectedRows = [];

    var docTmpl = $('#mustache-documents').html();
    var isBottomNavBar = false;

    Mustache.parse(docTmpl);

    var documents; // will hold all documents data from current view


    //ADDED
    var debounce = function(fn, delay) {
        var timer = null;

        return function () {
            var context = this, args = arguments;

            clearTimeout(timer);

            timer = setTimeout(function () {
                fn.apply(context, args);
            }, delay);
        };
    }

    var updateBindings = function (data, cb) {
        $.each(data, function (key, val) {
            var $el = $('[data-bind="' + key + '"]');

            if (!$el.length) {
                return;
            }

            var tagName = $el.prop('tagName').toLowerCase();

            switch (tagName) {
                case 'input':
                    $el.val(val);
                    break;

                case 'a':
                    if ($el.data('bind-attr') === 'href') {
                        $el.attr('href', val);
                    } else {
                        $el.text(val);
                    }
                    break;

                case 'span':
                case 'p':
                case 'strong':
                    $el.text(val);
                    break;

                default:
                    console.warn('Invalid tagName: ' + tagName);
                    break;
            }
        });

        if (cb) {
            cb(data);
        }
    };
    // ------

    var showFormErrors = function (errors) {
        $.each(errors, function (key, val) {
            var $input = $('[name="' + key + '"]');

            $input.closest('.form-group').addClass('has-error');

            if (val.length) {
                $input.closest('.form-group').find('.help-block').remove();
                $input.closest('.form-group').append('<span class="help-block">' + val[0] + '</span>');
            }
        });
    };
  
    var showDialog = function(msg, confirm, confirmCb) {
        if (confirm) {
            bootbox.confirm({
                message: msg,
                buttons: {
                    confirm: {
                        label: 'Confirm',
                        className: 'btn-warning'
                    },
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-primary'
                    }
                },
                callback: function (result) {
                    if (!result) {
                        return;
                    }

                    // global callback a priority
                    if (window.confirmCb) {
                        window.confirmCb();
                    } else if (confirmCb) {
                        confirmCb();
                    }
                }
            });
        } else {
            bootbox.alert({
                message: msg,
                buttons: {
                    ok: {
                        className: 'btn-primary'
                    }
                }
            });
        }
    };

    var showAlert = function (msg, type) {
        $('[data-alert]').removeClass().addClass('alert alert-' + type).find('.msg').text(msg);
        $('html, body').animate({ scrollTop: $('[data-alert]').offset().top - 50 });
    };

    var clearAlert = function () {
        $('[data-alert]').removeClass().addClass('hidden').find('.msg').text('');
    };

    var selectRow = function (el) {
        var id    = el.id;
        var index = $.inArray(id, window.selectedRows);

        if (index === -1) {
            window.selectedRows.push(id);
            $(el).find('input[type="checkbox"]').prop('checked', true);
            $(el).addClass('success');
        } else {
            window.selectedRows.splice(index, 1);
            $(el).find('input[type="checkbox"]').prop('checked', false);
            $(el).removeClass('success');
        }

        changeSelectAllCheckbox();

        if (window.selectedRows.length) {
            $('.js-btn-action').removeClass('hidden');
        } else {
            $('.js-btn-action').addClass('hidden');
        }
    };

    var changeSelectAllCheckbox = function () {
        var rowLength  = $('.js-table').find('tbody tr').length;
        var cbLength   = $('.js-table').find('tbody input[type="checkbox"]').filter(':checked').length;
        var checkedAll = false;

        if (rowLength === cbLength && rowLength !== 0) {
            checkedAll = true;
        }

        $('.js-select-all').prop('checked', checkedAll);
    };

    $(document).ready(function () {
        var $form = $('.js-form').ajaxForm({
            clearForm: ($.trim($('.js-form').data('reset')) === '1'),
            resetForm: ($.trim($('.js-form').data('reset')) === '1'),
            beforeSubmit: function () {
                var $btn = $('.js-form').find('[type="submit"]');
                $btn.text($btn.data('text-progress'));
                clearAlert();
            },
            success: function (responseText, statusText, xhr, $form) {
                showAlert(responseText.msg, 'success');

                if (responseText.redirect === undefined) {
                    console.log("This is undefined");
                }

                if ($.trim($form.data('redirect')) !== '') {
                    setTimeout(function () {
                        window.location.href = $form.data('redirect');
                    }, 1000);
                } else if (responseText.redirect !== undefined) {
                    setTimeout(function () {
                        window.location.href = responseText.redirect;
                    }, 1000);
                }

                $form.find('input[type="file"]').val('');
                $form.find('.js-file-selected').text('-');
            },
            error: function (xhr, status, error) {
                var errors = JSON.parse(xhr.responseText);

                if (errors.type === 'dialog') {
                    showDialog(errors.msg, errors.confirm);
                } else {
                    showFormErrors(errors.errors);
                }
            },
            complete: function () {
                var $btn = $('.js-form').find('[type="submit"]');
                $btn.text($btn.data('text-default'));
            }
        });

        $form.find('input, select, textarea').on('change keyup', function () {
            var $this = $(this);

            $this.closest('.form-group').removeClass('has-error');
            $this.closest('.form-group').find('.help-block').remove();
        });

        var $table     = $('.js-table');
        var $dataTable = $table.DataTable({
            paging: false,
            searching: false,
            info: false,
            serverSide: true,
            processing: true,
            searchDelay: 250,
            order: [[ $table.data('order-col'), $table.data('order-type') ]],
            ajax: {
                url: $table.data('url'),
                data: function (d) {
                    d.search.value = $('.js-table-search-input').val();
                    d.length       = $table.data('page-length');
                    d.page         = (d.start / d.length) + 1;

                    var $filter = $('.js-table-filter');

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
                        return '<input type="checkbox">';
                    },
                    targets: 0
                }
            ],
            columns: window.columnConfig,
            rowCallback: function(row, data) {
                if ($.inArray(data.DT_RowId, window.selectedRows) !== -1) {
                    $(row).addClass('success');
                    $(row).find('input[type="checkbox"]').prop('checked', true);
                } else {
                    $(row).removeClass('success');
                    $(row).find('input[type="checkbox"]').prop('checked', false);
                }
            },
            drawCallback: function (settings) {
                var pagination = settings.json.pagination;
                var $pBasic = $('.pagination-basic');
                var $pTable = $('.pagination-table');
                var html = [];
                var $btn = $('.js-table-filter').find('[type="submit"]');

                $btn.text($btn.data('text-default'));
                if(isBottomNavBar === true){
                    var element = document.getElementById("site-footer");
                    element.scrollIntoView(true);   
                }  
                if ($pBasic.length) {
                    $pBasic.find('.page-status').text(pagination.current + ' of ' + pagination.last);
                    $pBasic.find('.page-total').text(pagination.total + ' item' + (pagination.total > 1 ? 's' : ''));
                }

                if ($pTable.length) {
                    var currentPage = pagination.current;
                    var totalPages = pagination.last;
                    var range = calculatePaginationRange(pagination.current, pagination.last);

                //   var range = calculatePaginationRange(pagination.current, pagination.last);
              
                html.push(
                        '<li><a href="#" class="page-first js-table-pager" data-page="first">&laquo;</a></li>'
                      );
                      html.push(
                        '<li><a href="#" class="page-prev js-table-pager" data-page="previous">&lsaquo;</a></li>'
                      );
                      for (var i = range.startPage; i <= range.endPage; i++) {
                        if (i === currentPage) {   
                            html.push('<li class="active"><span>' + i + '</span></li>');
                        } else {
                            html.push('<li><a href="#" class="js-table-pager" data-page="' + (i - 1) + '">' + i + '</a></li>');
                        }
                    }
                    html.push(
                        '<li><a href="#" class="page-next js-table-pager" data-page="next">&rsaquo;</a></li>'
                      );
                      html.push(
                        '<li><a href="#" class="page-last js-table-pager" data-page="last">&raquo;</a></li>'
                      );
                      html.push(
                        '<li><input id="data-table-input" type="number" min="1" max="'+totalPages+'" value="' +
                          currentPage +
                          '"></input>&nbsp of &nbsp' +
                          totalPages +
                          " Pages</li>"
                      );
                    $pTable.empty().html(html.join(''));
                }

                changeSelectAllCheckbox();
            }
        });
        $(document).off('click', '.js-table-pager').on('click', '.js-table-pager', function (e) {
            e.preventDefault();
            isBottomNavBar = false;
            $dataTable.page($(this).data('page')).draw('page');
        });
        $(document).on('click', '.scrollToBottom', function(e){
            e.preventDefault();
            isBottomNavBar  = true;
          });
  $(document).on('change', '#data-table-input', function (e) {
        e.preventDefault();
        $dataTable.page(e.target.value - 1).draw('page');
    });
        $('.js-table tbody').on('click', 'tr', function () {
            selectRow(this);
        });

        var throttledSearch = $.fn.dataTable.util.throttle(
            function () {
                $dataTable.search(this.value).draw();
            },
            250
        );

        $(document).on('click', '.js-table-pager', function (e) {
            e.preventDefault();
            $dataTable.page($(this).data('page')).draw('page');
        });

        $('.js-table-search-input').on('keyup', throttledSearch);

        $('.js-table-search-form').on('submit', function (e) {
            e.preventDefault();
            $('.js-table-search-input').trigger('keyup');
        });

        $('.js-select-all').on('change', function () {
            var isChecked = $(this).is(':checked');

            if (isChecked) {
                $table.find('tr').not('.success').click();
            } else {
                $table.find('tr').filter('.success').click();
            }
        });

        $('.js-table-filter').on('submit', function (e) {
            e.preventDefault();

            var $btn = $(this).find('[type="submit"]');

            $btn.text($btn.data('text-progress'));
            $('.js-table-search-input').trigger('keyup');
        });

        $('.js-input-file').on('change', function () {
            var filepath = $(this).val();
            var basename = $.trim(filepath.replace(/\\/g,'/').replace(/.*\//, ''));

            $('.js-file-selected').text(basename);
        });

        $('.close[data-dismiss="alert"]').on('click', function (e) {
            e.preventDefault();
            clearAlert();
        });

        $('.datetimepicker').each(function() {
            rome(this, {
                inputFormat: "DD/MM/YYYY HH:mm"
            });
        });

        $('.datepicker').each(function() {
            rome(this, {
                inputFormat: "DD/MM/YYYY",
                time: false
            });
        });
    });
    $(document).on('input', '#data-table-input', function () {
        var value = parseInt($(this).val());
        if (value < parseInt($(this).attr('min'))) {
          value = parseInt($(this).attr('min'));
      }
      else if (value > parseInt($(this).attr('max'))) {
          value = parseInt($(this).attr('max'));
      }
      $(this).val(value);
    });
  function calculatePaginationRange(currentPage, totalPages) {
        var numPagesToShow = Math.min(totalPages, 5);
        var startPage;
        var endPage;
  
        if (totalPages <= 5) {
            startPage = 1;
            endPage = totalPages;
        } else {
            if (currentPage <= 3) {
                startPage = 1;
                endPage = 5;
            } else if (currentPage >= totalPages - 2) {
                startPage = totalPages - 4;
                endPage = totalPages;
            } else {
                startPage = currentPage - 2;
                endPage = currentPage + 2;
            }
        }
  
        return { startPage: startPage, endPage: endPage };
    }

    //ADDED
    var initForm = function(config) {
        var $formEl = $(config.form);
        var $form = $formEl.ajaxForm({
            clearForm: ($.trim($formEl.data('reset')) === '1'),
            resetForm: ($.trim($formEl.data('reset')) === '1'),
            beforeSubmit: function () {
                var $btn = $(config.btn);

                $btn.text($btn.data('text-progress'));
                clearAlert();

                if (config.beforeSubmitCb) {
                    config.beforeSubmitCb();
                }
            },
            success: function (responseText, statusText, xhr, $form) {
                if (config.successCb) {
                    config.successCb(responseText, statusText, xhr, $form);
                } else {
                    showAlert(responseText.msg, 'success');
                }

                if ($.trim($form.data('redirect')) !== '') {
                    setTimeout(function () {
                        window.location = $form.data('redirect');
                    }, 1000);
                }

                if ($.trim(responseText.redirect) !== '') {
                    setTimeout(function () {
                        window.location = responseText.redirect;
                    }, 1000);
                }

                $form.find('[name="comments"]').val('');
                $form.find('input[type="file"]').val('');
                $form.find('.js-file-selected').text('-');
            },
            error: function (xhr, status, error) {
                var errors = JSON.parse(xhr.responseText);

                if (errors.type === 'dialog') {
                    showDialog(errors.msg, errors.confirm);
                } else {
                    showFormErrors(errors.errors, $formEl);
                }

                if (config.errorCb) {
                    config.errorCb(xhr, status, error);
                }
            },
            complete: function () {
                var $btn = $(config.btn);
                $btn.text($btn.data('text-default'));

                if (config.completeCb) {
                    config.completeCb();
                }
            }
        });

        $form.find('input, select, textarea').on('change keyup', debounce(function () {
            var $this = $(this);

            $this.closest('.form-group').removeClass('has-error');
            $this.closest('.form-group').find('.help-block').remove();
        }, 250));

        if ($(config.btn).length && !$.contains($formEl[0], $(config.btn)[0])) {
            $(config.btn).off('click').on('click', function (e) {
                e.preventDefault();
                $formEl.submit();
            });
        }
    };

    //ADDED
    var updateDocuments = function (cb) {
        var $container = $('.section-documents');
        var projectId  = $container.data('project-id');
        var data       = {
            id: $container.data('id'),
            type: $container.data('type'),
        };

        $('[data-toggle="tooltip"]').tooltip('destroy');

        $.get('/documents', data, function (resp) {
            $container.html(Mustache.render(docTmpl, resp));
            documents = resp.documents;
        }).always(function () {
            $('[data-toggle="tooltip"]').tooltip();

            if (cb) {
                cb();
            }
        });
    };

    //ADDED
    var updateUpdatedBy = function () {
        $.get(meta_url, function (resp) {
            updateBindings(resp);
        });
    };

    //ADDED
    var loadComments = function ($el) {
        var url = $el.data('url');
        var html = [];

        $el.html('Loading comments...');

        $.get(url, function (res) {
            if (res.length) {
                $.each(res, function (index, val) {
                    html.push('<div class="media">');
                        html.push('<div class="media-body">');
                            html.push('<p class="media-heading"><strong>' + val.name +'</strong> <small class="text-muted" title="' + val.date +'">' + val.diff + '</small></p>');
                            html.push(val.comment);
                        html.push('</div>');
                    html.push('</div>');

                    if (index < (res.length -1)) {
                        html.push('<hr style="margin:10px 0;">');
                    }
                });

                $('.js-comments-count').text(res.length);
            } else {
                html.push('No comments found.');
            }

            $el.html(html.join(''));
        });
    };

    //ADDED
    $(document).ready(function () {
        if ($('.js-well-comments').length) {
            $('.js-toggle-comments').on('click', function (e) {
                e.preventDefault();
                var $this = $(this);

                if ($this.text() === 'Hide') {
                    $this.text('Show');
                } else {
                    $this.text('Hide');
                }

                $('.js-well-comments').toggle();
            });

            loadComments($('.js-well-comments'));
        }

        initForm({
            form: '.js-form',
            btn: '.js-form [type="submit"]',
            successCb: function (data) {
                showAlert(data.msg, 'success');

                if (typeof meta_url !== 'undefined') {
                    updateUpdatedBy();
                }

                loadComments($('.js-well-comments'));
            }
        });

        $(document).on('change', '.js-input-file', function () {
            var $this    = $(this);
            var filepath = $this.val();
            var basename = $.trim(filepath.replace(/\\/g,'/').replace(/.*\//, ''));

            if ($.trim($this.data('target')) !== '') {
                $($this.data('target')).text(basename);
            } else {
                $('.js-file-selected').text(basename);
            }
        });

        $('.close[data-dismiss="alert"]').on('click', function (e) {
            e.preventDefault();
            clearAlert();
        });

        $('.js-datepicker').each(function() {
            var $this = $(this);

            $($this).datepicker({
              format: 'dd/mm/yyyy',
              autoHide: true,
            });
        });

        $('.js-datepicker-disable-future').each(function() {
            var $this = $(this);

            $($this).datepicker({
              format: 'dd/mm/yyyy',
              autoHide: true,
              endDate: "today",
            });
        });

        $('.js-select').each(function () {
            $(this).select2();
        });

        if ($('.js-stretch').length) {
            autosize($('.js-stretch'));
        }

        // On page load, retrieve donors list
        if ($('.section-documents').length) {
            updateDocuments();
        }

        $('#modal-create-document').on('shown.bs.modal', function () {
            $('[name="documentable_type"]').val(documentable_type);
            $('[name="documentable_id"]').val(documentable_id);

            initForm({
                form: '#modal-create-document .js-modal-form',
                btn: '#modal-create-document .btn-submit-modal-form',
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDocuments(function () {
                        $('#modal-create-document').modal('hide');
                    });

                    updateUpdatedBy();
                }
            });
        });

        $('#modal-edit-document').on('shown.bs.modal', function (e) {
            var $this = $(this);
            var $el   = $(e.relatedTarget);
            var docId = parseInt($el.data('id'), 10);

            $.each(documents, function (ind, val) {
                if (val.id === docId) {
                    $this.find('.js-file-selected').text(val.filename);
                    $this.find('[name="type"]').val(val.type);
                    $this.find('[name="comments"]').val(val.comments_raw);
                    autosize.update($this.find('.js-stretch'));
                }
            });

            $('[name="documentable_type"]').val(documentable_type);
            $('[name="documentable_id"]').val(documentable_id);
            $this.find('.js-modal-form').attr('action', $el.data('edit-url'));

            initForm({
                form: '#modal-edit-document .js-modal-form',
                btn: '#modal-edit-document .btn-submit-modal-form',
                successCb: function (responseText, statusText, xhr, $form) {
                    updateDocuments(function () {
                        $('#modal-edit-document').modal('hide');
                    });

                    updateUpdatedBy();
                }
            });
        });

        $('#modal-edit-document').on('hidden.bs.modal', function (e) {
            $(this).find('.js-modal-form').attr('action', '#');
        });

        $('#modal-create-document').on('hide.bs.modal', function () {
            var $this = $(this);

            $this.find('.js-file-selected').text('-');
            $this.find('.js-modal-form')[0].reset();
        });

        $(document).on('click', '.js-delete-document', function (e) {
            e.preventDefault();

            var $this = $(this);

            showDialog('Are you sure you want to delete this document?', true, function () {
                $this.find('.fa').removeClass('fa-times').addClass('fa-spinner fa-pulse fa-fw');

                $.post($this.data('delete-url'), function (resp) {
                    updateDocuments();
                    updateUpdatedBy();
                }).fail(function () {
                    showAlert('Something went wrong, please try again later.', 'danger');
                }).always(function () {
                    $this.find('.fa').removeClass('fa-spinner fa-pulse fa-fw').addClass('fa-times');
                });
            });
        });
    });

    // Expose some functions to global window object
    window.clearAlert = clearAlert;
    window.showAlert  = showAlert;
    window.showDialog = showDialog;
    window.showFormErrors = showFormErrors;

    window.initForm        = initForm;
    window.updateUpdatedBy = updateUpdatedBy;
    window.debounce        = debounce;
})(jQuery);
