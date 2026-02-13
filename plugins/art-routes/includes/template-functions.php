<?php

/**
 * Template Functions for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get the icon URL for a location (artwork) with full fallback chain.
 *
 * Fallback order: per-item icon → edition default icon → global default icon → empty string.
 *
 * @param int      $post_id    The artwork post ID.
 * @param int|null $edition_id The edition ID (if known). If null, will be looked up from post meta.
 * @return string The icon URL or empty string if no icon configured.
 */
function art_routes_get_location_icon_url($post_id, $edition_id = null)
{
    // 1. Per-item icon
    $icon_filename = get_post_meta($post_id, '_artwork_icon', true);
    if (!empty($icon_filename)) {
        return art_routes_get_icon_url($icon_filename);
    }

    // 2. Edition default icon
    if (is_null($edition_id)) {
        $edition_id = get_post_meta($post_id, '_edition_id', true);
    }
    if (!empty($edition_id)) {
        $edition_icon = get_post_meta($edition_id, '_edition_default_location_icon', true);
        if (!empty($edition_icon)) {
            return art_routes_get_icon_url($edition_icon);
        }
    }

    // 3. Global default icon
    $global_icon = get_option('art_routes_default_location_icon', '');
    if (!empty($global_icon)) {
        return art_routes_get_icon_url($global_icon);
    }

    return '';
}

/**
 * Get the icon URL for an information point with full fallback chain.
 *
 * Fallback order: per-item icon → legacy icon_url field → default info icon.
 *
 * @param int $post_id The information point post ID.
 * @return string The icon URL (always returns a value due to hardcoded default).
 */
function art_routes_get_info_point_icon_url($post_id)
{
    // 1. New icon field
    $icon_filename = get_post_meta($post_id, '_info_point_icon', true);
    if (!empty($icon_filename)) {
        return art_routes_get_icon_url($icon_filename);
    }

    // 2. Legacy icon_url field (backward compatibility)
    $legacy_url = get_post_meta($post_id, '_info_point_icon_url', true);
    if (!empty($legacy_url)) {
        return $legacy_url;
    }

    // 3. Hardcoded default
    return art_routes_get_icon_url(art_routes_get_default_info_icon());
}

/**
 * Get all art routes
 */
function art_routes_get_routes()
{
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
function art_routes_get_route_data($route_id)
{
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
    $route_data['route_path'] = art_routes_get_route_path($route_id);

    // Get artworks
    $route_data['artworks'] = art_routes_get_route_artworks($route_id);

    // Get information points
    $route_data['information_points'] = art_routes_get_route_information_points($route_id);

    return $route_data;
}

/**
 * Get route path for a specific route
 */
function art_routes_get_route_path($route_id)
{
    $path_string = get_post_meta($route_id, '_route_path', true);
    if (empty($path_string)) {
        return [];
    }
    $path = [];
    // Try to decode as JSON first
    $json = json_decode($path_string, true);
    if (is_array($json) && !empty($json)) {
        // Check first element to determine format
        $first = $json[0];
        if (isset($first['lat']) && isset($first['lng'])) {
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
        } elseif (is_array($first) && count($first) >= 2 && is_numeric($first[0]) && is_numeric($first[1])) {
            // Legacy array format: [[lat, lng], [lat, lng], ...]
            // Convert to object format for consistency
            foreach ($json as $pt) {
                if (is_array($pt) && count($pt) >= 2 && is_numeric($pt[0]) && is_numeric($pt[1])) {
                    $path[] = ['lat' => floatval($pt[0]), 'lng' => floatval($pt[1])];
                }
            }
            return $path;
        }
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
                $path[] = ['lat' => (float)$lat, 'lng' => (float)$lng];
            }
        }
    }
    return $path;
}

/**
 * Get artworks for a specific route
 */
function art_routes_get_route_artworks($route_id)
{
    // Now return ALL artworks instead of filtering by route
    return art_routes_get_all_artworks();
}

/**
 * Get all artworks (not tied to specific routes)
 */
function art_routes_get_all_artworks()
{
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
            $icon_url = art_routes_get_location_icon_url($artwork->ID);

            $artwork_data = [
                'id' => $artwork->ID,
                'title' => $artwork->post_title,
                'description' => $artwork->post_content,
                'excerpt' => $artwork->post_excerpt,
                'image_url' => get_the_post_thumbnail_url($artwork->ID, 'large'),
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'number' => get_post_meta($artwork->ID, '_artwork_number', true),
                'location' => get_post_meta($artwork->ID, '_artwork_location', true),
                'permalink' => get_permalink($artwork->ID),
                'icon_url' => $icon_url ? esc_url($icon_url) : '',
                'wheelchair_accessible' => get_post_meta($artwork->ID, '_wheelchair_accessible', true),
                'stroller_accessible' => get_post_meta($artwork->ID, '_stroller_accessible', true),
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
function art_routes_get_route_information_points($route_id)
{
    // Now return ALL information points instead of filtering by route
    return art_routes_get_all_information_points();
}

/**
 * Get all information points (not tied to specific routes)
 */
function art_routes_get_all_information_points()
{
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
                'image_url' => get_the_post_thumbnail_url($info_post->ID, 'medium'),
                'permalink' => get_permalink($info_post->ID),
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'icon_url' => art_routes_get_info_point_icon_url($info_post->ID),
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
function art_routes_get_template_part($template_name, $args = [])
{
    if (!empty($args) && is_array($args)) {
        extract($args);
    }

    // Look for template in theme first
    $template = locate_template('art-routes/' . $template_name . '.php');

    // If not found in theme, load from plugin
    if (empty($template)) {
        $template = ART_ROUTES_PLUGIN_DIR . 'templates/' . $template_name . '.php';
    }

    if (file_exists($template)) {
        include $template;
    }
}

/**
 * Register the art route template with WordPress
 */
function art_routes_register_templates($templates)
{
    $templates['art-route-map-template.php'] = 'Art Route Map Template';
    return $templates;
}
add_filter('theme_page_templates', 'art_routes_register_templates', 10, 1);

/**
 * Handle template redirection for our custom template
 */
function art_routes_template_include($template)
{
    // Return early if not a single page or not using our template
    if (!is_singular() || get_page_template_slug() !== 'art-route-map-template.php') {
        return $template;
    }

    // Look for template in theme directory first
    $located = locate_template('art-routes/art-route-map-template.php');

    // If not found in theme, use plugin template
    if (empty($located)) {
        $located = ART_ROUTES_PLUGIN_DIR . 'templates/art-route-map-template.php';
    }

    if (file_exists($located)) {
        return $located;
    }

    // Fall back to original template if ours doesn't exist
    return $template;
}
add_filter('template_include', 'art_routes_template_include');

/**
 * Handle template redirection for artwork posts
 */
function art_routes_single_artwork_template($template)
{
    // Only handle single artwork posts
    if (is_singular('artwork')) {
        // Look for template in theme directory first
        $located = locate_template('art-routes/single-artwork.php');

        // If not found in theme, use plugin template
        if (empty($located)) {
            $located = ART_ROUTES_PLUGIN_DIR . 'templates/single-artwork.php';
        }

        if (file_exists($located)) {
            return $located;
        }
    }

    return $template;
}
add_filter('template_include', 'art_routes_single_artwork_template', 99);

/**
 * Automatically append map to route content
 */
function art_routes_append_map_to_route_content($content)
{
    // Only apply on singular art_route pages
    if (!is_singular('art_route')) {
        return $content;
    }

    // Get the current post ID
    $route_id = get_the_ID();
    $route_data = art_routes_get_route_data($route_id);

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
                    <span class="route-length">
                        <?php echo esc_html(art_routes_format_length($route_data['length'])); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($route_data['duration'])) : ?>
                    <span class="route-duration">
                        <?php echo esc_html(art_routes_format_duration($route_data['duration'])); ?>
                    </span>
                <?php endif; ?>

                <?php if (!empty($route_data['type'])) : ?>
                    <span class="route-type">
                        <?php
                        $route_types = [
                            'walking' => __('Walking route', 'art-routes'),
                            'cycling' => __('Bicycle route', 'art-routes'),
                            'wheelchair' => __('Wheelchair friendly', 'art-routes'),
                            'children' => __('Child-friendly route', 'art-routes'),
                        ];
                        echo esc_html(isset($route_types[$route_data['type']]) ? $route_types[$route_data['type']] : $route_data['type']);
                        ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- GPX Export Button -->
            <div class="route-actions">
                <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=art_routes_export_gpx&route_id=' . $route_id), 'art_routes_export_gpx')); ?>" 
                   class="gpx-export-button" 
                   download="<?php echo esc_attr(sanitize_file_name($route_data['title'])); ?>.gpx">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Export to GPX', 'art-routes'); ?>
                </a>
            </div>
        </div>

        <!-- Map container -->
        <div id="art-route-map" class="art-route-map" style="height: 600px;"></div>

        <?php
        // Display map controls using the reusable template tag
        art_routes_display_map_controls();
        ?>

        <!-- Loading indicator -->
        <div id="map-loading" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php esc_html_e('Loading map...', 'art-routes'); ?></p>
        </div>

        <!-- Location error message -->
        <div id="location-error" class="map-error" style="display: none;">
            <p></p>
            <button id="retry-location" class="button"><?php esc_html_e('Retry', 'art-routes'); ?></button>
        </div>

        <!-- Route progress -->
        <div class="route-progress" style="display: none;">
            <h3><?php esc_html_e('Progress', 'art-routes'); ?></h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%;"></div>
            </div>
            <p><?php esc_html_e('You have completed', 'art-routes'); ?> <span id="progress-percentage">0</span>% <?php esc_html_e('of this route', 'art-routes'); ?></p>
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
add_filter('the_content', 'art_routes_append_map_to_route_content');

/**
 * Display map visibility toggle controls
 * 
 * @param array $options Configuration options for the controls
 */
function art_routes_display_map_controls($options = [])
{
    // Default options
    $defaults = [
        'show_artworks' => true,
        'show_info_points' => true,
        'show_route' => true,
        'show_user_location' => true,
        'show_navigation' => true,
        'artworks_checked' => true,
        'info_points_checked' => true,
        'route_checked' => true,
        'user_location_checked' => true,
        'css_class' => 'map-controls',
        'title' => __('Map Display Options', 'art-routes'),
    ];

    $options = wp_parse_args($options, $defaults);

    // Don't display if no controls are enabled
    if (
        !$options['show_artworks'] && !$options['show_info_points'] &&
        !$options['show_route'] && !$options['show_user_location'] && !$options['show_navigation']
    ) {
        return;
    }

?>
    <!-- Map Controls -->
    <div class="<?php echo esc_attr($options['css_class']); ?>">
        <h4 class="map-controls-title"><?php echo esc_html($options['title']); ?></h4>
        <div class="map-controls-grid">
            <?php if ($options['show_artworks']) : ?>
                <label class="map-control-item">
                    <input type="checkbox" id="toggle-artworks" <?php checked($options['artworks_checked']); ?>>
                    <span class="map-control-icon dashicons dashicons-art"></span>
                    <span class="map-control-label"><?php esc_html_e('Show Artworks', 'art-routes'); ?></span>
                </label>
            <?php endif; ?>

            <?php if ($options['show_info_points']) : ?>
                <label class="map-control-item">
                    <input type="checkbox" id="toggle-info-points" <?php checked($options['info_points_checked']); ?>>
                    <span class="map-control-icon dashicons dashicons-info"></span>
                    <span class="map-control-label"><?php esc_html_e('Show Information Points', 'art-routes'); ?></span>
                </label>
            <?php endif; ?>

            <?php if ($options['show_route']) : ?>
                <label class="map-control-item">
                    <input type="checkbox" id="toggle-route" <?php checked($options['route_checked']); ?>>
                    <span class="map-control-icon dashicons dashicons-chart-line"></span>
                    <span class="map-control-label"><?php esc_html_e('Show Route', 'art-routes'); ?></span>
                </label>
            <?php endif; ?>

            <?php if ($options['show_user_location']) : ?>
                <label class="map-control-item">
                    <input type="checkbox" id="toggle-user-location" <?php checked($options['user_location_checked']); ?>>
                    <span class="map-control-icon dashicons dashicons-location"></span>
                    <span class="map-control-label"><?php esc_html_e('Show My Location', 'art-routes'); ?></span>
                </label>
            <?php endif; ?>
        </div>

        <?php if ($options['show_navigation']) : ?>
            <div class="map-navigation-buttons">
                <button type="button" id="go-to-my-location" class="map-nav-button">
                    <span class="map-control-icon dashicons dashicons-location-alt"></span>
                    <span class="map-control-label"><?php esc_html_e('Go to My Location', 'art-routes'); ?></span>
                </button>

                <button type="button" id="go-to-route" class="map-nav-button">
                    <span class="map-control-icon dashicons dashicons-admin-site"></span>
                    <span class="map-control-label"><?php esc_html_e('Go to Route', 'art-routes'); ?></span>
                </button>
            </div>
        <?php endif; ?>
    </div>
<?php
}

/**
 * Format duration in minutes to a readable string (e.g. "2 hours 23 minutes")
 */
function art_routes_format_duration($minutes)
{
    $minutes = intval($minutes);
    if ($minutes < 1) return __('Less than a minute', 'art-routes');
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    $parts = array();
    if ($hours > 0) {
        /* translators: %d: number of hours */
        $parts[] = sprintf(_n('%d hour', '%d hours', $hours, 'art-routes'), $hours);
    }
    if ($mins > 0) {
        /* translators: %d: number of minutes */
        $parts[] = sprintf(_n('%d minute', '%d minutes', $mins, 'art-routes'), $mins);
    }
    return implode(' ', $parts);
}

/**
 * Format route length in kilometers to a consistent string (e.g. "3.2 km")
 */
function art_routes_format_length($km)
{
    $km = floatval($km);
    return number_format(round($km, 1), 1) . ' km';
}

/**
 * Get all routes for a specific edition
 *
 * @param int $edition_id The edition ID to filter by
 * @return array Array of route data
 */
function art_routes_get_edition_routes($edition_id)
{
    if (!$edition_id) {
        return [];
    }

    $routes = get_posts([
        'post_type' => 'art_route',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $result = [];
    foreach ($routes as $route) {
        $result[] = art_routes_get_route_data($route->ID);
    }

    return $result;
}

/**
 * Get all artworks for a specific edition
 *
 * @param int $edition_id The edition ID to filter by
 * @return array Array of artwork data with id, title, description, excerpt, image_url,
 *               latitude, longitude, number, location, permalink, icon_url,
 *               wheelchair_accessible, stroller_accessible, artists
 */
function art_routes_get_edition_artworks($edition_id)
{
    if (!$edition_id) {
        return [];
    }

    // Query artworks filtered by edition
    $artworks = get_posts([
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $result = [];

    foreach ($artworks as $artwork) {
        $latitude = get_post_meta($artwork->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($artwork->ID, '_artwork_longitude', true);

        // Ensure location data exists
        if (is_numeric($latitude) && is_numeric($longitude)) {
            $icon_url = art_routes_get_location_icon_url($artwork->ID, $edition_id);

            $artwork_data = [
                'id' => $artwork->ID,
                'title' => $artwork->post_title,
                'description' => $artwork->post_content,
                'excerpt' => $artwork->post_excerpt,
                'image_url' => get_the_post_thumbnail_url($artwork->ID, 'large'),
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'number' => get_post_meta($artwork->ID, '_artwork_number', true),
                'location' => get_post_meta($artwork->ID, '_artwork_location', true),
                'permalink' => get_permalink($artwork->ID),
                'icon_url' => $icon_url ? esc_url($icon_url) : '',
                'wheelchair_accessible' => get_post_meta($artwork->ID, '_wheelchair_accessible', true),
                'stroller_accessible' => get_post_meta($artwork->ID, '_stroller_accessible', true),
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
 * Get all information points for a specific edition
 *
 * @param int $edition_id The edition ID to filter by
 * @return array Array of information point data with id, title, excerpt, image_url,
 *               permalink, latitude, longitude, icon_url
 */
function art_routes_get_edition_information_points($edition_id)
{
    if (!$edition_id) {
        return [];
    }

    // Query information points filtered by edition
    $info_point_posts = get_posts([
        'post_type' => 'information_point',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
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
                'image_url' => get_the_post_thumbnail_url($info_post->ID, 'medium'),
                'permalink' => get_permalink($info_post->ID),
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'icon_url' => art_routes_get_info_point_icon_url($info_post->ID),
            ];
        }
    }

    return $info_points;
}

/**
 * Template include filter for Edition single pages
 *
 * Loads the single-edition.php template from the plugin or theme override.
 *
 * @param string $template The path of the template to include
 * @return string The path of the template to include
 */
function art_routes_single_edition_template($template)
{
    if (is_singular('edition')) {
        // Look for template in theme directory first
        $located = locate_template('art-routes/single-edition.php');

        // If not found in theme, use plugin template
        if (empty($located)) {
            $located = ART_ROUTES_PLUGIN_DIR . 'templates/single-edition.php';
        }

        if (file_exists($located)) {
            return $located;
        }
    }

    return $template;
}
add_filter('template_include', 'art_routes_single_edition_template', 99);

/**
 * Prepare map data for Edition single page
 *
 * Formats routes, artworks, and info points data for JavaScript map initialization.
 *
 * @param int   $edition_id  The edition ID
 * @param array $routes      Array of route data from art_routes_get_edition_routes()
 * @param array $artworks    Array of artwork data from art_routes_get_edition_artworks()
 * @param array $info_points Array of info point data from art_routes_get_edition_information_points()
 * @return array Formatted data for JavaScript
 */
function art_routes_prepare_edition_map_data($edition_id, $routes, $artworks, $info_points)
{
    $map_data = [
        'edition_id' => $edition_id,
        'routes' => [],
        'artworks' => $artworks,
        'info_points' => $info_points,
    ];

    // Process routes
    if (!empty($routes)) {
        foreach ($routes as $route) {
            $map_data['routes'][] = [
                'id' => $route['id'],
                'title' => $route['title'],
                'route_path' => $route['route_path'],
                'length' => $route['length'],
                'duration' => $route['duration'],
                'type' => $route['type'],
                'url' => get_permalink($route['id']),
            ];
        }
    }

    return $map_data;
}
