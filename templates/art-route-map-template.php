<?php

/**
 * Template Name: Art Route Map
 * Template for displaying an interactive art route map
 *
 * @package WP Art Routes
 */

// Get the current route ID (falls back to default if not set), with nonce verification
if ( isset( $_GET['route_id'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'art_route_map_nonce' ) ) {
	$route_id = intval( $_GET['route_id'] );
} else {
	$route_id = get_option( 'wp_art_routes_default_route_id', 1 );
}

get_header();
?>

<div class="wp-art-routes-container">
	<?php
	// Display the route map using our shortcode
	echo do_shortcode( '[art_route_map route_id="' . $route_id . '"]' );
	?>
</div>

<?php
get_footer();
