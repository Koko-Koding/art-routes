<?php
/**
 * Register Custom Post Types and Taxonomies
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types for routes and artworks
 */
function wp_art_routes_register_post_types() {
    // Register Routes post type
    register_post_type('art_route', [
        'labels' => [
            'name' => __('Routes', 'wp-art-routes'),
            'singular_name' => __('Route', 'wp-art-routes'),
            'add_new' => __('Add New Route', 'wp-art-routes'),
            'add_new_item' => __('Add New Route', 'wp-art-routes'),
            'edit_item' => __('Edit Route', 'wp-art-routes'),
            'view_item' => __('View Route', 'wp-art-routes'),
            'search_items' => __('Search Routes', 'wp-art-routes'),
            'not_found' => __('No routes found', 'wp-art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-location-alt',
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'art-route'],
    ]);
    
    // Register Artworks post type
    register_post_type('artwork', [
        'labels' => [
            'name' => __('Artworks', 'wp-art-routes'),
            'singular_name' => __('Artwork', 'wp-art-routes'),
            'add_new' => __('Add New Artwork', 'wp-art-routes'),
            'add_new_item' => __('Add New Artwork', 'wp-art-routes'),
            'edit_item' => __('Edit Artwork', 'wp-art-routes'),
            'view_item' => __('View Artwork', 'wp-art-routes'),
            'search_items' => __('Search Artworks', 'wp-art-routes'),
            'not_found' => __('No artworks found', 'wp-art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-format-image',
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'artwork'],
    ]);
    
    // Register Information Points post type
    register_post_type('information_point', [
        'labels' => [
            'name' => __('Information Points', 'wp-art-routes'),
            'singular_name' => __('Information Point', 'wp-art-routes'),
            'add_new' => __('Add New Info Point', 'wp-art-routes'),
            'add_new_item' => __('Add New Info Point', 'wp-art-routes'),
            'edit_item' => __('Edit Info Point', 'wp-art-routes'),
            'view_item' => __('View Info Point', 'wp-art-routes'),
            'search_items' => __('Search Info Points', 'wp-art-routes'),
            'not_found' => __('No info points found', 'wp-art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-info', // Use an info icon
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'info-point'],
        'show_in_menu' => 'edit.php?post_type=art_route', // Add under the main Routes menu
    ]);

}
add_action('init', 'wp_art_routes_register_post_types');