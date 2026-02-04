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

    // Enqueue Leaflet CSS (bundled locally for WordPress.org compliance)
    wp_enqueue_style(
        'leaflet',
        plugins_url('assets/lib/leaflet/leaflet.css', dirname(__FILE__)),
        array(),
        '1.9.4'
    );

    // Enqueue Leaflet JS (bundled locally for WordPress.org compliance)
    wp_enqueue_script(
        'leaflet',
        plugins_url('assets/lib/leaflet/leaflet.js', dirname(__FILE__)),
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
            'saved' => __('Saved!', 'wp-art-routes'),
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
            'useGlobalDefault' => __('Use global default', 'wp-art-routes'),
            'settingsSaved' => __('Settings saved successfully.', 'wp-art-routes'),
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
    $selected_edition_id = isset($_GET['edition_id']) ? absint(wp_unslash($_GET['edition_id'])) : 0;

    ?>
    <div class="wrap" id="edition-dashboard">
        <h1><?php esc_html_e('Edition Dashboard', 'wp-art-routes'); ?></h1>

        <div class="edition-selector-wrap">
            <label for="edition-select"><?php esc_html_e('Select Edition:', 'wp-art-routes'); ?></label>
            <select id="edition-select">
                <option value=""><?php esc_html_e('-- Select an edition --', 'wp-art-routes'); ?></option>
                <?php foreach ($editions as $edition) : ?>
                    <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($selected_edition_id, $edition->ID); ?>>
                        <?php echo esc_html($edition->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a id="view-frontend-link" href="#" class="button" style="display: none;" target="_blank">
                <?php esc_html_e('View Frontend', 'wp-art-routes'); ?>
            </a>
        </div>

        <div id="dashboard-content" style="display: none;">
            <!-- Map container -->
            <div id="dashboard-map-container" class="dashboard-section">
                <div id="dashboard-map" style="height: 300px; margin-bottom: 20px;"></div>
                <div id="map-legend" class="map-legend">
                    <span class="legend-item"><span class="legend-marker route"></span> <?php esc_html_e('Routes', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker location"></span> <?php esc_html_e('Locations', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker info-point"></span> <?php esc_html_e('Info Points', 'wp-art-routes'); ?></span>
                    <span class="legend-item"><span class="legend-marker draft"></span> <?php esc_html_e('Draft (faded)', 'wp-art-routes'); ?></span>
                </div>
            </div>

            <!-- Routes section -->
            <div id="routes-section" class="dashboard-section collapsible">
                <h2 class="section-header">
                    <span class="toggle-icon">▼</span>
                    <span class="section-title"><?php esc_html_e('Routes', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php esc_html_e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php esc_html_e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php esc_html_e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php esc_html_e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php esc_html_e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php esc_html_e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php esc_html_e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php esc_html_e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-title"><?php esc_html_e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php esc_html_e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php esc_html_e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="routes-table-body">
                            <tr class="no-items"><td colspan="4"><?php esc_html_e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Locations section -->
            <div id="locations-section" class="dashboard-section collapsible collapsed">
                <h2 class="section-header">
                    <span class="toggle-icon">▶</span>
                    <span class="section-title"><?php esc_html_e('Locations', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content" style="display: none;">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php esc_html_e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php esc_html_e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php esc_html_e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php esc_html_e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php esc_html_e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php esc_html_e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php esc_html_e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php esc_html_e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-number"><?php esc_html_e('#', 'wp-art-routes'); ?></th>
                                <th class="column-title"><?php esc_html_e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php esc_html_e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-lat"><?php esc_html_e('Lat', 'wp-art-routes'); ?></th>
                                <th class="column-lng"><?php esc_html_e('Lng', 'wp-art-routes'); ?></th>
                                <th class="column-icon"><?php esc_html_e('Icon', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php esc_html_e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="locations-table-body">
                            <tr class="no-items"><td colspan="8"><?php esc_html_e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Info Points section -->
            <div id="info-points-section" class="dashboard-section collapsible collapsed">
                <h2 class="section-header">
                    <span class="toggle-icon">▶</span>
                    <span class="section-title"><?php esc_html_e('Info Points', 'wp-art-routes'); ?></span>
                    <span class="section-counts"></span>
                </h2>
                <div class="section-content" style="display: none;">
                    <div class="bulk-actions">
                        <select class="bulk-action-select">
                            <option value=""><?php esc_html_e('Bulk Actions', 'wp-art-routes'); ?></option>
                            <option value="publish"><?php esc_html_e('Publish', 'wp-art-routes'); ?></option>
                            <option value="draft"><?php esc_html_e('Set to Draft', 'wp-art-routes'); ?></option>
                            <option value="delete"><?php esc_html_e('Delete', 'wp-art-routes'); ?></option>
                        </select>
                        <button type="button" class="button bulk-apply"><?php esc_html_e('Apply', 'wp-art-routes'); ?></button>
                        <span class="selection-buttons">
                            <button type="button" class="button select-all"><?php esc_html_e('Select All', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-none"><?php esc_html_e('Select None', 'wp-art-routes'); ?></button>
                            <button type="button" class="button select-drafts"><?php esc_html_e('Select Drafts', 'wp-art-routes'); ?></button>
                        </span>
                    </div>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox" class="select-all-checkbox" /></th>
                                <th class="column-title"><?php esc_html_e('Title', 'wp-art-routes'); ?></th>
                                <th class="column-status"><?php esc_html_e('Status', 'wp-art-routes'); ?></th>
                                <th class="column-lat"><?php esc_html_e('Lat', 'wp-art-routes'); ?></th>
                                <th class="column-lng"><?php esc_html_e('Lng', 'wp-art-routes'); ?></th>
                                <th class="column-icon"><?php esc_html_e('Icon', 'wp-art-routes'); ?></th>
                                <th class="column-actions"><?php esc_html_e('Actions', 'wp-art-routes'); ?></th>
                            </tr>
                        </thead>
                        <tbody id="info-points-table-body">
                            <tr class="no-items"><td colspan="7"><?php esc_html_e('Loading...', 'wp-art-routes'); ?></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Edition Settings section -->
            <div id="edition-settings-section" class="dashboard-section collapsible collapsed">
                <h2 class="section-header">
                    <span class="toggle-icon">▶</span>
                    <span class="section-title"><?php esc_html_e('Edition Settings', 'wp-art-routes'); ?></span>
                </h2>
                <div class="section-content" style="display: none;">
                    <form id="edition-settings-form">
                        <!-- Event Dates -->
                        <div class="settings-group">
                            <h3><?php esc_html_e('Event Dates', 'wp-art-routes'); ?></h3>
                            <p class="description"><?php esc_html_e('Optional: Set the event dates for this edition.', 'wp-art-routes'); ?></p>
                            <table class="form-table">
                                <tr>
                                    <th><label for="edition_start_date"><?php esc_html_e('Start Date:', 'wp-art-routes'); ?></label></th>
                                    <td><input type="date" id="edition_start_date" name="start_date" class="regular-text" /></td>
                                </tr>
                                <tr>
                                    <th><label for="edition_end_date"><?php esc_html_e('End Date:', 'wp-art-routes'); ?></label></th>
                                    <td><input type="date" id="edition_end_date" name="end_date" class="regular-text" /></td>
                                </tr>
                            </table>
                        </div>

                        <!-- Default Location Icon -->
                        <div class="settings-group">
                            <h3><?php esc_html_e('Default Location Icon', 'wp-art-routes'); ?></h3>
                            <p class="description"><?php esc_html_e('Default icon for locations in this edition that do not have an icon assigned.', 'wp-art-routes'); ?></p>
                            <table class="form-table">
                                <tr>
                                    <th><label for="edition_default_icon"><?php esc_html_e('Icon:', 'wp-art-routes'); ?></label></th>
                                    <td>
                                        <select id="edition_default_icon" name="default_location_icon" style="max-width: 300px;">
                                            <option value=""><?php esc_html_e('Use global default', 'wp-art-routes'); ?></option>
                                            <!-- Options populated via JavaScript -->
                                        </select>
                                        <span id="edition_default_icon_preview" style="margin-left: 10px; vertical-align: middle;"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Terminology Overrides -->
                        <div class="settings-group">
                            <h3><?php esc_html_e('Terminology Overrides', 'wp-art-routes'); ?></h3>
                            <p class="description"><?php esc_html_e('Override the global terminology labels for this edition. Leave empty to use the global settings (shown as placeholders).', 'wp-art-routes'); ?></p>
                            <table class="form-table terminology-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Type', 'wp-art-routes'); ?></th>
                                        <th><?php esc_html_e('Singular', 'wp-art-routes'); ?></th>
                                        <th><?php esc_html_e('Plural', 'wp-art-routes'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Route', 'wp-art-routes'); ?></strong>
                                            <p class="description"><?php esc_html_e('The main paths users follow', 'wp-art-routes'); ?></p>
                                        </td>
                                        <td><input type="text" name="terminology[route][singular]" id="term_route_singular" class="regular-text" /></td>
                                        <td><input type="text" name="terminology[route][plural]" id="term_route_plural" class="regular-text" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Location', 'wp-art-routes'); ?></strong>
                                            <p class="description"><?php esc_html_e('Main content items (artworks, performances, etc.)', 'wp-art-routes'); ?></p>
                                        </td>
                                        <td><input type="text" name="terminology[location][singular]" id="term_location_singular" class="regular-text" /></td>
                                        <td><input type="text" name="terminology[location][plural]" id="term_location_plural" class="regular-text" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Info Point', 'wp-art-routes'); ?></strong>
                                            <p class="description"><?php esc_html_e('Information markers along routes', 'wp-art-routes'); ?></p>
                                        </td>
                                        <td><input type="text" name="terminology[info_point][singular]" id="term_info_point_singular" class="regular-text" /></td>
                                        <td><input type="text" name="terminology[info_point][plural]" id="term_info_point_plural" class="regular-text" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e('Creator', 'wp-art-routes'); ?></strong>
                                            <p class="description"><?php esc_html_e('People/entities associated with locations', 'wp-art-routes'); ?></p>
                                        </td>
                                        <td><input type="text" name="terminology[creator][singular]" id="term_creator_singular" class="regular-text" /></td>
                                        <td><input type="text" name="terminology[creator][plural]" id="term_creator_plural" class="regular-text" /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="submit">
                            <button type="submit" class="button button-primary" id="save-edition-settings">
                                <?php esc_html_e('Save Settings', 'wp-art-routes'); ?>
                            </button>
                            <span id="settings-save-status" style="margin-left: 10px;"></span>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <div id="no-edition-message" <?php echo $selected_edition_id ? 'style="display: none;"' : ''; ?>>
            <p><?php esc_html_e('Select an edition to manage its content.', 'wp-art-routes'); ?></p>
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
    $edition_id = isset($_POST['edition_id']) ? absint(wp_unslash($_POST['edition_id'])) : 0;

    if (!$edition_id) {
        wp_send_json_error(['message' => __('No edition selected.', 'wp-art-routes')]);
    }

    // Verify edition exists and is correct post type
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        wp_send_json_error(['message' => __('Invalid edition.', 'wp-art-routes')]);
    }

    // Get available icons (includes both built-in and custom uploaded icons)
    $icon_files = wp_art_routes_get_available_icons();
    $available_icons = [];
    foreach ($icon_files as $file) {
        $available_icons[] = [
            'filename' => $file,
            'display_name' => wp_art_routes_get_icon_display_name($file),
            'url' => wp_art_routes_get_icon_url($file),
        ];
    }
    // Sort by display name
    usort($available_icons, function ($a, $b) {
        return strcmp($a['display_name'], $b['display_name']);
    });

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
            $icon_url = wp_art_routes_get_icon_url($icon);
        } elseif (!empty($edition_default_icon)) {
            $icon_url = wp_art_routes_get_icon_url($edition_default_icon);
        } elseif (!empty($global_default_icon)) {
            $icon_url = wp_art_routes_get_icon_url($global_default_icon);
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
            'icon_url' => $icon ? wp_art_routes_get_icon_url($icon) : '',
            'edit_url' => get_edit_post_link($info_point->ID, 'raw'),
        ];
    }

    // Get edition settings
    $terminology = get_post_meta($edition_id, '_edition_terminology', true);
    if (!is_array($terminology)) {
        $terminology = [];
    }
    $start_date = get_post_meta($edition_id, '_edition_start_date', true);
    $end_date = get_post_meta($edition_id, '_edition_end_date', true);
    $default_location_icon = get_post_meta($edition_id, '_edition_default_location_icon', true);

    // Get global terminology for placeholders
    $global_terminology = wp_art_routes_get_global_terminology();

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
        'settings' => [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'default_location_icon' => $default_location_icon,
            'terminology' => $terminology,
            'global_terminology' => $global_terminology,
        ],
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
    $post_id = isset($_POST['post_id']) ? absint(wp_unslash($_POST['post_id'])) : 0;
    $field = isset($_POST['field']) ? sanitize_key(wp_unslash($_POST['field'])) : '';
    $value = isset($_POST['value']) ? wp_unslash($_POST['value']) : '';

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
            // Validate icon filename exists in available icons
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
            $response_data['icon'] = $sanitized_value;
            $response_data['icon_url'] = $sanitized_value ? wp_art_routes_get_icon_url($sanitized_value) : '';
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
    $bulk_action = isset($_POST['bulk_action']) ? sanitize_key(wp_unslash($_POST['bulk_action'])) : '';
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Array values are sanitized via absint.
    $post_ids = isset($_POST['post_ids']) ? array_map('absint', wp_unslash((array) $_POST['post_ids'])) : [];

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

/**
 * AJAX handler for saving edition settings
 */
function wp_art_routes_dashboard_save_settings()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check capabilities
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('You do not have permission to save settings.', 'wp-art-routes')]);
    }

    // Get and validate edition ID
    $edition_id = isset($_POST['edition_id']) ? absint(wp_unslash($_POST['edition_id'])) : 0;

    if (!$edition_id) {
        wp_send_json_error(['message' => __('No edition selected.', 'wp-art-routes')]);
    }

    // Verify edition exists and is correct post type
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        wp_send_json_error(['message' => __('Invalid edition.', 'wp-art-routes')]);
    }

    // Save event dates
    $start_date = isset($_POST['start_date']) ? sanitize_text_field(wp_unslash($_POST['start_date'])) : '';
    $end_date = isset($_POST['end_date']) ? sanitize_text_field(wp_unslash($_POST['end_date'])) : '';

    update_post_meta($edition_id, '_edition_start_date', $start_date);
    update_post_meta($edition_id, '_edition_end_date', $end_date);

    // Save default location icon
    $default_icon = isset($_POST['default_location_icon']) ? sanitize_text_field(wp_unslash($_POST['default_location_icon'])) : '';

    // Validate icon exists if provided
    if (!empty($default_icon)) {
        $available_icons = wp_art_routes_get_available_icons();
        if (!in_array($default_icon, $available_icons, true)) {
            $default_icon = ''; // Reset if invalid
        }
    }
    update_post_meta($edition_id, '_edition_default_location_icon', $default_icon);

    // Save terminology overrides
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Each field is sanitized individually below
    $terminology = isset($_POST['terminology']) ? wp_unslash($_POST['terminology']) : [];
    $sanitized_terminology = [];

    $allowed_types = ['route', 'location', 'info_point', 'creator'];
    $allowed_fields = ['singular', 'plural'];

    foreach ($allowed_types as $type) {
        if (isset($terminology[$type]) && is_array($terminology[$type])) {
            $sanitized_terminology[$type] = [];
            foreach ($allowed_fields as $field) {
                if (isset($terminology[$type][$field])) {
                    $sanitized_terminology[$type][$field] = sanitize_text_field($terminology[$type][$field]);
                }
            }
        }
    }

    update_post_meta($edition_id, '_edition_terminology', $sanitized_terminology);

    wp_send_json_success([
        'message' => __('Settings saved successfully.', 'wp-art-routes'),
    ]);
}
add_action('wp_ajax_wp_art_routes_dashboard_save_settings', 'wp_art_routes_dashboard_save_settings');
