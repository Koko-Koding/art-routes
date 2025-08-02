<?php

/**
 * Scripts and Styles for the Art Routes Plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue frontend scripts and styles for the art route map
 */
function wp_art_routes_enqueue_scripts() {
	// Only enqueue on pages with our shortcode or template or single post type
	if ( ! wp_art_routes_is_route_page() ) {
		return;
	}

	// Leaflet CSS (local)
	wp_enqueue_style(
		'wp-art-routes-leaflet-css',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/leaflet/leaflet.css',
		array(),
		WP_ART_ROUTES_VERSION
	);

	// Leaflet JS (local)
	wp_enqueue_script(
		'wp-art-routes-leaflet-js',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/leaflet/leaflet.js',
		array(),
		WP_ART_ROUTES_VERSION,
		true
	);

	// Our custom CSS
	wp_enqueue_style(
		'wp-art-routes-map-css',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/css/art-route-map.css',
		array(),
		WP_ART_ROUTES_VERSION
	);

	// Our custom JS
	wp_enqueue_script(
		'wp-art-routes-map-js',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/js/art-route-map.js',
		array( 'jquery', 'wp-art-routes-leaflet-js' ),
		WP_ART_ROUTES_VERSION,
		true
	);

	// Determine Route ID
	$route_id = 0;
	if ( is_singular( 'art_route' ) ) {
		$route_id = get_the_ID();
	} elseif ( is_page_template( 'art-route-map-template.php' ) ) {
		if ( isset( $_GET['route_id'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'art_route_map_nonce' ) ) {
			$route_id = intval( $_GET['route_id'] );
		} else {
			$route_id = get_option( 'wp_art_routes_default_route', 0 );
		}
	} else {
		// Attempt to find route_id if shortcode is present on a non-singular/non-template page
		global $post;
		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'art_route_map' ) ) {
			// Basic regex to extract route_id - might fail with complex attributes
			// It's generally better to handle this within the shortcode callback itself if more complex logic is needed.
			preg_match( '/\\[art_route_map.*?route_id=([\'"]?)(\d+)\1.*?\\]/', $post->post_content, $matches );
			if ( ! empty( $matches[2] ) ) {
				$route_id = intval( $matches[2] );
			} else {
				// If shortcode exists but no ID, check for default route
				$route_id = get_option( 'wp_art_routes_default_route', 0 );
			}
		}
	}

	// Get route data if we have a valid ID
	$route_data = null;
	if ( $route_id > 0 ) {
		$route_data = wp_art_routes_get_route_data( $route_id );
	}

	// Localize script if we have data
	if ( $route_data ) {
		$js_data = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'wp_art_routes_nonce' ),
			'route_path' => $route_data['route_path'],
			'artworks' => $route_data['artworks'],
			'information_points' => $route_data['information_points'], // Ensure this is included
			'show_completed_route' => $route_data['show_completed_route'],
			'show_artwork_toasts' => $route_data['show_artwork_toasts'],
			'plugin_url' => WP_ART_ROUTES_PLUGIN_URL, // Pass plugin URL for JS
			'i18n' => array(
				'routeComplete' => __( 'Congratulations! You have completed this route!', 'wp-art-routes' ),
				'nearbyArtwork' => __( 'You are near an artwork!', 'wp-art-routes' ),
			),
		);
		wp_localize_script( 'wp-art-routes-map-js', 'artRouteData', $js_data );
	} else {
		// Localize with empty data or defaults if the script expects artRouteData to always exist
		wp_localize_script(
			'wp-art-routes-map-js',
			'artRouteData',
			array(
				'error' => 'No route data found or specified.',
				'plugin_url' => WP_ART_ROUTES_PLUGIN_URL, // Still pass URL
				'route_path' => array(),
				'artworks' => array(),
				'information_points' => array(),
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'wp_art_routes_enqueue_scripts' );

/**
 * Enqueue admin scripts and styles for the route editor and location picker
 */
function wp_art_routes_enqueue_admin_scripts( $hook ) {
	global $post;

	// Check if we need the route editor scripts
	$is_edit_page       = $hook === 'post.php' || $hook === 'post-new.php';
	$is_route_type      = isset( $post ) && $post->post_type === 'art_route';
	$is_artwork_type    = isset( $post ) && $post->post_type === 'artwork';
	$is_info_point_type = isset( $post ) && $post->post_type === 'information_point';

	// Only load on relevant pages
	if ( ! $is_edit_page || ( ! $is_route_type && ! $is_artwork_type && ! $is_info_point_type ) ) {
		return;
	}

	// Leaflet CSS (local)
	wp_enqueue_style(
		'wp-art-routes-admin-leaflet-css',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/leaflet/leaflet.css',
		array(),
		WP_ART_ROUTES_VERSION
	);

	// Leaflet JS (local)
	wp_enqueue_script(
		'wp-art-routes-admin-leaflet-js',
		WP_ART_ROUTES_PLUGIN_URL . 'assets/leaflet/leaflet.js',
		array( 'jquery' ),
		WP_ART_ROUTES_VERSION,
		true
	);

	// Route editor (for art_route post type)
	if ( $is_route_type ) {
		wp_enqueue_style(
			'wp-art-routes-editor-css',
			WP_ART_ROUTES_PLUGIN_URL . 'assets/css/route-editor-admin.css',
			array(),
			WP_ART_ROUTES_VERSION
		);

		wp_enqueue_script(
			'wp-art-routes-editor-js',
			WP_ART_ROUTES_PLUGIN_URL . 'assets/js/route-editor-admin.js',
			array( 'jquery', 'wp-art-routes-admin-leaflet-js', 'jquery-ui-draggable' ),
			WP_ART_ROUTES_VERSION,
			true
		);

		// Pass data to JavaScript
		wp_localize_script(
			'wp-art-routes-editor-js',
			'routeEditorData',
			array(
				'modalHTML' => wp_art_routes_get_route_editor_modal_html(),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'get_points_nonce' => wp_create_nonce( 'get_route_points_nonce' ),
				'save_points_nonce' => wp_create_nonce( 'save_route_points_nonce' ),
				'route_id' => isset( $post ) ? $post->ID : 0,
				'i18n' => array(
					'addArtwork' => __( 'Add Artwork', 'wp-art-routes' ),
					'addInfoPoint' => __( 'Add Info Point', 'wp-art-routes' ),
					'artwork' => __( 'Artwork', 'wp-art-routes' ),
					'infoPoint' => __( 'Info Point', 'wp-art-routes' ),
					'edit' => __( 'Edit', 'wp-art-routes' ),
					'remove' => __( 'Remove', 'wp-art-routes' ),
					'confirmRemove' => __( 'Are you sure you want to remove this point from the route?', 'wp-art-routes' ),
					'errorLoadingPoints' => __( 'Error loading points for this route.', 'wp-art-routes' ),
					'errorSavingPoints' => __( 'Error saving points.', 'wp-art-routes' ),
					'savingPoints' => __( 'Saving points...', 'wp-art-routes' ),
					'pointsSaved' => __( 'Points saved successfully.', 'wp-art-routes' ),
					'draftWarning' => __( 'Warning: This point is a draft and won\'t be visible on the public map.', 'wp-art-routes' ), // Added draft warning
				),
			)
		);
	}

	// Location picker (for artwork and information_point post types)
	if ( $is_artwork_type || $is_info_point_type ) {
		wp_enqueue_style(
			'wp-art-routes-location-picker-css',
			WP_ART_ROUTES_PLUGIN_URL . 'assets/css/artwork-location-picker.css',
			array(),
			WP_ART_ROUTES_VERSION
		);

		wp_enqueue_script(
			'wp-art-routes-location-picker-js',
			WP_ART_ROUTES_PLUGIN_URL . 'assets/js/artwork-location-picker.js',
			array( 'jquery', 'wp-art-routes-admin-leaflet-js' ),
			WP_ART_ROUTES_VERSION,
			true
		);

		// Pass the modal HTML to JavaScript
		wp_localize_script(
			'wp-art-routes-location-picker-js',
			'artworkLocationModalHTML',
			wp_art_routes_get_location_picker_modal_html()
		);
	}
}
add_action( 'admin_enqueue_scripts', 'wp_art_routes_enqueue_admin_scripts' );

/**
 * Check if current page should display a route map
 */
function wp_art_routes_is_route_page() {
	// Check for shortcode in content
	global $post;

	if ( is_singular() && isset( $post->post_content ) && has_shortcode( $post->post_content, 'art_route_map' ) ) {
		return true;
	}

	// Check for our template
	if ( is_page_template( 'art-route-map-template.php' ) ) {
		return true;
	}

	// Check if viewing a single art_route post type
	if ( is_singular( 'art_route' ) ) {
		return true;
	}

	// Check if viewing a single artwork post type (for the location map)
	if ( is_singular( 'artwork' ) ) {
		return true;
	}

	// Check if viewing a single information_point post type (for the location map)
	if ( is_singular( 'information_point' ) ) {
		return true;
	}

	// Default to false
	return false;
}

/**
 * Get the route editor modal HTML
 */
function wp_art_routes_get_route_editor_modal_html() {
	ob_start();
	?>
	<div id="route-editor-modal" class="route-editor-modal" style="display: none;">
		<div class="route-editor-modal-content">
			<div class="route-editor-header">
				<h2><?php esc_html_e( 'Route Editor', 'wp-art-routes' ); ?></h2>
				<span class="close-modal">&times;</span>
			</div>
			<div class="route-editor-body">
				<div class="route-editor-controls">
					<h4><?php esc_html_e( 'Route Path', 'wp-art-routes' ); ?></h4>
					<button id="start-drawing" class="button"><?php esc_html_e( 'Start Drawing', 'wp-art-routes' ); ?></button>
					<button id="stop-drawing" class="button"><?php esc_html_e( 'Stop Drawing', 'wp-art-routes' ); ?></button>
					<button id="clear-route" class="button button-secondary"><?php esc_html_e( 'Clear Path', 'wp-art-routes' ); ?></button>
					<p id="drawing-instructions" class="description"><?php esc_html_e( 'Select an action.', 'wp-art-routes' ); ?></p>

					<h4><?php esc_html_e( 'Points of Interest', 'wp-art-routes' ); ?></h4>
					<button id="add-artwork" class="button"><?php esc_html_e( 'Add Artwork', 'wp-art-routes' ); ?></button>
					<button id="add-info-point" class="button"><?php esc_html_e( 'Add Info Point', 'wp-art-routes' ); ?></button>
					<p id="adding-point-info" class="description" style="display: none;"></p>

					<h4><?php esc_html_e( 'Map View', 'wp-art-routes' ); ?></h4>
					<button id="fit-route-bounds" class="button"><?php esc_html_e( 'Fit Route', 'wp-art-routes' ); ?></button>
					<button id="locate-user" class="button"><?php esc_html_e( 'My Location', 'wp-art-routes' ); ?></button>

					<h4><?php esc_html_e( 'Search Location', 'wp-art-routes' ); ?></h4>
					<input type="text" id="route-search" placeholder="<?php esc_attr_e( 'Enter address or place...', 'wp-art-routes' ); ?>" />
					<button id="search-location" class="button"><?php esc_html_e( 'Search', 'wp-art-routes' ); ?></button>
				</div>
				<div class="control-info">
					<p id="drawing-instructions"><?php esc_html_e( 'Use controls above to draw the route or add points. Click on the map to place items.', 'wp-art-routes' ); ?></p>
					<p>
						<span id="point-count">0</span> <?php esc_html_e( 'route points', 'wp-art-routes' ); ?> |
						<span id="artwork-count">0</span> <?php esc_html_e( 'artworks', 'wp-art-routes' ); ?> |
						<span id="info-point-count">0</span> <?php esc_html_e( 'info points', 'wp-art-routes' ); ?>
					</p>
					<p><?php esc_html_e( 'Route distance:', 'wp-art-routes' ); ?> <span id="route-distance">0</span> km</p>
					<p id="save-status" style="color: green; font-weight: bold;"></p>
				</div>
				<div id="route-editor-map"></div>
			</div>
			<div class="route-editor-footer">
				<button type="button" class="button button-secondary" id="cancel-route"><?php esc_html_e( 'Close', 'wp-art-routes' ); ?></button>
				<button type="button" class="button button-primary" id="save-route"><?php esc_html_e( 'Save Changes', 'wp-art-routes' ); ?></button>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Get the location picker modal HTML
 */
function wp_art_routes_get_location_picker_modal_html() {
	ob_start();
	?>
	<div id="artwork-location-modal" class="location-picker-modal" style="display: none;">
		<div class="location-picker-modal-content">
			<div class="location-picker-header">
				<h2><?php esc_html_e( 'Pick Artwork Location', 'wp-art-routes' ); ?></h2>
				<span class="close-modal">&times;</span>
			</div>
			<div class="location-picker-body">
				<div class="location-picker-controls">
					<div class="control-group">
						<label for="location-search"><?php esc_html_e( 'Search Location:', 'wp-art-routes' ); ?></label>
						<input type="text" id="location-search" class="regular-text" placeholder="<?php esc_attr_e( 'Enter location...', 'wp-art-routes' ); ?>">
						<button type="button" class="button" id="search-artwork-location"><?php esc_html_e( 'Search', 'wp-art-routes' ); ?></button>
					</div>
					<div class="control-info">
						<p><?php esc_html_e( 'Click on the map to select the artwork location.', 'wp-art-routes' ); ?></p>
						<p><?php esc_html_e( 'Selected coordinates:', 'wp-art-routes' ); ?></p>
						<p id="selected-coordinates">None</p>
					</div>
				</div>
				<div id="location-picker-map"></div>
			</div>
			<div class="location-picker-footer">
				<button type="button" class="button button-secondary" id="cancel-location"><?php esc_html_e( 'Cancel', 'wp-art-routes' ); ?></button>
				<button type="button" class="button button-primary" id="save-location"><?php esc_html_e( 'Save Location', 'wp-art-routes' ); ?></button>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Add the inline location map script for the artwork admin
 */
function wp_art_routes_add_location_map_script() {
	global $post;

	// Only add to artwork or information_point post type
	if ( ! $post || ! in_array( get_post_type( $post->ID ), array( 'artwork', 'information_point' ) ) ) {
		return;
	}

	?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			// Initialize small map for location in the meta box
			window.locationMap = L.map('artwork_location_map').setView([52.1326, 5.2913], 8);

			// Add tile layer
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				maxZoom: 19
			}).addTo(window.locationMap);

			// Get saved coordinates
			var lat = $('#artwork_latitude').val();
			var lng = $('#artwork_longitude').val();

			// Add marker if coordinates exist
			if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
				window.locationMarker = L.marker([lat, lng]).addTo(window.locationMap);
				window.locationMap.setView([lat, lng], 14);
			}

			// Handle map click events on the small map too
			window.locationMap.on('click', function(e) {
				// Update form fields
				$('#artwork_latitude').val(e.latlng.lat.toFixed(6));
				$('#artwork_longitude').val(e.latlng.lng.toFixed(6));

				// Update or add marker
				if (window.locationMarker) {
					window.locationMarker.setLatLng(e.latlng);
				} else {
					window.locationMarker = L.marker(e.latlng).addTo(window.locationMap);
				}
			});
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'wp_art_routes_add_location_map_script' );
