<?php
/**
 * Template Functions for the Art Routes Plugin
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all art routes
 */
function wp_art_routes_get_routes() {
	return get_posts(
		array(
			'post_type' => 'art_route',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
		)
	);
}

/**
 * Get route data for a specific route
 */
function wp_art_routes_get_route_data( $route_id ) {
	$route = get_post( $route_id );

	if ( ! $route || $route->post_type !== 'art_route' ) {
		return null;
	}

	$show_completed_route = get_post_meta( $route_id, '_show_completed_route', true );
	$show_artwork_toasts  = get_post_meta( $route_id, '_show_artwork_toasts', true );

	// Default to true if not set
	if ( $show_completed_route === '' ) {
		$show_completed_route = '1';
	}

	// Default to true if not set
	if ( $show_artwork_toasts === '' ) {
		$show_artwork_toasts = '1';
	}

	$route_data = array(
		'id' => $route->ID,
		'title' => $route->post_title,
		'description' => $route->post_content,
		'excerpt' => $route->post_excerpt,
		'image' => get_the_post_thumbnail_url( $route->ID, 'large' ),
		'length' => get_post_meta( $route->ID, '_route_length', true ),
		'duration' => get_post_meta( $route->ID, '_route_duration', true ),
		'type' => get_post_meta( $route->ID, '_route_type', true ),
		'show_completed_route' => $show_completed_route === '1',
		'show_artwork_toasts' => $show_artwork_toasts === '1',
	);

	// Get route path
	$route_data['route_path'] = wp_art_routes_get_route_path( $route_id );

	// Get artworks
	$route_data['artworks'] = wp_art_routes_get_route_artworks( $route_id );

	// Get information points
	$route_data['information_points'] = wp_art_routes_get_route_information_points( $route_id );

	return $route_data;
}

/**
 * Get route path for a specific route
 */
function wp_art_routes_get_route_path( $route_id ) {
	$path_string = get_post_meta( $route_id, '_route_path', true );
	if ( empty( $path_string ) ) {
		return array();
	}
	$path = array();
	// Try to decode as JSON first
	$json = json_decode( $path_string, true );
	if ( is_array( $json ) && isset( $json[0]['lat'] ) && isset( $json[0]['lng'] ) ) {
		// New format: array of objects with lat/lng and possibly extra metadata
		foreach ( $json as $pt ) {
			if ( isset( $pt['lat'] ) && isset( $pt['lng'] ) && is_numeric( $pt['lat'] ) && is_numeric( $pt['lng'] ) ) {
				// Keep all properties (lat, lng, is_start, is_end, notes, ...)
				$pt['lat'] = floatval( $pt['lat'] );
				$pt['lng'] = floatval( $pt['lng'] );
				$path[]    = $pt;
			}
		}
		return $path;
	}
	// Fallback: old format (lines of lat, lng)
	$lines = explode( "\n", $path_string );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( empty( $line ) ) {
			continue;
		}
		$parts = explode( ',', $line );
		if ( count( $parts ) >= 2 ) {
			$lat = trim( $parts[0] );
			$lng = trim( $parts[1] );
			if ( is_numeric( $lat ) && is_numeric( $lng ) ) {
				$path[] = array( (float) $lat, (float) $lng );
			}
		}
	}
	return $path;
}

/**
 * Get artworks for a specific route
 */
function wp_art_routes_get_route_artworks( $route_id ) {
	// Now return ALL artworks instead of filtering by route
	return wp_art_routes_get_all_artworks();
}

/**
 * Get all artworks (not tied to specific routes)
 */
function wp_art_routes_get_all_artworks() {
	// Query all published artworks
	$artworks = get_posts(
		array(
			'post_type' => 'artwork',
			'posts_per_page' => -1,
			'post_status' => 'publish', // Only get published artworks
			'orderby' => 'title',
			'order' => 'ASC',
		)
	);

	$result = array();

	foreach ( $artworks as $artwork ) {
		$latitude  = get_post_meta( $artwork->ID, '_artwork_latitude', true );
		$longitude = get_post_meta( $artwork->ID, '_artwork_longitude', true );

		// Ensure location data exists
		if ( is_numeric( $latitude ) && is_numeric( $longitude ) ) {
			// Get icon information - now stored as dashicon class name
			$icon_class = get_post_meta( $artwork->ID, '_artwork_icon', true );

			// Use fallback if no icon is set
			if ( empty( $icon_class ) ) {
				$icon_class = 'dashicons-art';
			}

			$artwork_data = array(
				'id' => $artwork->ID,
				'title' => $artwork->post_title,
				'description' => $artwork->post_content,
				'image_url' => get_the_post_thumbnail_url( $artwork->ID, 'large' ),
				'latitude' => (float) $latitude,
				'longitude' => (float) $longitude,
				'number' => get_post_meta( $artwork->ID, '_artwork_number', true ),
				'location' => get_post_meta( $artwork->ID, '_artwork_location', true ),
				'permalink' => get_permalink( $artwork->ID ),
				'icon_class' => $icon_class, // Changed from icon_url to icon_class
			);

			// Get artist information
			$artist_ids = get_post_meta( $artwork->ID, '_artwork_artist_ids', true );
			$artists    = array();

			if ( is_array( $artist_ids ) && ! empty( $artist_ids ) ) {
				foreach ( $artist_ids as $artist_id ) {
					$artist_post = get_post( $artist_id );
					if ( $artist_post ) {
						$post_type_obj   = get_post_type_object( $artist_post->post_type );
						$post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $artist_post->post_type;

						$artists[] = array(
							'id' => $artist_id,
							'title' => $artist_post->post_title,
							'url' => get_permalink( $artist_id ),
							'post_type' => $artist_post->post_type,
							'post_type_label' => $post_type_label,
						);
					}
				}
			}

			$artwork_data['artists'] = $artists;
			$result[]                = $artwork_data;
		}
	}

	return $result;
}

/**
 * Get information points for a specific route
 */
function wp_art_routes_get_route_information_points( $route_id ) {
	// Now return ALL information points instead of filtering by route
	return wp_art_routes_get_all_information_points();
}

/**
 * Get all information points (not tied to specific routes)
 */
function wp_art_routes_get_all_information_points() {
	// Query all published information points
	$info_point_posts = get_posts(
		array(
			'post_type' => 'information_point',
			'posts_per_page' => -1,
			'post_status' => 'publish', // Only get published points
			'orderby' => 'title',
			'order' => 'ASC',
		)
	);

	$info_points = array();

	foreach ( $info_point_posts as $info_post ) {
		$latitude  = get_post_meta( $info_post->ID, '_artwork_latitude', true );
		$longitude = get_post_meta( $info_post->ID, '_artwork_longitude', true );

		// Ensure location data exists
		if ( is_numeric( $latitude ) && is_numeric( $longitude ) ) {
			// Get icon information - now stored as dashicon class name
			$icon_class = get_post_meta( $info_post->ID, '_info_point_icon', true );

			// Use fallback if no icon is set
			if ( empty( $icon_class ) ) {
				$icon_class = 'dashicons-info';
			}

			$info_points[] = array(
				'id' => $info_post->ID,
				'title' => $info_post->post_title,
				'excerpt' => has_excerpt( $info_post->ID ) ? get_the_excerpt( $info_post->ID ) : wp_trim_words( $info_post->post_content, 30, '...' ),
				'image_url' => get_the_post_thumbnail_url( $info_post->ID, 'medium' ), // Use medium size for popup
				'permalink' => get_permalink( $info_post->ID ), // Link to the info point post itself
				'latitude' => (float) $latitude,
				'longitude' => (float) $longitude,
				'icon_class' => $icon_class, // Changed from icon_url to icon_class
			);
		}
	}

	return $info_points;
}

/**
 * Load a template from the plugin
 *
 * First checks in the theme directory for an override
 * then falls back to the plugin template
 */
function wp_art_routes_get_template_part( $template_name, $args = array() ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args );
	}

	// Look for template in theme first
	$template = locate_template( 'wp-art-routes/' . $template_name . '.php' );

	// If not found in theme, load from plugin
	if ( empty( $template ) ) {
		$template = WP_ART_ROUTES_PLUGIN_DIR . 'templates/' . $template_name . '.php';
	}

	if ( file_exists( $template ) ) {
		include $template;
	}
}

/**
 * Register the art route template with WordPress
 */
function wp_art_routes_register_templates( $templates ) {
	$templates['art-route-map-template.php'] = 'Art Route Map Template';
	return $templates;
}
add_filter( 'theme_page_templates', 'wp_art_routes_register_templates', 10, 1 );

/**
 * Handle template redirection for our custom template
 */
function wp_art_routes_template_include( $template ) {
	// Return early if not a single page or not using our template
	if ( ! is_singular() || get_page_template_slug() !== 'art-route-map-template.php' ) {
		return $template;
	}

	// Look for template in theme directory first
	$located = locate_template( 'wp-art-routes/art-route-map-template.php' );

	// If not found in theme, use plugin template
	if ( empty( $located ) ) {
		$located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/art-route-map-template.php';
	}

	if ( file_exists( $located ) ) {
		return $located;
	}

	// Fall back to original template if ours doesn't exist
	return $template;
}
add_filter( 'template_include', 'wp_art_routes_template_include' );

/**
 * Handle template redirection for artwork posts
 */
function wp_art_routes_single_artwork_template( $template ) {
	// Only handle single artwork posts
	if ( is_singular( 'artwork' ) ) {
		// Look for template in theme directory first
		$located = locate_template( 'wp-art-routes/single-artwork.php' );

		// If not found in theme, use plugin template
		if ( empty( $located ) ) {
			$located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/single-artwork.php';
		}

		if ( file_exists( $located ) ) {
			return $located;
		}
	}

	return $template;
}
add_filter( 'template_include', 'wp_art_routes_single_artwork_template', 99 );

/**
 * Handle template redirection for information point posts
 */
function wp_art_routes_single_information_point_template( $template ) {
	// Only handle single information_point posts
	if ( is_singular( 'information_point' ) ) {
		// Look for template in theme directory first
		$located = locate_template( 'wp-art-routes/single-information_point.php' );

		// If not found in theme, use plugin template
		if ( empty( $located ) ) {
			$located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/single-information_point.php';
		}

		if ( file_exists( $located ) ) {
			return $located;
		}
	}

	return $template;
}
add_filter( 'template_include', 'wp_art_routes_single_information_point_template', 99 );

/**
 * Automatically append map to route content
 */
function wp_art_routes_append_map_to_route_content( $content ) {
	// Only apply on singular art_route pages
	if ( ! is_singular( 'art_route' ) ) {
		return $content;
	}

	// Get the current post ID
	$route_id   = get_the_ID();
	$route_data = wp_art_routes_get_route_data( $route_id );

	if ( ! $route_data ) {
		return $content;
	}

	// Generate the HTML for the map
	ob_start();
	?>
	<div class="art-route-container">
		<div class="art-route-details">
			<div class="route-meta">
				<?php if ( ! empty( $route_data['length'] ) ) : ?>
					<span class="route-length">
						<?php echo esc_html( wp_art_routes_format_length( $route_data['length'] ) ); ?>
					</span>
				<?php endif; ?>
				
				<?php if ( ! empty( $route_data['duration'] ) ) : ?>
					<span class="route-duration">
						<?php echo esc_html( wp_art_routes_format_duration( $route_data['duration'] ) ); ?>
					</span>
				<?php endif; ?>
				
				<?php if ( ! empty( $route_data['type'] ) ) : ?>
					<span class="route-type">
						<?php
						$route_types = array(
							'walking' => __( 'Walking route', 'wp-art-routes' ),
							'cycling' => __( 'Bicycle route', 'wp-art-routes' ),
							'wheelchair' => __( 'Wheelchair friendly', 'wp-art-routes' ),
							'children' => __( 'Child-friendly route', 'wp-art-routes' ),
						);
						echo isset( $route_types[ $route_data['type'] ] ) ? esc_html( $route_types[ $route_data['type'] ] ) : esc_html( $route_data['type'] );
						?>
					</span>
				<?php endif; ?>
			</div>
		</div>
		
		<!-- Map container -->
		<div id="art-route-map" class="art-route-map" style="height: 600px;"></div>
		
		<?php
		// Display map controls using the reusable template tag
		wp_art_routes_display_map_controls();
		?>
		
		<!-- Loading indicator -->
		<div id="map-loading" class="map-loading" style="display: none;">
			<div class="spinner"></div>
			<p><?php esc_html_e( 'Loading map...', 'wp-art-routes' ); ?></p>
		</div>
		
		<!-- Location error message -->
		<div id="location-error" class="map-error" style="display: none;">
			<p></p>
			<button id="retry-location" class="button"><?php esc_html_e( 'Retry', 'wp-art-routes' ); ?></button>
		</div>
		
		<!-- Route progress -->
		<div class="route-progress" style="display: none;">
			<h3><?php esc_html_e( 'Progress', 'wp-art-routes' ); ?></h3>
			<div class="progress-bar">
				<div class="progress-fill" style="width: 0%;"></div>
			</div>
			<p><?php esc_html_e( 'You have completed', 'wp-art-routes' ); ?> <span id="progress-percentage">0</span>% <?php esc_html_e( 'of this route', 'wp-art-routes' ); ?></p>
		</div>
		
		<!-- Artwork modal -->
		<div id="artwork-modal" class="artwork-modal" style="display: none;">
			<div class="artwork-modal-content">
				<span class="close-modal">&times;</span>
				<div class="artwork-image">
					<img id="artwork-img" src="" alt="">
				</div>
				<div class="artwork-info">
					<h3 id="artwork-title"></h3>
					<div id="artwork-description"></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	$map_content = ob_get_clean();

	// Append the map to the content
	return $content . $map_content;
}
add_filter( 'the_content', 'wp_art_routes_append_map_to_route_content' );

/**
 * Display map visibility toggle controls
 *
 * @param array $options Configuration options for the controls
 */
function wp_art_routes_display_map_controls( $options = array() ) {
	// Default options
	$defaults = array(
		'show_artworks' => true,
		'show_info_points' => true,
		'show_route' => true,
		'show_user_location' => true,
		'show_navigation' => true,
		'artworks_checked' => true,
		'info_points_checked' => true,
		'route_checked' => true,
		'user_location_checked' => true,
		'css_class' => 'map-controls',
		'title' => __( 'Map Display Options', 'wp-art-routes' ),
	);

	$options = wp_parse_args( $options, $defaults );

	// Don't display if no controls are enabled
	if ( ! $options['show_artworks'] && ! $options['show_info_points'] &&
		! $options['show_route'] && ! $options['show_user_location'] && ! $options['show_navigation'] ) {
		return;
	}

	?>
	<!-- Map Controls -->
	<div class="<?php echo esc_attr( $options['css_class'] ); ?>">
		<h4 class="map-controls-title"><?php echo esc_html( $options['title'] ); ?></h4>
		<div class="map-controls-grid">
			<?php if ( $options['show_artworks'] ) : ?>
				<label class="map-control-item">
					<input type="checkbox" id="toggle-artworks" <?php checked( $options['artworks_checked'] ); ?>>
					<span class="map-control-icon dashicons dashicons-art"></span>
					<span class="map-control-label"><?php esc_html_e( 'Show Artworks', 'wp-art-routes' ); ?></span>
				</label>
			<?php endif; ?>
			
			<?php if ( $options['show_info_points'] ) : ?>
				<label class="map-control-item">
					<input type="checkbox" id="toggle-info-points" <?php checked( $options['info_points_checked'] ); ?>>
					<span class="map-control-icon dashicons dashicons-info"></span>
					<span class="map-control-label"><?php esc_html_e( 'Show Information Points', 'wp-art-routes' ); ?></span>
				</label>
			<?php endif; ?>
			
			<?php if ( $options['show_route'] ) : ?>
				<label class="map-control-item">
					<input type="checkbox" id="toggle-route" <?php checked( $options['route_checked'] ); ?>>
					<span class="map-control-icon dashicons dashicons-chart-line"></span>
					<span class="map-control-label"><?php esc_html_e( 'Show Route', 'wp-art-routes' ); ?></span>
				</label>
			<?php endif; ?>
			
			<?php if ( $options['show_user_location'] ) : ?>
				<label class="map-control-item">
					<input type="checkbox" id="toggle-user-location" <?php checked( $options['user_location_checked'] ); ?>>
					<span class="map-control-icon dashicons dashicons-location"></span>
					<span class="map-control-label"><?php esc_html_e( 'Show My Location', 'wp-art-routes' ); ?></span>
				</label>
			<?php endif; ?>
		</div>
		
		<?php if ( $options['show_navigation'] ) : ?>
			<div class="map-navigation-buttons">
				<button type="button" id="go-to-my-location" class="map-nav-button">
					<span class="map-control-icon dashicons dashicons-location-alt"></span>
					<span class="map-control-label"><?php esc_html_e( 'Go to My Location', 'wp-art-routes' ); ?></span>
				</button>
				
				<button type="button" id="go-to-route" class="map-nav-button">
					<span class="map-control-icon dashicons dashicons-admin-site"></span>
					<span class="map-control-label"><?php esc_html_e( 'Go to Route', 'wp-art-routes' ); ?></span>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Format duration in minutes to a readable string (e.g. "2 hours 23 minutes")
 */
function wp_art_routes_format_duration( $minutes ) {
	$minutes = intval( $minutes );
	if ( $minutes < 1 ) {
		return __( 'Less than a minute', 'wp-art-routes' );
	}
	$hours = floor( $minutes / 60 );
	$mins  = $minutes % 60;
	$parts = array();
	if ( $hours > 0 ) {
		$parts[] = sprintf( _n( '%d hour', '%d hours', $hours, 'wp-art-routes' ), $hours );
	}
	if ( $mins > 0 ) {
		$parts[] = sprintf( _n( '%d minute', '%d minutes', $mins, 'wp-art-routes' ), $mins );
	}
	return implode( ' ', $parts );
}

/**
 * Format route length in kilometers to a consistent string (e.g. "3.2 km")
 */
function wp_art_routes_format_length( $km ) {
	$km = floatval( $km );
	return number_format( round( $km, 1 ), 1 ) . ' km';
}