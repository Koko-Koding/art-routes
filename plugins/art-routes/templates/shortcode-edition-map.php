<?php
/**
 * Template for the edition_map shortcode
 * Displays an interactive map with routes, locations, and info points from an edition.
 *
 * @package WP Art Routes
 *
 * Variables passed via art_routes_get_template_part():
 * @var array $atts        Shortcode attributes
 * @var int   $edition_id  Edition ID
 * @var array $edition     Edition data from art_routes_get_edition_data()
 * @var array $routes      Array of route data from art_routes_get_edition_routes()
 * @var array $artworks    Array of artwork data from art_routes_get_edition_artworks()
 * @var array $info_points Array of info point data from art_routes_get_edition_information_points()
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Generate a unique ID for this map instance
$map_id = 'edition-map-' . $edition_id . '-' . uniqid();

// Define a set of distinctive colors for routes
$route_colors = [
    '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4',
    '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990',
    '#dcbeff', '#9A6324', '#800000', '#aaffc3', '#808000',
    '#ffd8b1', '#000075', '#a9a9a9', '#000000', '#ffe119'
];

// Prepare data for JavaScript
$js_data = art_routes_prepare_edition_map_data($edition_id, $routes, $artworks, $info_points);
$js_data['colors'] = $route_colors;

// Get terminology labels for this edition
$route_label_singular = art_routes_label('route', false, $edition_id);
$route_label_plural = art_routes_label('route', true, $edition_id);
$location_label_plural = art_routes_label('location', true, $edition_id);
$info_point_label_plural = art_routes_label('info_point', true, $edition_id);

// Define route type icons and labels
$route_types = [
    'walking' => [
        'icon' => 'dashicons dashicons-admin-users',
        'label' => __('Walking route', 'art-routes')
    ],
    'cycling' => [
        'icon' => 'dashicons dashicons-controls-repeat',
        'label' => __('Bicycle route', 'art-routes')
    ],
    'wheelchair' => [
        'icon' => 'dashicons dashicons-universal-access',
        'label' => __('Wheelchair friendly', 'art-routes')
    ],
    'children' => [
        'icon' => 'dashicons dashicons-buddicons-groups',
        'label' => __('Child-friendly route', 'art-routes')
    ],
];

?>

<div class="edition-map-container">
    <?php if ($atts['show_legend'] && !empty($routes)) : ?>
        <div class="edition-map-legend">
            <h3><?php echo esc_html($route_label_plural); ?></h3>

            <?php if (count($routes) > 1) : ?>
                <button class="zoom-to-all-routes-button edition-map-zoom-all" data-map-id="<?php echo esc_attr($map_id); ?>">
                    <span class="dashicons dashicons-admin-site"></span>
                    <?php
                    /* translators: %s: route label plural */
                    printf(esc_html__('Show All %s', 'art-routes'), esc_html($route_label_plural));
                    ?>
                </button>
            <?php endif; ?>

            <ul class="edition-route-list">
                <?php foreach ($js_data['routes'] as $index => $route) :
                    $color = $route_colors[$index % count($route_colors)];
                ?>
                    <li class="edition-route-item">
                        <div class="edition-route-header">
                            <div class="edition-route-title-container">
                                <span class="edition-route-color-indicator" style="background-color: <?php echo esc_attr($color); ?>;"></span>
                                <span class="edition-route-title"><?php echo esc_html($route['title']); ?></span>
                            </div>
                            <a href="<?php echo esc_url($route['url']); ?>" class="edition-route-link" title="<?php esc_attr_e('View route details', 'art-routes'); ?>">
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </a>
                        </div>

                        <div class="edition-route-meta">
                            <?php if (!empty($route['type']) && isset($route_types[$route['type']])) : ?>
                                <span class="edition-route-type">
                                    <span class="<?php echo esc_attr($route_types[$route['type']]['icon']); ?>"></span>
                                    <?php echo esc_html($route_types[$route['type']]['label']); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($route['length'])) : ?>
                                <span class="edition-route-length">
                                    <span class="dashicons dashicons-location"></span>
                                    <?php echo esc_html(art_routes_format_length($route['length'])); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($route['duration'])) : ?>
                                <span class="edition-route-duration">
                                    <span class="dashicons dashicons-clock"></span>
                                    <?php echo esc_html(art_routes_format_duration($route['duration'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <button class="edition-zoom-to-route-button" data-route-index="<?php echo esc_attr($index); ?>" data-map-id="<?php echo esc_attr($map_id); ?>">
                            <span class="dashicons dashicons-search"></span>
                            <?php
                            /* translators: %s: route label singular */
                            printf(esc_html__('Zoom to %s', 'art-routes'), esc_html($route_label_singular));
                            ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="edition-map-wrapper">
        <!-- Map container -->
        <div id="<?php echo esc_attr($map_id); ?>" class="edition-map"<?php echo !empty($atts['height']) ? ' style="height: ' . esc_attr($atts['height']) . ';"' : ''; ?>></div>

        <!-- Loading indicator -->
        <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php esc_html_e('Loading map...', 'art-routes'); ?></p>
        </div>
    </div>

    <?php if ($atts['show_legend']) : ?>
        <!-- Map Controls -->
        <div class="edition-map-controls">
            <h4 class="edition-map-controls-title"><?php esc_html_e('Map Display Options', 'art-routes'); ?></h4>
            <div class="edition-map-controls-grid">
                <?php if (!empty($routes)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-routes" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-chart-line"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: route label plural */
                            printf(esc_html__('Show %s', 'art-routes'), esc_html($route_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>

                <?php if ($atts['show_locations'] && !empty($artworks)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-locations" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-art"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: location label plural */
                            printf(esc_html__('Show %s', 'art-routes'), esc_html($location_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>

                <?php if ($atts['show_info_points'] && !empty($info_points)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-info-points" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-info"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: info point label plural */
                            printf(esc_html__('Show %s', 'art-routes'), esc_html($info_point_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
// Enqueue edition map assets
wp_enqueue_style('art-routes-edition-map-shortcode-css');
wp_enqueue_script('art-routes-edition-map-js');

// Pass per-instance data via inline script
wp_add_inline_script('art-routes-edition-map-js',
    'var artRoutesEditionMapInstances = artRoutesEditionMapInstances || [];' .
    'artRoutesEditionMapInstances.push(' . wp_json_encode([
        'mapId'   => $map_id,
        'mapData' => $js_data,
        'options' => ['variant' => 'shortcode'],
    ]) . ');',
    'before'
);
?>
