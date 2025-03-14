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