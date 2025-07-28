<?php
/**
 * Single Artwork Template
 *
 * Template for displaying individual artwork posts
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="wp-art-routes-single-artwork">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'artwork-single' ); ?>>
			
			<header class="artwork-header">
				<h1 class="artwork-title"><?php the_title(); ?></h1>
				
				<?php
				// Display artwork number and location if available
				$artwork_number   = get_post_meta( get_the_ID(), '_artwork_number', true );
				$artwork_location = get_post_meta( get_the_ID(), '_artwork_location', true );

				if ( ! empty( $artwork_number ) || ! empty( $artwork_location ) ) :
					?>
					<div class="artwork-meta">
						<?php if ( ! empty( $artwork_number ) ) : ?>
							<span class="artwork-number">
								<strong><?php esc_html_e( 'Number:', 'wp-art-routes' ); ?></strong> <?php echo esc_html( $artwork_number ); ?>
							</span>
						<?php endif; ?>
						
						<?php if ( ! empty( $artwork_location ) ) : ?>
							<span class="artwork-location">
								<strong><?php esc_html_e( 'Location:', 'wp-art-routes' ); ?></strong> <?php echo esc_html( $artwork_location ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="artwork-featured-image">
					<?php the_post_thumbnail( 'large', array( 'class' => 'artwork-image' ) ); ?>
				</div>
			<?php endif; ?>

			<div class="artwork-content">
				<?php the_content(); ?>
			</div>

			<?php
			// Display associated artists
			$artist_ids = get_post_meta( get_the_ID(), '_artwork_artist_ids', true );

			if ( is_array( $artist_ids ) && ! empty( $artist_ids ) ) :
				?>
				<div class="artwork-artists">
					<h3 class="artists-heading">
						<?php
						echo count( $artist_ids ) > 1
							? __( 'Artists:', 'wp-art-routes' )
							: __( 'Artist:', 'wp-art-routes' );
						?>
					</h3>
					
					<ul class="artists-list">
						<?php
						foreach ( $artist_ids as $artist_id ) :
							$artist_post = get_post( $artist_id );
							if ( $artist_post ) :
								$post_type_obj = get_post_type_object( $artist_post->post_type );
								?>
							<li class="artist-item">
								<a href="<?php echo esc_url( get_permalink( $artist_id ) ); ?>" class="artist-link">
									<?php echo esc_html( $artist_post->post_title ); ?>
								</a>
							</li>
								<?php
							endif;
						endforeach;
						?>
					</ul>
				</div>
			<?php endif; ?>

			<?php
			// Display map if coordinates are available
			$latitude  = get_post_meta( get_the_ID(), '_artwork_latitude', true );
			$longitude = get_post_meta( get_the_ID(), '_artwork_longitude', true );

			if ( is_numeric( $latitude ) && is_numeric( $longitude ) ) :
				?>
				<div class="artwork-location-map">
					<h3><?php esc_html_e( 'Location on Map', 'wp-art-routes' ); ?></h3>
					<div id="artwork-single-map" style="height: 300px; margin: 20px 0;"></div>
					
					<script type="text/javascript">
						jQuery(document).ready(function($) {
							// Initialize map
							var map = L.map('artwork-single-map').setView([<?php echo esc_js( $latitude ); ?>, <?php echo esc_js( $longitude ); ?>], 15);
							
							// Add tile layer
							L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
								attribution: '© OpenStreetMap contributors'
							}).addTo(map);
							
							// Add marker for artwork
							var artworkIcon = L.divIcon({
								className: 'artwork-marker',
								html: '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'<?php echo esc_js( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>\');"></div><div class="artwork-marker-overlay"></div></div>',
								iconSize: [40, 40],
								iconAnchor: [20, 20]
							});
							
							L.marker([<?php echo esc_js( $latitude ); ?>, <?php echo esc_js( $longitude ); ?>], {
								icon: artworkIcon
							}).addTo(map)
							.bindPopup('<strong><?php echo esc_js( get_the_title() ); ?></strong>')
							.openPopup();
						});
					</script>
				</div>
			<?php endif; ?>

		</article>
	<?php endwhile; ?>
</div>

<style>
.wp-art-routes-single-artwork {
	max-width: 800px;
	margin: 0 auto;
	padding: 20px;
}

.artwork-header {
	margin-bottom: 30px;
}

.artwork-title {
	font-size: 2.5em;
	margin-bottom: 15px;
	color: #333;
}

.artwork-meta {
	display: flex;
	gap: 20px;
	flex-wrap: wrap;
	margin-bottom: 20px;
	padding: 15px;
	background-color: #f8f9fa;
	border-radius: 5px;
}

.artwork-meta span {
	color: #666;
}

.artwork-featured-image {
	margin-bottom: 30px;
}

.artwork-image {
	width: 100%;
	height: auto;
	border-radius: 8px;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.artwork-content {
	margin-bottom: 40px;
	line-height: 1.6;
	font-size: 1.1em;
}

.artwork-artists {
	margin-bottom: 40px;
	padding: 20px;
	background-color: #f8f9fa;
	border-radius: 8px;
}

.artwork-artists .artists-heading {
	margin: 0 0 15px 0;
	padding-bottom: 0px;
	color: #333;
	font-size: 1.3em;
}

.artists-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.artist-item {
	margin-bottom: 8px;
	padding: 8px 0;
	border-bottom: 1px solid #e9ecef;
}

.artist-item:last-child {
	border-bottom: none;
}

.artist-link {
	color: #0073aa;
	text-decoration: none;
	font-weight: 500;
	font-size: 1.1em;
}

.artist-link:hover {
	text-decoration: underline;
}

.artist-post-type {
	color: #666;
	font-style: italic;
	font-size: 0.9em;
	margin-left: 8px;
}

.artwork-location-map {
	margin-bottom: 40px;
}

.artwork-location-map .leaflet-popup-content {
	margin: 13px 24px 13px 20px;
}

.artwork-location-map h3 {
	margin-bottom: 15px;
	color: #333;
}

/* Mobile responsive */
@media (max-width: 768px) {
	.wp-art-routes-single-artwork {
		padding: 15px;
	}
	
	.artwork-title {
		font-size: 2em;
	}
	
	.artwork-meta {
		flex-direction: column;
		gap: 10px;
	}
}
</style>

<?php get_footer(); ?>