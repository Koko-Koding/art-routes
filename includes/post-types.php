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
        'show_in_menu' => 'edit.php?post_type=art_route', // Add under the main Routes menu
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

/**
 * Register REST API meta for artworks and information points
 */
function wp_art_routes_register_artwork_meta() {
    // Register meta for artwork post type
    register_post_meta('artwork', '_artwork_latitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('artwork', '_artwork_longitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    
    // Register new meta fields for artwork number and location
    register_post_meta('artwork', '_artwork_number', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('artwork', '_artwork_location', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    
    // Register artwork icon field (filename)
    register_post_meta('artwork', '_artwork_icon', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'wp_art_routes_register_artwork_meta');

/**
 * Register REST API meta for information points
 */
function wp_art_routes_register_information_point_meta() {
    register_post_meta('information_point', '_artwork_latitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('information_point', '_artwork_longitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    // Register the new icon field (filename instead of URL)
    register_post_meta('information_point', '_info_point_icon', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
    // Keep the old field for backward compatibility during transition
    register_post_meta('information_point', '_info_point_icon_url', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'wp_art_routes_register_information_point_meta');

/**
 * Register REST fields for artwork meta data
 */
function wp_art_routes_register_artwork_rest_fields() {
    register_rest_field('artwork', 'latitude', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_latitude', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_latitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork latitude coordinate', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'longitude', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_longitude', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_longitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork longitude coordinate', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'number', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_number', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_number', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork number', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'location', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_location', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_location', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork location description', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
    
    // Artwork icon field (filename)
    register_rest_field('artwork', 'icon', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_icon', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_icon', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork icon filename', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Icon URL (computed from filename)
    register_rest_field('artwork', 'icon_url', [
        'get_callback' => function($post) {
            $icon_filename = get_post_meta($post['id'], '_artwork_icon', true);
            if (!empty($icon_filename)) {
                $icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
                return $icons_url . $icon_filename;
            }
            // No default icon for artworks - they will use their featured image or a generic marker
            return '';
        },
        'schema' => [
            'description' => __('Artwork icon URL', 'wp-art-routes'),
            'type' => 'string',
            'format' => 'uri',
            'context' => ['view', 'edit'],
        ],
    ]);
}
add_action('rest_api_init', 'wp_art_routes_register_artwork_rest_fields');

/**
 * Register REST fields for information point meta data
 */
function wp_art_routes_register_information_point_rest_fields() {
    register_rest_field('information_point', 'latitude', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_latitude', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_latitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point latitude coordinate', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('information_point', 'longitude', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_artwork_longitude', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_artwork_longitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point longitude coordinate', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // New icon field (filename)
    register_rest_field('information_point', 'icon', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_info_point_icon', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_info_point_icon', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point icon filename', 'wp-art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Icon URL (computed from filename)
    register_rest_field('information_point', 'icon_url', [
        'get_callback' => function($post) {
            $icon_filename = get_post_meta($post['id'], '_info_point_icon', true);
            if (!empty($icon_filename)) {
                $icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
                return $icons_url . $icon_filename;
            }
            // Fallback to old icon_url field for backward compatibility
            $old_icon_url = get_post_meta($post['id'], '_info_point_icon_url', true);
            if (!empty($old_icon_url)) {
                return $old_icon_url;
            }
            // Default icon if no icon is set
            $icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
            return $icons_url . 'WB plattegrond-Informatie.svg';
        },
        'schema' => [
            'description' => __('Information point icon URL', 'wp-art-routes'),
            'type' => 'string',
            'format' => 'uri',
            'context' => ['view', 'edit'],
        ],
    ]);
}
add_action('rest_api_init', 'wp_art_routes_register_information_point_rest_fields');