/* global bootbox */

'use strict';

(function ($) {
    $(document).ready(function () {
        $('.js-gallery-upload-form').ajaxForm({
            beforeSubmit: function () {
                var $btn = $('.js-gallery-upload-form').find('[type="submit"]');
                $btn.text($btn.data('text-progress'));
                $('.js-gallery-upload-form').find('.help-block').remove();
                window.clearAlert();
            },
            success: function (responseText, statusText, xhr, $form) {
                $form.find('input[type="file"]').val('');
                $form.find('.js-file-selected').html('Uploaded file: <a href="' + responseText.url + '" target="_blank" rel="noopener noreferrer">' + responseText.name + ' (' + responseText.size + ')</a>');
                $('#file').val(responseText.path);
                $('.js-gallery-form [type="submit"]').prop('disabled', false);
            },
            error: function (xhr, status, error) {
                var errors = JSON.parse(xhr.responseText);

                if (errors.type === 'dialog') {
                    window.showDialog(errors.msg, errors.confirm);
                } else {
                    window.showFormErrors(errors.errors);
                }

                $('.js-gallery-form [type="submit"]').prop('disabled', true);
            },
            complete: function () {
                var $btn = $('.js-gallery-upload-form').find('[type="submit"]');
                $btn.text($btn.data('text-default'));
            }
        });

        $('.js-gallery-form').ajaxForm({
            clearForm: ($.trim($('.js-gallery-form').data('reset')) === '1'),
            resetForm: ($.trim($('.js-gallery-form').data('reset')) === '1'),
            beforeSubmit: function () {
                var $btn = $('.js-gallery-form').find('[type="submit"]');
                $btn.text($btn.data('text-progress'));
                window.clearAlert();

                if (($('[name="gallery_file"]').val()) !== '' || $.trim($('[name="file"]').val()) === '') {
                    window.showDialog('Please upload a file for your gallery item.');
                    $btn.text($btn.data('text-default'));
                    return false;
                }
            },
            success: function (responseText, statusText, xhr, $form) {
                window.showAlert(responseText.msg, 'success');

                if ($.trim(responseText.redirect) !== '') {
                    setTimeout(function () {
                        window.location.href = responseText.redirect;
                    }, 1500);
                }

                $form.find('input[type="file"]').val('');
                $form.find('.js-file-selected').text('-');
            },
            error: function (xhr, status, error) {
                var errors = JSON.parse(xhr.responseText);

                if (errors.type === 'dialog') {
                    window.showDialog(errors.msg, errors.confirm);
                } else {
                    window.showFormErrors(errors.errors);
                }
            },
            complete: function () {
                var $btn = $('.js-gallery-form').find('[type="submit"]');
                $btn.text($btn.data('text-default'));
            }
        });

        if ($('.item-preview-image').length) {
            $('.item-preview-image').magnificPopup({
                type: 'image' // this is default type
            });
        }

        $('.js-bulk-download').on('click', function (e) {
            e.preventDefault();

            var ids = $(this).closest('.gallery-container').find('[name="id[]"]:checked').map(function() {
                return this.value;
            }).get();

            if (!ids.length) {
                window.showDialog('Please select at least one item for bulk download');
                return;
            }

            $.post('/galleries/download', {ids: ids}, function (res) {
                window.location = res;
            });
        });

        $('.js-bulk-delete').on('click', function (e) {
            e.preventDefault();

            var ids = $(this).closest('.gallery-container').find('[name="id[]"]:checked').map(function() {
                return this.value;
            }).get();

            if (!ids.length) {
                window.showDialog('Please select at least one item for bulk delete');
                return;
            }

            window.showDialog('Are you sure you want to do this? This process is irreversible', true, function () {
                $('.delete-galleries-form').find('[name="ids"]').val(ids.join(','));
                $('.delete-galleries-form').submit();
            });
        });

        $('.js-delete-gallery').on('click', function (e) {
            e.preventDefault();

            window.showDialog('Are you sure you want to do this? This process is irreversible', true, function () {
                $('.delete-galleries-form').submit();
            });
        });
    });
})(jQuery);
