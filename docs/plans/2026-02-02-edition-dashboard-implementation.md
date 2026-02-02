# Edition Dashboard Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a comprehensive admin dashboard for managing all routes, locations, and info points within an edition from a single page.

**Architecture:** PHP admin page with vanilla JavaScript for interactivity. Server renders the shell and edition selector; JavaScript fetches data via AJAX and renders tables/map. All edits save immediately via AJAX with visual feedback.

**Tech Stack:** PHP (WordPress admin page, AJAX handlers), vanilla JavaScript, Leaflet.js (already loaded by plugin), WordPress admin CSS patterns.

---

## Task 1: Register Admin Page and Create PHP Shell

**Files:**
- Create: `includes/edition-dashboard.php`
- Modify: `wp-art-routes.php:55` (add require statement)

**Step 1: Create the dashboard PHP file with page registration**

Create `includes/edition-dashboard.php`:

```php
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
```

**Step 2: Add require statement to main plugin file**

In `wp-art-routes.php`, add after line 55 (after import-export.php require):

```php
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/edition-dashboard.php';
```

**Step 3: Verify page loads**

1. Go to WordPress admin
2. Navigate to Art Routes menu
3. Verify "Dashboard" submenu appears between "Editions" and "Routes"
4. Click it and verify the page loads with edition selector

**Step 4: Commit**

```bash
git add includes/edition-dashboard.php wp-art-routes.php
git commit -m "feat(dashboard): Add Edition Dashboard admin page shell"
```

---

## Task 2: Add CSS Styles for Dashboard

**Files:**
- Create: `assets/css/edition-dashboard.css`
- Modify: `includes/edition-dashboard.php` (add enqueue)

**Step 1: Create the CSS file**

Create `assets/css/edition-dashboard.css`:

```css
/**
 * Edition Dashboard Styles
 */

#edition-dashboard {
    max-width: 1400px;
}

/* Edition selector */
.edition-selector-wrap {
    margin: 20px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.edition-selector-wrap select {
    min-width: 300px;
    padding: 5px 10px;
}

/* Dashboard sections */
.dashboard-section {
    margin-bottom: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.dashboard-section .section-header {
    margin: 0;
    padding: 12px 15px;
    font-size: 14px;
    font-weight: 600;
    background: #f6f7f7;
    border-bottom: 1px solid #c3c4c7;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-section .section-header:hover {
    background: #f0f0f1;
}

.dashboard-section .toggle-icon {
    font-size: 12px;
    width: 16px;
}

.dashboard-section .section-counts {
    color: #646970;
    font-weight: normal;
    margin-left: auto;
}

.dashboard-section .section-content {
    padding: 0;
}

.dashboard-section.collapsed .section-content {
    display: none;
}

/* Bulk actions */
.bulk-actions {
    padding: 10px 15px;
    background: #f6f7f7;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    align-items: center;
    gap: 5px;
    flex-wrap: wrap;
}

.bulk-actions .selection-buttons {
    margin-left: auto;
}

.bulk-actions .selection-buttons .button {
    margin-left: 5px;
}

/* Map styles */
#dashboard-map-container {
    margin-bottom: 20px;
}

#dashboard-map {
    border: 1px solid #c3c4c7;
    border-radius: 4px;
}

.map-legend {
    display: flex;
    gap: 20px;
    padding: 10px 0;
    font-size: 13px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-marker {
    display: inline-block;
    width: 16px;
    height: 16px;
    border-radius: 50%;
}

.legend-marker.route {
    background: #3388ff;
    border-radius: 2px;
    height: 4px;
    width: 20px;
}

.legend-marker.location {
    background: #2ecc71;
}

.legend-marker.info-point {
    background: #e67e22;
}

.legend-marker.draft {
    background: #999;
    opacity: 0.5;
}

/* Table styles */
.dashboard-section table {
    border: none;
    margin: 0;
}

.dashboard-section table th,
.dashboard-section table td {
    padding: 10px 12px;
}

.column-check,
.check-column {
    width: 30px;
    padding: 10px 8px !important;
}

.column-number {
    width: 60px;
}

.column-status {
    width: 100px;
}

.column-lat,
.column-lng {
    width: 100px;
}

.column-icon {
    width: 80px;
}

.column-actions {
    width: 100px;
}

/* Status badge */
.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.status-badge.publish {
    background: #d4edda;
    color: #155724;
}

.status-badge.draft {
    background: #fff3cd;
    color: #856404;
}

.status-badge:hover {
    opacity: 0.8;
}

/* Inline editing */
.editable-cell {
    cursor: pointer;
    padding: 4px 6px;
    border-radius: 3px;
    transition: background-color 0.2s;
}

.editable-cell:hover {
    background: #f0f0f1;
}

.editable-cell.editing {
    padding: 0;
}

.editable-cell input {
    width: 100%;
    padding: 4px 6px;
    border: 2px solid #2271b1;
    border-radius: 3px;
    outline: none;
}

.editable-cell.saving {
    opacity: 0.6;
}

.editable-cell.saved {
    animation: saved-flash 0.5s ease;
}

.editable-cell.error {
    background: #fcebea;
}

@keyframes saved-flash {
    0% { background: #d4edda; }
    100% { background: transparent; }
}

/* Icon preview in table */
.icon-preview {
    width: 24px;
    height: 24px;
    object-fit: contain;
    vertical-align: middle;
}

.icon-cell {
    cursor: pointer;
}

.icon-cell:hover {
    opacity: 0.7;
}

/* Action buttons */
.row-actions {
    display: flex;
    gap: 10px;
}

.row-actions a {
    color: #2271b1;
    text-decoration: none;
}

.row-actions a:hover {
    color: #135e96;
}

.row-actions .delete:hover {
    color: #b32d2e;
}

/* Empty state */
.no-items td {
    text-align: center;
    padding: 20px !important;
    color: #646970;
}

/* Loading state */
.loading-row td {
    text-align: center;
    padding: 20px !important;
}

/* Responsive */
@media screen and (max-width: 782px) {
    .bulk-actions {
        flex-direction: column;
        align-items: flex-start;
    }

    .bulk-actions .selection-buttons {
        margin-left: 0;
        margin-top: 10px;
    }

    .map-legend {
        flex-wrap: wrap;
    }
}
```

**Step 2: Add enqueue function to edition-dashboard.php**

Add after the `add_action('admin_menu', ...)` line:

```php
/**
 * Enqueue dashboard assets
 */
function wp_art_routes_enqueue_dashboard_assets($hook)
{
    // Only load on our dashboard page
    if ($hook !== 'edition_page_wp-art-routes-dashboard') {
        return;
    }

    // Enqueue Leaflet CSS and JS
    wp_enqueue_style(
        'leaflet',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css',
        [],
        '1.9.4'
    );
    wp_enqueue_script(
        'leaflet',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.js',
        [],
        '1.9.4',
        true
    );

    // Enqueue dashboard CSS
    wp_enqueue_style(
        'wp-art-routes-dashboard',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/edition-dashboard.css',
        [],
        WP_ART_ROUTES_VERSION
    );

    // Enqueue dashboard JS (will be created in next task)
    wp_enqueue_script(
        'wp-art-routes-dashboard',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/edition-dashboard.js',
        ['jquery', 'leaflet'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Localize script with data
    wp_localize_script('wp-art-routes-dashboard', 'wpArtRoutesDashboard', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wp_art_routes_dashboard'),
        'strings' => [
            'confirmDelete' => __('Are you sure you want to delete the selected items? This cannot be undone.', 'wp-art-routes'),
            'confirmDeleteSingle' => __('Are you sure you want to delete this item? This cannot be undone.', 'wp-art-routes'),
            'noItemsSelected' => __('Please select at least one item.', 'wp-art-routes'),
            'saving' => __('Saving...', 'wp-art-routes'),
            'saved' => __('Saved', 'wp-art-routes'),
            'error' => __('Error saving. Please try again.', 'wp-art-routes'),
            'loading' => __('Loading...', 'wp-art-routes'),
            'noItems' => __('No items found.', 'wp-art-routes'),
            'published' => __('Published', 'wp-art-routes'),
            'drafts' => __('drafts', 'wp-art-routes'),
        ],
        'iconsUrl' => WP_ART_ROUTES_PLUGIN_URL . 'assets/icons/',
    ]);
}
add_action('admin_enqueue_scripts', 'wp_art_routes_enqueue_dashboard_assets');
```

**Step 3: Verify styles load**

1. Navigate to Dashboard page
2. Check browser DevTools for CSS loading
3. Verify section headers have background color

**Step 4: Commit**

```bash
git add assets/css/edition-dashboard.css includes/edition-dashboard.php
git commit -m "feat(dashboard): Add CSS styles and asset enqueuing"
```

---

## Task 3: Create AJAX Handler for Fetching Edition Data

**Files:**
- Modify: `includes/edition-dashboard.php` (add AJAX handlers)

**Step 1: Add the get_items AJAX handler**

Add at the end of `includes/edition-dashboard.php`:

```php
/**
 * AJAX handler: Get all items for an edition
 */
function wp_art_routes_dashboard_get_items()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    // Get edition ID
    $edition_id = isset($_POST['edition_id']) ? absint($_POST['edition_id']) : 0;
    if (!$edition_id) {
        wp_send_json_error(['message' => __('Invalid edition ID.', 'wp-art-routes')]);
    }

    // Verify edition exists
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        wp_send_json_error(['message' => __('Edition not found.', 'wp-art-routes')]);
    }

    // Get routes (including drafts)
    $routes = get_posts([
        'post_type' => 'art_route',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $routes_data = [];
    foreach ($routes as $route) {
        $route_path = wp_art_routes_get_route_path($route->ID);
        $routes_data[] = [
            'id' => $route->ID,
            'title' => $route->post_title,
            'status' => $route->post_status,
            'edit_url' => get_edit_post_link($route->ID, 'raw'),
            'route_path' => $route_path,
        ];
    }

    // Get locations (including drafts)
    $locations = get_posts([
        'post_type' => 'artwork',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $icons_url = WP_ART_ROUTES_PLUGIN_URL . 'assets/icons/';
    $locations_data = [];
    foreach ($locations as $location) {
        $icon_filename = get_post_meta($location->ID, '_artwork_icon', true);
        $locations_data[] = [
            'id' => $location->ID,
            'title' => $location->post_title,
            'status' => $location->post_status,
            'number' => get_post_meta($location->ID, '_artwork_number', true),
            'latitude' => get_post_meta($location->ID, '_artwork_latitude', true),
            'longitude' => get_post_meta($location->ID, '_artwork_longitude', true),
            'icon' => $icon_filename,
            'icon_url' => $icon_filename ? $icons_url . $icon_filename : '',
            'edit_url' => get_edit_post_link($location->ID, 'raw'),
        ];
    }

    // Get info points (including drafts)
    $info_points = get_posts([
        'post_type' => 'information_point',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $info_points_data = [];
    foreach ($info_points as $info_point) {
        $icon_filename = get_post_meta($info_point->ID, '_info_point_icon', true);
        if (empty($icon_filename)) {
            $icon_filename = 'WB plattegrond-Informatie.svg'; // Default
        }
        $info_points_data[] = [
            'id' => $info_point->ID,
            'title' => $info_point->post_title,
            'status' => $info_point->post_status,
            'latitude' => get_post_meta($info_point->ID, '_artwork_latitude', true),
            'longitude' => get_post_meta($info_point->ID, '_artwork_longitude', true),
            'icon' => $icon_filename,
            'icon_url' => $icons_url . $icon_filename,
            'edit_url' => get_edit_post_link($info_point->ID, 'raw'),
        ];
    }

    // Get available icons for dropdown
    $icons_dir = WP_ART_ROUTES_PLUGIN_DIR . 'assets/icons/';
    $available_icons = [];
    if (is_dir($icons_dir)) {
        $files = scandir($icons_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $display_name = pathinfo($file, PATHINFO_FILENAME);
                $display_name = str_replace(['WB plattegrond-', '-'], ['', ' '], $display_name);
                $display_name = ucwords(trim($display_name));
                $available_icons[] = [
                    'filename' => $file,
                    'display_name' => $display_name,
                ];
            }
        }
    }

    wp_send_json_success([
        'edition' => [
            'id' => $edition->ID,
            'title' => $edition->post_title,
            'permalink' => get_permalink($edition->ID),
        ],
        'routes' => $routes_data,
        'locations' => $locations_data,
        'info_points' => $info_points_data,
        'available_icons' => $available_icons,
    ]);
}
add_action('wp_ajax_wp_art_routes_dashboard_get_items', 'wp_art_routes_dashboard_get_items');
```

**Step 2: Test AJAX endpoint manually**

In browser console on Dashboard page:
```javascript
jQuery.post(wpArtRoutesDashboard.ajaxUrl, {
    action: 'wp_art_routes_dashboard_get_items',
    nonce: wpArtRoutesDashboard.nonce,
    edition_id: 123 // Replace with real edition ID
}, function(response) {
    console.log(response);
});
```

**Step 3: Commit**

```bash
git add includes/edition-dashboard.php
git commit -m "feat(dashboard): Add AJAX handler for fetching edition data"
```

---

## Task 4: Create AJAX Handler for Updating Items

**Files:**
- Modify: `includes/edition-dashboard.php` (add update handler)

**Step 1: Add the update_item AJAX handler**

Add after the get_items handler:

```php
/**
 * AJAX handler: Update a single item field
 */
function wp_art_routes_dashboard_update_item()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    // Get parameters
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $field = isset($_POST['field']) ? sanitize_key($_POST['field']) : '';
    $value = isset($_POST['value']) ? $_POST['value'] : '';

    if (!$post_id || !$field) {
        wp_send_json_error(['message' => __('Invalid parameters.', 'wp-art-routes')]);
    }

    // Verify post exists and is editable
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(['message' => __('Item not found.', 'wp-art-routes')]);
    }

    // Only allow our post types
    $allowed_types = ['art_route', 'artwork', 'information_point'];
    if (!in_array($post->post_type, $allowed_types, true)) {
        wp_send_json_error(['message' => __('Invalid item type.', 'wp-art-routes')]);
    }

    // Update based on field
    switch ($field) {
        case 'title':
            $value = sanitize_text_field($value);
            if (empty($value)) {
                wp_send_json_error(['message' => __('Title cannot be empty.', 'wp-art-routes')]);
            }
            $result = wp_update_post([
                'ID' => $post_id,
                'post_title' => $value,
            ]);
            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }
            break;

        case 'status':
            $value = sanitize_key($value);
            if (!in_array($value, ['publish', 'draft'], true)) {
                wp_send_json_error(['message' => __('Invalid status.', 'wp-art-routes')]);
            }
            $result = wp_update_post([
                'ID' => $post_id,
                'post_status' => $value,
            ]);
            if (is_wp_error($result)) {
                wp_send_json_error(['message' => $result->get_error_message()]);
            }
            break;

        case 'number':
            $value = sanitize_text_field($value);
            update_post_meta($post_id, '_artwork_number', $value);
            break;

        case 'latitude':
            $value = floatval($value);
            if ($value < -90 || $value > 90) {
                wp_send_json_error(['message' => __('Latitude must be between -90 and 90.', 'wp-art-routes')]);
            }
            update_post_meta($post_id, '_artwork_latitude', $value);
            break;

        case 'longitude':
            $value = floatval($value);
            if ($value < -180 || $value > 180) {
                wp_send_json_error(['message' => __('Longitude must be between -180 and 180.', 'wp-art-routes')]);
            }
            update_post_meta($post_id, '_artwork_longitude', $value);
            break;

        case 'icon':
            $value = sanitize_file_name($value);
            $meta_key = ($post->post_type === 'information_point') ? '_info_point_icon' : '_artwork_icon';
            if (empty($value)) {
                delete_post_meta($post_id, $meta_key);
            } else {
                update_post_meta($post_id, $meta_key, $value);
            }
            // Return icon URL for UI update
            $icons_url = WP_ART_ROUTES_PLUGIN_URL . 'assets/icons/';
            wp_send_json_success([
                'message' => __('Saved', 'wp-art-routes'),
                'icon_url' => $value ? $icons_url . $value : '',
            ]);
            return;

        default:
            wp_send_json_error(['message' => __('Unknown field.', 'wp-art-routes')]);
    }

    wp_send_json_success(['message' => __('Saved', 'wp-art-routes')]);
}
add_action('wp_ajax_wp_art_routes_dashboard_update_item', 'wp_art_routes_dashboard_update_item');
```

**Step 2: Test update endpoint manually**

In browser console:
```javascript
jQuery.post(wpArtRoutesDashboard.ajaxUrl, {
    action: 'wp_art_routes_dashboard_update_item',
    nonce: wpArtRoutesDashboard.nonce,
    post_id: 123, // Replace with real post ID
    field: 'status',
    value: 'publish'
}, function(response) {
    console.log(response);
});
```

**Step 3: Commit**

```bash
git add includes/edition-dashboard.php
git commit -m "feat(dashboard): Add AJAX handler for updating single item field"
```

---

## Task 5: Create AJAX Handler for Bulk Actions

**Files:**
- Modify: `includes/edition-dashboard.php` (add bulk handler)

**Step 1: Add the bulk_action AJAX handler**

Add after the update_item handler:

```php
/**
 * AJAX handler: Perform bulk action on multiple items
 */
function wp_art_routes_dashboard_bulk_action()
{
    // Verify nonce
    if (!check_ajax_referer('wp_art_routes_dashboard', 'nonce', false)) {
        wp_send_json_error(['message' => __('Security check failed.', 'wp-art-routes')]);
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permission denied.', 'wp-art-routes')]);
    }

    // Get parameters
    $action = isset($_POST['bulk_action']) ? sanitize_key($_POST['bulk_action']) : '';
    $post_ids = isset($_POST['post_ids']) ? array_map('absint', (array) $_POST['post_ids']) : [];

    if (!$action || empty($post_ids)) {
        wp_send_json_error(['message' => __('Invalid parameters.', 'wp-art-routes')]);
    }

    // Validate action
    if (!in_array($action, ['publish', 'draft', 'delete'], true)) {
        wp_send_json_error(['message' => __('Invalid action.', 'wp-art-routes')]);
    }

    // Allowed post types
    $allowed_types = ['art_route', 'artwork', 'information_point'];

    $success_count = 0;
    $error_count = 0;

    foreach ($post_ids as $post_id) {
        $post = get_post($post_id);

        // Skip if post doesn't exist or wrong type
        if (!$post || !in_array($post->post_type, $allowed_types, true)) {
            $error_count++;
            continue;
        }

        switch ($action) {
            case 'publish':
            case 'draft':
                $result = wp_update_post([
                    'ID' => $post_id,
                    'post_status' => $action,
                ]);
                if (!is_wp_error($result)) {
                    $success_count++;
                } else {
                    $error_count++;
                }
                break;

            case 'delete':
                // Use wp_delete_post with force delete (skip trash)
                $result = wp_delete_post($post_id, true);
                if ($result) {
                    $success_count++;
                } else {
                    $error_count++;
                }
                break;
        }
    }

    // Build response message
    $action_labels = [
        'publish' => __('published', 'wp-art-routes'),
        'draft' => __('set to draft', 'wp-art-routes'),
        'delete' => __('deleted', 'wp-art-routes'),
    ];

    $message = sprintf(
        /* translators: %1$d: success count, %2$s: action label */
        _n('%1$d item %2$s.', '%1$d items %2$s.', $success_count, 'wp-art-routes'),
        $success_count,
        $action_labels[$action]
    );

    if ($error_count > 0) {
        $message .= ' ' . sprintf(
            /* translators: %d: error count */
            _n('%d item failed.', '%d items failed.', $error_count, 'wp-art-routes'),
            $error_count
        );
    }

    wp_send_json_success([
        'message' => $message,
        'success_count' => $success_count,
        'error_count' => $error_count,
    ]);
}
add_action('wp_ajax_wp_art_routes_dashboard_bulk_action', 'wp_art_routes_dashboard_bulk_action');
```

**Step 2: Test bulk action endpoint**

In browser console:
```javascript
jQuery.post(wpArtRoutesDashboard.ajaxUrl, {
    action: 'wp_art_routes_dashboard_bulk_action',
    nonce: wpArtRoutesDashboard.nonce,
    bulk_action: 'publish',
    post_ids: [123, 124, 125] // Replace with real post IDs
}, function(response) {
    console.log(response);
});
```

**Step 3: Commit**

```bash
git add includes/edition-dashboard.php
git commit -m "feat(dashboard): Add AJAX handler for bulk actions"
```

---

## Task 6: Create JavaScript - Core Setup and Edition Loading

**Files:**
- Create: `assets/js/edition-dashboard.js`

**Step 1: Create the JavaScript file with core structure**

Create `assets/js/edition-dashboard.js`:

```javascript
/**
 * Edition Dashboard JavaScript
 *
 * Handles edition selection, data loading, table rendering, inline editing,
 * bulk actions, and map display.
 */
(function($) {
    'use strict';

    // Dashboard state
    const state = {
        editionId: null,
        edition: null,
        routes: [],
        locations: [],
        infoPoints: [],
        availableIcons: [],
        map: null,
        mapLayers: {
            routes: null,
            locations: null,
            infoPoints: null
        }
    };

    // Cache DOM elements
    const $editionSelect = $('#edition-select');
    const $dashboardContent = $('#dashboard-content');
    const $noEditionMessage = $('#no-edition-message');
    const $viewFrontendLink = $('#view-frontend-link');

    /**
     * Initialize the dashboard
     */
    function init() {
        // Bind edition selector change
        $editionSelect.on('change', onEditionChange);

        // Bind collapsible section headers
        $('.section-header').on('click', toggleSection);

        // If edition is pre-selected (from URL), load it
        if ($editionSelect.val()) {
            loadEdition($editionSelect.val());
        }
    }

    /**
     * Handle edition dropdown change
     */
    function onEditionChange() {
        const editionId = $editionSelect.val();

        if (editionId) {
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('edition_id', editionId);
            window.history.pushState({}, '', url);

            loadEdition(editionId);
        } else {
            $dashboardContent.hide();
            $noEditionMessage.show();
            $viewFrontendLink.hide();
            state.editionId = null;
        }
    }

    /**
     * Load edition data via AJAX
     */
    function loadEdition(editionId) {
        state.editionId = editionId;

        // Show loading state
        $dashboardContent.show();
        $noEditionMessage.hide();
        showTableLoading('routes');
        showTableLoading('locations');
        showTableLoading('info-points');

        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_get_items',
                nonce: wpArtRoutesDashboard.nonce,
                edition_id: editionId
            },
            success: function(response) {
                if (response.success) {
                    state.edition = response.data.edition;
                    state.routes = response.data.routes;
                    state.locations = response.data.locations;
                    state.infoPoints = response.data.info_points;
                    state.availableIcons = response.data.available_icons;

                    // Update UI
                    $viewFrontendLink.attr('href', state.edition.permalink).show();

                    renderTables();
                    initMap();
                } else {
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                alert(wpArtRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Show loading state in a table
     */
    function showTableLoading(type) {
        const $tbody = $(`#${type}-table-body`);
        const colspan = type === 'routes' ? 4 : (type === 'locations' ? 8 : 7);
        $tbody.html(`<tr class="loading-row"><td colspan="${colspan}">${wpArtRoutesDashboard.strings.loading}</td></tr>`);
    }

    /**
     * Toggle collapsible section
     */
    function toggleSection() {
        const $section = $(this).closest('.collapsible');
        const $content = $section.find('.section-content');
        const $icon = $section.find('.toggle-icon');

        if ($section.hasClass('collapsed')) {
            $section.removeClass('collapsed');
            $content.slideDown(200);
            $icon.text('▼');
        } else {
            $section.addClass('collapsed');
            $content.slideUp(200);
            $icon.text('▶');
        }

        // Save state to localStorage
        saveCollapseState();
    }

    /**
     * Save collapse state to localStorage
     */
    function saveCollapseState() {
        if (!state.editionId) return;

        const collapseState = {
            routes: $('#routes-section').hasClass('collapsed'),
            locations: $('#locations-section').hasClass('collapsed'),
            infoPoints: $('#info-points-section').hasClass('collapsed')
        };

        localStorage.setItem(`dashboard-collapse-${state.editionId}`, JSON.stringify(collapseState));
    }

    /**
     * Restore collapse state from localStorage
     */
    function restoreCollapseState() {
        if (!state.editionId) return;

        const saved = localStorage.getItem(`dashboard-collapse-${state.editionId}`);
        if (!saved) return;

        try {
            const collapseState = JSON.parse(saved);

            ['routes', 'locations', 'infoPoints'].forEach(function(type) {
                const sectionId = type === 'infoPoints' ? 'info-points-section' : `${type}-section`;
                const $section = $(`#${sectionId}`);

                if (collapseState[type]) {
                    $section.addClass('collapsed');
                    $section.find('.section-content').hide();
                    $section.find('.toggle-icon').text('▶');
                } else {
                    $section.removeClass('collapsed');
                    $section.find('.section-content').show();
                    $section.find('.toggle-icon').text('▼');
                }
            });
        } catch (e) {
            // Invalid JSON, ignore
        }
    }

    // Initialize on document ready
    $(document).ready(init);

    // Expose for debugging
    window.wpArtRoutesDashboardState = state;

})(jQuery);
```

**Step 2: Verify JavaScript loads and edition selector works**

1. Navigate to Dashboard page
2. Open browser console, should see no errors
3. Select an edition from dropdown
4. Check console for AJAX request

**Step 3: Commit**

```bash
git add assets/js/edition-dashboard.js
git commit -m "feat(dashboard): Add JavaScript core setup and edition loading"
```

---

## Task 7: Create JavaScript - Table Rendering

**Files:**
- Modify: `assets/js/edition-dashboard.js`

**Step 1: Add table rendering functions**

Add after the `restoreCollapseState` function:

```javascript
    /**
     * Render all tables
     */
    function renderTables() {
        renderRoutesTable();
        renderLocationsTable();
        renderInfoPointsTable();
        updateSectionCounts();
        restoreCollapseState();
        bindTableEvents();
    }

    /**
     * Update section header counts
     */
    function updateSectionCounts() {
        // Routes
        const routesPublished = state.routes.filter(r => r.status === 'publish').length;
        const routesDrafts = state.routes.length - routesPublished;
        $('#routes-section .section-counts').text(
            `(${state.routes.length}) - ${routesPublished} ${wpArtRoutesDashboard.strings.published}, ${routesDrafts} ${wpArtRoutesDashboard.strings.drafts}`
        );

        // Locations
        const locationsPublished = state.locations.filter(l => l.status === 'publish').length;
        const locationsDrafts = state.locations.length - locationsPublished;
        $('#locations-section .section-counts').text(
            `(${state.locations.length}) - ${locationsPublished} ${wpArtRoutesDashboard.strings.published}, ${locationsDrafts} ${wpArtRoutesDashboard.strings.drafts}`
        );

        // Info Points
        const infoPointsPublished = state.infoPoints.filter(i => i.status === 'publish').length;
        const infoPointsDrafts = state.infoPoints.length - infoPointsPublished;
        $('#info-points-section .section-counts').text(
            `(${state.infoPoints.length}) - ${infoPointsPublished} ${wpArtRoutesDashboard.strings.published}, ${infoPointsDrafts} ${wpArtRoutesDashboard.strings.drafts}`
        );
    }

    /**
     * Render routes table
     */
    function renderRoutesTable() {
        const $tbody = $('#routes-table-body');

        if (state.routes.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="4">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        let html = '';
        state.routes.forEach(function(route) {
            html += `
                <tr data-id="${route.id}" data-type="route">
                    <td class="check-column">
                        <input type="checkbox" class="item-checkbox" value="${route.id}" data-status="${route.status}" />
                    </td>
                    <td class="column-title">
                        <span class="editable-cell" data-field="title" data-value="${escapeHtml(route.title)}">${escapeHtml(route.title)}</span>
                    </td>
                    <td class="column-status">
                        <span class="status-badge ${route.status}" data-id="${route.id}">${route.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft'}</span>
                    </td>
                    <td class="column-actions">
                        <div class="row-actions">
                            <a href="${route.edit_url}" target="_blank" title="Edit">✏️</a>
                            <a href="#" class="delete" data-id="${route.id}" title="Delete">🗑️</a>
                        </div>
                    </td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    /**
     * Render locations table
     */
    function renderLocationsTable() {
        const $tbody = $('#locations-table-body');

        if (state.locations.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="8">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        let html = '';
        state.locations.forEach(function(location) {
            const iconHtml = location.icon_url
                ? `<img src="${location.icon_url}" class="icon-preview" alt="" />`
                : '—';

            html += `
                <tr data-id="${location.id}" data-type="location">
                    <td class="check-column">
                        <input type="checkbox" class="item-checkbox" value="${location.id}" data-status="${location.status}" />
                    </td>
                    <td class="column-number">
                        <span class="editable-cell" data-field="number" data-value="${escapeHtml(location.number || '')}">${escapeHtml(location.number || '—')}</span>
                    </td>
                    <td class="column-title">
                        <span class="editable-cell" data-field="title" data-value="${escapeHtml(location.title)}">${escapeHtml(location.title)}</span>
                    </td>
                    <td class="column-status">
                        <span class="status-badge ${location.status}" data-id="${location.id}">${location.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft'}</span>
                    </td>
                    <td class="column-lat">
                        <span class="editable-cell" data-field="latitude" data-value="${location.latitude || ''}">${location.latitude || '—'}</span>
                    </td>
                    <td class="column-lng">
                        <span class="editable-cell" data-field="longitude" data-value="${location.longitude || ''}">${location.longitude || '—'}</span>
                    </td>
                    <td class="column-icon">
                        <span class="icon-cell" data-id="${location.id}" data-icon="${escapeHtml(location.icon || '')}">${iconHtml}</span>
                    </td>
                    <td class="column-actions">
                        <div class="row-actions">
                            <a href="${location.edit_url}" target="_blank" title="Edit">✏️</a>
                            <a href="#" class="delete" data-id="${location.id}" title="Delete">🗑️</a>
                        </div>
                    </td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    /**
     * Render info points table
     */
    function renderInfoPointsTable() {
        const $tbody = $('#info-points-table-body');

        if (state.infoPoints.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="7">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        let html = '';
        state.infoPoints.forEach(function(infoPoint) {
            const iconHtml = infoPoint.icon_url
                ? `<img src="${infoPoint.icon_url}" class="icon-preview" alt="" />`
                : '—';

            html += `
                <tr data-id="${infoPoint.id}" data-type="info_point">
                    <td class="check-column">
                        <input type="checkbox" class="item-checkbox" value="${infoPoint.id}" data-status="${infoPoint.status}" />
                    </td>
                    <td class="column-title">
                        <span class="editable-cell" data-field="title" data-value="${escapeHtml(infoPoint.title)}">${escapeHtml(infoPoint.title)}</span>
                    </td>
                    <td class="column-status">
                        <span class="status-badge ${infoPoint.status}" data-id="${infoPoint.id}">${infoPoint.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft'}</span>
                    </td>
                    <td class="column-lat">
                        <span class="editable-cell" data-field="latitude" data-value="${infoPoint.latitude || ''}">${infoPoint.latitude || '—'}</span>
                    </td>
                    <td class="column-lng">
                        <span class="editable-cell" data-field="longitude" data-value="${infoPoint.longitude || ''}">${infoPoint.longitude || '—'}</span>
                    </td>
                    <td class="column-icon">
                        <span class="icon-cell" data-id="${infoPoint.id}" data-icon="${escapeHtml(infoPoint.icon || '')}">${iconHtml}</span>
                    </td>
                    <td class="column-actions">
                        <div class="row-actions">
                            <a href="${infoPoint.edit_url}" target="_blank" title="Edit">✏️</a>
                            <a href="#" class="delete" data-id="${infoPoint.id}" title="Delete">🗑️</a>
                        </div>
                    </td>
                </tr>
            `;
        });

        $tbody.html(html);
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Bind table events (placeholder - will be implemented in next task)
     */
    function bindTableEvents() {
        // Will be implemented in Task 8
    }
```

**Step 2: Verify tables render**

1. Navigate to Dashboard page
2. Select an edition
3. Verify routes, locations, info points tables appear with data

**Step 3: Commit**

```bash
git add assets/js/edition-dashboard.js
git commit -m "feat(dashboard): Add table rendering functions"
```

---

## Task 8: Create JavaScript - Inline Editing and Status Toggle

**Files:**
- Modify: `assets/js/edition-dashboard.js`

**Step 1: Replace the empty bindTableEvents function**

Replace `function bindTableEvents() { }` with:

```javascript
    /**
     * Bind table interaction events
     */
    function bindTableEvents() {
        // Unbind first to prevent duplicates
        $(document).off('.dashboard');

        // Editable cells - click to edit
        $(document).on('click.dashboard', '.editable-cell:not(.editing)', startEditing);

        // Status badge click - toggle status
        $(document).on('click.dashboard', '.status-badge', toggleStatus);

        // Delete button
        $(document).on('click.dashboard', '.row-actions .delete', deleteItem);

        // Checkbox header - select all in section
        $(document).on('change.dashboard', '.select-all-checkbox', selectAllInSection);

        // Selection buttons
        $(document).on('click.dashboard', '.select-all', function() {
            selectItems($(this).closest('.dashboard-section'), 'all');
        });
        $(document).on('click.dashboard', '.select-none', function() {
            selectItems($(this).closest('.dashboard-section'), 'none');
        });
        $(document).on('click.dashboard', '.select-drafts', function() {
            selectItems($(this).closest('.dashboard-section'), 'drafts');
        });

        // Bulk action apply
        $(document).on('click.dashboard', '.bulk-apply', applyBulkAction);

        // Icon cell click
        $(document).on('click.dashboard', '.icon-cell', showIconSelector);
    }

    /**
     * Start inline editing
     */
    function startEditing() {
        const $cell = $(this);
        if ($cell.hasClass('editing')) return;

        const currentValue = $cell.data('value') || '';
        const field = $cell.data('field');

        $cell.addClass('editing');
        $cell.html(`<input type="text" value="${escapeHtml(currentValue)}" data-original="${escapeHtml(currentValue)}" />`);

        const $input = $cell.find('input');
        $input.focus().select();

        // Save on blur or Enter
        $input.on('blur', function() {
            finishEditing($cell, $(this).val());
        });

        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).blur();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditing($cell, $(this).data('original'));
            }
        });
    }

    /**
     * Finish inline editing and save
     */
    function finishEditing($cell, newValue) {
        const originalValue = $cell.find('input').data('original');
        const field = $cell.data('field');
        const postId = $cell.closest('tr').data('id');

        // If value hasn't changed, just restore display
        if (newValue === originalValue) {
            $cell.removeClass('editing');
            $cell.html(newValue || '—');
            $cell.data('value', newValue);
            return;
        }

        // Show saving state
        $cell.addClass('saving');
        $cell.removeClass('editing');
        $cell.html(newValue || '—');
        $cell.data('value', newValue);

        // Save via AJAX
        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_update_item',
                nonce: wpArtRoutesDashboard.nonce,
                post_id: postId,
                field: field,
                value: newValue
            },
            success: function(response) {
                $cell.removeClass('saving');
                if (response.success) {
                    $cell.addClass('saved');
                    setTimeout(() => $cell.removeClass('saved'), 500);

                    // Update local state
                    updateLocalState(postId, field, newValue);

                    // Update map if coordinates changed
                    if (field === 'latitude' || field === 'longitude') {
                        updateMap();
                    }
                } else {
                    $cell.addClass('error');
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                    // Restore original value
                    $cell.html(originalValue || '—');
                    $cell.data('value', originalValue);
                    setTimeout(() => $cell.removeClass('error'), 2000);
                }
            },
            error: function() {
                $cell.removeClass('saving');
                $cell.addClass('error');
                alert(wpArtRoutesDashboard.strings.error);
                $cell.html(originalValue || '—');
                $cell.data('value', originalValue);
                setTimeout(() => $cell.removeClass('error'), 2000);
            }
        });
    }

    /**
     * Cancel editing
     */
    function cancelEditing($cell, originalValue) {
        $cell.removeClass('editing');
        $cell.html(originalValue || '—');
    }

    /**
     * Toggle item status (publish/draft)
     */
    function toggleStatus() {
        const $badge = $(this);
        const postId = $badge.data('id');
        const $row = $badge.closest('tr');
        const currentStatus = $badge.hasClass('publish') ? 'publish' : 'draft';
        const newStatus = currentStatus === 'publish' ? 'draft' : 'publish';

        // Optimistic UI update
        $badge.removeClass(currentStatus).addClass(newStatus);
        $badge.text(newStatus === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft');
        $row.find('.item-checkbox').data('status', newStatus);

        // Save via AJAX
        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_update_item',
                nonce: wpArtRoutesDashboard.nonce,
                post_id: postId,
                field: 'status',
                value: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update local state
                    updateLocalState(postId, 'status', newStatus);
                    updateSectionCounts();
                    updateMap();
                } else {
                    // Revert on error
                    $badge.removeClass(newStatus).addClass(currentStatus);
                    $badge.text(currentStatus === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft');
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                // Revert on error
                $badge.removeClass(newStatus).addClass(currentStatus);
                $badge.text(currentStatus === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft');
                alert(wpArtRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Update local state after edit
     */
    function updateLocalState(postId, field, value) {
        // Find and update in routes
        const route = state.routes.find(r => r.id === postId);
        if (route) {
            route[field] = value;
            return;
        }

        // Find and update in locations
        const location = state.locations.find(l => l.id === postId);
        if (location) {
            location[field] = value;
            return;
        }

        // Find and update in info points
        const infoPoint = state.infoPoints.find(i => i.id === postId);
        if (infoPoint) {
            infoPoint[field] = value;
        }
    }

    /**
     * Delete single item
     */
    function deleteItem(e) {
        e.preventDefault();
        const postId = $(this).data('id');

        if (!confirm(wpArtRoutesDashboard.strings.confirmDeleteSingle)) {
            return;
        }

        const $row = $(this).closest('tr');
        $row.css('opacity', '0.5');

        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_bulk_action',
                nonce: wpArtRoutesDashboard.nonce,
                bulk_action: 'delete',
                post_ids: [postId]
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        removeFromLocalState(postId);
                        updateSectionCounts();
                        updateMap();
                    });
                } else {
                    $row.css('opacity', '1');
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                $row.css('opacity', '1');
                alert(wpArtRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Remove item from local state
     */
    function removeFromLocalState(postId) {
        state.routes = state.routes.filter(r => r.id !== postId);
        state.locations = state.locations.filter(l => l.id !== postId);
        state.infoPoints = state.infoPoints.filter(i => i.id !== postId);
    }
```

**Step 2: Verify inline editing works**

1. Click on a title cell - should turn into input
2. Change value and press Enter or click away
3. Verify value saves and shows green flash
4. Click status badge - should toggle

**Step 3: Commit**

```bash
git add assets/js/edition-dashboard.js
git commit -m "feat(dashboard): Add inline editing and status toggle"
```

---

## Task 9: Create JavaScript - Selection and Bulk Actions

**Files:**
- Modify: `assets/js/edition-dashboard.js`

**Step 1: Add selection and bulk action functions**

Add after `removeFromLocalState`:

```javascript
    /**
     * Select all items in section via header checkbox
     */
    function selectAllInSection() {
        const $section = $(this).closest('.dashboard-section');
        const isChecked = $(this).is(':checked');
        $section.find('.item-checkbox').prop('checked', isChecked);
    }

    /**
     * Select items by criteria
     */
    function selectItems($section, criteria) {
        const $checkboxes = $section.find('.item-checkbox');

        switch (criteria) {
            case 'all':
                $checkboxes.prop('checked', true);
                $section.find('.select-all-checkbox').prop('checked', true);
                break;
            case 'none':
                $checkboxes.prop('checked', false);
                $section.find('.select-all-checkbox').prop('checked', false);
                break;
            case 'drafts':
                $checkboxes.each(function() {
                    const isDraft = $(this).data('status') === 'draft';
                    $(this).prop('checked', isDraft);
                });
                break;
        }
    }

    /**
     * Apply bulk action
     */
    function applyBulkAction() {
        const $section = $(this).closest('.dashboard-section');
        const action = $section.find('.bulk-action-select').val();
        const $checked = $section.find('.item-checkbox:checked');
        const postIds = $checked.map(function() { return $(this).val(); }).get();

        if (!action) {
            return;
        }

        if (postIds.length === 0) {
            alert(wpArtRoutesDashboard.strings.noItemsSelected);
            return;
        }

        // Confirm delete
        if (action === 'delete') {
            if (!confirm(wpArtRoutesDashboard.strings.confirmDelete)) {
                return;
            }
        }

        // Disable UI during request
        const $button = $(this);
        $button.prop('disabled', true).text(wpArtRoutesDashboard.strings.saving);
        $checked.closest('tr').css('opacity', '0.5');

        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_bulk_action',
                nonce: wpArtRoutesDashboard.nonce,
                bulk_action: action,
                post_ids: postIds
            },
            success: function(response) {
                $button.prop('disabled', false).text($button.closest('.bulk-actions').find('.bulk-action-select option:first').text().replace('Bulk Actions', 'Apply'));

                if (response.success) {
                    // Reload edition data to refresh tables
                    loadEdition(state.editionId);
                } else {
                    $checked.closest('tr').css('opacity', '1');
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                $button.prop('disabled', false);
                $checked.closest('tr').css('opacity', '1');
                alert(wpArtRoutesDashboard.strings.error);
            }
        });

        // Reset select
        $section.find('.bulk-action-select').val('');
    }

    /**
     * Show icon selector dropdown
     */
    function showIconSelector() {
        const $cell = $(this);
        const currentIcon = $cell.data('icon') || '';
        const postId = $cell.data('id');
        const $row = $cell.closest('tr');
        const postType = $row.data('type');

        // Build dropdown HTML
        let optionsHtml = '<option value="">— No Icon —</option>';
        state.availableIcons.forEach(function(icon) {
            const selected = icon.filename === currentIcon ? 'selected' : '';
            optionsHtml += `<option value="${escapeHtml(icon.filename)}" ${selected}>${escapeHtml(icon.display_name)}</option>`;
        });

        // Replace cell with select
        const originalHtml = $cell.html();
        $cell.html(`<select class="icon-select">${optionsHtml}</select>`);

        const $select = $cell.find('select');
        $select.focus();

        $select.on('change', function() {
            const newIcon = $(this).val();
            saveIcon($cell, postId, newIcon, originalHtml);
        });

        $select.on('blur', function() {
            // Restore original if no change
            setTimeout(function() {
                if ($cell.find('select').length) {
                    $cell.html(originalHtml);
                }
            }, 200);
        });
    }

    /**
     * Save icon selection
     */
    function saveIcon($cell, postId, newIcon, originalHtml) {
        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_update_item',
                nonce: wpArtRoutesDashboard.nonce,
                post_id: postId,
                field: 'icon',
                value: newIcon
            },
            success: function(response) {
                if (response.success) {
                    // Update cell with new icon
                    const iconHtml = response.data.icon_url
                        ? `<img src="${response.data.icon_url}" class="icon-preview" alt="" />`
                        : '—';
                    $cell.html(iconHtml);
                    $cell.data('icon', newIcon);

                    // Update local state
                    updateLocalState(postId, 'icon', newIcon);
                    updateLocalState(postId, 'icon_url', response.data.icon_url);
                } else {
                    $cell.html(originalHtml);
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                $cell.html(originalHtml);
                alert(wpArtRoutesDashboard.strings.error);
            }
        });
    }
```

**Step 2: Verify bulk actions work**

1. Check several items in locations table
2. Click "Select Drafts" - only drafts should be selected
3. Choose "Publish" from bulk actions dropdown
4. Click "Apply" - items should be published

**Step 3: Commit**

```bash
git add assets/js/edition-dashboard.js
git commit -m "feat(dashboard): Add selection and bulk actions"
```

---

## Task 10: Create JavaScript - Map Initialization and Rendering

**Files:**
- Modify: `assets/js/edition-dashboard.js`

**Step 1: Add map functions**

Add after `saveIcon`:

```javascript
    /**
     * Initialize the map
     */
    function initMap() {
        // Destroy existing map if any
        if (state.map) {
            state.map.remove();
            state.map = null;
        }

        // Create map
        state.map = L.map('dashboard-map', {
            scrollWheelZoom: false
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(state.map);

        // Render markers
        updateMap();
    }

    /**
     * Update map markers
     */
    function updateMap() {
        if (!state.map) return;

        // Clear existing layers
        if (state.mapLayers.routes) {
            state.map.removeLayer(state.mapLayers.routes);
        }
        if (state.mapLayers.locations) {
            state.map.removeLayer(state.mapLayers.locations);
        }
        if (state.mapLayers.infoPoints) {
            state.map.removeLayer(state.mapLayers.infoPoints);
        }

        // Create layer groups
        state.mapLayers.routes = L.layerGroup();
        state.mapLayers.locations = L.layerGroup();
        state.mapLayers.infoPoints = L.layerGroup();

        const bounds = L.latLngBounds();
        let hasPoints = false;

        // Add routes as polylines
        state.routes.forEach(function(route) {
            if (route.route_path && route.route_path.length > 0) {
                const latLngs = route.route_path.map(function(point) {
                    // Handle both array format [lat, lng] and object format {lat, lng}
                    if (Array.isArray(point)) {
                        return [point[0], point[1]];
                    } else {
                        return [point.lat, point.lng];
                    }
                });

                const opacity = route.status === 'publish' ? 1 : 0.4;
                const polyline = L.polyline(latLngs, {
                    color: '#3388ff',
                    weight: 4,
                    opacity: opacity
                });

                polyline.bindTooltip(route.title);
                polyline.addTo(state.mapLayers.routes);

                latLngs.forEach(function(ll) {
                    bounds.extend(ll);
                    hasPoints = true;
                });
            }
        });

        // Add locations as markers
        state.locations.forEach(function(location) {
            if (location.latitude && location.longitude) {
                const opacity = location.status === 'publish' ? 1 : 0.5;
                const marker = L.circleMarker([location.latitude, location.longitude], {
                    radius: 8,
                    fillColor: '#2ecc71',
                    color: '#27ae60',
                    weight: 2,
                    opacity: opacity,
                    fillOpacity: opacity * 0.8
                });

                const label = location.number ? `${location.number}: ${location.title}` : location.title;
                marker.bindTooltip(label);
                marker.addTo(state.mapLayers.locations);

                bounds.extend([location.latitude, location.longitude]);
                hasPoints = true;
            }
        });

        // Add info points as markers
        state.infoPoints.forEach(function(infoPoint) {
            if (infoPoint.latitude && infoPoint.longitude) {
                const opacity = infoPoint.status === 'publish' ? 1 : 0.5;
                const marker = L.circleMarker([infoPoint.latitude, infoPoint.longitude], {
                    radius: 6,
                    fillColor: '#e67e22',
                    color: '#d35400',
                    weight: 2,
                    opacity: opacity,
                    fillOpacity: opacity * 0.8
                });

                marker.bindTooltip(infoPoint.title);
                marker.addTo(state.mapLayers.infoPoints);

                bounds.extend([infoPoint.latitude, infoPoint.longitude]);
                hasPoints = true;
            }
        });

        // Add layers to map
        state.mapLayers.routes.addTo(state.map);
        state.mapLayers.locations.addTo(state.map);
        state.mapLayers.infoPoints.addTo(state.map);

        // Fit bounds
        if (hasPoints) {
            state.map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            // Default view (Netherlands)
            state.map.setView([52.1326, 5.2913], 7);
        }
    }
```

**Step 2: Verify map displays**

1. Select an edition with routes and locations
2. Map should show with markers
3. Draft items should appear faded
4. Toggle status of an item - map should update

**Step 3: Commit**

```bash
git add assets/js/edition-dashboard.js
git commit -m "feat(dashboard): Add map initialization and rendering"
```

---

## Task 11: Fix Localization String and Final Polish

**Files:**
- Modify: `includes/edition-dashboard.php` (fix strings)

**Step 1: Update localized strings**

In the `wp_localize_script` call, update the strings array to include all needed strings:

```php
'strings' => [
    'confirmDelete' => __('Are you sure you want to delete the selected items? This cannot be undone.', 'wp-art-routes'),
    'confirmDeleteSingle' => __('Are you sure you want to delete this item? This cannot be undone.', 'wp-art-routes'),
    'noItemsSelected' => __('Please select at least one item.', 'wp-art-routes'),
    'saving' => __('Saving...', 'wp-art-routes'),
    'saved' => __('Saved', 'wp-art-routes'),
    'error' => __('Error saving. Please try again.', 'wp-art-routes'),
    'loading' => __('Loading...', 'wp-art-routes'),
    'noItems' => __('No items found.', 'wp-art-routes'),
    'published' => __('published', 'wp-art-routes'),
    'drafts' => __('drafts', 'wp-art-routes'),
],
```

**Step 2: Test full workflow**

1. Navigate to Dashboard
2. Select edition
3. Verify map loads with all items
4. Verify all three sections show with counts
5. Test inline editing (title, number, coordinates)
6. Test status toggle
7. Test bulk select drafts → publish
8. Test icon selector
9. Test delete item
10. Collapse/expand sections, change edition, come back - verify state persists

**Step 3: Commit**

```bash
git add includes/edition-dashboard.php
git commit -m "feat(dashboard): Fix localization strings and polish"
```

---

## Task 12: Update CLAUDE.md Documentation

**Files:**
- Modify: `CLAUDE.md`

**Step 1: Add Edition Dashboard to documentation**

Add a new section after "Import/Export System":

```markdown
### Edition Dashboard

Located at Art Routes → Dashboard (`includes/edition-dashboard.php`):

**Features:**
- Overview map showing all edition content (routes as polylines, locations/info points as markers)
- Draft items shown at 50% opacity on map
- Collapsible sections for Routes, Locations, Info Points
- Inline editing for title, number, coordinates
- Status toggle (click badge to publish/draft)
- Icon selector dropdown
- Bulk actions: publish, draft, delete selected
- Quick selection: Select All, Select None, Select Drafts

**JavaScript:** `assets/js/edition-dashboard.js`
**CSS:** `assets/css/edition-dashboard.css`

**AJAX Endpoints:**
| Action | Purpose |
|--------|---------|
| `wp_art_routes_dashboard_get_items` | Fetch all routes/locations/info points for edition (including drafts) |
| `wp_art_routes_dashboard_update_item` | Update single field (title, status, number, lat, lng, icon) |
| `wp_art_routes_dashboard_bulk_action` | Bulk publish/draft/delete |
```

**Step 2: Update Core PHP Files table**

Add to the table:

```markdown
| `edition-dashboard.php` | Edition Dashboard admin page - bulk management UI with map |
```

**Step 3: Commit**

```bash
git add CLAUDE.md
git commit -m "docs: Add Edition Dashboard documentation to CLAUDE.md"
```

---

## Summary

This plan creates the Edition Dashboard in 12 incremental tasks:

1. **Task 1**: PHP admin page shell with HTML structure
2. **Task 2**: CSS styles and asset enqueuing
3. **Task 3**: AJAX handler for fetching edition data
4. **Task 4**: AJAX handler for updating single items
5. **Task 5**: AJAX handler for bulk actions
6. **Task 6**: JavaScript core setup and edition loading
7. **Task 7**: Table rendering functions
8. **Task 8**: Inline editing and status toggle
9. **Task 9**: Selection and bulk actions
10. **Task 10**: Map initialization and rendering
11. **Task 11**: Localization and polish
12. **Task 12**: Documentation update

Each task is self-contained with clear verification steps and commits.
