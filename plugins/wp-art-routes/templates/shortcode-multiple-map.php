<?php
/**
 * Template for the art_routes_map shortcode
 * Displays multiple routes on a single map with color coding
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Variables should be passed via wp_art_routes_get_template_part()
// $atts = shortcode attributes
// $routes = array of route data

// Check if routes data exists
if (empty($routes)) {
    echo '<p class="wp-art-routes-error">' . esc_html__('No routes found.', 'wp-art-routes') . '</p>';
    return;
}

// Define a set of distinctive colors for routes
$route_colors = [
    '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4', 
    '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990',
    '#dcbeff', '#9A6324', '#800000', '#aaffc3', '#808000',
    '#ffd8b1', '#000075', '#a9a9a9', '#000000', '#ffe119'
];

// Prepare data for JavaScript
$js_data = [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('wp_art_routes_nonce'),
    'routes' => [],
    'artworks' => wp_art_routes_get_all_artworks(), // Add global artworks
    'information_points' => wp_art_routes_get_all_information_points(), // Add global info points
    'colors' => $route_colors,
    'mapSettings' => [
        'center_lat' => !empty($atts['center_lat']) ? floatval($atts['center_lat']) : null,
        'center_lng' => !empty($atts['center_lng']) ? floatval($atts['center_lng']) : null,
        'zoom' => !empty($atts['zoom']) ? intval($atts['zoom']) : null,
    ],
];

// Process each route
foreach ($routes as $index => $route) {
    // Assign a color from our palette (cycle through if more routes than colors)
    $color = $route_colors[$index % count($route_colors)];
    
    // Get post permalink for the route
    $route_url = get_permalink($route['id']);
    
    // Add route data to JS array
    $js_data['routes'][] = [
        'id' => $route['id'],
        'title' => $route['title'],
        'description' => $route['description'],
        'excerpt' => $route['excerpt'],
        'route_path' => $route['route_path'],
        'artworks' => $route['artworks'],
        'color' => $color,
        'length' => $route['length'],
        'duration' => $route['duration'],
        'type' => $route['type'],
        'url' => $route_url,
    ];
}

// Define route type icons and labels
$route_types = [
    'walking' => [
        'icon' => 'dashicons dashicons-admin-users',
        'label' => __('Walking route', 'wp-art-routes')
    ],
    'cycling' => [
        'icon' => 'dashicons dashicons-controls-repeat',
        'label' => __('Bicycle route', 'wp-art-routes')
    ],
    'wheelchair' => [
        'icon' => 'dashicons dashicons-universal-access',
        'label' => __('Wheelchair friendly', 'wp-art-routes')
    ],
    'children' => [
        'icon' => 'dashicons dashicons-buddicons-groups',
        'label' => __('Child-friendly route', 'wp-art-routes')
    ],
];

// Generate a unique ID for this map instance
$map_id = 'art-routes-map-' . uniqid();
?>

<div class="art-routes-container">
    <?php if ($atts['show_legend']): ?>
        <div class="art-routes-legend">
            <h3><?php esc_html_e('Routes', 'wp-art-routes'); ?></h3>
            <ul class="route-list">
                <?php foreach ($js_data['routes'] as $index => $route): ?>
                    <li class="route-item">
                        <div class="route-header">
                            <div class="route-title-container">
                                <span class="route-color-indicator" style="background-color: <?php echo esc_attr($route['color']); ?>;"></span>
                                <span class="route-title"><?php echo esc_html($route['title']); ?></span>
                            </div>
                            <a href="<?php echo esc_url($route['url']); ?>" class="route-link" title="<?php esc_attr_e('View route details', 'wp-art-routes'); ?>">
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </a>
                        </div>
                        
                        <div class="route-meta multiple">
                            <?php if (!empty($route['type']) && isset($route_types[$route['type']])): ?>
                                <span class="route-type">
                                    <span class="<?php echo esc_attr($route_types[$route['type']]['icon']); ?>"></span>
                                    <?php echo esc_html($route_types[$route['type']]['label']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($route['length'])): ?>
                                <span class="route-length">
                                    <span class="dashicons dashicons-location"></span>
                                    <?php echo esc_html(wp_art_routes_format_length($route['length'])); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($route['duration'])): ?>
                                <span class="route-duration">
                                    <span class="dashicons dashicons-clock"></span>
                                    <?php echo esc_html(wp_art_routes_format_duration($route['duration'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($atts['show_description']): ?>
                            <div class="route-description">
                                <?php 
                                if (!empty($route['excerpt'])) {
                                    echo wp_kses_post($route['excerpt']);
                                } else {
                                    // If no excerpt, create one from content
                                    echo wp_kses_post(wp_trim_words($route['description'], 25, '...'));
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <button class="zoom-to-route-button" data-route-index="<?php echo esc_attr($index); ?>">
                            <span class="dashicons dashicons-search"></span>
                            <?php esc_html_e('Zoom to Route', 'wp-art-routes'); ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="art-routes-map-container">
        <?php if ($atts['show_title']): ?>
            <h2 class="art-routes-map-title"><?php esc_html_e('Art Routes Map', 'wp-art-routes'); ?></h2>
        <?php endif; ?>
        
        <!-- Map container -->
        <div id="<?php echo esc_attr($map_id); ?>" class="art-routes-map"<?php echo !empty($atts['height']) ? ' style="height: ' . esc_attr($atts['height']) . ';"' : ''; ?>></div>
        
        <!-- Loading indicator -->
        <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php esc_html_e('Loading map...', 'wp-art-routes'); ?></p>
        </div>
    </div>
</div>


<?php
// Enqueue multiple routes map assets
wp_enqueue_style('wp-art-routes-edition-map-shortcode-css');
wp_enqueue_script('wp-art-routes-multiple-routes-map-js');

// Pass per-instance data via inline script
wp_add_inline_script('wp-art-routes-multiple-routes-map-js',
    'var wpArtRoutesMultiMapInstances = wpArtRoutesMultiMapInstances || [];' .
    'wpArtRoutesMultiMapInstances.push(' . wp_json_encode([
        'mapId'   => $map_id,
        'mapData' => $js_data,
    ]) . ');',
    'before'
);
?>
