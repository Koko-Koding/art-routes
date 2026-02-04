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
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="wp-art-routes-single-edition">
    <?php while (have_posts()) : the_post();
        $edition_id = get_the_ID();

        // Get edition-specific content
        $start_date = get_post_meta($edition_id, '_edition_start_date', true);
        $end_date = get_post_meta($edition_id, '_edition_end_date', true);

        // Get edition-linked items
        $routes = wp_art_routes_get_edition_routes($edition_id);
        $artworks = wp_art_routes_get_edition_artworks($edition_id);
        $info_points = wp_art_routes_get_edition_information_points($edition_id);

        // Get terminology labels for this edition
        $route_label_singular = wp_art_routes_label('route', false, $edition_id);
        $route_label_plural = wp_art_routes_label('route', true, $edition_id);
        $location_label_singular = wp_art_routes_label('location', false, $edition_id);
        $location_label_plural = wp_art_routes_label('location', true, $edition_id);
        $info_point_label_singular = wp_art_routes_label('info_point', false, $edition_id);
        $info_point_label_plural = wp_art_routes_label('info_point', true, $edition_id);
        $creator_label_singular = wp_art_routes_label('creator', false, $edition_id);
        $creator_label_plural = wp_art_routes_label('creator', true, $edition_id);
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
                                esc_html__('%1$s - %2$s', 'wp-art-routes'),
                                esc_html($formatted_start),
                                esc_html($formatted_end)
                            );
                        } elseif ($formatted_start) {
                            /* translators: %s: start date */
                            printf(esc_html__('Starting %s', 'wp-art-routes'), esc_html($formatted_start));
                        } elseif ($formatted_end) {
                            /* translators: %s: end date */
                            printf(esc_html__('Until %s', 'wp-art-routes'), esc_html($formatted_end));
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
                $js_data = wp_art_routes_prepare_edition_map_data($edition_id, $routes, $artworks, $info_points);
            ?>
                <section class="edition-map-section">
                    <h2 class="edition-section-title"><?php esc_html_e('Map', 'wp-art-routes'); ?></h2>
                    <div id="<?php echo esc_attr($map_id); ?>" class="edition-map" style="height: 500px;"></div>

                    <!-- Loading indicator -->
                    <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
                        <div class="spinner"></div>
                        <p><?php esc_html_e('Loading map...', 'wp-art-routes'); ?></p>
                    </div>
                </section>

                <script type="text/javascript">
                    document.addEventListener('DOMContentLoaded', function() {
                        const mapId = '<?php echo esc_js($map_id); ?>';
                        const editionMapData = <?php echo wp_json_encode($js_data); ?>;

                        // Initialize the edition map
                        if (typeof initializeEditionMap === 'function') {
                            initializeEditionMap(mapId, editionMapData);
                        } else {
                            // Fallback initialization if function not available
                            initializeEditionMapFallback(mapId, editionMapData);
                        }
                    });

                    /**
                     * Fallback map initialization
                     */
                    function initializeEditionMapFallback(mapId, mapData) {
                        // Show loading indicator
                        const loadingEl = document.getElementById('map-loading-' + mapId);
                        if (loadingEl) loadingEl.style.display = 'block';

                        // Create the map
                        const map = L.map(mapId);

                        // Add the OpenStreetMap tile layer
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                            maxZoom: 19
                        }).addTo(map);

                        // Create bounds for all content
                        const allBounds = L.latLngBounds();
                        let hasValidCoordinates = false;

                        // Define colors for routes
                        const routeColors = [
                            '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4',
                            '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990'
                        ];

                        // Add routes
                        if (mapData.routes && mapData.routes.length > 0) {
                            mapData.routes.forEach(function(route, index) {
                                if (route.route_path && route.route_path.length > 0) {
                                    const color = routeColors[index % routeColors.length];
                                    const polyline = L.polyline(route.route_path, {
                                        color: color,
                                        weight: 4,
                                        opacity: 0.7
                                    }).addTo(map);

                                    polyline.bindPopup('<strong>' + route.title + '</strong>');

                                    route.route_path.forEach(function(point) {
                                        if (point.lat && point.lng) {
                                            allBounds.extend([point.lat, point.lng]);
                                        } else if (Array.isArray(point)) {
                                            allBounds.extend(point);
                                        }
                                        hasValidCoordinates = true;
                                    });
                                }
                            });
                        }

                        // Add artwork markers
                        if (mapData.artworks && mapData.artworks.length > 0) {
                            mapData.artworks.forEach(function(artwork) {
                                const artworkIcon = L.divIcon({
                                    className: 'artwork-marker',
                                    html: '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'' + (artwork.image_url || '') + '\');"></div></div>',
                                    iconSize: [40, 40],
                                    iconAnchor: [20, 20]
                                });

                                const marker = L.marker([artwork.latitude, artwork.longitude], {
                                    icon: artworkIcon
                                }).addTo(map);

                                let popupContent = '<div class="artwork-popup">';
                                if (artwork.image_url) {
                                    popupContent += '<div class="artwork-popup-image"><img src="' + artwork.image_url + '" alt="' + artwork.title + '"></div>';
                                }
                                popupContent += '<div class="artwork-popup-content"><h3>' + artwork.title + '</h3>';
                                if (artwork.excerpt) {
                                    popupContent += '<div class="artwork-excerpt">' + artwork.excerpt + '</div>';
                                }
                                popupContent += '<a href="' + artwork.permalink + '" class="artwork-link"><?php echo esc_js(__('View details', 'wp-art-routes')); ?></a>';
                                popupContent += '</div></div>';

                                marker.bindPopup(popupContent, { maxWidth: 300 });

                                allBounds.extend([artwork.latitude, artwork.longitude]);
                                hasValidCoordinates = true;
                            });
                        }

                        // Add information point markers
                        if (mapData.info_points && mapData.info_points.length > 0) {
                            mapData.info_points.forEach(function(infoPoint) {
                                let iconHtml = '<div class="info-point-marker-inner">i</div>';
                                if (infoPoint.icon_url) {
                                    iconHtml = '<div class="info-point-marker-inner" style="background: url(\'' + infoPoint.icon_url + '\') center center / contain no-repeat;"></div>';
                                }

                                const infoPointIcon = L.divIcon({
                                    className: 'info-point-marker',
                                    html: iconHtml,
                                    iconSize: [30, 30],
                                    iconAnchor: [15, 15]
                                });

                                const marker = L.marker([infoPoint.latitude, infoPoint.longitude], {
                                    icon: infoPointIcon
                                }).addTo(map);

                                let popupContent = '<div class="info-point-popup">';
                                if (infoPoint.image_url) {
                                    popupContent += '<div class="info-point-popup-image"><img src="' + infoPoint.image_url + '" alt="' + infoPoint.title + '"></div>';
                                }
                                popupContent += '<div class="info-point-popup-content"><h3>' + infoPoint.title + '</h3>';
                                if (infoPoint.excerpt) {
                                    popupContent += '<div class="info-point-excerpt">' + infoPoint.excerpt + '</div>';
                                }
                                popupContent += '<a href="' + infoPoint.permalink + '" class="info-point-link"><?php echo esc_js(__('Read more', 'wp-art-routes')); ?></a>';
                                popupContent += '</div></div>';

                                marker.bindPopup(popupContent, { maxWidth: 300 });

                                allBounds.extend([infoPoint.latitude, infoPoint.longitude]);
                                hasValidCoordinates = true;
                            });
                        }

                        // Set map view
                        if (hasValidCoordinates && allBounds.isValid()) {
                            map.fitBounds(allBounds, { padding: [30, 30] });
                        } else {
                            // Default view (Netherlands)
                            map.setView([52.1326, 5.2913], 7);
                        }

                        // Hide loading indicator
                        if (loadingEl) loadingEl.style.display = 'none';
                    }
                </script>
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
                                                    <?php echo esc_html(wp_art_routes_format_length($route['length'])); ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($route['duration'])) : ?>
                                                <span class="route-duration">
                                                    <?php echo esc_html(wp_art_routes_format_duration($route['duration'])); ?>
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
                                        printf(esc_html__('View %s', 'wp-art-routes'), esc_html($route_label_singular));
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
                                        printf(esc_html__('View %s', 'wp-art-routes'), esc_html($location_label_singular));
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

<style>
/* Edition Single Template Styles */
.wp-art-routes-single-edition {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header */
.edition-header {
    margin-bottom: 40px;
    text-align: center;
}

.edition-title {
    font-size: 2.5em;
    margin-bottom: 15px;
    color: #333;
}

.edition-dates {
    font-size: 1.1em;
    color: #666;
    margin-bottom: 20px;
}

.edition-featured-image {
    margin-top: 20px;
}

.edition-image {
    width: 100%;
    max-width: 800px;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Content */
.edition-content {
    margin-bottom: 40px;
    line-height: 1.7;
    font-size: 1.1em;
}

/* Section Titles */
.edition-section-title {
    font-size: 1.8em;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #0073aa;
    color: #333;
}

/* Map Section */
.edition-map-section {
    margin-bottom: 50px;
}

.edition-map {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.map-loading {
    text-align: center;
    padding: 20px;
}

.map-loading .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #0073aa;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Routes Section */
.edition-routes-section {
    margin-bottom: 50px;
}

.edition-routes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
}

.edition-route-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.edition-route-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.route-card-image img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.route-card-content {
    padding: 20px;
}

.route-card-title {
    font-size: 1.3em;
    margin: 0 0 10px 0;
}

.route-card-title a {
    color: #333;
    text-decoration: none;
}

.route-card-title a:hover {
    color: #0073aa;
}

.route-card-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #666;
}

.route-card-excerpt {
    color: #555;
    margin-bottom: 15px;
    font-size: 0.95em;
    line-height: 1.5;
}

.route-card-link {
    display: inline-block;
    color: #0073aa;
    text-decoration: none;
    font-weight: 500;
}

.route-card-link:hover {
    text-decoration: underline;
}

/* Locations Section */
.edition-locations-section {
    margin-bottom: 50px;
}

.edition-locations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 25px;
}

.edition-location-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}

.edition-location-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.location-card-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.location-card-content {
    padding: 20px;
}

.location-card-title {
    font-size: 1.2em;
    margin: 0 0 10px 0;
}

.location-card-title a {
    color: #333;
    text-decoration: none;
}

.location-card-title a:hover {
    color: #0073aa;
}

.location-number {
    color: #0073aa;
    font-weight: bold;
}

.location-card-address {
    color: #666;
    font-size: 0.9em;
    margin-bottom: 8px;
}

.location-card-creators {
    font-size: 0.9em;
    color: #555;
    margin-bottom: 10px;
}

.location-card-creators a {
    color: #0073aa;
    text-decoration: none;
}

.location-card-creators a:hover {
    text-decoration: underline;
}

.creators-label {
    font-weight: 500;
}

.location-card-excerpt {
    color: #555;
    margin-bottom: 15px;
    font-size: 0.9em;
    line-height: 1.5;
}

.location-card-link {
    display: inline-block;
    color: #0073aa;
    text-decoration: none;
    font-weight: 500;
}

.location-card-link:hover {
    text-decoration: underline;
}

/* Info Points Section */
.edition-info-points-section {
    margin-bottom: 50px;
}

.edition-info-points-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.edition-info-point-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    transition: background-color 0.2s;
}

.edition-info-point-item:hover {
    background: #f0f0f0;
}

.info-point-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
}

.info-point-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.info-point-content {
    flex: 1;
}

.info-point-title {
    display: block;
    font-size: 1.1em;
    font-weight: 500;
    color: #333;
    text-decoration: none;
    margin-bottom: 5px;
}

.info-point-title:hover {
    color: #0073aa;
}

.info-point-excerpt {
    margin: 0;
    color: #666;
    font-size: 0.95em;
    line-height: 1.5;
}

/* Map Marker Styles (inline for template) */
.artwork-marker-inner {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    background: #f4f4f4;
}

.artwork-marker-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
}

.info-point-marker-inner {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #ffc107;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 16px;
    border: 2px solid #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .wp-art-routes-single-edition {
        padding: 15px;
    }

    .edition-title {
        font-size: 2em;
    }

    .edition-section-title {
        font-size: 1.5em;
    }

    .edition-routes-grid,
    .edition-locations-grid {
        grid-template-columns: 1fr;
    }

    .edition-info-point-item {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .edition-title {
        font-size: 1.6em;
    }

    .route-card-meta {
        flex-direction: column;
        gap: 5px;
    }
}
</style>

<?php get_footer(); ?>
