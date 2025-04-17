<?php
/**
 * AJAX Handlers for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for marking an artwork as visited
 */
function wp_art_routes_ajax_mark_artwork_visited() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_art_routes_nonce')) {
        wp_send_json_error('Invalid nonce');
        die();
    }
    
    // Check user is logged in (optional)
    if (!is_user_logged_in()) {
        // For anonymous users, you might store in a cookie or session
        // This depends on your requirements
        wp_send_json_success([
            'status' => 'anonymous',
            'message' => 'Stored in session',
        ]);
        die();
    }
    
    // Get data
    $artwork_id = isset($_POST['artwork_id']) ? intval($_POST['artwork_id']) : 0;
    $user_id = get_current_user_id();
    
    if ($artwork_id <= 0) {
        wp_send_json_error('Invalid artwork ID');
        die();
    }
    
    // Get user's visited artworks
    $visited = get_user_meta($user_id, 'wp_art_routes_visited_artworks', true);
    
    if (!is_array($visited)) {
        $visited = [];
    }
    
    // Add current artwork if not already visited
    if (!in_array($artwork_id, $visited)) {
        $visited[] = $artwork_id;
        update_user_meta($user_id, 'wp_art_routes_visited_artworks', $visited);
    }
    
    wp_send_json_success([
        'status' => 'success',
        'message' => 'Artwork marked as visited',
    ]);
    die();
}
add_action('wp_ajax_wp_art_routes_mark_artwork_visited', 'wp_art_routes_ajax_mark_artwork_visited');
add_action('wp_ajax_nopriv_wp_art_routes_mark_artwork_visited', 'wp_art_routes_ajax_mark_artwork_visited');

/**
 * AJAX handler for searching posts for artist association
 */
function wp_art_routes_search_posts_for_artist() {
    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'artist_search_nonce')) {
        wp_send_json_error('Invalid nonce');
        die();
    }
    
    $term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';
    
    if (empty($term)) {
        wp_send_json([]);
        die();
    }
    
    // Prepare query arguments
    $args = array(
        'post_status' => 'publish',
        'posts_per_page' => 10,
        's' => $term,
    );
    
    // If specific post type is requested
    if (!empty($post_type)) {
        $args['post_type'] = $post_type;
    } else {
        // Get all public post types except excluded ones
        $excluded_post_types = array('revision', 'attachment', 'nav_menu_item', 'custom_css', 
                                    'customize_changeset', 'oembed_cache', 'user_request', 
                                    'wp_block', 'art_route', 'artwork');
        $post_types = get_post_types(array('public' => true), 'names');
        $filtered_post_types = array_diff($post_types, $excluded_post_types);
        
        $args['post_type'] = $filtered_post_types;
    }
    
    $search_query = new WP_Query($args);
    $results = array();
    
    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $post_id = get_the_ID();
            $post_type_obj = get_post_type_object(get_post_type());
            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : get_post_type();
            
            $results[] = array(
                'id' => $post_id,
                'label' => get_the_title(),
                'post_type' => get_post_type(),
                'post_type_label' => $post_type_label
            );
        }
    }
    
    wp_reset_postdata();
    wp_send_json($results);
    die();
}
add_action('wp_ajax_search_posts_for_artist', 'wp_art_routes_search_posts_for_artist');