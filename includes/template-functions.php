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
    
    $route_data = [
        'id' => $route->ID,
        'title' => $route->post_title,
        'description' => $route->post_content,
        'excerpt' => $route->post_excerpt,
        'image' => get_the_post_thumbnail_url($route->ID, 'large'),
        'length' => get_post_meta($route->ID, '_route_length', true),
        'duration' => get_post_meta($route->ID, '_route_duration', true),
        'type' => get_post_meta($route->ID, '_route_type', true),
    ];
    
    // Get route path
    $route_data['route_path'] = wp_art_routes_get_route_path($route_id);
    
    // Get artworks
    $route_data['artworks'] = wp_art_routes_get_route_artworks($route_id);
    
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
    $lines = explode("\n", $path_string);
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        if (empty($line)) {
            continue;
        }
        
        // Split by comma
        $parts = explode(',', $line);
        
        if (count($parts) >= 2) {
            $lat = trim($parts[0]);
            $lng = trim($parts[1]);
            
            if (is_numeric($lat) && is_numeric($lng)) {
                $path[] = [(float)$lat, (float)$lng];
            }
        }
    }
    
    return $path;
}

/**
 * Get artworks for a specific route
 */
function wp_art_routes_get_route_artworks($route_id) {
    $artworks = get_posts([
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_artwork_route_id',
                'value' => $route_id,
            ],
        ],
    ]);
    
    $result = [];
    
    foreach ($artworks as $artwork) {
        // Get artist info
        $artists = get_the_terms($artwork->ID, 'artist');
        $artist = !empty($artists) ? $artists[0] : null;
        
        $result[] = [
            'id' => $artwork->ID,
            'title' => $artwork->post_title,
            'description' => $artwork->post_content,
            'image_url' => get_the_post_thumbnail_url($artwork->ID, 'large'),
            'latitude' => (float)get_post_meta($artwork->ID, '_artwork_latitude', true),
            'longitude' => (float)get_post_meta($artwork->ID, '_artwork_longitude', true),
            'artist' => $artist ? $artist->name : '',
            'artist_url' => $artist ? get_term_link($artist) : '',
        ];
    }
    
    return $result;
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