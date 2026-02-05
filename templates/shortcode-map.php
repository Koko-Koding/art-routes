<?php
/**
 * Template for the art_route_map shortcode
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Variables should be passed via wp_art_routes_get_template_part()
// $atts = shortcode attributes
// $route = route data

// Check if route data exists
if (empty($route)) {
    echo '<p class="wp-art-routes-error">' . esc_html__('Route not found.', 'wp-art-routes') . '</p>';
    return;
}

// Prepare route data for JavaScript
$js_data = [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('wp_art_routes_nonce'),
    'route_path' => $route['route_path'],
    'artworks' => $route['artworks'],
    'information_points' => $route['information_points'],
    'show_completed_route' => $route['show_completed_route'],
    'show_artwork_toasts' => $route['show_artwork_toasts'],
    'i18n' => [
        'routeComplete' => __('Congratulations! You have completed this route!', 'wp-art-routes'),
        'nearbyArtwork' => __('You are near an artwork!', 'wp-art-routes'),
        'readMore' => __('Read more', 'wp-art-routes'),
        'artist' => __('Artist', 'wp-art-routes'),
        'artists' => __('Artists', 'wp-art-routes'),
        'startPoint' => __('Start Point', 'wp-art-routes'),
        'endPoint' => __('End Point', 'wp-art-routes'),
        'goToMyLocation' => __('Go to My Location', 'wp-art-routes'),
        'goToRoute' => __('Go to Route', 'wp-art-routes'),
        'gettingLocation' => __('Getting location...', 'wp-art-routes'),
        'locationError' => __('Could not get your location', 'wp-art-routes'),
        'locationPermissionDenied' => __('Location access denied. Please allow location access in your browser.', 'wp-art-routes'),
        'locationUnavailable' => __('Location information is unavailable.', 'wp-art-routes'),
        'locationTimeout' => __('Location request timed out.', 'wp-art-routes'),
        'geolocationNotSupported' => __('Geolocation is not supported by this browser.', 'wp-art-routes'),
    ],
];
?>

<div class="art-route-container">
    <?php if ($atts['show_title']) : ?>
        <h2 class="art-route-title"><?php echo esc_html($route['title']); ?></h2>
    <?php endif; ?>
    
    <?php if ($atts['show_description'] && !empty($route['description'])) : ?>
        <div class="art-route-description">
            <?php echo wp_kses_post($route['description']); ?>
        </div>
    <?php endif; ?>
    
    <div class="art-route-details">
        <div class="route-meta">
            <?php if (!empty($route['length'])) : ?>
                <span class="route-length">
                    <?php echo esc_html(wp_art_routes_format_length($route['length'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if (!empty($route['duration'])) : ?>
                <span class="route-duration">
                    <?php echo esc_html(wp_art_routes_format_duration($route['duration'])); ?>
                </span>
            <?php endif; ?>
            
            <?php if (!empty($route['type'])) : ?>
                <span class="route-type">
                    <?php 
                    $route_types = [
                        'walking' => __('Walking route', 'wp-art-routes'),
                        'cycling' => __('Bicycle route', 'wp-art-routes'),
                        'wheelchair' => __('Wheelchair friendly', 'wp-art-routes'),
                        'children' => __('Child-friendly route', 'wp-art-routes'),
                    ];
                    echo isset($route_types[$route['type']]) ? esc_html($route_types[$route['type']]) : esc_html($route['type']); 
                    ?>
                </span>
            <?php endif; ?>
        </div>
        
        <!-- GPX Export Button -->
        <div class="route-actions">
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=wp_art_routes_export_gpx&route_id=' . $route['id']), 'wp_art_routes_export_gpx')); ?>" 
               class="gpx-export-button" 
               download="<?php echo esc_attr(sanitize_file_name($route['title'])); ?>.gpx">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e('Export to GPX', 'wp-art-routes'); ?>
            </a>
        </div>
    </div>
    
    <!-- Map container -->
    <div id="art-route-map" class="art-route-map"<?php echo !empty($atts['height']) ? ' style="height: ' . esc_attr($atts['height']) . ';"' : ''; ?>></div>
    
    <?php 
    // Display map controls using the reusable template tag
    wp_art_routes_display_map_controls(); 
    ?>
    
    <!-- Loading indicator -->
    <div id="map-loading" class="map-loading" style="display: none;">
        <div class="spinner"></div>
        <p><?php esc_html_e('Loading map...', 'wp-art-routes'); ?></p>
    </div>
    
    <!-- Location error message -->
    <div id="location-error" class="map-error" style="display: none;">
        <p></p>
        <button id="retry-location" class="button"><?php esc_html_e('Retry', 'wp-art-routes'); ?></button>
    </div>
    
    <!-- Route progress -->
    <div class="route-progress" style="display: none;">
        <h3><?php esc_html_e('Progress', 'wp-art-routes'); ?></h3>
        <div class="progress-bar">
            <div class="progress-fill" style="width: 0%;"></div>
        </div>
        <p><?php esc_html_e('You have completed', 'wp-art-routes'); ?> <span id="progress-percentage">0</span>% <?php esc_html_e('of this route', 'wp-art-routes'); ?></p>
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

<!-- Map data passed to JavaScript -->
<script type="text/javascript">
    var artRouteData = <?php echo json_encode($js_data); ?>;
</script>