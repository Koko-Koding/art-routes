/**
 * Custom Icons Upload/Delete
 *
 * Handles AJAX upload and delete of custom icons in the Settings page.
 * Expects artRoutesCustomIcons to be localized with: { deleteNonce, i18n }
 *
 * @package WP Art Routes
 */

jQuery(document).ready(function($) {
    if (typeof artRoutesCustomIcons === 'undefined') {
        return;
    }

    var i18n = artRoutesCustomIcons.i18n;

    // Handle icon upload
    $('#custom-icon-upload-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'art_routes_upload_custom_icon');

        var $btn = $('#upload-icon-btn');
        var $status = $('#upload-status');

        $btn.prop('disabled', true);
        $status.text(i18n.uploading);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $status.css('color', 'green').text(response.data.message);
                    // Add the new icon to the grid
                    var iconHtml = '<div class="custom-icon-item" data-filename="' + response.data.filename + '" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">' +
                        '<div class="icon-preview" style="height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">' +
                        '<img src="' + response.data.url + '" alt="" style="max-width: 100%; max-height: 60px;">' +
                        '</div>' +
                        '<div class="icon-filename" style="font-size: 12px; word-break: break-all; margin-bottom: 10px;">' + response.data.filename + '</div>' +
                        '<button type="button" class="button button-small button-link-delete delete-custom-icon" data-filename="' + response.data.filename + '">' + i18n.deleteBtn + '</button>' +
                        '</div>';
                    $('#custom-icons-grid').append(iconHtml);
                    $('#no-custom-icons-message').hide();
                    $('#custom_icon_file').val('');
                } else {
                    $status.css('color', 'red').text(response.data.message || i18n.uploadFailed);
                }
            },
            error: function() {
                $status.css('color', 'red').text(i18n.uploadFailed);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Handle icon delete
    $(document).on('click', '.delete-custom-icon', function() {
        var $btn = $(this);
        var filename = $btn.data('filename');

        if (!confirm(i18n.confirmDelete)) {
            return;
        }

        $btn.prop('disabled', true).text(i18n.deleting);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'art_routes_delete_custom_icon',
                filename: filename,
                nonce: artRoutesCustomIcons.deleteNonce
            },
            success: function(response) {
                if (response.success) {
                    $btn.closest('.custom-icon-item').fadeOut(300, function() {
                        $(this).remove();
                        if ($('#custom-icons-grid .custom-icon-item').length === 0) {
                            $('#no-custom-icons-message').show();
                        }
                    });
                } else {
                    alert(response.data.message || i18n.deleteFailed);
                    $btn.prop('disabled', false).text(i18n.deleteBtn);
                }
            },
            error: function() {
                alert(i18n.deleteFailed);
                $btn.prop('disabled', false).text(i18n.deleteBtn);
            }
        });
    });
});
