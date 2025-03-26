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
    echo '<p class="wp-art-routes-error">' . __('Route not found.', 'wp-art-routes') . '</p>';
    return;
}

// Set up HTML element styles
$container_style = '';
if (!empty($atts['height'])) {
    $container_style = 'style="height: ' . esc_attr($atts['height']) . ';"';
}

// Prepare route data for JavaScript
$js_data = [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('wp_art_routes_nonce'),
    'route_path' => $route['route_path'],
    'artworks' => $route['artworks'],
    'show_completed_route' => $route['show_completed_route'],
    'show_artwork_toasts' => $route['show_artwork_toasts'],
    'i18n' => [
        'routeComplete' => __('Congratulations! You have completed this route!', 'wp-art-routes'),
        'nearbyArtwork' => __('You are near an artwork!', 'wp-art-routes'),
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
                <span class="route-length"><?php echo esc_html($route['length']); ?> km</span>
            <?php endif; ?>
            
            <?php if (!empty($route['duration'])) : ?>
                <span class="route-duration"><?php echo esc_html($route['duration']); ?> <?php _e('minutes', 'wp-art-routes'); ?></span>
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
                    echo isset($route_types[$route['type']]) ? $route_types[$route['type']] : $route['type']; 
                    ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Map container -->
    <div id="art-route-map" class="art-route-map" <?php echo $container_style; ?>></div>
    
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
                <a id="artwork-artist-link" class="artist-link" href=""><?php _e('View Artist', 'wp-art-routes'); ?></a>
            </div>
        </div>
    </div>
</div>

<!-- Map data passed to JavaScript -->
<script type="text/javascript">
    var artRouteData = <?php echo json_encode($js_data); ?>;
</script>