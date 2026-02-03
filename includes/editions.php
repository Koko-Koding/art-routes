<?php
/**
 * Edition Custom Post Type for WP Art Routes Plugin
 *
 * Editions are containers that group routes, locations, and info points
 * for specific events or time periods (e.g., "Gluren bij de Buren 2024").
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the Edition custom post type
 */
function wp_art_routes_register_edition_post_type()
{
    register_post_type('edition', [
        'labels' => [
            'name'               => __('Editions', 'wp-art-routes'),
            'singular_name'      => __('Edition', 'wp-art-routes'),
            'add_new'            => __('Add New', 'wp-art-routes'),
            'add_new_item'       => __('Add New Edition', 'wp-art-routes'),
            'edit_item'          => __('Edit Edition', 'wp-art-routes'),
            'new_item'           => __('New Edition', 'wp-art-routes'),
            'view_item'          => __('View Edition', 'wp-art-routes'),
            'view_items'         => __('View Editions', 'wp-art-routes'),
            'search_items'       => __('Search Editions', 'wp-art-routes'),
            'not_found'          => __('No editions found', 'wp-art-routes'),
            'not_found_in_trash' => __('No editions found in Trash', 'wp-art-routes'),
            'all_items'          => __('Editions', 'wp-art-routes'),
            'archives'           => __('Edition Archives', 'wp-art-routes'),
            'attributes'         => __('Edition Attributes', 'wp-art-routes'),
            'menu_name'          => __('Art Routes', 'wp-art-routes'),
        ],
        'public'             => true,
        'has_archive'        => true,
        'supports'           => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon'          => 'dashicons-location-alt',
        'menu_position'      => 5,
        'show_in_rest'       => true,
        'rewrite'            => ['slug' => 'edition'],
    ]);
}
add_action('init', 'wp_art_routes_register_edition_post_type');

/**
 * Remove "Add New" submenu item from Editions menu
 *
 * Editions should only be created deliberately, not from the menu.
 * Users can still create editions from the list table "Add New" button if needed.
 */
function wp_art_routes_remove_edition_add_new_submenu()
{
    remove_submenu_page('edit.php?post_type=edition', 'post-new.php?post_type=edition');
}
add_action('admin_menu', 'wp_art_routes_remove_edition_add_new_submenu', 999);

/**
 * Register Edition meta fields for REST API
 */
function wp_art_routes_register_edition_meta()
{
    // Edition terminology overrides
    register_post_meta('edition', '_edition_terminology', [
        'type'              => 'array',
        'single'            => true,
        'show_in_rest'      => [
            'schema' => [
                'type'  => 'object',
                'items' => [
                    'type' => 'object',
                ],
            ],
        ],
        'sanitize_callback' => 'wp_art_routes_sanitize_edition_terminology',
        'auth_callback'     => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Edition start date
    register_post_meta('edition', '_edition_start_date', [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Edition end date
    register_post_meta('edition', '_edition_end_date', [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Edition default location icon
    register_post_meta('edition', '_edition_default_location_icon', [
        'type'              => 'string',
        'single'            => true,
        'show_in_rest'      => true,
        'sanitize_callback' => 'wp_art_routes_sanitize_icon_filename',
        'auth_callback'     => function () {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'wp_art_routes_register_edition_meta');

/**
 * Sanitize edition terminology array
 *
 * @param mixed $value The value to sanitize
 * @return array Sanitized terminology array
 */
function wp_art_routes_sanitize_edition_terminology($value)
{
    if (!is_array($value)) {
        return [];
    }

    $sanitized = [];
    $allowed_types = ['route', 'location', 'info_point', 'creator'];
    $allowed_keys = ['singular', 'plural'];

    foreach ($value as $type => $fields) {
        if (!in_array($type, $allowed_types, true)) {
            continue;
        }
        if (!is_array($fields)) {
            continue;
        }

        $sanitized[$type] = [];
        foreach ($fields as $key => $field_value) {
            if (in_array($key, $allowed_keys, true)) {
                $sanitized[$type][$key] = sanitize_text_field($field_value);
            }
        }
    }

    return $sanitized;
}

/**
 * Add meta boxes for Edition post type
 */
function wp_art_routes_add_edition_meta_boxes()
{
    // Terminology Overrides meta box
    add_meta_box(
        'edition_terminology',
        __('Terminology Overrides', 'wp-art-routes'),
        'wp_art_routes_render_edition_terminology_meta_box',
        'edition',
        'normal',
        'high'
    );

    // Event Dates meta box
    add_meta_box(
        'edition_dates',
        __('Event Dates', 'wp-art-routes'),
        'wp_art_routes_render_edition_dates_meta_box',
        'edition',
        'side',
        'default'
    );

    // Edition Settings meta box (default icon)
    add_meta_box(
        'edition_settings',
        __('Edition Settings', 'wp-art-routes'),
        'wp_art_routes_render_edition_settings_meta_box',
        'edition',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'wp_art_routes_add_edition_meta_boxes');

/**
 * Render Terminology Overrides meta box
 *
 * @param WP_Post $post The post object
 */
function wp_art_routes_render_edition_terminology_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_edition_terminology', 'edition_terminology_nonce');

    // Get saved terminology overrides
    $terminology = get_post_meta($post->ID, '_edition_terminology', true);
    if (!is_array($terminology)) {
        $terminology = [];
    }

    // Get global terminology for placeholders
    $global = wp_art_routes_get_global_terminology();

    // Define the terminology types and their fields
    $types = [
        'route' => [
            'label' => __('Route', 'wp-art-routes'),
            'description' => __('The main paths users follow', 'wp-art-routes'),
        ],
        'location' => [
            'label' => __('Location', 'wp-art-routes'),
            'description' => __('Main content items (artworks, performances, etc.)', 'wp-art-routes'),
        ],
        'info_point' => [
            'label' => __('Info Point', 'wp-art-routes'),
            'description' => __('Information markers along routes', 'wp-art-routes'),
        ],
        'creator' => [
            'label' => __('Creator', 'wp-art-routes'),
            'description' => __('People/entities associated with locations (artists, performers, etc.)', 'wp-art-routes'),
        ],
    ];

    ?>
    <p class="description">
        <?php _e('Override the global terminology labels for this edition. Leave empty to use the global settings (shown as placeholders).', 'wp-art-routes'); ?>
    </p>

    <table class="form-table" role="presentation">
        <?php foreach ($types as $type => $config) : ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html($config['label']); ?>
                    <p class="description" style="font-weight: normal;">
                        <?php echo esc_html($config['description']); ?>
                    </p>
                </th>
                <td>
                    <p>
                        <label for="edition_terminology_<?php echo esc_attr($type); ?>_singular">
                            <?php _e('Singular:', 'wp-art-routes'); ?>
                        </label>
                        <input type="text"
                               id="edition_terminology_<?php echo esc_attr($type); ?>_singular"
                               name="edition_terminology[<?php echo esc_attr($type); ?>][singular]"
                               value="<?php echo esc_attr($terminology[$type]['singular'] ?? ''); ?>"
                               placeholder="<?php echo esc_attr($global[$type]['singular'] ?? ''); ?>"
                               class="regular-text" />
                    </p>
                    <p>
                        <label for="edition_terminology_<?php echo esc_attr($type); ?>_plural">
                            <?php _e('Plural:', 'wp-art-routes'); ?>
                        </label>
                        <input type="text"
                               id="edition_terminology_<?php echo esc_attr($type); ?>_plural"
                               name="edition_terminology[<?php echo esc_attr($type); ?>][plural]"
                               value="<?php echo esc_attr($terminology[$type]['plural'] ?? ''); ?>"
                               placeholder="<?php echo esc_attr($global[$type]['plural'] ?? ''); ?>"
                               class="regular-text" />
                    </p>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php
}

/**
 * Render Event Dates meta box
 *
 * @param WP_Post $post The post object
 */
function wp_art_routes_render_edition_dates_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_edition_dates', 'edition_dates_nonce');

    // Get saved dates
    $start_date = get_post_meta($post->ID, '_edition_start_date', true);
    $end_date = get_post_meta($post->ID, '_edition_end_date', true);

    ?>
    <p>
        <label for="edition_start_date">
            <?php _e('Start Date:', 'wp-art-routes'); ?>
        </label>
        <input type="date"
               id="edition_start_date"
               name="edition_start_date"
               value="<?php echo esc_attr($start_date); ?>"
               class="widefat" />
    </p>

    <p>
        <label for="edition_end_date">
            <?php _e('End Date:', 'wp-art-routes'); ?>
        </label>
        <input type="date"
               id="edition_end_date"
               name="edition_end_date"
               value="<?php echo esc_attr($end_date); ?>"
               class="widefat" />
    </p>

    <p class="description">
        <?php _e('Optional: Set the event dates for this edition.', 'wp-art-routes'); ?>
    </p>
    <?php
}

/**
 * Render Edition Settings meta box
 *
 * @param WP_Post $post The post object
 */
function wp_art_routes_render_edition_settings_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_edition_settings', 'edition_settings_nonce');

    // Get saved default icon
    $default_icon = get_post_meta($post->ID, '_edition_default_location_icon', true);
    $available_icons = wp_art_routes_get_available_icons();

    // Get global default for fallback label
    $global_default = get_option('wp_art_routes_default_location_icon', '');
    $global_label = $global_default ? wp_art_routes_get_icon_display_name($global_default) : __('none', 'wp-art-routes');

    ?>
    <p>
        <label for="edition_default_location_icon">
            <?php _e('Default Location Icon:', 'wp-art-routes'); ?>
        </label>
        <select name="edition_default_location_icon" id="edition_default_location_icon" class="widefat">
            <option value="">
                <?php
                /* translators: %s: global default icon name */
                printf(esc_html__('Use global default (%s)', 'wp-art-routes'), esc_html($global_label));
                ?>
            </option>
            <?php foreach ($available_icons as $icon_filename) : ?>
                <option value="<?php echo esc_attr($icon_filename); ?>" <?php selected($default_icon, $icon_filename); ?>>
                    <?php echo esc_html(wp_art_routes_get_icon_display_name($icon_filename)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <?php if (!empty($default_icon)) : ?>
        <p style="text-align: center;">
            <img src="<?php echo esc_url(wp_art_routes_get_icon_url($default_icon)); ?>" alt="" style="width: 48px; height: 48px;">
        </p>
    <?php endif; ?>

    <p class="description">
        <?php _e('Default icon for locations in this edition that do not have an icon assigned.', 'wp-art-routes'); ?>
    </p>
    <?php
}

/**
 * Save Edition settings meta box data
 *
 * @param int $post_id The post ID
 */
function wp_art_routes_save_edition_settings($post_id)
{
    // Verify nonce
    if (!isset($_POST['edition_settings_nonce']) || !wp_verify_nonce($_POST['edition_settings_nonce'], 'save_edition_settings')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save default location icon
    if (isset($_POST['edition_default_location_icon'])) {
        $icon = $_POST['edition_default_location_icon'];

        // Validate icon exists in available icons
        if (empty($icon)) {
            delete_post_meta($post_id, '_edition_default_location_icon');
        } else {
            $available_icons = wp_art_routes_get_available_icons();
            if (in_array($icon, $available_icons, true)) {
                update_post_meta($post_id, '_edition_default_location_icon', $icon);
            }
        }
    }
}
add_action('save_post_edition', 'wp_art_routes_save_edition_settings');

/**
 * Save Edition terminology meta box data
 *
 * @param int $post_id The post ID
 */
function wp_art_routes_save_edition_terminology($post_id)
{
    // Verify nonce
    if (!isset($_POST['edition_terminology_nonce']) || !wp_verify_nonce($_POST['edition_terminology_nonce'], 'save_edition_terminology')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save terminology overrides
    if (isset($_POST['edition_terminology']) && is_array($_POST['edition_terminology'])) {
        $terminology = wp_art_routes_sanitize_edition_terminology($_POST['edition_terminology']);
        update_post_meta($post_id, '_edition_terminology', $terminology);
    } else {
        delete_post_meta($post_id, '_edition_terminology');
    }
}
add_action('save_post_edition', 'wp_art_routes_save_edition_terminology');

/**
 * Save Edition dates meta box data
 *
 * @param int $post_id The post ID
 */
function wp_art_routes_save_edition_dates($post_id)
{
    // Verify nonce
    if (!isset($_POST['edition_dates_nonce']) || !wp_verify_nonce($_POST['edition_dates_nonce'], 'save_edition_dates')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save start date
    if (isset($_POST['edition_start_date'])) {
        $start_date = sanitize_text_field($_POST['edition_start_date']);
        // Validate date format (Y-m-d)
        if (empty($start_date) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_date)) {
            if (!empty($start_date)) {
                update_post_meta($post_id, '_edition_start_date', $start_date);
            } else {
                delete_post_meta($post_id, '_edition_start_date');
            }
        }
    }

    // Save end date
    if (isset($_POST['edition_end_date'])) {
        $end_date = sanitize_text_field($_POST['edition_end_date']);
        // Validate date format (Y-m-d)
        if (empty($end_date) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_date)) {
            if (!empty($end_date)) {
                update_post_meta($post_id, '_edition_end_date', $end_date);
            } else {
                delete_post_meta($post_id, '_edition_end_date');
            }
        }
    }
}
add_action('save_post_edition', 'wp_art_routes_save_edition_dates');

/**
 * Get all published editions
 *
 * @param array $args Optional. Additional query arguments.
 * @return WP_Post[] Array of edition post objects
 */
function wp_art_routes_get_editions($args = [])
{
    $default_args = [
        'post_type'      => 'edition',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];

    $query_args = wp_parse_args($args, $default_args);

    return get_posts($query_args);
}

/**
 * Get edition data by ID
 *
 * Returns a structured array containing all edition data including
 * post data, terminology overrides, and dates.
 *
 * @param int $edition_id The edition post ID
 * @return array|null Edition data array or null if not found
 */
function wp_art_routes_get_edition_data($edition_id)
{
    $edition = get_post($edition_id);

    if (!$edition || $edition->post_type !== 'edition') {
        return null;
    }

    $terminology = get_post_meta($edition_id, '_edition_terminology', true);
    if (!is_array($terminology)) {
        $terminology = [];
    }

    return [
        'id'          => $edition->ID,
        'title'       => $edition->post_title,
        'content'     => $edition->post_content,
        'excerpt'     => $edition->post_excerpt,
        'permalink'   => get_permalink($edition->ID),
        'thumbnail'   => get_post_thumbnail_id($edition->ID),
        'start_date'  => get_post_meta($edition_id, '_edition_start_date', true),
        'end_date'    => get_post_meta($edition_id, '_edition_end_date', true),
        'terminology' => $terminology,
        'status'      => $edition->post_status,
    ];
}

/**
 * Get the edition assigned to a post
 *
 * @param int $post_id The post ID
 * @return int|null Edition ID or null if not assigned
 */
function wp_art_routes_get_post_edition($post_id)
{
    $edition_id = get_post_meta($post_id, '_edition_id', true);

    if (empty($edition_id)) {
        return null;
    }

    // Verify the edition exists and is published
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition' || $edition->post_status !== 'publish') {
        return null;
    }

    return (int) $edition_id;
}

/**
 * Get edition-aware label
 *
 * Convenience function that checks edition overrides first, then falls back
 * to global settings, then to hardcoded defaults.
 *
 * @param string   $type       The terminology type: 'route', 'location', 'info_point', 'creator'
 * @param bool     $plural     Whether to return the plural form (default: false)
 * @param int|null $edition_id Optional edition ID to check for overrides
 * @return string The label for the requested type
 */
function wp_art_routes_edition_label($type, $plural = false, $edition_id = null)
{
    return wp_art_routes_label($type, $plural, $edition_id);
}

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
