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
                    '<p class="edition-delete-question">' + wpArtRoutesEditionDelete.strings.whatToDo + '</p>' +
                    '<div class="edition-delete-buttons">' +
                        '<button type="button" class="button button-secondary" id="edition-delete-only">' +
                            wpArtRoutesEditionDelete.strings.deleteEditionOnly +
                        '</button>' +
                        '<button type="button" class="button button-link-delete" id="edition-delete-all">' +
                            wpArtRoutesEditionDelete.strings.deleteEverything +
                        '</button>' +
                    '</div>' +
                    '<p class="edition-delete-cancel">' +
                        '<a href="#" id="edition-delete-cancel">' + wpArtRoutesEditionDelete.strings.cancel + '</a>' +
                    '</p>' +
                    '<div class="edition-delete-loading" style="display:none;">' +
                        '<span class="spinner is-active"></span> ' + wpArtRoutesEditionDelete.strings.deleting +
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

            // Modal button handlers
            this.$modal.on('click', '#edition-delete-only', function() {
                self.executeDelete('edition_only');
            });

            this.$modal.on('click', '#edition-delete-all', function() {
                self.executeDelete('everything');
            });

            this.$modal.on('click', '#edition-delete-cancel', function(e) {
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

            // Show thickbox with loading state
            this.$modal.find('.edition-delete-counts').html('<span class="spinner is-active"></span> ' + wpArtRoutesEditionDelete.strings.loading);
            this.$modal.find('.edition-delete-buttons').hide();
            this.$modal.find('.edition-delete-question').hide();

            tb_show(
                wpArtRoutesEditionDelete.strings.modalTitle,
                '#TB_inline?width=400&height=300&inlineId=edition-delete-modal'
            );

            // Fetch content counts
            $.ajax({
                url: wpArtRoutesEditionDelete.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'wp_art_routes_get_edition_content_counts',
                    nonce: wpArtRoutesEditionDelete.nonce,
                    edition_ids: self.pendingEditionIds
                },
                success: function(response) {
                    if (response.success) {
                        self.displayCounts(response.data);
                    } else {
                        self.showError(response.data.message || wpArtRoutesEditionDelete.strings.error);
                    }
                },
                error: function() {
                    self.showError(wpArtRoutesEditionDelete.strings.error);
                }
            });
        },

        displayCounts: function(data) {
            var counts = data.counts;
            var titles = data.titles;
            var total = counts.routes + counts.locations + counts.info_points;

            // Show title(s)
            var titleText = titles.length === 1
                ? wpArtRoutesEditionDelete.strings.deleteEdition.replace('%s', '"' + titles[0] + '"')
                : wpArtRoutesEditionDelete.strings.deleteEditions.replace('%d', titles.length);
            this.$modal.find('.edition-delete-title').html('<strong>' + titleText + '</strong>');

            // If no content, proceed directly
            if (total === 0) {
                this.$modal.find('.edition-delete-counts').html('<p>' + wpArtRoutesEditionDelete.strings.noContent + '</p>');
                this.$modal.find('.edition-delete-question').hide();
                this.$modal.find('#edition-delete-all').hide();
                this.$modal.find('#edition-delete-only').text(wpArtRoutesEditionDelete.strings.delete).show();
                this.$modal.find('.edition-delete-buttons').show();
            } else {
                // Show counts
                var countsHtml = '<p>' + wpArtRoutesEditionDelete.strings.containsContent + '</p><ul>';
                if (counts.routes > 0) {
                    countsHtml += '<li>' + counts.routes + ' ' + (counts.routes === 1 ? wpArtRoutesEditionDelete.strings.route : wpArtRoutesEditionDelete.strings.routes) + '</li>';
                }
                if (counts.locations > 0) {
                    countsHtml += '<li>' + counts.locations + ' ' + (counts.locations === 1 ? wpArtRoutesEditionDelete.strings.location : wpArtRoutesEditionDelete.strings.locations) + '</li>';
                }
                if (counts.info_points > 0) {
                    countsHtml += '<li>' + counts.info_points + ' ' + (counts.info_points === 1 ? wpArtRoutesEditionDelete.strings.infoPoint : wpArtRoutesEditionDelete.strings.infoPoints) + '</li>';
                }
                countsHtml += '</ul>';

                this.$modal.find('.edition-delete-counts').html(countsHtml);
                this.$modal.find('.edition-delete-question').show();
                this.$modal.find('#edition-delete-only').text(wpArtRoutesEditionDelete.strings.deleteEditionOnly).show();
                this.$modal.find('#edition-delete-all').show();
                this.$modal.find('.edition-delete-buttons').show();
            }
        },

        showError: function(message) {
            this.$modal.find('.edition-delete-counts').html('<p class="error">' + message + '</p>');
        },

        executeDelete: function(mode) {
            var self = this;
            var action = mode === 'everything'
                ? 'wp_art_routes_delete_edition_all'
                : 'wp_art_routes_delete_edition_only';

            // Show loading state
            this.$modal.find('.edition-delete-buttons').hide();
            this.$modal.find('.edition-delete-cancel').hide();
            this.$modal.find('.edition-delete-question').hide();
            this.$modal.find('.edition-delete-loading').show();

            $.ajax({
                url: wpArtRoutesEditionDelete.ajaxUrl,
                type: 'POST',
                data: {
                    action: action,
                    nonce: wpArtRoutesEditionDelete.nonce,
                    edition_ids: self.pendingEditionIds
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the page to show updated list
                        window.location.reload();
                    } else {
                        self.$modal.find('.edition-delete-loading').hide();
                        self.showError(response.data.message || wpArtRoutesEditionDelete.strings.error);
                        self.$modal.find('.edition-delete-cancel').show();
                    }
                },
                error: function() {
                    self.$modal.find('.edition-delete-loading').hide();
                    self.showError(wpArtRoutesEditionDelete.strings.error);
                    self.$modal.find('.edition-delete-cancel').show();
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
