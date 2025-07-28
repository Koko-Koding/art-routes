<?php
/**
 * Template Name: Art Route Map
 * Template for displaying an interactive art route map
 *
 * @package WP Art Routes
 */

// Get the current route ID (falls back to default if not set)
$route_id = isset( $_GET['route_id'] ) ? intval( $_GET['route_id'] ) : get_option( 'wp_art_routes_default_route_id', 1 );

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