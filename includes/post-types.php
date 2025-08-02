<?php
/**
 * Register Custom Post Types and Taxonomies
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register custom post types for routes and artworks
 */
function wp_art_routes_register_post_types() {
	// Register Routes post type
	register_post_type(
		'art_route',
		array(
			'labels' => array(
				'name' => __( 'Routes', 'wp-art-routes' ),
				'singular_name' => __( 'Route', 'wp-art-routes' ),
				'add_new' => __( 'Add New Route', 'wp-art-routes' ),
				'add_new_item' => __( 'Add New Route', 'wp-art-routes' ),
				'edit_item' => __( 'Edit Route', 'wp-art-routes' ),
				'view_item' => __( 'View Route', 'wp-art-routes' ),
				'search_items' => __( 'Search Routes', 'wp-art-routes' ),
				'not_found' => __( 'No routes found', 'wp-art-routes' ),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'menu_icon' => 'dashicons-location-alt',
			'show_in_rest' => true,
			'rewrite' => array( 'slug' => 'art-route' ),
		)
	);

	// Register Artworks post type
	register_post_type(
		'artwork',
		array(
			'labels' => array(
				'name' => __( 'Artworks', 'wp-art-routes' ),
				'singular_name' => __( 'Artwork', 'wp-art-routes' ),
				'add_new' => __( 'Add New Artwork', 'wp-art-routes' ),
				'add_new_item' => __( 'Add New Artwork', 'wp-art-routes' ),
				'edit_item' => __( 'Edit Artwork', 'wp-art-routes' ),
				'view_item' => __( 'View Artwork', 'wp-art-routes' ),
				'search_items' => __( 'Search Artworks', 'wp-art-routes' ),
				'not_found' => __( 'No artworks found', 'wp-art-routes' ),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'menu_icon' => 'dashicons-format-image',
			'show_in_rest' => true,
			'rewrite' => array( 'slug' => 'artwork' ),
			'show_in_menu' => 'edit.php?post_type=art_route', // Add under the main Routes menu
		)
	);

	// Register Information Points post type
	register_post_type(
		'information_point',
		array(
			'labels' => array(
				'name' => __( 'Information Points', 'wp-art-routes' ),
				'singular_name' => __( 'Information Point', 'wp-art-routes' ),
				'add_new' => __( 'Add New Info Point', 'wp-art-routes' ),
				'add_new_item' => __( 'Add New Info Point', 'wp-art-routes' ),
				'edit_item' => __( 'Edit Info Point', 'wp-art-routes' ),
				'view_item' => __( 'View Info Point', 'wp-art-routes' ),
				'search_items' => __( 'Search Info Points', 'wp-art-routes' ),
				'not_found' => __( 'No info points found', 'wp-art-routes' ),
			),
			'public' => true,
			'has_archive' => true,
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
			'menu_icon' => 'dashicons-info', // Use an info icon
			'show_in_rest' => true,
			'rewrite' => array( 'slug' => 'info-point' ),
			'show_in_menu' => 'edit.php?post_type=art_route', // Add under the main Routes menu
		)
	);
}
add_action( 'init', 'wp_art_routes_register_post_types' );

/**
 * Register REST API meta for artworks and information points
 */
function wp_art_routes_register_artwork_meta() {
	// Register meta for artwork post type
	register_post_meta(
		'artwork',
		'_artwork_latitude',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'artwork',
		'_artwork_longitude',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Register new meta fields for artwork number and location
	register_post_meta(
		'artwork',
		'_artwork_number',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'artwork',
		'_artwork_location',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	// Register artwork icon field (filename)
	register_post_meta(
		'artwork',
		'_artwork_icon',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'wp_art_routes_register_artwork_meta' );

/**
 * Register REST API meta for information points
 */
function wp_art_routes_register_information_point_meta() {
	register_post_meta(
		'information_point',
		'_artwork_latitude',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	register_post_meta(
		'information_point',
		'_artwork_longitude',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	// Register the new icon field (filename instead of URL)
	register_post_meta(
		'information_point',
		'_info_point_icon',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'sanitize_text_field',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
	// Keep the old field for backward compatibility during transition
	register_post_meta(
		'information_point',
		'_info_point_icon_url',
		array(
			'type' => 'string',
			'single' => true,
			'show_in_rest' => true,
			'sanitize_callback' => 'esc_url_raw',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}
add_action( 'init', 'wp_art_routes_register_information_point_meta' );

/**
 * Register REST fields for artwork meta data
 */
function wp_art_routes_register_artwork_rest_fields() {
	register_rest_field(
		'artwork',
		'latitude',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_latitude', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_latitude', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork latitude coordinate', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	register_rest_field(
		'artwork',
		'longitude',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_longitude', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_longitude', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork longitude coordinate', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	register_rest_field(
		'artwork',
		'number',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_number', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_number', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork number', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	register_rest_field(
		'artwork',
		'location',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_location', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_location', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork location description', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// Artwork icon field (filename)
	register_rest_field(
		'artwork',
		'icon',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_icon', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_icon', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork icon filename', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// Icon class name (stored directly)
	register_rest_field(
		'artwork',
		'icon_class',
		array(
			'get_callback' => function ( $post ) {
				$icon_class = get_post_meta( $post['id'], '_artwork_icon', true );
				// Use fallback if no icon is set
				return ! empty( $icon_class ) ? $icon_class : 'dashicons-art';
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_icon', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Artwork icon dashicon class name', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// Deprecated: Keep icon_url for backward compatibility but make it computed from icon_class
	register_rest_field(
		'artwork',
		'icon_url',
		array(
			'get_callback' => function () {
				// For backward compatibility, return empty string as we no longer use URLs
				return '';
			},
			'schema' => array(
				'description' => __( 'Deprecated: Artwork icon URL (now using dashicons)', 'wp-art-routes' ),
				'type' => 'string',
				'format' => 'uri',
				'context' => array( 'view', 'edit' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'wp_art_routes_register_artwork_rest_fields' );

/**
 * Register REST fields for information point meta data
 */
function wp_art_routes_register_information_point_rest_fields() {
	register_rest_field(
		'information_point',
		'latitude',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_latitude', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_latitude', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Information point latitude coordinate', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	register_rest_field(
		'information_point',
		'longitude',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_artwork_longitude', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_artwork_longitude', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Information point longitude coordinate', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// New icon field (filename)
	register_rest_field(
		'information_point',
		'icon',
		array(
			'get_callback' => function ( $post ) {
				return get_post_meta( $post['id'], '_info_point_icon', true );
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_info_point_icon', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Information point icon filename', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// Icon class name (stored directly)
	register_rest_field(
		'information_point',
		'icon_class',
		array(
			'get_callback' => function ( $post ) {
				$icon_class = get_post_meta( $post['id'], '_info_point_icon', true );
				// Use fallback if no icon is set
				return ! empty( $icon_class ) ? $icon_class : 'dashicons-info';
			},
			'update_callback' => function ( $value, $post ) {
				return update_post_meta( $post->ID, '_info_point_icon', sanitize_text_field( $value ) );
			},
			'schema' => array(
				'description' => __( 'Information point icon dashicon class name', 'wp-art-routes' ),
				'type' => 'string',
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	// Deprecated: Keep icon_url for backward compatibility but return empty string
	register_rest_field(
		'information_point',
		'icon_url',
		array(
			'get_callback' => function () {
				// For backward compatibility, return empty string as we no longer use URLs
				return '';
			},
			'schema' => array(
				'description' => __( 'Deprecated: Information point icon URL (now using dashicons)', 'wp-art-routes' ),
				'type' => 'string',
				'format' => 'uri',
				'context' => array( 'view', 'edit' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'wp_art_routes_register_information_point_rest_fields' );
