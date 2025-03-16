<?php
/**
 * Scripts and Styles for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue frontend scripts and styles for the art route map
 */
function wp_art_routes_enqueue_scripts() {
    // Only enqueue on pages with our shortcode or template
    if (!wp_art_routes_is_route_page()) {
        return;
    }
    
    // Leaflet CSS
    wp_enqueue_style(
        'wp-art-routes-leaflet-css',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css',
        [],
        '1.9.4'
    );
    
    // Leaflet JS
    wp_enqueue_script(
        'wp-art-routes-leaflet-js',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js',
        [],
        '1.9.4',
        true
    );
    
    // Our custom CSS
    wp_enqueue_style(
        'wp-art-routes-map-css',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/css/art-route-map.css',
        [],
        WP_ART_ROUTES_VERSION
    );
    
    // Our custom JS
    wp_enqueue_script(
        'wp-art-routes-map-js',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/art-route-map.js',
        ['jquery', 'wp-art-routes-leaflet-js'],
        WP_ART_ROUTES_VERSION,
        true
    );
    
    // If we're on a single art_route post, pass the data directly to JavaScript
    if (is_singular('art_route')) {
        $route_id = get_the_ID();
        $route_data = wp_art_routes_get_route_data($route_id);
        
        if ($route_data) {
            $js_data = [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wp_art_routes_nonce'),
                'route_path' => $route_data['route_path'],
                'artworks' => $route_data['artworks'],
                'show_completed_route' => $route_data['show_completed_route'],
                'show_artwork_toasts' => $route_data['show_artwork_toasts'],
                'i18n' => [
                    'routeComplete' => __('Congratulations! You have completed this route!', 'wp-art-routes'),
                    'nearbyArtwork' => __('You are near an artwork!', 'wp-art-routes'),
                ],
            ];
            
            wp_localize_script('wp-art-routes-map-js', 'artRouteData', $js_data);
        }
    }
}
add_action('wp_enqueue_scripts', 'wp_art_routes_enqueue_scripts');

/**
 * Enqueue admin scripts and styles for the route editor and location picker
 */
function wp_art_routes_enqueue_admin_scripts($hook) {
    global $post;

    // Check if we need the route editor scripts
    $is_route_edit = $hook === 'post.php' || $hook === 'post-new.php';
    $is_route_type = isset($post) && $post->post_type === 'art_route';
    $is_artwork_type = isset($post) && $post->post_type === 'artwork';
    
    // Only load on relevant pages
    if (!$is_route_edit || (!$is_route_type && !$is_artwork_type)) {
        return;
    }

    // Leaflet CSS (shared)
    wp_enqueue_style(
        'wp-art-routes-admin-leaflet-css',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css',
        [],
        '1.9.4'
    );
    
    // Leaflet JS (shared)
    wp_enqueue_script(
        'wp-art-routes-admin-leaflet-js',
        'https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js',
        ['jquery'],
        '1.9.4',
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
            ['jquery', 'wp-art-routes-admin-leaflet-js'],
            WP_ART_ROUTES_VERSION,
            true
        );
        
        // Pass the modal HTML to JavaScript
        wp_localize_script(
            'wp-art-routes-editor-js',
            'routeEditorModalHTML',
            wp_art_routes_get_route_editor_modal_html()
        );
    }
    
    // Location picker (for artwork post type)
    if ($is_artwork_type) {
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
    }
}
add_action('admin_enqueue_scripts', 'wp_art_routes_enqueue_admin_scripts');

/**
 * Check if current page should display a route map
 */
function wp_art_routes_is_route_page() {
    // Check for shortcode in content
    global $post;
    
    if (is_singular() && isset($post->post_content) && has_shortcode($post->post_content, 'art_route_map')) {
        return true;
    }
    
    // Check for our template
    if (is_page_template('art-route-map-template.php')) {
        return true;
    }
    
    // Check if viewing a single art_route post type
    if (is_singular('art_route')) {
        return true;
    }
    
    // Default to false
    return false;
}

/**
 * Get the route editor modal HTML
 */
function wp_art_routes_get_route_editor_modal_html() {
    ob_start();
    ?>
    <div id="route-editor-modal" class="route-editor-modal" style="display: none;">
        <div class="route-editor-modal-content">
            <div class="route-editor-header">
                <h2><?php _e('Route Editor', 'wp-art-routes'); ?></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="route-editor-body">
                <div class="route-editor-controls">
                    <div class="control-group">
                        <button type="button" class="button" id="start-drawing"><?php _e('Start Drawing', 'wp-art-routes'); ?></button>
                        <button type="button" class="button" id="stop-drawing"><?php _e('Stop Drawing', 'wp-art-routes'); ?></button>
                        <button type="button" class="button" id="clear-route"><?php _e('Clear Route', 'wp-art-routes'); ?></button>
                    </div>
                    <div class="control-group">
                        <label for="route-search"><?php _e('Search Location:', 'wp-art-routes'); ?></label>
                        <input type="text" id="route-search" class="regular-text" placeholder="<?php _e('Enter location...', 'wp-art-routes'); ?>">
                        <button type="button" class="button" id="search-location"><?php _e('Search', 'wp-art-routes'); ?></button>
                    </div>
                    <div class="control-info">
                        <p id="drawing-instructions"><?php _e('Click "Start Drawing" then click on the map to create your route. Click "Stop Drawing" when finished.', 'wp-art-routes'); ?></p>
                        <p><span id="point-count">0</span> <?php _e('points in route', 'wp-art-routes'); ?></p>
                        <p><?php _e('Total distance:', 'wp-art-routes'); ?> <span id="route-distance">0</span> km</p>
                    </div>
                </div>
                <div id="route-editor-map"></div>
            </div>
            <div class="route-editor-footer">
                <button type="button" class="button button-secondary" id="cancel-route"><?php _e('Cancel', 'wp-art-routes'); ?></button>
                <button type="button" class="button button-primary" id="save-route"><?php _e('Save Route', 'wp-art-routes'); ?></button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Get the location picker modal HTML
 */
function wp_art_routes_get_location_picker_modal_html() {
    ob_start();
    ?>
    <div id="artwork-location-modal" class="location-picker-modal" style="display: none;">
        <div class="location-picker-modal-content">
            <div class="location-picker-header">
                <h2><?php _e('Pick Artwork Location', 'wp-art-routes'); ?></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="location-picker-body">
                <div class="location-picker-controls">
                    <div class="control-group">
                        <label for="location-search"><?php _e('Search Location:', 'wp-art-routes'); ?></label>
                        <input type="text" id="location-search" class="regular-text" placeholder="<?php _e('Enter location...', 'wp-art-routes'); ?>">
                        <button type="button" class="button" id="search-artwork-location"><?php _e('Search', 'wp-art-routes'); ?></button>
                    </div>
                    <div class="control-info">
                        <p><?php _e('Click on the map to select the artwork location.', 'wp-art-routes'); ?></p>
                        <p><?php _e('Selected coordinates:', 'wp-art-routes'); ?></p>
                        <p id="selected-coordinates">None</p>
                    </div>
                </div>
                <div id="location-picker-map"></div>
            </div>
            <div class="location-picker-footer">
                <button type="button" class="button button-secondary" id="cancel-location"><?php _e('Cancel', 'wp-art-routes'); ?></button>
                <button type="button" class="button button-primary" id="save-location"><?php _e('Save Location', 'wp-art-routes'); ?></button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Add the inline location map script for the artwork admin
 */
function wp_art_routes_add_location_map_script() {
    global $post;
    
    // Only add to artwork post type
    if (!$post || get_post_type($post->ID) !== 'artwork') {
        return;
    }
    
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Initialize small map for artwork location in the meta box
            window.locationMap = L.map('artwork_location_map').setView([52.1326, 5.2913], 8);
            
            // Add tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(window.locationMap);
            
            // Get saved coordinates
            var lat = $('#artwork_latitude').val();
            var lng = $('#artwork_longitude').val();
            
            // Add marker if coordinates exist
            if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                window.locationMarker = L.marker([lat, lng]).addTo(window.locationMap);
                window.locationMap.setView([lat, lng], 14);
            }
            
            // Handle map click events on the small map too
            window.locationMap.on('click', function(e) {
                // Update form fields
                $('#artwork_latitude').val(e.latlng.lat.toFixed(6));
                $('#artwork_longitude').val(e.latlng.lng.toFixed(6));
                
                // Update or add marker
                if (window.locationMarker) {
                    window.locationMarker.setLatLng(e.latlng);
                } else {
                    window.locationMarker = L.marker(e.latlng).addTo(window.locationMap);
                }
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'wp_art_routes_add_location_map_script');