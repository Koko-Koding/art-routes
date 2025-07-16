<?php
/**
 * Template Functions for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all art routes
 */
function wp_art_routes_get_routes() {
    return get_posts([
        'post_type' => 'art_route',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
}

/**
 * Get route data for a specific route
 */
function wp_art_routes_get_route_data($route_id) {
    $route = get_post($route_id);
    
    if (!$route || $route->post_type !== 'art_route') {
        return null;
    }
    
    $show_completed_route = get_post_meta($route_id, '_show_completed_route', true);
    $show_artwork_toasts = get_post_meta($route_id, '_show_artwork_toasts', true);
    
    // Default to true if not set
    if ($show_completed_route === '') {
        $show_completed_route = '1';
    }
    
    // Default to true if not set
    if ($show_artwork_toasts === '') {
        $show_artwork_toasts = '1';
    }
    
    $route_data = [
        'id' => $route->ID,
        'title' => $route->post_title,
        'description' => $route->post_content,
        'excerpt' => $route->post_excerpt,
        'image' => get_the_post_thumbnail_url($route->ID, 'large'),
        'length' => get_post_meta($route->ID, '_route_length', true),
        'duration' => get_post_meta($route->ID, '_route_duration', true),
        'type' => get_post_meta($route->ID, '_route_type', true),
        'show_completed_route' => $show_completed_route === '1',
        'show_artwork_toasts' => $show_artwork_toasts === '1',
    ];
    
    // Get route path
    $route_data['route_path'] = wp_art_routes_get_route_path($route_id);
    
    // Get artworks
    $route_data['artworks'] = wp_art_routes_get_route_artworks($route_id);
    
    // Get information points
    $route_data['information_points'] = wp_art_routes_get_route_information_points($route_id);
    
    return $route_data;
}

/**
 * Get route path for a specific route
 */
function wp_art_routes_get_route_path($route_id) {
    $path_string = get_post_meta($route_id, '_route_path', true);
    if (empty($path_string)) {
        return [];
    }
    $path = [];
    // Try to decode as JSON first
    $json = json_decode($path_string, true);
    if (is_array($json) && isset($json[0]['lat']) && isset($json[0]['lng'])) {
        // New format: array of objects with lat/lng and possibly extra metadata
        foreach ($json as $pt) {
            if (isset($pt['lat']) && isset($pt['lng']) && is_numeric($pt['lat']) && is_numeric($pt['lng'])) {
                // Keep all properties (lat, lng, is_start, is_end, notes, ...)
                $pt['lat'] = floatval($pt['lat']);
                $pt['lng'] = floatval($pt['lng']);
                $path[] = $pt;
            }
        }
        return $path;
    }
    // Fallback: old format (lines of lat, lng)
    $lines = explode("\n", $path_string);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) {
            continue;
        }
        $parts = explode(',', $line);
        if (count($parts) >= 2) {
            $lat = trim($parts[0]);
            $lng = trim($parts[1]);
            if (is_numeric($lat) && is_numeric($lng)) {
                $path[] = [ (float)$lat, (float)$lng ];
            }
        }
    }
    return $path;
}

/**
 * Get artworks for a specific route
 */
function wp_art_routes_get_route_artworks($route_id) {
    // Now return ALL artworks instead of filtering by route
    return wp_art_routes_get_all_artworks();
}

/**
 * Get all artworks (not tied to specific routes)
 */
function wp_art_routes_get_all_artworks() {
    // Query all published artworks
    $artworks = get_posts([
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'post_status' => 'publish', // Only get published artworks
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
    
    $result = [];
    
    foreach ($artworks as $artwork) {
        $latitude = get_post_meta($artwork->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($artwork->ID, '_artwork_longitude', true);
        
        // Ensure location data exists
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $artwork_data = [
                'id' => $artwork->ID,
                'title' => $artwork->post_title,
                'description' => $artwork->post_content,
                'image_url' => get_the_post_thumbnail_url($artwork->ID, 'large'),
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'number' => get_post_meta($artwork->ID, '_artwork_number', true),
                'location' => get_post_meta($artwork->ID, '_artwork_location', true),
                'permalink' => get_permalink($artwork->ID),
            ];
            
            // Get artist information
            $artist_ids = get_post_meta($artwork->ID, '_artwork_artist_ids', true);
            $artists = [];
            
            if (is_array($artist_ids) && !empty($artist_ids)) {
                foreach ($artist_ids as $artist_id) {
                    $artist_post = get_post($artist_id);
                    if ($artist_post) {
                        $post_type_obj = get_post_type_object($artist_post->post_type);
                        $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $artist_post->post_type;
                        
                        $artists[] = [
                            'id' => $artist_id,
                            'title' => $artist_post->post_title,
                            'url' => get_permalink($artist_id),
                            'post_type' => $artist_post->post_type,
                            'post_type_label' => $post_type_label
                        ];
                    }
                }
            }
            
            $artwork_data['artists'] = $artists;
            $result[] = $artwork_data;
        }
    }
    
    return $result;
}

/**
 * Get information points for a specific route
 */
function wp_art_routes_get_route_information_points($route_id) {
    // Now return ALL information points instead of filtering by route
    return wp_art_routes_get_all_information_points();
}

/**
 * Get all information points (not tied to specific routes)
 */
function wp_art_routes_get_all_information_points() {
    // Query all published information points
    $info_point_posts = get_posts([
        'post_type' => 'information_point',
        'posts_per_page' => -1,
        'post_status' => 'publish', // Only get published points
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $info_points = [];

    foreach ($info_point_posts as $info_post) {
        $latitude = get_post_meta($info_post->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($info_post->ID, '_artwork_longitude', true);

        // Ensure location data exists
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $info_points[] = [
                'id' => $info_post->ID,
                'title' => $info_post->post_title,
                'excerpt' => has_excerpt($info_post->ID) ? get_the_excerpt($info_post->ID) : wp_trim_words($info_post->post_content, 30, '...'),
                'image_url' => get_the_post_thumbnail_url($info_post->ID, 'medium'), // Use medium size for popup
                'permalink' => get_permalink($info_post->ID), // Link to the info point post itself
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'icon_url' => get_post_meta($info_post->ID, '_info_point_icon_url', true), // Custom icon URL
            ];
        }
    }

    return $info_points;
}

/**
 * Load a template from the plugin
 * 
 * First checks in the theme directory for an override
 * then falls back to the plugin template
 */
function wp_art_routes_get_template_part($template_name, $args = []) {
    if (!empty($args) && is_array($args)) {
        extract($args);
    }

    // Look for template in theme first
    $template = locate_template('wp-art-routes/' . $template_name . '.php');
    
    // If not found in theme, load from plugin
    if (empty($template)) {
        $template = WP_ART_ROUTES_PLUGIN_DIR . 'templates/' . $template_name . '.php';
    }
    
    if (file_exists($template)) {
        include $template;
    }
}

/**
 * Register the art route template with WordPress
 */
function wp_art_routes_register_templates($templates) {
    $templates['art-route-map-template.php'] = 'Art Route Map Template';
    return $templates;
}
add_filter('theme_page_templates', 'wp_art_routes_register_templates', 10, 1);

/**
 * Handle template redirection for our custom template
 */
function wp_art_routes_template_include($template) {
    // Return early if not a single page or not using our template
    if (!is_singular() || get_page_template_slug() !== 'art-route-map-template.php') {
        return $template;
    }
    
    // Look for template in theme directory first
    $located = locate_template('wp-art-routes/art-route-map-template.php');
    
    // If not found in theme, use plugin template
    if (empty($located)) {
        $located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/art-route-map-template.php';
    }
    
    if (file_exists($located)) {
        return $located;
    }
    
    // Fall back to original template if ours doesn't exist
    return $template;
}
add_filter('template_include', 'wp_art_routes_template_include');

/**
 * Handle template redirection for artwork posts
 */
function wp_art_routes_single_artwork_template($template) {
    // Only handle single artwork posts
    if (is_singular('artwork')) {
        // Look for template in theme directory first
        $located = locate_template('wp-art-routes/single-artwork.php');
        
        // If not found in theme, use plugin template
        if (empty($located)) {
            $located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/single-artwork.php';
        }
        
        if (file_exists($located)) {
            return $located;
        }
    }
    
    return $template;
}
add_filter('template_include', 'wp_art_routes_single_artwork_template', 99);

/**
 * Automatically append map to route content
 */
function wp_art_routes_append_map_to_route_content($content) {
    // Only apply on singular art_route pages
    if (!is_singular('art_route')) {
        return $content;
    }
    
    // Get the current post ID
    $route_id = get_the_ID();
    $route_data = wp_art_routes_get_route_data($route_id);
    
    if (!$route_data) {
        return $content;
    }
    
    // Generate the HTML for the map
    ob_start();
    ?>
    <div class="art-route-container">
        <div class="art-route-details">
            <div class="route-meta">
                <?php if (!empty($route_data['length'])) : ?>
                    <span class="route-length"><?php echo esc_html($route_data['length']); ?> km</span>
                <?php endif; ?>
                
                <?php if (!empty($route_data['duration'])) : ?>
                    <span class="route-duration"><?php echo esc_html($route_data['duration']); ?> <?php _e('minutes', 'wp-art-routes'); ?></span>
                <?php endif; ?>
                
                <?php if (!empty($route_data['type'])) : ?>
                    <span class="route-type">
                        <?php 
                        $route_types = [
                            'walking' => __('Walking route', 'wp-art-routes'),
                            'cycling' => __('Bicycle route', 'wp-art-routes'),
                            'wheelchair' => __('Wheelchair friendly', 'wp-art-routes'),
                            'children' => __('Child-friendly route', 'wp-art-routes'),
                        ];
                        echo isset($route_types[$route_data['type']]) ? $route_types[$route_data['type']] : $route_data['type']; 
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Map container -->
        <div id="art-route-map" class="art-route-map" style="height: 600px;"></div>
        
        <!-- Loading indicator -->
        <div id="map-loading" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php _e('Loading map...', 'wp-art-routes'); ?></p>
        </div>
        
        <!-- Location error message -->
        <div id="location-error" class="map-error" style="display: none;">
            <p></p>
            <button id="retry-location" class="button"><?php _e('Retry', 'wp-art-routes'); ?></button>
        </div>
        
        <!-- Route progress -->
        <div class="route-progress" style="display: none;">
            <h3><?php _e('Progress', 'wp-art-routes'); ?></h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%;"></div>
            </div>
            <p><?php _e('You have completed', 'wp-art-routes'); ?> <span id="progress-percentage">0</span>% <?php _e('of this route', 'wp-art-routes'); ?></p>
        </div>
        
        <!-- Artwork modal -->
        <div id="artwork-modal" class="artwork-modal" style="display: none;">
            <div class="artwork-modal-content">
                <span class="close-modal">&times;</span>
                <div class="artwork-image">
                    <img id="artwork-img" src="" alt="">
                </div>
                <div class="artwork-info">
                    <h3 id="artwork-title"></h3>
                    <div id="artwork-description"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $map_content = ob_get_clean();
    
    // Append the map to the content
    return $content . $map_content;
}
add_filter('the_content', 'wp_art_routes_append_map_to_route_content');