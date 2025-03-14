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
function wp_art_routes_register_shortcodes() {
    add_shortcode('art_route_map', 'wp_art_routes_map_shortcode');
}
add_action('init', 'wp_art_routes_register_shortcodes');

/**
 * Shortcode to display the art route map
 */
function wp_art_routes_map_shortcode($atts) {
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