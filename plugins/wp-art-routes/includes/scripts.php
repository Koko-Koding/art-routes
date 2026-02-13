<?php

/**
 * Scripts and Styles for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register all frontend assets early (register only, enqueue from templates as needed)
 */
function wp_art_routes_register_frontend_assets()
{
    // Leaflet CSS (bundled locally for WordPress.org compliance)
    wp_register_style(
        'wp-art-routes-leaflet-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/lib/leaflet/leaflet.css',
        [],
        '1.9.4'
    );

    // Leaflet JS (bundled locally for WordPress.org compliance)
    wp_register_script(
        'wp-art-routes-leaflet-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/lib/leaflet/leaflet.js',
        [],
        '1.9.4',
        true
    );

    // Main frontend map CSS
    wp_register_style(
        'wp-art-routes-map-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/art-route-map.css',
        [],
        WP_ART_ROUTES_VERSION
    );

    // Related artworks CSS
    wp_register_style(
        'wp-art-routes-related-artworks-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/related-artworks.css',
        [],
        WP_ART_ROUTES_VERSION
    );

    // Edition map shortcode CSS
    wp_register_style(
        'wp-art-routes-edition-map-shortcode-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/edition-map-shortcode.css',
        ['wp-art-routes-leaflet-css'],
        WP_ART_ROUTES_VERSION
    );

    // Single edition CSS
    wp_register_style(
        'wp-art-routes-single-edition-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/single-edition.css',
        ['wp-art-routes-leaflet-css'],
        WP_ART_ROUTES_VERSION
    );

    // Single artwork CSS
    wp_register_style(
        'wp-art-routes-single-artwork-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/single-artwork.css',
        ['wp-art-routes-leaflet-css'],
        WP_ART_ROUTES_VERSION
    );

    // Route icons CSS
    wp_register_style(
        'wp-art-routes-route-icons-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/route-icons.css',
        [],
        WP_ART_ROUTES_VERSION
    );

    // Edition map JS (unified shortcode + single)
    wp_register_script(
        'wp-art-routes-edition-map-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/edition-map.js',
        ['wp-art-routes-leaflet-js'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Localize i18n for edition map
    wp_localize_script('wp-art-routes-edition-map-js', 'wpArtRoutesEditionMapI18n', [
        'viewDetails' => __('View details', 'art-routes'),
        'readMore'    => __('Read more', 'art-routes'),
    ]);

    // Multiple routes map JS
    wp_register_script(
        'wp-art-routes-multiple-routes-map-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/multiple-routes-map.js',
        ['wp-art-routes-leaflet-js'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Localize i18n for multiple routes map
    wp_localize_script('wp-art-routes-multiple-routes-map-js', 'wpArtRoutesMultiMapI18n', [
        'showAllRoutes' => __('Show All Routes', 'art-routes'),
        'readMore'      => __('Read more', 'art-routes'),
    ]);

    // Single artwork map JS
    wp_register_script(
        'wp-art-routes-single-artwork-map-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/single-artwork-map.js',
        ['jquery', 'wp-art-routes-leaflet-js'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Route icons JS
    wp_register_script(
        'wp-art-routes-route-icons-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/route-icons.js',
        [],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Main frontend map JS
    wp_register_script(
        'wp-art-routes-map-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/art-route-map.js',
        ['jquery', 'wp-art-routes-leaflet-js'],
        WP_ART_ROUTES_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'wp_art_routes_register_frontend_assets', 5);

/**
 * Enqueue frontend scripts and styles for the art route map
 */
function wp_art_routes_enqueue_scripts()
{
    // Only enqueue on pages with our shortcode or template or single post type
    global $post;
    $has_related_artworks_shortcode = false;
    if (isset($post->post_content) && has_shortcode($post->post_content, 'related_artworks')) {
        $has_related_artworks_shortcode = true;
    }
    if (!wp_art_routes_is_route_page() && !$has_related_artworks_shortcode) {
        return;
    }

    // Enqueue Leaflet
    wp_enqueue_style('wp-art-routes-leaflet-css');
    wp_enqueue_script('wp-art-routes-leaflet-js');

    // Our custom CSS
    wp_enqueue_style('wp-art-routes-map-css');

    // Related artworks CSS (only if shortcode is present)
    if ($has_related_artworks_shortcode) {
        wp_enqueue_style('wp-art-routes-related-artworks-css');
    }

    // Early-enqueue CSS for single post types to prevent FOUC
    if (is_singular('edition')) {
        wp_enqueue_style('wp-art-routes-single-edition-css');
    }
    if (is_singular('artwork')) {
        wp_enqueue_style('wp-art-routes-single-artwork-css');
    }

    // Our custom JS
    wp_enqueue_script('wp-art-routes-map-js');

    // Determine Route ID
    $route_id = 0;
    if (is_singular('art_route')) {
        $route_id = get_the_ID();
    } elseif (is_page_template('art-route-map-template.php')) {
        $route_id = isset($_GET['route_id']) ? intval(wp_unslash($_GET['route_id'])) : get_option('wp_art_routes_default_route', 0);
    } else {
        // Attempt to find route_id if shortcode is present on a non-singular/non-template page
        global $post;
        if (isset($post->post_content) && has_shortcode($post->post_content, 'art_route_map')) {
            preg_match('/\\[art_route_map.*?route_id=([\'"]?)(\d+)\1.*?\\]/', $post->post_content, $matches);
            if (!empty($matches[2])) {
                $route_id = intval($matches[2]);
            } else {
                $route_id = get_option('wp_art_routes_default_route', 0);
            }
        }
    }

    // Get route data if we have a valid ID
    $route_data = null;
    if ($route_id > 0) {
        $route_data = wp_art_routes_get_route_data($route_id);
    }

    // Localize script if we have data
    if ($route_data) {
        $js_data = [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wp_art_routes_nonce'),
            'route_path' => $route_data['route_path'],
            'artworks' => $route_data['artworks'],
            'information_points' => $route_data['information_points'],
            'show_completed_route' => $route_data['show_completed_route'],
            'show_artwork_toasts' => $route_data['show_artwork_toasts'],
            'plugin_url' => WP_ART_ROUTES_PLUGIN_URL,
            'i18n' => [
                'routeComplete' => __('Congratulations! You have completed this route!', 'art-routes'),
                'nearbyArtwork' => __('You are near an artwork!', 'art-routes'),
            ],
        ];
        wp_localize_script('wp-art-routes-map-js', 'artRouteData', $js_data);
    } else {
        wp_localize_script('wp-art-routes-map-js', 'artRouteData', [
            'error' => 'No route data found or specified.',
            'plugin_url' => WP_ART_ROUTES_PLUGIN_URL,
            'route_path' => [],
            'artworks' => [],
            'information_points' => [],
        ]);
    }
}
add_action('wp_enqueue_scripts', 'wp_art_routes_enqueue_scripts');

/**
 * Enqueue admin scripts and styles for the route editor, location picker, and other admin pages
 */
function wp_art_routes_enqueue_admin_scripts($hook)
{
    global $post;

    // Check if we need the route editor scripts
    $is_edit_page = $hook === 'post.php' || $hook === 'post-new.php';
    $is_route_type = isset($post) && $post->post_type === 'art_route';
    $is_artwork_type = isset($post) && $post->post_type === 'artwork';
    $is_info_point_type = isset($post) && $post->post_type === 'information_point';

    // Settings page
    if ($hook === 'edition_page_wp-art-routes-settings') {
        wp_enqueue_script(
            'wp-art-routes-custom-icons-js',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/js/admin/custom-icons.js',
            ['jquery'],
            WP_ART_ROUTES_VERSION,
            true
        );
        wp_localize_script('wp-art-routes-custom-icons-js', 'wpArtRoutesCustomIcons', [
            'deleteNonce' => wp_create_nonce('wp_art_routes_delete_icon'),
            'i18n' => [
                'uploading'     => __('Uploading...', 'art-routes'),
                'uploadFailed'  => __('Upload failed.', 'art-routes'),
                'confirmDelete' => __('Are you sure you want to delete this icon? This cannot be undone.', 'art-routes'),
                'deleting'      => __('Deleting...', 'art-routes'),
                'deleteFailed'  => __('Delete failed.', 'art-routes'),
                'deleteBtn'     => __('Delete', 'art-routes'),
            ],
        ]);
    }

    // Import/Export page
    if ($hook === 'edition_page_wp-art-routes-import-export') {
        wp_enqueue_style(
            'wp-art-routes-import-export-css',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/css/import-export.css',
            [],
            WP_ART_ROUTES_VERSION
        );
        wp_enqueue_script(
            'wp-art-routes-import-export-js',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/js/admin/import-export.js',
            [],
            WP_ART_ROUTES_VERSION,
            true
        );
        wp_localize_script('wp-art-routes-import-export-js', 'wpArtRoutesImportExport', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'i18n' => [
                'selectEdition' => __('Please select an edition.', 'art-routes'),
            ],
        ]);
    }

    // Only load post-editor scripts on relevant pages
    if (!$is_edit_page || (!$is_route_type && !$is_artwork_type && !$is_info_point_type)) {
        return;
    }

    // Leaflet CSS (shared, bundled locally for WordPress.org compliance)
    wp_enqueue_style(
        'wp-art-routes-admin-leaflet-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/lib/leaflet/leaflet.css',
        [],
        '1.9.4'
    );

    // Leaflet JS (shared, bundled locally for WordPress.org compliance)
    wp_enqueue_script(
        'wp-art-routes-admin-leaflet-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/lib/leaflet/leaflet.js',
        ['jquery'],
        '1.9.4',
        true
    );

    // Meta boxes CSS (for artist association styles)
    wp_enqueue_style(
        'wp-art-routes-meta-boxes-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/meta-boxes.css',
        [],
        WP_ART_ROUTES_VERSION
    );

    // Icon preview JS (shared by artwork, info point, and route icon meta boxes)
    wp_enqueue_script(
        'wp-art-routes-icon-preview-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/admin/icon-preview.js',
        ['jquery'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Route editor (for art_route post type)
    if ($is_route_type) {
        wp_enqueue_style(
            'wp-art-routes-editor-css',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/css/route-editor-admin.css',
            [],
            WP_ART_ROUTES_VERSION
        );

        wp_enqueue_script(
            'wp-art-routes-editor-js',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/js/route-editor-admin.js',
            ['jquery', 'wp-art-routes-admin-leaflet-js', 'jquery-ui-draggable'],
            WP_ART_ROUTES_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script(
            'wp-art-routes-editor-js',
            'routeEditorData',
            [
                'modalHTML' => wp_art_routes_get_route_editor_modal_html(),
                'ajax_url' => admin_url('admin-ajax.php'),
                'get_points_nonce' => wp_create_nonce('get_route_points_nonce'),
                'save_points_nonce' => wp_create_nonce('save_route_points_nonce'),
                'route_id' => isset($post) ? $post->ID : 0,
                'i18n' => [
                    'addArtwork' => __('Add Artwork', 'art-routes'),
                    'addInfoPoint' => __('Add Info Point', 'art-routes'),
                    'artwork' => __('Artwork', 'art-routes'),
                    'infoPoint' => __('Info Point', 'art-routes'),
                    'edit' => __('Edit', 'art-routes'),
                    'remove' => __('Remove', 'art-routes'),
                    'confirmRemove' => __('Are you sure you want to remove this point from the route?', 'art-routes'),
                    'errorLoadingPoints' => __('Error loading points for this route.', 'art-routes'),
                    'errorSavingPoints' => __('Error saving points.', 'art-routes'),
                    'savingPoints' => __('Saving points...', 'art-routes'),
                    'pointsSaved' => __('Points saved successfully.', 'art-routes'),
                    'draftWarning' => __('Warning: This point is a draft and won\'t be visible on the public map.', 'art-routes'),
                ]
            ]
        );
    }

    // Location picker and mini map (for artwork and information_point post types)
    if ($is_artwork_type || $is_info_point_type) {
        wp_enqueue_style(
            'wp-art-routes-location-picker-css',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/css/artwork-location-picker.css',
            [],
            WP_ART_ROUTES_VERSION
        );

        wp_enqueue_script(
            'wp-art-routes-location-picker-js',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/js/artwork-location-picker.js',
            ['jquery', 'wp-art-routes-admin-leaflet-js'],
            WP_ART_ROUTES_VERSION,
            true
        );

        // Pass the modal HTML to JavaScript
        wp_localize_script(
            'wp-art-routes-location-picker-js',
            'artworkLocationModalHTML',
            wp_art_routes_get_location_picker_modal_html()
        );

        // Location map mini (inline map in the meta box)
        wp_enqueue_script(
            'wp-art-routes-location-map-mini-js',
            WP_ART_ROUTES_PLUGIN_URL . 'assets/js/admin/location-map-mini.js',
            ['jquery', 'wp-art-routes-admin-leaflet-js'],
            WP_ART_ROUTES_VERSION,
            true
        );

        // Artist search (for artwork only)
        if ($is_artwork_type) {
            wp_enqueue_script('jquery-ui-autocomplete');

            wp_enqueue_script(
                'wp-art-routes-artist-search-js',
                WP_ART_ROUTES_PLUGIN_URL . 'assets/js/admin/artist-search.js',
                ['jquery', 'jquery-ui-autocomplete'],
                WP_ART_ROUTES_VERSION,
                true
            );
            wp_localize_script('wp-art-routes-artist-search-js', 'wpArtRoutesArtistSearch', [
                'nonce'      => wp_create_nonce('artist_search_nonce'),
                'removeText' => __('Remove', 'art-routes'),
            ]);
        }
    }
}
add_action('admin_enqueue_scripts', 'wp_art_routes_enqueue_admin_scripts');

/**
 * Check if current page should display a route map
 */
function wp_art_routes_is_route_page()
{
    global $post;

    // Check for shortcodes and blocks in post content
    if (isset($post->post_content)) {
        if (has_shortcode($post->post_content, 'art_route_map')) {
            return true;
        }
        if (has_shortcode($post->post_content, 'edition_map')) {
            return true;
        }
        if (has_shortcode($post->post_content, 'art_routes_map')) {
            return true;
        }
        if (has_block('wp-art-routes/edition-map', $post->post_content)) {
            return true;
        }
        if (has_block('wp-art-routes/routes-map', $post->post_content)) {
            return true;
        }
    }

    if (is_page_template('art-route-map-template.php')) {
        return true;
    }

    if (is_singular('art_route')) {
        return true;
    }

    if (is_singular('artwork')) {
        return true;
    }

    if (is_singular('edition')) {
        return true;
    }

    return false;
}

/**
 * Get the route editor modal HTML
 */
function wp_art_routes_get_route_editor_modal_html()
{
    ob_start();
?>
    <div id="route-editor-modal" class="route-editor-modal" style="display: none;">
        <div class="route-editor-modal-content">
            <div class="route-editor-header">
                <h2><?php esc_html_e('Route Editor', 'art-routes'); ?></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="route-editor-body">
                <div class="route-editor-controls">
                    <h4><?php esc_html_e('Route Path', 'art-routes'); ?></h4>
                    <button id="start-drawing" class="button"><?php esc_html_e('Start Drawing', 'art-routes'); ?></button>
                    <button id="stop-drawing" class="button"><?php esc_html_e('Stop Drawing', 'art-routes'); ?></button>
                    <button id="clear-route" class="button button-secondary"><?php esc_html_e('Clear Path', 'art-routes'); ?></button>
                    <p id="drawing-instructions" class="description"><?php esc_html_e('Select an action.', 'art-routes'); ?></p>

                    <h4><?php esc_html_e('Points of Interest', 'art-routes'); ?></h4>
                    <button id="add-artwork" class="button"><?php esc_html_e('Add Artwork', 'art-routes'); ?></button>
                    <button id="add-info-point" class="button"><?php esc_html_e('Add Info Point', 'art-routes'); ?></button>
                    <p id="adding-point-info" class="description" style="display: none;"></p>

                    <h4><?php esc_html_e('Map View', 'art-routes'); ?></h4>
                    <button id="fit-route-bounds" class="button"><?php esc_html_e('Fit Route', 'art-routes'); ?></button>
                    <button id="locate-user" class="button"><?php esc_html_e('My Location', 'art-routes'); ?></button>

                    <h4><?php esc_html_e('Search Location', 'art-routes'); ?></h4>
                    <input type="text" id="route-search" placeholder="<?php esc_attr_e('Enter address or place...', 'art-routes'); ?>" />
                    <button id="search-location" class="button"><?php esc_html_e('Search', 'art-routes'); ?></button>
                </div>
                <div class="control-info">
                    <p id="drawing-instructions"><?php esc_html_e('Use controls above to draw the route or add points. Click on the map to place items.', 'art-routes'); ?></p>
                    <p>
                        <span id="point-count">0</span> <?php esc_html_e('route points', 'art-routes'); ?> |
                        <span id="artwork-count">0</span> <?php esc_html_e('artworks', 'art-routes'); ?> |
                        <span id="info-point-count">0</span> <?php esc_html_e('info points', 'art-routes'); ?>
                    </p>
                    <p><?php esc_html_e('Route distance:', 'art-routes'); ?> <span id="route-distance">0</span> km</p>
                    <p id="save-status" style="color: green; font-weight: bold;"></p>
                </div>
                <div id="route-editor-map"></div>
            </div>
            <div class="route-editor-footer">
                <button type="button" class="button button-secondary" id="cancel-route"><?php esc_html_e('Close', 'art-routes'); ?></button>
                <button type="button" class="button button-primary" id="save-route"><?php esc_html_e('Save Changes', 'art-routes'); ?></button>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}

/**
 * Get the location picker modal HTML
 */
function wp_art_routes_get_location_picker_modal_html()
{
    ob_start();
?>
    <div id="artwork-location-modal" class="location-picker-modal" style="display: none;">
        <div class="location-picker-modal-content">
            <div class="location-picker-header">
                <h2><?php esc_html_e('Pick Artwork Location', 'art-routes'); ?></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="location-picker-body">
                <div class="location-picker-controls">
                    <div class="control-group">
                        <label for="location-search"><?php esc_html_e('Search Location:', 'art-routes'); ?></label>
                        <input type="text" id="location-search" class="regular-text" placeholder="<?php esc_html_e('Enter location...', 'art-routes'); ?>">
                        <button type="button" class="button" id="search-artwork-location"><?php esc_html_e('Search', 'art-routes'); ?></button>
                    </div>
                    <div class="control-info">
                        <p><?php esc_html_e('Click on the map to select the artwork location.', 'art-routes'); ?></p>
                        <p><?php esc_html_e('Selected coordinates:', 'art-routes'); ?></p>
                        <p id="selected-coordinates">None</p>
                    </div>
                </div>
                <div id="location-picker-map"></div>
            </div>
            <div class="location-picker-footer">
                <button type="button" class="button button-secondary" id="cancel-location"><?php esc_html_e('Cancel', 'art-routes'); ?></button>
                <button type="button" class="button button-primary" id="save-location"><?php esc_html_e('Save Location', 'art-routes'); ?></button>
            </div>
        </div>
    </div>
<?php
    return ob_get_clean();
}
