/* global bootbox */
/* global initDatatable */
/* global showDialog */
/* global showAlert */

'use strict';

(function ($) {
    var $btnMultiDeactivate = $('.js-btn-user-multi-deactivate');
    var $btnDeactivate = $('.js-btn-user-deactivate');
    var $btnReactivate = $('.js-btn-user-reactivate');
    var $btnSignTos = $('.js-btn-user-signtos');

    var generatePassword = function () {
        var charset  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        var length   = 16;
        var password = '';

        for (var i = 0, n = charset.length; i < length; ++i) {
            password += charset.charAt(Math.floor(Math.random() * n));
        }

        return password;
    };

    $(document).ready(function () {
        $('.js-toggle-password').on('click', function () {
            var $this  = $(this);
            var $input = $($this.data('target'));

            if ($this.find('.fa').hasClass('fa-eye')) {
                $input.attr('type', 'text');
                $this.find('.fa').removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                $input.attr('type', 'password');
                $this.find('.fa').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('.js-generate-password').on('click', function (e) {
            e.preventDefault();

            var password = generatePassword();
            var $input   = $($(this).data('target'));

            $input.val(password).change();

            if ($input.attr('type') === 'password') {
                $('.js-toggle-password').click();
            }
        });

        var $table = $('.js-table');

        $btnMultiDeactivate.on('click', function (e) {
            e.preventDefault();

            var message   = [];
            var dataTable = $table.DataTable();

            message.push('<p>Are you sure you want to deactivate selected user(s)?</p>');
            message.push('<ul>');

            $.each(window.selectedRows, function (ind, val) {
                var user = dataTable.row($('#' + val)[0]).data();
                message.push('<li>' + user.first_name + ' ' + user.last_name + ' (' + user.email + ')</li>');
            });

            message.push('</ul>');

            window.showDialog(message.join(''), true, function () {
                var data = {
                    users: window.selectedRows
                };

                $btnMultiDeactivate.text($btnMultiDeactivate.data('text-progress'));

                $.post('/users/multideactivate', data, function (resp) {
                    window.window.showAlert(resp.msg, 'success');

                    $('.js-table').find('tr.success').click();
                    window.selectedRows = [];

                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    window.window.showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnMultiDeactivate.text($btnMultiDeactivate.data('text-default'));
                });
            });
        });

        $btnDeactivate.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to deactivate ' + $('#first_name').val() + ' ' + $('#last_name').val() + '?</p>';

            window.showDialog(message, true, function () {
                var data = {
                    user: $btnDeactivate.data('user-id')
                };

                $btnDeactivate.text($btnDeactivate.data('text-progress'));

                $.post('/users/deactivate', data, function (resp) {
                    window.showAlert(resp.msg, 'success');

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    window.showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnDeactivate.text($btnDeactivate.data('text-default'));
                });
            });
        });

        $btnReactivate.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to reactivate ' + $('#first_name').val() + ' ' + $('#last_name').val() + '?</p>';

            window.showDialog(message, true, function () {
                var data = {
                    user: $btnReactivate.data('user-id')
                };

                $btnReactivate.text($btnReactivate.data('text-progress'));

                $.post('/users/reactivate', data, function (resp) {
                    window.showAlert(resp.msg, 'success');

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    window.showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnReactivate.text($btnReactivate.data('text-default'));
                });
            });
        });

        $btnSignTos.on('click', function (e) {
            e.preventDefault();

            var message = '<p>Are you sure you want to sign Term of Use for ' + $('#first_name').val() + ' ' + $('#last_name').val() + '?</p>';

            showDialog(message, true, function () {
                var data = {
                    user: $btnSignTos.data('user-id')
                };

                $btnSignTos.text($btnSignTos.data('text-progress'));

                $.post('/users/signtos', data, function (resp) {
                    showAlert(resp.msg, 'success');

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);
                }).fail(function (xhr) {
                    var resp = JSON.parse(xhr.responseText);
                    showAlert(resp.msg, 'danger');
                }).always(function () {
                    $btnSignTos.text($btnSignTos.data('text-default'));
                });
            });
        });

        if ($('.js-table').length) {
            initDatatable({
                table: '.js-table',
                filter: '.js-table-filter',
                search: {
                    form: '.js-table-search-form',
                    input: '.js-table-search-input',
                },
                pagination: {
                    basic: '.pagination-basic',
                    table: '.pagination-table',
                },
                columns: [
                    {
                        data: 'id',
                        className: 'text-center',
                        render: function (data, type, row) {
                            return '<a href="/users/edit/' + data + '">' + data + '</a>';
                        },
                    },
                    {
                        data: 'states',
                        className: 'text-center',
                    },
                    {
                        data: 'first_name',
                        className: 'text-center',
                    },
                    {
                        data: 'last_name',
                        className: 'text-center',
                    },
                    {
                        data: 'role',
                        className: 'text-center',
                    },
                    {
                        data: 'mfa',
                        className: 'text-center',
                    },
                    {
                        data: 'last_login',
                        className: 'text-center',
                    },
                    {
                        data: 'email',
                        className: 'text-center',
                    },
                ]
            });
        }

        $('[name="states[]"]').on('change', function () {
            var selected  = $(this).val();
            var $dioceses = $('[name="dioceses[]"]');

            if (!selected.length) {
                $dioceses.find('option').prop('disabled', false);
            } else {
                $dioceses.find('option').prop('disabled', true);

                $.each(selected, function (index, val) {
                    $dioceses.find('optgroup[label="' + val.toUpperCase() + '"]').find('option').prop('disabled', false);
                });
            }

            $dioceses.select2();
        }).change();
    });
})(jQuery);
