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
function wp_art_routes_register_shortcodes()
{
    add_shortcode('art_route_map', 'wp_art_routes_map_shortcode');
    add_shortcode('art_routes_map', 'wp_art_routes_multiple_map_shortcode');
    add_shortcode('art_route_icons', 'wp_art_routes_icons_shortcode'); // NEW
    add_shortcode('related_artworks', 'wp_art_routes_related_artworks_shortcode');
}
add_action('init', 'wp_art_routes_register_shortcodes');
/**
 * Shortcode to display related artworks for the current post/page/artist
 * Usage: [related_artworks]
 */
function wp_art_routes_related_artworks_shortcode($atts)
{
    global $post;
    if (empty($post) || !isset($post->ID)) {
        return '';
    }

    $artwork_args = [
        'post_type' => 'artwork',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
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
    wp_art_routes_get_template_part('shortcode-related-artworks', [
        'related_artworks' => $related_artworks,
    ]);
    return ob_get_clean();
}

/**
 * Shortcode to display the art route map
 */
function wp_art_routes_map_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts([
        'route_id' => get_option('wp_art_routes_default_route_id', 1),
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
    wp_art_routes_get_template_part('shortcode-map', [
        'atts' => $atts,
        'route' => wp_art_routes_get_route_data($atts['route_id']),
    ]);

    // Return the buffered content
    return ob_get_clean();
}

/**
 * Shortcode to display multiple art routes on a single map
 */
function wp_art_routes_multiple_map_shortcode($atts)
{
    // Parse attributes
    $atts = shortcode_atts([
        'ids' => '',                   // Comma-separated route IDs (empty = all routes)
        'exclude' => '',               // Comma-separated route IDs to exclude
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
    $routes = wp_art_routes_get_multiple_routes($route_ids, $exclude_ids);

    // Start output buffering
    ob_start();

    // Load shortcode template
    wp_art_routes_get_template_part('shortcode-multiple-map', [
        'atts' => $atts,
        'routes' => $routes,
    ]);

    // Return the buffered content
    return ob_get_clean();
}

/**
 * Shortcode to display all route icons as links
 */
function wp_art_routes_icons_shortcode($atts)
{
    // No attributes for now, but could add filtering later
    $routes = wp_art_routes_get_multiple_routes();
    ob_start();
    wp_art_routes_get_template_part('shortcode-route-icons', [
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
function wp_art_routes_get_multiple_routes($route_ids = [], $exclude_ids = [])
{
    $args = [
        'post_type' => 'art_route',
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
        $args['post__not_in'] = $exclude_ids;
    }

    $route_posts = get_posts($args);
    $routes = [];

    foreach ($route_posts as $route_post) {
        $routes[] = wp_art_routes_get_route_data($route_post->ID);
    }

    return $routes;
}
