<?php
/**
 * Edition Dashboard Admin Page for WP Art Routes Plugin
 *
 * Provides a comprehensive management interface for all content within an edition.
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Edition Dashboard submenu page
 */
function wp_art_routes_add_dashboard_page()
{
    add_submenu_page(
        'edit.php?post_type=edition',
        __('Dashboard', 'wp-art-routes'),
        __('Dashboard', 'wp-art-routes'),
        'manage_options',
        'wp-art-routes-dashboard',
        'wp_art_routes_render_dashboard_page',
        1 // Position: after Editions, before Routes
    );
}
add_action('admin_menu', 'wp_art_routes_add_dashboard_page');

/**
 * Enqueue assets for the Edition Dashboard page
 *
 * @param string $hook The current admin page hook.
 */
function wp_art_routes_enqueue_dashboard_assets($hook)
{
    // Only load on the dashboard page
    if ('edition_page_wp-art-routes-dashboard' !== $hook) {
        return;
    }

    // Enqueue Leaflet CSS
    wp_enqueue_style(
        'leaflet',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css',
        array(),
        '1.9.4'
    );

    // Enqueue Leaflet JS
    wp_enqueue_script(
        'leaflet',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js',
        array(),
        '1.9.4',
        true
    );

    // Enqueue dashboard CSS
    wp_enqueue_style(
        'wp-art-routes-dashboard',
        plugins_url('assets/css/edition-dashboard.css', dirname(__FILE__)),
        array('leaflet'),
        filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/css/edition-dashboard.css')
    );

    // Enqueue dashboard JS
    wp_enqueue_script(
        'wp-art-routes-dashboard',
        plugins_url('assets/js/edition-dashboard.js', dirname(__FILE__)),
        array('jquery', 'leaflet'),
        filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/js/edition-dashboard.js'),
        true
    );

    // Localize script with data and strings
    wp_localize_script('wp-art-routes-dashboard', 'wpArtRoutesDashboard', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_art_routes_dashboard'),
        'iconsUrl' => plugins_url('assets/icons/', dirname(__FILE__)),
        'strings' => array(
            'loading' => __('Loading...', 'wp-art-routes'),
            'noItems' => __('No items found.', 'wp-art-routes'),
            'noEditionSelected' => __('Select an edition to manage its content.', 'wp-art-routes'),
            'confirmDeleteSingle' => __('Are you sure you want to delete this item? This cannot be undone.', 'wp-art-routes'),
            'confirmDelete' => __('Are you sure you want to delete the selected items? This cannot be undone.', 'wp-art-routes'),
            'saving' => __('Saving...', 'wp-art-routes'),
            'saved' => __('Saved', 'wp-art-routes'),
            'error' => __('Error saving. Please try again.', 'wp-art-routes'),
            'noItemsSelected' => __('Please select at least one item.', 'wp-art-routes'),
            'publish' => __('Publish', 'wp-art-routes'),
            'draft' => __('Draft', 'wp-art-routes'),
            'edit' => __('Edit', 'wp-art-routes'),
            'view' => __('View', 'wp-art-routes'),
            'delete' => __('Delete', 'wp-art-routes'),
            'routes' => __('Routes', 'wp-art-routes'),
            'locations' => __('Locations', 'wp-art-routes'),
            'infoPoints' => __('Info Points', 'wp-art-routes'),
            'published' => __('published', 'wp-art-routes'),
            'drafts' => __('drafts', 'wp-art-routes'),
            'toDraft' => __('→ Draft', 'wp-art-routes'),
            'toPublish' => __('→ Publish', 'wp-art-routes'),
            'setToDraft' => __('Set to Draft', 'wp-art-routes'),
        ),
    ));
}
add_action('admin_enqueue_scripts', 'wp_art_routes_enqueue_dashboard_assets');

/**
 * Render the Edition Dashboard admin page
 */
function wp_art_routes_render_dashboard_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'wp-art-routes'));
    }

    // Get all editions for dropdown
    $editions = wp_art_routes_get_editions();

    // Get selected edition from URL parameter
    $selected_edition_id = isset($_GET['edition_id']) ? absint($_GET['edition_id']) : 0;

    ?>
    <div class="wrap" id="edition-dashboard">
        <h1><?php _e('Edition Dashboard', 'wp-art-routes'); ?></h1>

        <div class="edition-selector-wrap">
            <label for="edition-select"><?php _e('Select Edition:', 'wp-art-routes'); ?></label>
            <select id="edition-select">
                <option value=""><?php _e('-- Select an edition --', 'wp-art-routes'); ?></option>
                <?php foreach ($editions as $edition) : ?>
                    <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($selected_edition_id, $edition->ID); ?>>
                        <?php echo esc_html($edition->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a id="view-frontend-link" href="#" class="button" style="display: none;" target="_blank">
                <?php _e('View Frontend', 'wp-art-routes'); ?>
            </a>
        </div>

        <div id="dashboard-content" style="display: none;">
            <!-- Map container -->
            <div id="dashboard-map-container" class="dashboard-section">
                <div id="dashboard-map" style="height: 300px; margin-bottom: 20px;"></div>
                <div id="map-legend" class="map-legend">
                    <span class="legend-item"><span class="legend-marker route"></span> <?php _e('Routes', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker location"></span> <?php _e('Locations', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker info-point"></span> <?php _e('Info Points', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker draft"></span> <?php _e('Draft (faded)', 'wp-art-routes'); ?></span>
                </div>
            </div>

            <!-- Routes section -->
            <div id="routes-section" class="dashboard-section collapsible">
                <h2 class="section-header">
                    <span class="toggle-icon">▼</span>
                    <span class="section-title"><?php _e('Routes', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php _e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php _e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php _e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php _e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php _e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php _e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php _e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php _e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-title"><?php _e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php _e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php _e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="routes-table-body">
                            <tr class="no-items"><td colspan="4"><?php _e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Locations section -->
            <div id="locations-section" class="dashboard-section collapsible collapsed">
                <h2 class="section-header">
                    <span class="toggle-icon">▶</span>
                    <span class="section-title"><?php _e('Locations', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content" style="display: none;">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php _e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php _e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php _e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php _e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php _e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php _e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php _e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php _e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-number"><?php _e('#', 'wp-art-routes'); ?></th>
                                <th class="column-title"><?php _e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php _e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-lat"><?php _e('Lat', 'wp-art-routes'); ?></th>
                                <th class="column-lng"><?php _e('Lng', 'wp-art-routes'); ?></th>
                                <th class="column-icon"><?php _e('Icon', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php _e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="locations-table-body">
                            <tr class="no-items"><td colspan="8"><?php _e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Info Points section -->
            <div id="info-points-section" class="dashboard-section collapsible collapsed">
                <h2 class="section-header">
                    <span class="toggle-icon">▶</span>
                    <span class="section-title"><?php _e('Info Points', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content" style="display: none;">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php _e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php _e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php _e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php _e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php _e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php _e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php _e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php _e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-title"><?php _e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php _e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-lat"><?php _e('Lat', 'wp-art-routes'); ?></th>
                                <th class="column-lng"><?php _e('Lng', 'wp-art-routes'); ?></th>
                                <th class="column-icon"><?php _e('Icon', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php _e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="info-points-table-body">
                            <tr class="no-items"><td colspan="7"><?php _e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="no-edition-message" <?php echo $selected_edition_id ? 'style="display: none;"' : ''; ?>>
            <p><?php _e('Select an edition to manage its content.', 'wp-art-routes'); ?></p>
        </div>
    </div>
    <?php
}

/**
 * AJAX handler for fetching edition data (routes, locations, info points)
 */
function wp_art_routes_dashboard_get_items()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('You do not have permission to access this data.', 'wp-art-routes')]);
    }

    // Get and validate edition ID
    $edition_id = isset($_POST['edition_id']) ? absint($_POST['edition_id']) : 0;

    if (!$edition_id) {
        wp_send_json_error(['message' => __('No edition selected.', 'wp-art-routes')]);
    }

    // Verify edition exists and is correct post type
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        wp_send_json_error(['message' => __('Invalid edition.', 'wp-art-routes')]);
    }

    // Get icons directory info
    $icons_dir = plugin_dir_path(dirname(__FILE__)) . 'assets/icons/';
    $icons_url = plugins_url('assets/icons/', dirname(__FILE__));

    // Get available icons
    $available_icons = [];
    if (is_dir($icons_dir)) {
        $files = scandir($icons_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $icon_name = pathinfo($file, PATHINFO_FILENAME);
                $display_name = str_replace(['WB plattegrond-', '-'], ['', ' '], $icon_name);
                $display_name = ucwords(trim($display_name));
                $available_icons[] = [
                    'filename' => $file,
                    'display_name' => $display_name,
                    'url' => $icons_url . $file,
                ];
            }
        }
        // Sort by display name
        usort($available_icons, function ($a, $b) {
            return strcmp($a['display_name'], $b['display_name']);
        });
    }

    // Query routes for this edition
    $routes_query = new WP_Query([
        'post_type' => 'art_route',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $routes = [];
    foreach ($routes_query->posts as $route) {
        $route_path = wp_art_routes_get_route_path($route->ID);
        $routes[] = [
            'id' => $route->ID,
            'title' => $route->post_title,
            'status' => $route->post_status,
            'edit_url' => get_edit_post_link($route->ID, 'raw'),
            'route_path' => $route_path,
        ];
    }

    // Query locations (artworks) for this edition
    $locations_query = new WP_Query([
        'post_type' => 'artwork',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    // Get edition default icon for fallback
    $edition_default_icon = get_post_meta($edition_id, '_edition_default_location_icon', true);
    $global_default_icon = get_option('wp_art_routes_default_location_icon', '');

    $locations = [];
    foreach ($locations_query->posts as $location) {
        $icon = get_post_meta($location->ID, '_artwork_icon', true);

        // Build icon URL with fallback chain: location icon → edition default → global default
        $icon_url = '';
        if (!empty($icon)) {
            $icon_url = $icons_url . rawurlencode($icon);
        } elseif (!empty($edition_default_icon)) {
            $icon_url = $icons_url . rawurlencode($edition_default_icon);
        } elseif (!empty($global_default_icon)) {
            $icon_url = $icons_url . rawurlencode($global_default_icon);
        }

        $locations[] = [
            'id' => $location->ID,
            'title' => $location->post_title,
            'status' => $location->post_status,
            'number' => get_post_meta($location->ID, '_artwork_number', true),
            'latitude' => get_post_meta($location->ID, '_artwork_latitude', true),
            'longitude' => get_post_meta($location->ID, '_artwork_longitude', true),
            'icon' => $icon,
            'icon_url' => $icon_url,
            'edit_url' => get_edit_post_link($location->ID, 'raw'),
        ];
    }

    // Query info points for this edition
    $info_points_query = new WP_Query([
        'post_type' => 'information_point',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $info_points = [];
    foreach ($info_points_query->posts as $info_point) {
        $icon = get_post_meta($info_point->ID, '_info_point_icon', true);
        $info_points[] = [
            'id' => $info_point->ID,
            'title' => $info_point->post_title,
            'status' => $info_point->post_status,
            'latitude' => get_post_meta($info_point->ID, '_artwork_latitude', true),
            'longitude' => get_post_meta($info_point->ID, '_artwork_longitude', true),
            'icon' => $icon,
            'icon_url' => $icon ? $icons_url . rawurlencode($icon) : '',
            'edit_url' => get_edit_post_link($info_point->ID, 'raw'),
        ];
    }

    // Build response
    wp_send_json_success([
        'edition' => [
            'id' => $edition->ID,
            'title' => $edition->post_title,
            'status' => $edition->post_status,
            'permalink' => get_permalink($edition->ID),
            'edit_url' => get_edit_post_link($edition->ID, 'raw'),
        ],
        'routes' => $routes,
        'locations' => $locations,
        'info_points' => $info_points,
        'available_icons' => $available_icons,
    ]);
}
add_action('wp_ajax_wp_art_routes_dashboard_get_items', 'wp_art_routes_dashboard_get_items');

/**
 * AJAX handler for updating a single item field
 */
function wp_art_routes_dashboard_update_item()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('You do not have permission to update this item.', 'wp-art-routes')]);
    }

    // Get parameters
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $field = isset($_POST['field']) ? sanitize_key($_POST['field']) : '';
    $value = isset($_POST['value']) ? $_POST['value'] : '';

    // Validate post ID
    if (!$post_id) {
        wp_send_json_error(['message' => __('Invalid post ID.', 'wp-art-routes')]);
    }

    // Verify post exists and is one of our allowed post types
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(['message' => __('Post not found.', 'wp-art-routes')]);
    }

    $allowed_post_types = ['art_route', 'artwork', 'information_point'];
    if (!in_array($post->post_type, $allowed_post_types, true)) {
        wp_send_json_error(['message' => __('Invalid post type.', 'wp-art-routes')]);
    }

    // Validate field
    if (empty($field)) {
        wp_send_json_error(['message' => __('Invalid field.', 'wp-art-routes')]);
    }

    // Handle each field type
    $response_data = [];

    switch ($field) {
        case 'title':
            $sanitized_value = sanitize_text_field($value);
            $result = wp_update_post([
                'ID' => $post_id,
                'post_title' => $sanitized_value,
            ], true);

            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }
            $response_data['title'] = $sanitized_value;
            break;

        case 'status':
            // Only allow publish or draft
            if (!in_array($value, ['publish', 'draft'], true)) {
                wp_send_json_error(['message' => __('Invalid status value. Must be "publish" or "draft".', 'wp-art-routes')]);
            }

            $result = wp_update_post([
                'ID' => $post_id,
                'post_status' => $value,
            ], true);

            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }
            $response_data['status'] = $value;
            break;

        case 'number':
            $sanitized_value = sanitize_text_field($value);
            update_post_meta($post_id, '_artwork_number', $sanitized_value);
            $response_data['number'] = $sanitized_value;
            break;

        case 'latitude':
            $float_value = floatval($value);
            // Validate latitude range (-90 to 90)
            if ($float_value < -90 || $float_value > 90) {
                wp_send_json_error(['message' => __('Latitude must be between -90 and 90.', 'wp-art-routes')]);
            }
            update_post_meta($post_id, '_artwork_latitude', $float_value);
            $response_data['latitude'] = $float_value;
            break;

        case 'longitude':
            $float_value = floatval($value);
            // Validate longitude range (-180 to 180)
            if ($float_value < -180 || $float_value > 180) {
                wp_send_json_error(['message' => __('Longitude must be between -180 and 180.', 'wp-art-routes')]);
            }
            update_post_meta($post_id, '_artwork_longitude', $float_value);
            $response_data['longitude'] = $float_value;
            break;

        case 'icon':
            // Validate icon filename exists in available icons (don't use sanitize_file_name
            // as it converts spaces to dashes which breaks filenames like "WB plattegrond-10.svg")
            $sanitized_value = '';
            if (!empty($value)) {
                $available_icons = wp_art_routes_get_available_icons();
                if (in_array($value, $available_icons, true)) {
                    $sanitized_value = $value;
                }
            }

            // Use different meta key based on post type
            $meta_key = ($post->post_type === 'information_point') ? '_info_point_icon' : '_artwork_icon';
            update_post_meta($post_id, $meta_key, $sanitized_value);

            // Return icon data in response
            $icons_url = plugins_url('assets/icons/', dirname(__FILE__));
            $response_data['icon'] = $sanitized_value;
            $response_data['icon_url'] = $sanitized_value ? $icons_url . rawurlencode($sanitized_value) : '';
            $response_data['icon_display_name'] = $sanitized_value ? wp_art_routes_get_icon_display_name($sanitized_value) : '';
            break;

        default:
            wp_send_json_error(['message' => __('Unknown field type.', 'wp-art-routes')]);
    }

    wp_send_json_success($response_data);
}
add_action('wp_ajax_wp_art_routes_dashboard_update_item', 'wp_art_routes_dashboard_update_item');

/**
 * AJAX handler for bulk actions on dashboard items
 */
function wp_art_routes_dashboard_bulk_action()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('You do not have permission to perform this action.', 'wp-art-routes')]);
    }

    // Get parameters
    $bulk_action = isset($_POST['bulk_action']) ? sanitize_key($_POST['bulk_action']) : '';
    $post_ids = isset($_POST['post_ids']) ? array_map('absint', (array) $_POST['post_ids']) : [];

    // Validate action
    $allowed_actions = ['publish', 'draft', 'delete'];
    if (!in_array($bulk_action, $allowed_actions, true)) {
        wp_send_json_error(['message' => __('Invalid bulk action.', 'wp-art-routes')]);
    }

    // Validate post IDs
    if (empty($post_ids)) {
        wp_send_json_error(['message' => __('No items selected.', 'wp-art-routes')]);
    }

    // Allowed post types for bulk operations
    $allowed_post_types = ['art_route', 'artwork', 'information_point'];

    // Track results
    $success_count = 0;
    $error_count = 0;

    // Process each post
    foreach ($post_ids as $post_id) {
        // Verify post exists
        $post = get_post($post_id);
        if (!$post) {
            $error_count++;
            continue;
        }

        // Verify post type is allowed
        if (!in_array($post->post_type, $allowed_post_types, true)) {
            $error_count++;
            continue;
        }

        // Perform the action
        switch ($bulk_action) {
            case 'publish':
            case 'draft':
                $result = wp_update_post([
                    'ID' => $post_id,
                    'post_status' => $bulk_action,
                ], true);

                if (is_wp_error($result)) {
                    $error_count++;
                } else {
                    $success_count++;
                }
                break;

            case 'delete':
                // Force delete (skip trash)
                $result = wp_delete_post($post_id, true);

                if ($result === false || $result === null) {
                    $error_count++;
                } else {
                    $success_count++;
                }
                break;
        }
    }

    // Build response message
    $message = '';
    if ($success_count > 0) {
        switch ($bulk_action) {
            case 'publish':
                $message = sprintf(
                    /* translators: %d: number of items */
                    _n('%d item published.', '%d items published.', $success_count, 'wp-art-routes'),
                    $success_count
                );
                break;
            case 'draft':
                $message = sprintf(
                    /* translators: %d: number of items */
                    _n('%d item set to draft.', '%d items set to draft.', $success_count, 'wp-art-routes'),
                    $success_count
                );
                break;
            case 'delete':
                $message = sprintf(
                    /* translators: %d: number of items */
                    _n('%d item deleted.', '%d items deleted.', $success_count, 'wp-art-routes'),
                    $success_count
                );
                break;
        }
    }

    if ($error_count > 0) {
        $error_message = sprintf(
            /* translators: %d: number of items */
            _n('%d item could not be processed.', '%d items could not be processed.', $error_count, 'wp-art-routes'),
            $error_count
        );
        $message = $message ? $message . ' ' . $error_message : $error_message;
    }

    wp_send_json_success([
        'message' => $message,
        'success_count' => $success_count,
        'error_count' => $error_count,
    ]);
}
add_action('wp_ajax_wp_art_routes_dashboard_bulk_action', 'wp_art_routes_dashboard_bulk_action');
