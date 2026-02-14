<?php

/**
 * Shortcodes for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the art route map shortcode
 */
function art_routes_register_shortcodes()
{
    add_shortcode('art_route_map', 'art_routes_map_shortcode');
    add_shortcode('art_routes_map', 'art_routes_multiple_map_shortcode');
    add_shortcode('art_route_icons', 'art_routes_icons_shortcode'); // NEW
    add_shortcode('art_routes_related_artworks', 'art_routes_related_artworks_shortcode');
    add_shortcode('art_routes_edition_map', 'art_routes_edition_map_shortcode');
}
add_action('init', 'art_routes_register_shortcodes');
/**
 * Shortcode to display related artworks for the current post/page/artist
 * Usage: [related_artworks]
 */
function art_routes_related_artworks_shortcode($atts)
{
    global $post;
    if (empty($post) || !isset($post->ID)) {
        return '';
    }

    $artwork_args = [
        'post_type' => 'artro_artwork',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to find artworks linked to the current artist; no alternative relationship API
        'meta_query' => [
            [
                'key' => '_artwork_artist_ids',
                'value' => $post->ID,
                'compare' => 'LIKE',
            ],
        ],
    ];

    $related_artworks = get_posts($artwork_args);

    if (empty($related_artworks)) {
        return '...';
    }

    ob_start();
    art_routes_get_template_part('shortcode-related-artworks', [
        'related_artworks' => $related_artworks,
    ]);
    return ob_get_clean();
}

/**
 * Shortcode to display the art route map
 */
function art_routes_map_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts([
        'route_id' => get_option('art_routes_default_route_id', 1),
        'height' => '600px',
        'show_title' => 'true',
        'show_description' => 'true',
    ], $atts);

    // Convert string values to their appropriate types
    $atts['route_id'] = intval($atts['route_id']);
    $atts['show_title'] = ($atts['show_title'] === 'true');
    $atts['show_description'] = ($atts['show_description'] === 'true');

    // Start output buffering
    ob_start();

    // Load shortcode template
    art_routes_get_template_part('shortcode-map', [
        'atts' => $atts,
        'route' => art_routes_get_route_data($atts['route_id']),
    ]);

    // Return the buffered content
    return ob_get_clean();
}

/**
 * Shortcode to display multiple art routes on a single map
 */
function art_routes_multiple_map_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts([
        'ids' => '',                   // Comma-separated route IDs (empty = all routes)
        'exclude' => '',               // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Shortcode attribute name, not a WP_Query param; small dataset of routes
        'height' => '600px',           // Map height
        'show_title' => 'true',        // Show route titles
        'show_description' => 'true',  // Show route descriptions
        'show_legend' => 'true',       // Show route legend/toggle controls
        'center_lat' => '',            // Optional center latitude
        'center_lng' => '',            // Optional center longitude
        'zoom' => '',                  // Optional zoom level
    ], $atts);

    // Convert string values to their appropriate types
    $atts['show_title'] = ($atts['show_title'] === 'true');
    $atts['show_description'] = ($atts['show_description'] === 'true');
    $atts['show_legend'] = ($atts['show_legend'] === 'true');

    // Get route IDs
    $route_ids = [];
    if (!empty($atts['ids'])) {
        $route_ids = array_map('intval', explode(',', $atts['ids']));
    }

    // Get exclude IDs
    $exclude_ids = [];
    if (!empty($atts['exclude'])) {
        $exclude_ids = array_map('intval', explode(',', $atts['exclude']));
    }

    // Get routes
    $routes = art_routes_get_multiple_routes($route_ids, $exclude_ids);

    // Start output buffering
    ob_start();

    // Load shortcode template
    art_routes_get_template_part('shortcode-multiple-map', [
        'atts' => $atts,
        'routes' => $routes,
    ]);

    // Return the buffered content
    return ob_get_clean();
}

/**
 * Shortcode to display all route icons as links
 */
function art_routes_icons_shortcode($atts)
{
    // No attributes for now, but could add filtering later
    $routes = art_routes_get_multiple_routes();
    ob_start();
    art_routes_get_template_part('shortcode-route-icons', [
        'routes' => $routes,
    ]);
    return ob_get_clean();
}

/**
 * Get multiple routes data
 *
 * @param array $route_ids Optional array of route IDs to include (empty = all routes)
 * @param array $exclude_ids Optional array of route IDs to exclude
 * @return array Array of route data
 */
function art_routes_get_multiple_routes($route_ids = [], $exclude_ids = [])
{
    $args = [
        'post_type' => 'artro_route',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    // Filter by specific IDs if provided
    if (!empty($route_ids)) {
        $args['post__in'] = $route_ids;
    }

    // Exclude specific IDs if provided
    if (!empty($exclude_ids)) {
        // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- Small dataset of routes; exclusion is core shortcode functionality
        $args['post__not_in'] = $exclude_ids;
    }

    $route_posts = get_posts($args);
    $routes = [];

    foreach ($route_posts as $route_post) {
        $routes[] = art_routes_get_route_data($route_post->ID);
    }

    return $routes;
}

/**
 * Shortcode to display an Edition map
 *
 * Displays a map with routes, locations, and info points from a specific edition.
 * If no edition_id is provided, it will attempt to auto-detect from the current context.
 *
 * Usage: [edition_map edition_id="123" routes="all" show_locations="true" show_info_points="true" show_legend="true" height="500px"]
 *
 * @param array $atts Shortcode attributes
 * @return string HTML output for the map
 */
function art_routes_edition_map_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts([
        'edition_id' => 0,                // 0 = auto-detect
        'routes' => 'all',                // all, none, or comma-separated IDs
        'show_locations' => 'true',       // Show location markers
        'show_info_points' => 'true',     // Show info point markers
        'show_legend' => 'true',          // Show legend/toggle controls
        'height' => '500px',              // Map height
    ], $atts);

    // Convert string booleans to actual booleans
    $atts['show_locations'] = ($atts['show_locations'] === 'true');
    $atts['show_info_points'] = ($atts['show_info_points'] === 'true');
    $atts['show_legend'] = ($atts['show_legend'] === 'true');

    // Auto-detect edition if not specified
    $edition_id = intval($atts['edition_id']);
    if (!$edition_id) {
        $edition_id = art_routes_detect_edition_context();
    }

    // Still no edition? Show placeholder
    if (!$edition_id) {
        return '<div class="edition-map-placeholder"><p>' . __('Please select an Edition.', 'art-routes') . '</p></div>';
    }

    // Get edition data to verify it exists
    $edition = art_routes_get_edition_data($edition_id);
    if (!$edition) {
        return '<div class="edition-map-placeholder"><p>' . __('Edition not found.', 'art-routes') . '</p></div>';
    }

    // Get routes based on parameter
    $routes = [];
    if ($atts['routes'] !== 'none') {
        $all_routes = art_routes_get_edition_routes($edition_id);

        if ($atts['routes'] === 'all') {
            $routes = $all_routes;
        } else {
            // Filter by specific route IDs
            $route_ids = array_map('intval', explode(',', $atts['routes']));
            foreach ($all_routes as $route) {
                if (in_array($route['id'], $route_ids)) {
                    $routes[] = $route;
                }
            }
        }
    }

    // Get artworks if show_locations is true
    $artworks = [];
    if ($atts['show_locations']) {
        $artworks = art_routes_get_edition_artworks($edition_id);
    }

    // Get info points if show_info_points is true
    $info_points = [];
    if ($atts['show_info_points']) {
        $info_points = art_routes_get_edition_information_points($edition_id);
    }

    // Check if there's any content to display
    if (empty($routes) && empty($artworks) && empty($info_points)) {
        return '<div class="edition-map-placeholder"><p>' . __('No map content found for this edition.', 'art-routes') . '</p></div>';
    }

    // Start output buffering
    ob_start();

    // Load shortcode template
    art_routes_get_template_part('shortcode-edition-map', [
        'atts' => $atts,
        'edition_id' => $edition_id,
        'edition' => $edition,
        'routes' => $routes,
        'artworks' => $artworks,
        'info_points' => $info_points,
    ]);

    // Return the buffered content
    return ob_get_clean();
}
