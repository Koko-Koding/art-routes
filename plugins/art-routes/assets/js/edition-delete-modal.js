/**
 * Edition Delete Confirmation Modal
 *
 * Intercepts delete actions on the Editions list table and shows
 * a confirmation modal with options to keep or delete linked content.
 */
(function($) {
    'use strict';

    var EditionDeleteModal = {
        pendingEditionIds: [],
        $modal: null,

        init: function() {
            this.createModal();
            this.bindEvents();
        },

        createModal: function() {
            var modalHtml = '<div id="edition-delete-modal" style="display:none;">' +
                '<div class="edition-delete-modal-content">' +
                    '<p class="edition-delete-title"></p>' +
                    '<div class="edition-delete-counts"></div>' +
                    '<p class="edition-delete-question">' + artRoutesEditionDelete.strings.whatToDo + '</p>' +
                    '<div class="edition-delete-buttons">' +
                        '<button type="button" class="button button-secondary" id="edition-delete-only">' +
                            artRoutesEditionDelete.strings.deleteEditionOnly +
                        '</button>' +
                        '<button type="button" class="button button-link-delete" id="edition-delete-all">' +
                            artRoutesEditionDelete.strings.deleteEverything +
                        '</button>' +
                    '</div>' +
                    '<p class="edition-delete-cancel">' +
                        '<a href="#" id="edition-delete-cancel">' + artRoutesEditionDelete.strings.cancel + '</a>' +
                    '</p>' +
                    '<div class="edition-delete-loading" style="display:none;">' +
                        '<span class="spinner is-active"></span> ' + artRoutesEditionDelete.strings.deleting +
                    '</div>' +
                '</div>' +
            '</div>';

            $('body').append(modalHtml);
            this.$modal = $('#edition-delete-modal');
        },

        bindEvents: function() {
            var self = this;

            // Intercept individual trash/delete links
            $(document).on('click', '.post-type-edition .row-actions .submitdelete', function(e) {
                e.preventDefault();
                var $link = $(this);
                var href = $link.attr('href');
                var postId = self.extractPostId(href);

                if (postId) {
                    self.pendingEditionIds = [postId];
                    self.showModal();
                }
            });

            // Intercept bulk action form submission
            $('#posts-filter').on('submit', function(e) {
                var action = $('#bulk-action-selector-top').val();
                if (action === '' || action === '-1') {
                    action = $('#bulk-action-selector-bottom').val();
                }

                if (action === 'trash' || action === 'delete') {
                    var checkedIds = [];
                    $('input[name="post[]"]:checked').each(function() {
                        checkedIds.push(parseInt($(this).val(), 10));
                    });

                    if (checkedIds.length > 0) {
                        e.preventDefault();
                        self.pendingEditionIds = checkedIds;
                        self.showModal();
                    }
                }
            });

            // Modal button handlers - use document delegation since Thickbox copies the content
            $(document).on('click', '#edition-delete-only', function() {
                self.executeDelete('edition_only');
            });

            $(document).on('click', '#edition-delete-all', function() {
                self.executeDelete('everything');
            });

            $(document).on('click', '#edition-delete-cancel', function(e) {
                e.preventDefault();
                self.closeModal();
            });
        },

        extractPostId: function(href) {
            // Extract post ID from trash or delete link
            var match = href.match(/post=(\d+)/);
            return match ? parseInt(match[1], 10) : null;
        },

        showModal: function() {
            var self = this;

            console.log('Edition Delete Modal: showModal called', {
                editionIds: self.pendingEditionIds,
                ajaxUrl: artRoutesEditionDelete.ajaxUrl,
                nonce: artRoutesEditionDelete.nonce ? 'present' : 'missing'
            });

            // Show thickbox with loading state
            this.$modal.find('.edition-delete-counts').html('<span class="spinner is-active"></span> ' + artRoutesEditionDelete.strings.loading);
            this.$modal.find('.edition-delete-buttons').hide();
            this.$modal.find('.edition-delete-question').hide();

            tb_show(
                artRoutesEditionDelete.strings.modalTitle,
                '#TB_inline?width=400&height=300&inlineId=edition-delete-modal'
            );

            console.log('Edition Delete Modal: Making AJAX request...');

            // Fetch content counts
            $.ajax({
                url: artRoutesEditionDelete.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'art_routes_get_edition_content_counts',
                    nonce: artRoutesEditionDelete.nonce,
                    edition_ids: self.pendingEditionIds
                },
                success: function(response) {
                    console.log('Edition Delete Modal: AJAX success', response);
                    if (response && response.success) {
                        self.displayCounts(response.data);
                    } else {
                        var message = (response && response.data && response.data.message)
                            ? response.data.message
                            : artRoutesEditionDelete.strings.error;
                        self.showError(message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Edition Delete Modal: AJAX error', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    self.showError(artRoutesEditionDelete.strings.error);
                },
                complete: function(xhr, status) {
                    console.log('Edition Delete Modal: AJAX complete', status);
                }
            });
        },

        displayCounts: function(data) {
            var counts = data.counts;
            var titles = data.titles;
            var total = counts.routes + counts.locations + counts.info_points;

            // Thickbox copies content, so we need to target the visible copy inside #TB_ajaxContent
            var $visibleModal = $('#TB_ajaxContent').length ? $('#TB_ajaxContent') : this.$modal;

            // Show title(s)
            var titleText = titles.length === 1
                ? artRoutesEditionDelete.strings.deleteEdition.replace('%s', '"' + titles[0] + '"')
                : artRoutesEditionDelete.strings.deleteEditions.replace('%d', titles.length);
            $visibleModal.find('.edition-delete-title').html('<strong>' + titleText + '</strong>');

            // If no content, proceed directly
            if (total === 0) {
                $visibleModal.find('.edition-delete-counts').html('<p>' + artRoutesEditionDelete.strings.noContent + '</p>');
                $visibleModal.find('.edition-delete-question').hide();
                $visibleModal.find('#edition-delete-all').hide();
                $visibleModal.find('#edition-delete-only').text(artRoutesEditionDelete.strings.delete).show();
                $visibleModal.find('.edition-delete-buttons').show();
            } else {
                // Show counts
                var countsHtml = '<p>' + artRoutesEditionDelete.strings.containsContent + '</p><ul>';
                if (counts.routes > 0) {
                    countsHtml += '<li>' + counts.routes + ' ' + (counts.routes === 1 ? artRoutesEditionDelete.strings.route : artRoutesEditionDelete.strings.routes) + '</li>';
                }
                if (counts.locations > 0) {
                    countsHtml += '<li>' + counts.locations + ' ' + (counts.locations === 1 ? artRoutesEditionDelete.strings.location : artRoutesEditionDelete.strings.locations) + '</li>';
                }
                if (counts.info_points > 0) {
                    countsHtml += '<li>' + counts.info_points + ' ' + (counts.info_points === 1 ? artRoutesEditionDelete.strings.infoPoint : artRoutesEditionDelete.strings.infoPoints) + '</li>';
                }
                countsHtml += '</ul>';

                $visibleModal.find('.edition-delete-counts').html(countsHtml);
                $visibleModal.find('.edition-delete-question').show();
                $visibleModal.find('#edition-delete-only').text(artRoutesEditionDelete.strings.deleteEditionOnly).show();
                $visibleModal.find('#edition-delete-all').show();
                $visibleModal.find('.edition-delete-buttons').show();
            }
        },

        showError: function(message) {
            // Thickbox copies content, so we need to target the visible copy inside #TB_ajaxContent
            var $visibleModal = $('#TB_ajaxContent').length ? $('#TB_ajaxContent') : this.$modal;
            $visibleModal.find('.edition-delete-counts').html('<p class="error">' + message + '</p>');
            $visibleModal.find('.edition-delete-buttons').hide();
            $visibleModal.find('.edition-delete-question').hide();
            $visibleModal.find('.edition-delete-cancel').show();
        },

        executeDelete: function(mode) {
            var self = this;
            var action = mode === 'everything'
                ? 'art_routes_delete_edition_all'
                : 'art_routes_delete_edition_only';

            // Thickbox copies content, so we need to target the visible copy inside #TB_ajaxContent
            var $visibleModal = $('#TB_ajaxContent').length ? $('#TB_ajaxContent') : this.$modal;

            // Show loading state
            $visibleModal.find('.edition-delete-buttons').hide();
            $visibleModal.find('.edition-delete-cancel').hide();
            $visibleModal.find('.edition-delete-question').hide();
            $visibleModal.find('.edition-delete-loading').show();

            $.ajax({
                url: artRoutesEditionDelete.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: action,
                    nonce: artRoutesEditionDelete.nonce,
                    edition_ids: self.pendingEditionIds
                },
                success: function(response) {
                    if (response && response.success) {
                        // Reload the page to show updated list
                        window.location.reload();
                    } else {
                        $visibleModal.find('.edition-delete-loading').hide();
                        var message = (response && response.data && response.data.message)
                            ? response.data.message
                            : artRoutesEditionDelete.strings.error;
                        self.showError(message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Edition delete AJAX error:', status, error);
                    $visibleModal.find('.edition-delete-loading').hide();
                    self.showError(artRoutesEditionDelete.strings.error);
                }
            });
        },

        closeModal: function() {
            tb_remove();
            this.pendingEditionIds = [];
        }
    };

    $(document).ready(function() {
        // Only initialize on edition list table
        if ($('body').hasClass('post-type-edition') && $('body').hasClass('edit-php')) {
            EditionDeleteModal.init();
        }
    });

})(jQuery);
