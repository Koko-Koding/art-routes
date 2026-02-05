<?php
/**
 * Template for the edition_map shortcode
 * Displays an interactive map with routes, locations, and info points from an edition.
 *
 * @package WP Art Routes
 *
 * Variables passed via wp_art_routes_get_template_part():
 * @var array $atts        Shortcode attributes
 * @var int   $edition_id  Edition ID
 * @var array $edition     Edition data from wp_art_routes_get_edition_data()
 * @var array $routes      Array of route data from wp_art_routes_get_edition_routes()
 * @var array $artworks    Array of artwork data from wp_art_routes_get_edition_artworks()
 * @var array $info_points Array of info point data from wp_art_routes_get_edition_information_points()
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Generate a unique ID for this map instance
$map_id = 'edition-map-' . $edition_id . '-' . uniqid();

// Define a set of distinctive colors for routes
$route_colors = [
    '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4',
    '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990',
    '#dcbeff', '#9A6324', '#800000', '#aaffc3', '#808000',
    '#ffd8b1', '#000075', '#a9a9a9', '#000000', '#ffe119'
];

// Prepare data for JavaScript
$js_data = wp_art_routes_prepare_edition_map_data($edition_id, $routes, $artworks, $info_points);
$js_data['colors'] = $route_colors;

// Get terminology labels for this edition
$route_label_singular = wp_art_routes_label('route', false, $edition_id);
$route_label_plural = wp_art_routes_label('route', true, $edition_id);
$location_label_plural = wp_art_routes_label('location', true, $edition_id);
$info_point_label_plural = wp_art_routes_label('info_point', true, $edition_id);

// Define route type icons and labels
$route_types = [
    'walking' => [
        'icon' => 'dashicons dashicons-admin-users',
        'label' => __('Walking route', 'wp-art-routes')
    ],
    'cycling' => [
        'icon' => 'dashicons dashicons-controls-repeat',
        'label' => __('Bicycle route', 'wp-art-routes')
    ],
    'wheelchair' => [
        'icon' => 'dashicons dashicons-universal-access',
        'label' => __('Wheelchair friendly', 'wp-art-routes')
    ],
    'children' => [
        'icon' => 'dashicons dashicons-buddicons-groups',
        'label' => __('Child-friendly route', 'wp-art-routes')
    ],
];

?>

<div class="edition-map-container">
    <?php if ($atts['show_legend'] && !empty($routes)) : ?>
        <div class="edition-map-legend">
            <h3><?php echo esc_html($route_label_plural); ?></h3>

            <?php if (count($routes) > 1) : ?>
                <button class="zoom-to-all-routes-button edition-map-zoom-all" data-map-id="<?php echo esc_attr($map_id); ?>">
                    <span class="dashicons dashicons-admin-site"></span>
                    <?php
                    /* translators: %s: route label plural */
                    printf(esc_html__('Show All %s', 'wp-art-routes'), esc_html($route_label_plural));
                    ?>
                </button>
            <?php endif; ?>

            <ul class="edition-route-list">
                <?php foreach ($js_data['routes'] as $index => $route) :
                    $color = $route_colors[$index % count($route_colors)];
                ?>
                    <li class="edition-route-item">
                        <div class="edition-route-header">
                            <div class="edition-route-title-container">
                                <span class="edition-route-color-indicator" style="background-color: <?php echo esc_attr($color); ?>;"></span>
                                <span class="edition-route-title"><?php echo esc_html($route['title']); ?></span>
                            </div>
                            <a href="<?php echo esc_url($route['url']); ?>" class="edition-route-link" title="<?php esc_attr_e('View route details', 'wp-art-routes'); ?>">
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </a>
                        </div>

                        <div class="edition-route-meta">
                            <?php if (!empty($route['type']) && isset($route_types[$route['type']])) : ?>
                                <span class="edition-route-type">
                                    <span class="<?php echo esc_attr($route_types[$route['type']]['icon']); ?>"></span>
                                    <?php echo esc_html($route_types[$route['type']]['label']); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($route['length'])) : ?>
                                <span class="edition-route-length">
                                    <span class="dashicons dashicons-location"></span>
                                    <?php echo esc_html(wp_art_routes_format_length($route['length'])); ?>
                                </span>
                            <?php endif; ?>

                            <?php if (!empty($route['duration'])) : ?>
                                <span class="edition-route-duration">
                                    <span class="dashicons dashicons-clock"></span>
                                    <?php echo esc_html(wp_art_routes_format_duration($route['duration'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <button class="edition-zoom-to-route-button" data-route-index="<?php echo esc_attr($index); ?>" data-map-id="<?php echo esc_attr($map_id); ?>">
                            <span class="dashicons dashicons-search"></span>
                            <?php
                            /* translators: %s: route label singular */
                            printf(esc_html__('Zoom to %s', 'wp-art-routes'), esc_html($route_label_singular));
                            ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="edition-map-wrapper">
        <!-- Map container -->
        <div id="<?php echo esc_attr($map_id); ?>" class="edition-map"<?php echo !empty($atts['height']) ? ' style="height: ' . esc_attr($atts['height']) . ';"' : ''; ?>></div>

        <!-- Loading indicator -->
        <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php esc_html_e('Loading map...', 'wp-art-routes'); ?></p>
        </div>
    </div>

    <?php if ($atts['show_legend']) : ?>
        <!-- Map Controls -->
        <div class="edition-map-controls">
            <h4 class="edition-map-controls-title"><?php esc_html_e('Map Display Options', 'wp-art-routes'); ?></h4>
            <div class="edition-map-controls-grid">
                <?php if (!empty($routes)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-routes" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-chart-line"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: route label plural */
                            printf(esc_html__('Show %s', 'wp-art-routes'), esc_html($route_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>

                <?php if ($atts['show_locations'] && !empty($artworks)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-locations" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-art"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: location label plural */
                            printf(esc_html__('Show %s', 'wp-art-routes'), esc_html($location_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>

                <?php if ($atts['show_info_points'] && !empty($info_points)) : ?>
                    <label class="edition-map-control-item">
                        <input type="checkbox" class="edition-toggle-info-points" data-map-id="<?php echo esc_attr($map_id); ?>" checked>
                        <span class="edition-map-control-icon dashicons dashicons-info"></span>
                        <span class="edition-map-control-label">
                            <?php
                            /* translators: %s: info point label plural */
                            printf(esc_html__('Show %s', 'wp-art-routes'), esc_html($info_point_label_plural));
                            ?>
                        </span>
                    </label>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Map initialization script -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const mapId = '<?php echo esc_js($map_id); ?>';
        const editionMapData = <?php echo wp_json_encode($js_data); ?>;

        // Initialize the edition map
        initializeEditionMapShortcode(mapId, editionMapData);
    });

    /**
     * Initialize edition map for shortcode
     */
    function initializeEditionMapShortcode(mapId, mapData) {
        // Show loading indicator
        const loadingEl = document.getElementById('map-loading-' + mapId);
        if (loadingEl) loadingEl.style.display = 'block';

        // Map variables
        let map;
        const routeLayers = [];
        const routeBounds = [];
        let artworkLayerGroup;
        let infoPointLayerGroup;

        // Create the map
        map = L.map(mapId);

        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create bounds for all content
        const allBounds = L.latLngBounds();
        let hasValidCoordinates = false;

        // Create layer groups for artworks and info points
        artworkLayerGroup = L.layerGroup().addTo(map);
        infoPointLayerGroup = L.layerGroup().addTo(map);

        // Add routes
        if (mapData.routes && mapData.routes.length > 0) {
            mapData.routes.forEach(function(route, index) {
                const color = mapData.colors[index % mapData.colors.length];
                routeLayers[index] = L.layerGroup().addTo(map);
                routeBounds[index] = L.latLngBounds();

                if (route.route_path && route.route_path.length > 0) {
                    const polyline = L.polyline(route.route_path, {
                        color: color,
                        weight: 4,
                        opacity: 0.7,
                        lineJoin: 'round'
                    }).addTo(routeLayers[index]);

                    // Add popup with route title
                    polyline.bindPopup('<strong>' + route.title + '</strong><br><a href="' + route.url + '"><?php echo esc_js(__('View details', 'wp-art-routes')); ?></a>');

                    route.route_path.forEach(function(point) {
                        let lat, lng;
                        if (point.lat !== undefined && point.lng !== undefined) {
                            lat = point.lat;
                            lng = point.lng;
                        } else if (Array.isArray(point)) {
                            lat = point[0];
                            lng = point[1];
                        }
                        if (lat && lng) {
                            allBounds.extend([lat, lng]);
                            routeBounds[index].extend([lat, lng]);
                            hasValidCoordinates = true;
                        }
                    });
                }
            });
        }

        // Add artwork markers
        if (mapData.artworks && mapData.artworks.length > 0) {
            mapData.artworks.forEach(function(artwork) {
                // Use icon_url if available, otherwise fall back to image_url
                let artworkMarkerHtml;
                if (artwork.icon_url && artwork.icon_url.trim() !== '') {
                    // Show icon
                    artworkMarkerHtml = '<div class="artwork-marker-inner"><div class="artwork-marker-icon" style="background-image: url(\'' + artwork.icon_url + '\'); background-size: contain; background-repeat: no-repeat; background-position: center; width: 100%; height: 100%; border-radius: 50%;"></div></div>';
                } else {
                    // Show featured image or empty
                    const displayNumber = artwork.number && artwork.number.trim() !== '' ? artwork.number : '';
                    artworkMarkerHtml = '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'' + (artwork.image_url || '') + '\');"></div><div class="artwork-marker-overlay"></div><div class="artwork-marker-number">' + displayNumber + '</div></div>';
                }

                const artworkIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: artworkMarkerHtml,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                const marker = L.marker([artwork.latitude, artwork.longitude], {
                    icon: artworkIcon
                }).addTo(artworkLayerGroup);

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
                }).addTo(infoPointLayerGroup);

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

        // Store map reference and layer groups for controls
        window['editionMap_' + mapId] = {
            map: map,
            routeLayers: routeLayers,
            routeBounds: routeBounds,
            artworkLayerGroup: artworkLayerGroup,
            infoPointLayerGroup: infoPointLayerGroup,
            allBounds: allBounds
        };

        // Add zoom to route functionality
        document.querySelectorAll('.edition-zoom-to-route-button[data-map-id="' + mapId + '"]').forEach(function(button) {
            button.addEventListener('click', function() {
                const routeIndex = parseInt(this.getAttribute('data-route-index'));
                const mapRef = window['editionMap_' + mapId];

                if (mapRef && mapRef.routeBounds[routeIndex] && mapRef.routeBounds[routeIndex].isValid()) {
                    mapRef.map.flyToBounds(mapRef.routeBounds[routeIndex], {
                        padding: [30, 30],
                        duration: 0.8
                    });

                    // Visual feedback
                    document.querySelectorAll('.edition-zoom-to-route-button[data-map-id="' + mapId + '"]').forEach(function(btn) {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');
                }
            });
        });

        // Add zoom to all routes functionality
        const zoomAllButton = document.querySelector('.edition-map-zoom-all[data-map-id="' + mapId + '"]');
        if (zoomAllButton) {
            zoomAllButton.addEventListener('click', function() {
                const mapRef = window['editionMap_' + mapId];
                if (mapRef && mapRef.allBounds.isValid()) {
                    mapRef.map.flyToBounds(mapRef.allBounds, {
                        padding: [30, 30],
                        duration: 0.8
                    });

                    // Remove active state from all zoom buttons
                    document.querySelectorAll('.edition-zoom-to-route-button[data-map-id="' + mapId + '"]').forEach(function(btn) {
                        btn.classList.remove('active');
                    });
                }
            });
        }

        // Toggle route visibility
        const toggleRoutesCheckbox = document.querySelector('.edition-toggle-routes[data-map-id="' + mapId + '"]');
        if (toggleRoutesCheckbox) {
            toggleRoutesCheckbox.addEventListener('change', function() {
                const mapRef = window['editionMap_' + mapId];
                if (mapRef) {
                    mapRef.routeLayers.forEach(function(layer) {
                        if (this.checked) {
                            layer.addTo(mapRef.map);
                        } else {
                            mapRef.map.removeLayer(layer);
                        }
                    }, this);
                }
            });
        }

        // Toggle locations visibility
        const toggleLocationsCheckbox = document.querySelector('.edition-toggle-locations[data-map-id="' + mapId + '"]');
        if (toggleLocationsCheckbox) {
            toggleLocationsCheckbox.addEventListener('change', function() {
                const mapRef = window['editionMap_' + mapId];
                if (mapRef) {
                    if (this.checked) {
                        mapRef.artworkLayerGroup.addTo(mapRef.map);
                    } else {
                        mapRef.map.removeLayer(mapRef.artworkLayerGroup);
                    }
                }
            });
        }

        // Toggle info points visibility
        const toggleInfoPointsCheckbox = document.querySelector('.edition-toggle-info-points[data-map-id="' + mapId + '"]');
        if (toggleInfoPointsCheckbox) {
            toggleInfoPointsCheckbox.addEventListener('change', function() {
                const mapRef = window['editionMap_' + mapId];
                if (mapRef) {
                    if (this.checked) {
                        mapRef.infoPointLayerGroup.addTo(mapRef.map);
                    } else {
                        mapRef.map.removeLayer(mapRef.infoPointLayerGroup);
                    }
                }
            });
        }

        // Hide loading indicator
        if (loadingEl) loadingEl.style.display = 'none';
    }
</script>

<style>
/* Edition Map Shortcode Styles */
.edition-map-container {
    margin: 20px 0;
}

.edition-map-wrapper {
    position: relative;
}

.edition-map {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.edition-map-placeholder {
    padding: 40px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 8px;
    color: #666;
}

/* Legend Styles */
.edition-map-legend {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.edition-map-legend h3 {
    margin: 0 0 15px 0;
    font-size: 1.2em;
    color: #333;
}

.edition-map-zoom-all {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 15px;
    padding: 8px 16px;
    background: #0073aa;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.edition-map-zoom-all:hover {
    background: #005a87;
}

.edition-route-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.edition-route-item {
    padding: 12px;
    margin-bottom: 10px;
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.edition-route-item:last-child {
    margin-bottom: 0;
}

.edition-route-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.edition-route-title-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.edition-route-color-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.edition-route-title {
    font-weight: 500;
    color: #333;
}

.edition-route-link {
    color: #0073aa;
    text-decoration: none;
}

.edition-route-link:hover {
    color: #005a87;
}

.edition-route-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #666;
}

.edition-route-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.edition-route-meta .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
}

.edition-zoom-to-route-button {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background: #f0f0f0;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    transition: background-color 0.2s, border-color 0.2s;
}

.edition-zoom-to-route-button:hover {
    background: #e0e0e0;
    border-color: #ccc;
}

.edition-zoom-to-route-button.active {
    background: #0073aa;
    color: #fff;
    border-color: #0073aa;
}

.edition-zoom-to-route-button .dashicons {
    font-size: 14px;
    width: 14px;
    height: 14px;
}

/* Map Controls */
.edition-map-controls {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.edition-map-controls-title {
    margin: 0 0 10px 0;
    font-size: 1em;
    color: #333;
}

.edition-map-controls-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.edition-map-control-item {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}

.edition-map-control-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin: 0;
}

.edition-map-control-icon {
    font-size: 16px;
    width: 16px;
    height: 16px;
    color: #666;
}

.edition-map-control-label {
    font-size: 14px;
    color: #333;
}

/* Map Marker Styles */
.artwork-marker-inner {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    background: #f4f4f4;
    position: relative;
}

.artwork-marker-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
}

.artwork-marker-icon {
    width: 100%;
    height: 100%;
}

.artwork-marker-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 50%;
}

.artwork-marker-number {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
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

/* Loading Indicator */
.map-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 8px;
    z-index: 1000;
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

/* Popup Styles */
.artwork-popup,
.info-point-popup {
    min-width: 200px;
}

.artwork-popup-image img,
.info-point-popup-image img {
    width: 100%;
    max-height: 150px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 10px;
}

.artwork-popup-content h3,
.info-point-popup-content h3 {
    margin: 0 0 8px 0;
    font-size: 1.1em;
}

.artwork-excerpt,
.info-point-excerpt {
    margin-bottom: 10px;
    font-size: 0.9em;
    color: #555;
}

.artwork-link,
.info-point-link {
    display: inline-block;
    color: #0073aa;
    text-decoration: none;
    font-weight: 500;
}

.artwork-link:hover,
.info-point-link:hover {
    text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 768px) {
    .edition-map-legend {
        padding: 10px;
    }

    .edition-route-item {
        padding: 10px;
    }

    .edition-route-meta {
        flex-direction: column;
        gap: 5px;
    }

    .edition-map-controls-grid {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
