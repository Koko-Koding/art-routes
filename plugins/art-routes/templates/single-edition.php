<?php
/**
 * Single Edition Template
 *
 * Template for displaying individual edition posts showing:
 * - Edition title, featured image, content
 * - Optional date range
 * - Interactive map (if routes/artworks/info points exist)
 * - Routes grid section
 * - Locations grid section
 * - Info Points list section
 *
 * @package Art_Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="art-routes-single-edition">
    <?php while (have_posts()) : the_post();
        $edition_id = get_the_ID();

        // Get edition-specific content
        $start_date = get_post_meta($edition_id, '_edition_start_date', true);
        $end_date = get_post_meta($edition_id, '_edition_end_date', true);

        // Get edition-linked items
        $routes = art_routes_get_edition_routes($edition_id);
        $artworks = art_routes_get_edition_artworks($edition_id);
        $info_points = art_routes_get_edition_information_points($edition_id);

        // Get terminology labels for this edition
        $route_label_singular = art_routes_label('route', false, $edition_id);
        $route_label_plural = art_routes_label('route', true, $edition_id);
        $location_label_singular = art_routes_label('location', false, $edition_id);
        $location_label_plural = art_routes_label('location', true, $edition_id);
        $info_point_label_singular = art_routes_label('info_point', false, $edition_id);
        $info_point_label_plural = art_routes_label('info_point', true, $edition_id);
        $creator_label_singular = art_routes_label('creator', false, $edition_id);
        $creator_label_plural = art_routes_label('creator', true, $edition_id);
    ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('edition-single'); ?>>

            <header class="edition-header">
                <h1 class="edition-title"><?php the_title(); ?></h1>

                <?php if (!empty($start_date) || !empty($end_date)) : ?>
                    <div class="edition-dates">
                        <?php
                        // Format dates for display
                        $date_format = get_option('date_format', 'F j, Y');
                        $formatted_start = !empty($start_date) ? date_i18n($date_format, strtotime($start_date)) : '';
                        $formatted_end = !empty($end_date) ? date_i18n($date_format, strtotime($end_date)) : '';

                        if ($formatted_start && $formatted_end) {
                            printf(
                                /* translators: %1$s: start date, %2$s: end date */
                                esc_html__('%1$s - %2$s', 'art-routes'),
                                esc_html($formatted_start),
                                esc_html($formatted_end)
                            );
                        } elseif ($formatted_start) {
                            /* translators: %s: start date */
                            printf(esc_html__('Starting %s', 'art-routes'), esc_html($formatted_start));
                        } elseif ($formatted_end) {
                            /* translators: %s: end date */
                            printf(esc_html__('Until %s', 'art-routes'), esc_html($formatted_end));
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="edition-featured-image">
                        <?php the_post_thumbnail('large', ['class' => 'edition-image']); ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if (get_the_content()) : ?>
                <div class="edition-content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <?php
            // Display map section if there's content to show
            if (!empty($routes) || !empty($artworks) || !empty($info_points)) :
                // Generate a unique map ID
                $map_id = 'edition-map-' . $edition_id;

                // Prepare data for JavaScript
                $js_data = art_routes_prepare_edition_map_data($edition_id, $routes, $artworks, $info_points);
            ?>
                <section class="edition-map-section">
                    <h2 class="edition-section-title"><?php esc_html_e('Map', 'art-routes'); ?></h2>
                    <div id="<?php echo esc_attr($map_id); ?>" class="edition-map" style="height: 500px;"></div>

                    <!-- Loading indicator -->
                    <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
                        <div class="spinner"></div>
                        <p><?php esc_html_e('Loading map...', 'art-routes'); ?></p>
                    </div>
                </section>

                <?php
                // Enqueue edition map assets
                wp_enqueue_script('art-routes-edition-map-js');

                // Pass per-instance data via inline script
                wp_add_inline_script('art-routes-edition-map-js',
                    'var artRoutesEditionMapInstances = artRoutesEditionMapInstances || [];' .
                    'artRoutesEditionMapInstances.push(' . wp_json_encode([
                        'mapId'   => $map_id,
                        'mapData' => $js_data,
                        'options' => ['variant' => 'single'],
                    ]) . ');',
                    'before'
                );
                ?>
            <?php endif; ?>

            <?php if (!empty($routes)) : ?>
                <section class="edition-routes-section">
                    <h2 class="edition-section-title"><?php echo esc_html($route_label_plural); ?></h2>
                    <div class="edition-routes-grid">
                        <?php foreach ($routes as $route) : ?>
                            <div class="edition-route-card">
                                <?php if (!empty($route['image'])) : ?>
                                    <div class="route-card-image">
                                        <a href="<?php echo esc_url(get_permalink($route['id'])); ?>">
                                            <img src="<?php echo esc_url($route['image']); ?>" alt="<?php echo esc_attr($route['title']); ?>">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="route-card-content">
                                    <h3 class="route-card-title">
                                        <a href="<?php echo esc_url(get_permalink($route['id'])); ?>">
                                            <?php echo esc_html($route['title']); ?>
                                        </a>
                                    </h3>

                                    <?php if (!empty($route['length']) || !empty($route['duration'])) : ?>
                                        <div class="route-card-meta">
                                            <?php if (!empty($route['length'])) : ?>
                                                <span class="route-length">
                                                    <?php echo esc_html(art_routes_format_length($route['length'])); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($route['duration'])) : ?>
                                                <span class="route-duration">
                                                    <?php echo esc_html(art_routes_format_duration($route['duration'])); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($route['excerpt'])) : ?>
                                        <div class="route-card-excerpt">
                                            <?php echo wp_kses_post($route['excerpt']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?php echo esc_url(get_permalink($route['id'])); ?>" class="route-card-link">
                                        <?php
                                        /* translators: %s: route label singular */
                                        printf(esc_html__('View %s', 'art-routes'), esc_html($route_label_singular));
                                        ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($artworks)) : ?>
                <section class="edition-locations-section">
                    <h2 class="edition-section-title"><?php echo esc_html($location_label_plural); ?></h2>
                    <div class="edition-locations-grid">
                        <?php foreach ($artworks as $artwork) : ?>
                            <div class="edition-location-card">
                                <?php if (!empty($artwork['image_url'])) : ?>
                                    <div class="location-card-image">
                                        <a href="<?php echo esc_url($artwork['permalink']); ?>">
                                            <img src="<?php echo esc_url($artwork['image_url']); ?>" alt="<?php echo esc_attr($artwork['title']); ?>">
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <div class="location-card-content">
                                    <h3 class="location-card-title">
                                        <a href="<?php echo esc_url($artwork['permalink']); ?>">
                                            <?php if (!empty($artwork['number'])) : ?>
                                                <span class="location-number"><?php echo esc_html($artwork['number']); ?>.</span>
                                            <?php endif; ?>
                                            <?php echo esc_html($artwork['title']); ?>
                                        </a>
                                    </h3>

                                    <?php if (!empty($artwork['location'])) : ?>
                                        <div class="location-card-address">
                                            <?php echo esc_html($artwork['location']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($artwork['artists'])) : ?>
                                        <div class="location-card-creators">
                                            <span class="creators-label">
                                                <?php
                                                echo count($artwork['artists']) > 1
                                                    ? esc_html($creator_label_plural)
                                                    : esc_html($creator_label_singular);
                                                ?>:
                                            </span>
                                            <?php
                                            $artist_names = array_map(function($artist) {
                                                return '<a href="' . esc_url($artist['url']) . '">' . esc_html($artist['title']) . '</a>';
                                            }, $artwork['artists']);
                                            echo wp_kses_post(implode(', ', $artist_names));
                                            ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($artwork['excerpt'])) : ?>
                                        <div class="location-card-excerpt">
                                            <?php echo wp_kses_post($artwork['excerpt']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <a href="<?php echo esc_url($artwork['permalink']); ?>" class="location-card-link">
                                        <?php
                                        /* translators: %s: location label singular */
                                        printf(esc_html__('View %s', 'art-routes'), esc_html($location_label_singular));
                                        ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($info_points)) : ?>
                <section class="edition-info-points-section">
                    <h2 class="edition-section-title"><?php echo esc_html($info_point_label_plural); ?></h2>
                    <ul class="edition-info-points-list">
                        <?php foreach ($info_points as $info_point) : ?>
                            <li class="edition-info-point-item">
                                <?php if (!empty($info_point['icon_url'])) : ?>
                                    <span class="info-point-icon">
                                        <img src="<?php echo esc_url($info_point['icon_url']); ?>" alt="" aria-hidden="true">
                                    </span>
                                <?php endif; ?>
                                <div class="info-point-content">
                                    <a href="<?php echo esc_url($info_point['permalink']); ?>" class="info-point-title">
                                        <?php echo esc_html($info_point['title']); ?>
                                    </a>
                                    <?php if (!empty($info_point['excerpt'])) : ?>
                                        <p class="info-point-excerpt"><?php echo esc_html($info_point['excerpt']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>

        </article>
    <?php endwhile; ?>
</div>


<?php get_footer(); ?>
