# Edition Delete Confirmation Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a confirmation modal when deleting editions that lets users choose to keep or delete linked content.

**Architecture:** JavaScript intercepts delete actions on the Editions list table, fetches content counts via AJAX, displays a Thickbox modal with options, then executes the chosen deletion method via AJAX.

**Tech Stack:** WordPress Thickbox, jQuery, PHP AJAX handlers

---

## Task 1: Add AJAX handler to get edition content counts

**Files:**
- Modify: `includes/editions.php` (append to end)

**Step 1: Add the AJAX handler function**

Add to the end of `includes/editions.php`:

```php
/**
 * AJAX handler to get content counts for editions
 * Used by the delete confirmation modal
 */
function wp_art_routes_ajax_get_edition_content_counts() {
    check_ajax_referer('wp_art_routes_edition_delete', 'nonce');

    if (!current_user_can('delete_posts')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    $edition_ids = isset($_POST['edition_ids']) ? array_map('absint', (array) $_POST['edition_ids']) : [];

    if (empty($edition_ids)) {
        wp_send_json_error(['message' => __('No editions specified.', 'wp-art-routes')]);
    }

    $counts = [
        'routes' => 0,
        'locations' => 0,
        'info_points' => 0,
    ];

    // Count content for each edition (including drafts)
    foreach ($edition_ids as $edition_id) {
        // Routes
        $routes = get_posts([
            'post_type' => 'art_route',
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        $counts['routes'] += count($routes);

        // Locations (artworks)
        $locations = get_posts([
            'post_type' => 'artwork',
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        $counts['locations'] += count($locations);

        // Info Points
        $info_points = get_posts([
            'post_type' => 'information_point',
            'post_status' => ['publish', 'draft', 'pending', 'private'],
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        $counts['info_points'] += count($info_points);
    }

    // Get edition titles for display
    $titles = [];
    foreach ($edition_ids as $edition_id) {
        $titles[] = get_the_title($edition_id);
    }

    wp_send_json_success([
        'counts' => $counts,
        'titles' => $titles,
        'edition_ids' => $edition_ids,
    ]);
}
add_action('wp_ajax_wp_art_routes_get_edition_content_counts', 'wp_art_routes_ajax_get_edition_content_counts');
```

**Step 2: Verify the file saves correctly**

Run: Open `includes/editions.php` and verify the function was added at the end.

**Step 3: Commit**

```bash
git add includes/editions.php
git commit -m "feat(editions): Add AJAX handler for edition content counts"
```

---

## Task 2: Add AJAX handler to delete edition only

**Files:**
- Modify: `includes/editions.php` (append after previous function)

**Step 1: Add the delete-edition-only handler**

Add after the previous function in `includes/editions.php`:

```php
/**
 * AJAX handler to delete editions only (keep linked content)
 * Clears _edition_id meta from linked content before deletion
 */
function wp_art_routes_ajax_delete_edition_only() {
    check_ajax_referer('wp_art_routes_edition_delete', 'nonce');

    if (!current_user_can('delete_posts')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    $edition_ids = isset($_POST['edition_ids']) ? array_map('absint', (array) $_POST['edition_ids']) : [];

    if (empty($edition_ids)) {
        wp_send_json_error(['message' => __('No editions specified.', 'wp-art-routes')]);
    }

    $deleted_count = 0;
    $unlinked_count = 0;

    foreach ($edition_ids as $edition_id) {
        // Verify this is an edition
        if (get_post_type($edition_id) !== 'edition') {
            continue;
        }

        // Clear _edition_id from all linked content
        $post_types = ['art_route', 'artwork', 'information_point'];
        foreach ($post_types as $post_type) {
            $linked_posts = get_posts([
                'post_type' => $post_type,
                'post_status' => 'any',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'meta_key' => '_edition_id',
                'meta_value' => $edition_id,
            ]);

            foreach ($linked_posts as $post_id) {
                delete_post_meta($post_id, '_edition_id');
                $unlinked_count++;
            }
        }

        // Delete the edition (force delete, skip trash)
        $result = wp_delete_post($edition_id, true);
        if ($result) {
            $deleted_count++;
        }
    }

    wp_send_json_success([
        'deleted' => $deleted_count,
        'unlinked' => $unlinked_count,
        'message' => sprintf(
            /* translators: 1: number of editions deleted, 2: number of items unlinked */
            __('Deleted %1$d edition(s). %2$d item(s) were unlinked.', 'wp-art-routes'),
            $deleted_count,
            $unlinked_count
        ),
    ]);
}
add_action('wp_ajax_wp_art_routes_delete_edition_only', 'wp_art_routes_ajax_delete_edition_only');
```

**Step 2: Commit**

```bash
git add includes/editions.php
git commit -m "feat(editions): Add AJAX handler for delete-edition-only"
```

---

## Task 3: Add AJAX handler to delete edition and all content

**Files:**
- Modify: `includes/editions.php` (append after previous function)

**Step 1: Add the delete-everything handler**

Add after the previous function in `includes/editions.php`:

```php
/**
 * AJAX handler to delete editions AND all linked content
 * Permanently deletes routes, locations, and info points linked to the edition
 */
function wp_art_routes_ajax_delete_edition_all() {
    check_ajax_referer('wp_art_routes_edition_delete', 'nonce');

    if (!current_user_can('delete_posts')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    $edition_ids = isset($_POST['edition_ids']) ? array_map('absint', (array) $_POST['edition_ids']) : [];

    if (empty($edition_ids)) {
        wp_send_json_error(['message' => __('No editions specified.', 'wp-art-routes')]);
    }

    $deleted_editions = 0;
    $deleted_routes = 0;
    $deleted_locations = 0;
    $deleted_info_points = 0;

    foreach ($edition_ids as $edition_id) {
        // Verify this is an edition
        if (get_post_type($edition_id) !== 'edition') {
            continue;
        }

        // Delete all linked routes
        $routes = get_posts([
            'post_type' => 'art_route',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        foreach ($routes as $post_id) {
            if (wp_delete_post($post_id, true)) {
                $deleted_routes++;
            }
        }

        // Delete all linked locations
        $locations = get_posts([
            'post_type' => 'artwork',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        foreach ($locations as $post_id) {
            if (wp_delete_post($post_id, true)) {
                $deleted_locations++;
            }
        }

        // Delete all linked info points
        $info_points = get_posts([
            'post_type' => 'information_point',
            'post_status' => 'any',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_key' => '_edition_id',
            'meta_value' => $edition_id,
        ]);
        foreach ($info_points as $post_id) {
            if (wp_delete_post($post_id, true)) {
                $deleted_info_points++;
            }
        }

        // Delete the edition itself
        if (wp_delete_post($edition_id, true)) {
            $deleted_editions++;
        }
    }

    wp_send_json_success([
        'deleted_editions' => $deleted_editions,
        'deleted_routes' => $deleted_routes,
        'deleted_locations' => $deleted_locations,
        'deleted_info_points' => $deleted_info_points,
        'message' => sprintf(
            /* translators: 1: editions, 2: routes, 3: locations, 4: info points */
            __('Deleted %1$d edition(s), %2$d route(s), %3$d location(s), %4$d info point(s).', 'wp-art-routes'),
            $deleted_editions,
            $deleted_routes,
            $deleted_locations,
            $deleted_info_points
        ),
    ]);
}
add_action('wp_ajax_wp_art_routes_delete_edition_all', 'wp_art_routes_ajax_delete_edition_all');
```

**Step 2: Commit**

```bash
git add includes/editions.php
git commit -m "feat(editions): Add AJAX handler for delete-edition-and-content"
```

---

## Task 4: Create the JavaScript file for the delete modal

**Files:**
- Create: `assets/js/edition-delete-modal.js`

**Step 1: Create the JavaScript file**

Create `assets/js/edition-delete-modal.js`:

```javascript
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
```

**Step 2: Commit**

```bash
git add assets/js/edition-delete-modal.js
git commit -m "feat(editions): Add JavaScript for delete confirmation modal"
```

---

## Task 5: Create CSS for the delete modal

**Files:**
- Create: `assets/css/edition-delete-modal.css`

**Step 1: Create the CSS file**

Create `assets/css/edition-delete-modal.css`:

```css
/**
 * Edition Delete Confirmation Modal Styles
 */

#edition-delete-modal {
    padding: 20px;
}

.edition-delete-modal-content {
    text-align: center;
}

.edition-delete-title {
    font-size: 14px;
    margin-bottom: 15px;
}

.edition-delete-counts {
    text-align: left;
    margin-bottom: 20px;
}

.edition-delete-counts ul {
    margin: 10px 0 0 20px;
}

.edition-delete-counts li {
    margin-bottom: 5px;
}

.edition-delete-counts .spinner {
    float: none;
    margin: 0;
}

.edition-delete-question {
    font-weight: 600;
    margin-bottom: 15px;
}

.edition-delete-buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 15px;
}

.edition-delete-buttons .button {
    min-width: 140px;
}

.edition-delete-buttons .button-link-delete {
    background: #d63638;
    border-color: #d63638;
    color: #fff;
}

.edition-delete-buttons .button-link-delete:hover {
    background: #b32d2e;
    border-color: #b32d2e;
}

.edition-delete-buttons .button-link-delete:focus {
    box-shadow: 0 0 0 1px #fff, 0 0 0 3px #d63638;
}

.edition-delete-cancel {
    margin-top: 10px;
}

.edition-delete-cancel a {
    text-decoration: none;
}

.edition-delete-loading {
    padding: 20px;
}

.edition-delete-loading .spinner {
    float: none;
    margin: 0 5px 0 0;
}

.edition-delete-counts .error {
    color: #d63638;
}
```

**Step 2: Commit**

```bash
git add assets/css/edition-delete-modal.css
git commit -m "feat(editions): Add CSS for delete confirmation modal"
```

---

## Task 6: Enqueue the modal assets on Editions list page

**Files:**
- Modify: `includes/editions.php` (add new function before the AJAX handlers)

**Step 1: Add the asset enqueue function**

Add this function in `includes/editions.php`, before the AJAX handler functions:

```php
/**
 * Enqueue assets for the Edition delete confirmation modal
 *
 * @param string $hook The current admin page hook.
 */
function wp_art_routes_enqueue_edition_delete_modal_assets($hook) {
    // Only load on edition list table
    if ($hook !== 'edit.php') {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'edition') {
        return;
    }

    // Enqueue Thickbox
    add_thickbox();

    // Enqueue our CSS
    wp_enqueue_style(
        'wp-art-routes-edition-delete-modal',
        plugins_url('assets/css/edition-delete-modal.css', dirname(__FILE__)),
        [],
        filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/css/edition-delete-modal.css')
    );

    // Enqueue our JS
    wp_enqueue_script(
        'wp-art-routes-edition-delete-modal',
        plugins_url('assets/js/edition-delete-modal.js', dirname(__FILE__)),
        ['jquery', 'thickbox'],
        filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/js/edition-delete-modal.js'),
        true
    );

    // Localize script
    wp_localize_script('wp-art-routes-edition-delete-modal', 'wpArtRoutesEditionDelete', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_art_routes_edition_delete'),
        'strings' => [
            'modalTitle' => __('Delete Edition', 'wp-art-routes'),
            'deleteEdition' => __('Delete %s', 'wp-art-routes'),
            'deleteEditions' => __('Delete %d editions', 'wp-art-routes'),
            'containsContent' => __('This edition contains:', 'wp-art-routes'),
            'noContent' => __('This edition has no linked content.', 'wp-art-routes'),
            'whatToDo' => __('What would you like to do?', 'wp-art-routes'),
            'deleteEditionOnly' => __('Delete Edition Only', 'wp-art-routes'),
            'deleteEverything' => __('Delete Everything', 'wp-art-routes'),
            'delete' => __('Delete', 'wp-art-routes'),
            'cancel' => __('Cancel', 'wp-art-routes'),
            'deleting' => __('Deleting...', 'wp-art-routes'),
            'loading' => __('Loading...', 'wp-art-routes'),
            'error' => __('An error occurred. Please try again.', 'wp-art-routes'),
            'route' => __('Route', 'wp-art-routes'),
            'routes' => __('Routes', 'wp-art-routes'),
            'location' => __('Location', 'wp-art-routes'),
            'locations' => __('Locations', 'wp-art-routes'),
            'infoPoint' => __('Info Point', 'wp-art-routes'),
            'infoPoints' => __('Info Points', 'wp-art-routes'),
        ],
    ]);
}
add_action('admin_enqueue_scripts', 'wp_art_routes_enqueue_edition_delete_modal_assets');
```

**Step 2: Commit**

```bash
git add includes/editions.php
git commit -m "feat(editions): Enqueue delete modal assets on editions list"
```

---

## Task 7: Manual Testing

**Step 1: Test edition with no content**

1. Create a new edition with no routes/locations/info points linked
2. Click "Trash" on the edition
3. Modal should show "This edition has no linked content" with single "Delete" button
4. Click Delete, edition should be removed

**Step 2: Test edition with content - delete edition only**

1. Create an edition and link some routes/locations/info points to it
2. Click "Trash" on the edition
3. Modal should show counts of linked content
4. Click "Delete Edition Only"
5. Edition should be deleted, but routes/locations/info points should remain (now unlinked)

**Step 3: Test edition with content - delete everything**

1. Create an edition and link some routes/locations/info points to it
2. Click "Trash" on the edition
3. Click "Delete Everything"
4. Edition AND all linked content should be deleted

**Step 4: Test bulk delete**

1. Select multiple editions with checkboxes
2. Choose "Move to Trash" from bulk actions
3. Modal should show combined counts
4. Both options should work correctly

**Step 5: Test cancel**

1. Click "Trash" on an edition
2. Click "Cancel"
3. Nothing should be deleted

**Step 6: Commit verification**

```bash
git log --oneline -6
```

Expected: See commits for each task.

---

## Task 8: Final commit with all files

**Step 1: Verify all changes**

```bash
git status
```

**Step 2: If any uncommitted changes remain, commit them**

```bash
git add -A
git commit -m "feat(editions): Complete edition delete confirmation modal"
```
