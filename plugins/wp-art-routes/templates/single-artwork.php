<?php
/**
 * Single Artwork Template
 * 
 * Template for displaying individual artwork posts
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="wp-art-routes-single-artwork">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('artwork-single'); ?>>
            
            <header class="artwork-header">
                <h1 class="artwork-title"><?php the_title(); ?></h1>
                
                <?php
                // Display artwork number and location if available
                $artwork_number = get_post_meta(get_the_ID(), '_artwork_number', true);
                $artwork_location = get_post_meta(get_the_ID(), '_artwork_location', true);
                
                if (!empty($artwork_number) || !empty($artwork_location)) :
                ?>
                    <div class="artwork-meta">
                        <?php if (!empty($artwork_number)) : ?>
                            <span class="artwork-number">
                                <strong><?php esc_html_e('Number:', 'wp-art-routes'); ?></strong> <?php echo esc_html($artwork_number); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($artwork_location)) : ?>
                            <span class="artwork-location">
                                <strong><?php esc_html_e('Location:', 'wp-art-routes'); ?></strong> <?php echo esc_html($artwork_location); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="artwork-featured-image">
                    <?php the_post_thumbnail('large', ['class' => 'artwork-image']); ?>
                </div>
            <?php endif; ?>

            <div class="artwork-content">
                <?php the_content(); ?>
            </div>

            <?php
            // Accessibility icons (wheelchair & stroller)
            $wheelchair_accessible = get_post_meta(get_the_ID(), '_wheelchair_accessible', true);
            $stroller_accessible = get_post_meta(get_the_ID(), '_stroller_accessible', true);
            if ($wheelchair_accessible === '1' || $stroller_accessible === '1') :
            ?>
                <div class="artwork-accessibility">
                    <?php if ($wheelchair_accessible === '1') : ?>
                        <span class="artwork-accessibility-item" title="<?php esc_attr_e('Wheelchair accessible', 'wp-art-routes'); ?>">
                            <img src="<?php echo esc_url(plugins_url('assets/icons/legacy/WB-plattegrond-Rolstoel.svg', dirname(__FILE__))); ?>" alt="<?php esc_attr_e('Wheelchair accessible', 'wp-art-routes'); ?>" />
                            <span class="artwork-accessibility-label"><?php esc_html_e('Wheelchair accessible', 'wp-art-routes'); ?></span>
                        </span>
                    <?php endif; ?>
                    <?php if ($stroller_accessible === '1') : ?>
                        <span class="artwork-accessibility-item" title="<?php esc_attr_e('Stroller accessible', 'wp-art-routes'); ?>">
                            <img src="<?php echo esc_url(plugins_url('assets/icons/legacy/WB-plattegrond-Kinderwagen.svg', dirname(__FILE__))); ?>" alt="<?php esc_attr_e('Stroller accessible', 'wp-art-routes'); ?>" />
                            <span class="artwork-accessibility-label"><?php esc_html_e('Stroller accessible', 'wp-art-routes'); ?></span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php
            // Display associated artists
            $artist_ids = get_post_meta(get_the_ID(), '_artwork_artist_ids', true);
            
            if (is_array($artist_ids) && !empty($artist_ids)) :
            ?>
                <div class="artwork-artists">
                    <h3 class="artists-heading">
                        <?php
                        echo count($artist_ids) > 1
                            ? esc_html__('Artists:', 'wp-art-routes')
                            : esc_html__('Artist:', 'wp-art-routes');
                        ?>
                    </h3>
                    
                    <ul class="artists-list">
                        <?php foreach ($artist_ids as $artist_id) : 
                            $artist_post = get_post($artist_id);
                            if ($artist_post) :
                                $post_type_obj = get_post_type_object($artist_post->post_type);
                        ?>
                            <li class="artist-item">
                                <a href="<?php echo esc_url(get_permalink($artist_id)); ?>" class="artist-link">
                                    <?php echo esc_html($artist_post->post_title); ?>
                                </a>
                            </li>
                        <?php 
                            endif;
                        endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php
            // Display map if coordinates are available
            $latitude = get_post_meta(get_the_ID(), '_artwork_latitude', true);
            $longitude = get_post_meta(get_the_ID(), '_artwork_longitude', true);
            
            if (is_numeric($latitude) && is_numeric($longitude)) :
            ?>
                <div class="artwork-location-map">
                    <h3><?php esc_html_e('Location on Map', 'wp-art-routes'); ?></h3>
                    <div id="artwork-single-map" style="height: 300px; margin: 20px 0;"></div>
                    
                    <?php
                    // Enqueue single artwork map assets
                    wp_enqueue_script('wp-art-routes-single-artwork-map-js');

                    // Pass artwork data to the map script
                    $icon_url = wp_art_routes_get_location_icon_url(get_the_ID());
                    wp_localize_script('wp-art-routes-single-artwork-map-js', 'wpArtRoutesSingleArtwork', [
                        'latitude'     => (float) $latitude,
                        'longitude'    => (float) $longitude,
                        'title'        => get_the_title(),
                        'thumbnailUrl' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                        'iconUrl'      => $icon_url ? esc_url($icon_url) : '',
                        'number'       => get_post_meta(get_the_ID(), '_artwork_number', true) ?: '',
                    ]);
                    ?>
                </div>
            <?php endif; ?>

        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
