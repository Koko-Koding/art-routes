<?php
/**
 * Template Name: Art Route Map
 * Template for displaying an interactive art route map
 *
 * @package WP Art Routes
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Get the current route ID (falls back to default if not set)
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public-facing display template, no data modification; nonce would break bookmarkable URLs
$route_id = isset($_GET['route_id']) ? absint($_GET['route_id']) : absint(get_option('art_routes_default_route_id', 1));

// Validate that the route exists and is the correct post type
$route_post = get_post($route_id);
if (!$route_post || $route_post->post_type !== 'art_route' || $route_post->post_status !== 'publish') {
    $route_id = absint(get_option('art_routes_default_route_id', 1));
}

get_header();
?>

<div class="art-routes-container">
    <?php
    // Display the route map using our shortcode - route_id is already validated as absint
    echo do_shortcode('[art_route_map route_id="' . esc_attr($route_id) . '"]');
    ?>
</div>

<?php
get_footer();