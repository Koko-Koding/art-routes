<?php

/**
 * Template for displaying related artworks for the current post/page/artist
 *
 * @package Art_Routes
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (empty($related_artworks)) {
    return '...';
}
?>
<section class="art-routes-related-artworks">
    <h2>Gerelateerde werken</h2>
    <div class="related-artworks-list">
        <?php foreach ($related_artworks as $artwork) : ?>
            <article class="related-artwork-item">
                <a href="<?php echo esc_url(get_permalink($artwork->ID)); ?>" class="related-artwork-link">
                    <div class="related-artwork-image">
                        <?php echo get_the_post_thumbnail($artwork->ID, 'medium'); ?>
                    </div>
                    <div class="related-artwork-info">
                        <h3 class="related-artwork-title"><?php echo esc_html(get_the_title($artwork->ID)); ?></h3>
                        <div class="related-artwork-excerpt">
                            <?php
                            $excerpt = $artwork->post_excerpt;
                            if (empty($excerpt)) {
                                $excerpt = wp_trim_words(strip_shortcodes($artwork->post_content), 20, '...');
                            }
                            echo esc_html($excerpt);
                            ?>
                        </div>
                        <span class="related-artwork-more">Lees verder &rarr;</span>
                    </div>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</section>