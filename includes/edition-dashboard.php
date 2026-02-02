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
