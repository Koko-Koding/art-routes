<?php
/**
 * Single Information Point Template
 * 
 * Template for displaying individual information point posts
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="wp-art-routes-single-info-point">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('info-point-single'); ?>>
            
            <header class="info-point-header">
                <h1 class="info-point-title"><?php the_title(); ?></h1>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="info-point-featured-image">
                    <?php the_post_thumbnail('large', ['class' => 'info-point-image']); ?>
                </div>
            <?php endif; ?>

            <div class="info-point-content">
                <?php the_content(); ?>
            </div>

            <?php
            // Display map if coordinates are available
            $latitude = get_post_meta(get_the_ID(), '_artwork_latitude', true);
            $longitude = get_post_meta(get_the_ID(), '_artwork_longitude', true);
            
            if (is_numeric($latitude) && is_numeric($longitude)) :
            ?>
                <div class="info-point-location-map">
                    <h3><?php _e('Location on Map', 'wp-art-routes'); ?></h3>
                    <div id="info-point-single-map" style="height: 300px; margin: 20px 0;"></div>
                    
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            // Initialize map
                            var map = L.map('info-point-single-map').setView([<?php echo esc_js($latitude); ?>, <?php echo esc_js($longitude); ?>], 15);
                            
                            // Add tile layer
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '© OpenStreetMap contributors'
                            }).addTo(map);
                            
                            // Add marker for information point
                            var infoPointIcon = L.divIcon({
                                className: 'info-point-marker',
                                html: '<div class="info-point-marker-inner"><i>i</i></div>',
                                iconSize: [30, 30],
                                iconAnchor: [15, 15]
                            });
                            
                            L.marker([<?php echo esc_js($latitude); ?>, <?php echo esc_js($longitude); ?>], {
                                icon: infoPointIcon
                            }).addTo(map)
                            .bindPopup('<strong><?php echo esc_js(get_the_title()); ?></strong>')
                            .openPopup();
                        });
                    </script>
                </div>
            <?php endif; ?>

        </article>
    <?php endwhile; ?>
</div>

<style>
.wp-art-routes-single-info-point {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.info-point-header {
    margin-bottom: 30px;
}

.info-point-title {
    font-size: 2.5em;
    margin-bottom: 15px;
    color: #333;
}

.info-point-featured-image {
    margin-bottom: 30px;
}

.info-point-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.info-point-content {
    margin-bottom: 40px;
    line-height: 1.6;
    font-size: 1.1em;
}

.info-point-location-map {
    margin-bottom: 40px;
}

.info-point-location-map .leaflet-popup-content {
    margin: 13px 24px 13px 20px;
}

.info-point-location-map h3 {
    margin-bottom: 15px;
    color: #333;
}

/* Info point marker styling */
.info-point-marker {
    background: transparent;
}

.info-point-marker-inner {
    width: 30px;
    height: 30px;
    background-color: #007cba;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .wp-art-routes-single-info-point {
        padding: 15px;
    }
    
    .info-point-title {
        font-size: 2em;
    }
}
</style>

<?php get_footer(); ?>