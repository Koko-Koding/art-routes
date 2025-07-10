<?php
/**
 * Template for the art_routes_map shortcode
 * Displays multiple routes on a single map with color coding
 *
 * @package WP Art Routes
 */

// Don't allow direct access to the template
if (!defined('ABSPATH')) {
    exit;
}

// Variables should be passed via wp_art_routes_get_template_part()
// $atts = shortcode attributes
// $routes = array of route data

// Check if routes data exists
if (empty($routes)) {
    echo '<p class="wp-art-routes-error">' . __('No routes found.', 'wp-art-routes') . '</p>';
    return;
}

// Set up HTML element styles
$container_style = '';
if (!empty($atts['height'])) {
    $container_style = 'style="height: ' . esc_attr($atts['height']) . ';"';
}

// Define a set of distinctive colors for routes
$route_colors = [
    '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4', 
    '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990',
    '#dcbeff', '#9A6324', '#800000', '#aaffc3', '#808000',
    '#ffd8b1', '#000075', '#a9a9a9', '#000000', '#ffe119'
];

// Prepare data for JavaScript
$js_data = [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('wp_art_routes_nonce'),
    'routes' => [],
    'artworks' => wp_art_routes_get_all_artworks(), // Add global artworks
    'information_points' => wp_art_routes_get_all_information_points(), // Add global info points
    'colors' => $route_colors,
    'mapSettings' => [
        'center_lat' => !empty($atts['center_lat']) ? floatval($atts['center_lat']) : null,
        'center_lng' => !empty($atts['center_lng']) ? floatval($atts['center_lng']) : null,
        'zoom' => !empty($atts['zoom']) ? intval($atts['zoom']) : null,
    ],
];

// Process each route
foreach ($routes as $index => $route) {
    // Assign a color from our palette (cycle through if more routes than colors)
    $color = $route_colors[$index % count($route_colors)];
    
    // Get post permalink for the route
    $route_url = get_permalink($route['id']);
    
    // Add route data to JS array
    $js_data['routes'][] = [
        'id' => $route['id'],
        'title' => $route['title'],
        'description' => $route['description'],
        'excerpt' => $route['excerpt'],
        'route_path' => $route['route_path'],
        'artworks' => $route['artworks'],
        'color' => $color,
        'length' => $route['length'],
        'duration' => $route['duration'],
        'type' => $route['type'],
        'url' => $route_url,
    ];
}

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

// Generate a unique ID for this map instance
$map_id = 'art-routes-map-' . uniqid();
?>

<div class="art-routes-container">
    <?php if ($atts['show_legend']): ?>
        <div class="art-routes-legend">
            <h3><?php _e('Routes', 'wp-art-routes'); ?></h3>
            <ul class="route-list">
                <?php foreach ($js_data['routes'] as $index => $route): ?>
                    <li class="route-item">
                        <div class="route-header">
                            <div class="route-title-container">
                                <span class="route-color-indicator" style="background-color: <?php echo esc_attr($route['color']); ?>;"></span>
                                <span class="route-title"><?php echo esc_html($route['title']); ?></span>
                            </div>
                            <a href="<?php echo esc_url($route['url']); ?>" class="route-link" title="<?php esc_attr_e('View route details', 'wp-art-routes'); ?>">
                                <span class="dashicons dashicons-arrow-right-alt2"></span>
                            </a>
                        </div>
                        
                        <div class="route-meta multiple">
                            <?php if (!empty($route['type']) && isset($route_types[$route['type']])): ?>
                                <span class="route-type">
                                    <span class="<?php echo esc_attr($route_types[$route['type']]['icon']); ?>"></span>
                                    <?php echo esc_html($route_types[$route['type']]['label']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($route['length'])): ?>
                                <span class="route-length">
                                    <span class="dashicons dashicons-location"></span>
                                    <?php echo esc_html($route['length']); ?> km
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($route['duration'])): ?>
                                <span class="route-duration">
                                    <span class="dashicons dashicons-clock"></span>
                                    <?php echo esc_html($route['duration']); ?> <?php _e('min', 'wp-art-routes'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($atts['show_description']): ?>
                            <div class="route-description">
                                <?php 
                                if (!empty($route['excerpt'])) {
                                    echo wp_kses_post($route['excerpt']);
                                } else {
                                    // If no excerpt, create one from content
                                    echo wp_kses_post(wp_trim_words($route['description'], 25, '...'));
                                }
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <button class="zoom-to-route-button" data-route-index="<?php echo esc_attr($index); ?>">
                            <span class="dashicons dashicons-search"></span>
                            <?php _e('Zoom to Route', 'wp-art-routes'); ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="art-routes-map-container">
        <?php if ($atts['show_title']): ?>
            <h2 class="art-routes-map-title"><?php _e('Art Routes Map', 'wp-art-routes'); ?></h2>
        <?php endif; ?>
        
        <!-- Map container -->
        <div id="<?php echo esc_attr($map_id); ?>" class="art-routes-map" <?php echo $container_style; ?>></div>
        
        <!-- Loading indicator -->
        <div id="map-loading-<?php echo esc_attr($map_id); ?>" class="map-loading" style="display: none;">
            <div class="spinner"></div>
            <p><?php _e('Loading map...', 'wp-art-routes'); ?></p>
        </div>
    </div>
</div>

<!-- Map data passed to JavaScript -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const mapId = '<?php echo esc_js($map_id); ?>';
        const artRoutesData = <?php echo json_encode($js_data); ?>;
        
        // Initialize the multiple routes map
        initializeMultipleRoutesMap(mapId, artRoutesData);
    });
    
    /**
     * Initialize multiple routes map
     */
    function initializeMultipleRoutesMap(mapId, routesData) {
        // Show loading indicator
        document.getElementById('map-loading-' + mapId).style.display = 'block';
        
        // Map variables
        let map, routeLayers = [], routeBounds = [], artworkMarkers = [];
        
        // Create the map
        map = L.map(mapId);
        
        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Create a bounds object for all routes
        const allBounds = L.latLngBounds();
        let hasValidCoordinates = false;
        
        // Process each route
        routesData.routes.forEach(function(route, index) {
            // Create a layer group for this route
            routeLayers[index] = L.layerGroup().addTo(map);
            
            // Create bounds for this specific route
            routeBounds[index] = L.latLngBounds();
            
            // Add route path if it exists
            if (route.route_path && route.route_path.length > 0) {
                // Create polyline with the route's color
                const routePolyline = L.polyline(route.route_path, {
                    color: route.color,
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round'
                }).addTo(routeLayers[index]);
                
                // Add points to both the global bounds and this route's bounds
                route.route_path.forEach(function(point) {
                    allBounds.extend(point);
                    routeBounds[index].extend(point);
                    hasValidCoordinates = true;
                });
            }
            
            // Add artwork markers
            if (route.artworks && route.artworks.length > 0) {
                route.artworks.forEach(function(artwork, artworkIndex) {
                    // Create a custom artwork marker
                    const artworkIcon = L.divIcon({
                        className: 'artwork-marker',
                        html: `
                            <div class="artwork-marker-inner" style="border-color: ${route.color};">
                                <div class="artwork-marker-image" style="background-image: url('${artwork.image_url}');"></div>
                                <div class="artwork-marker-overlay" style="background-color: ${route.color}; opacity: 0.3;"></div>
                                <div class="artwork-marker-number">${artworkIndex + 1}</div>
                            </div>
                        `,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });
                    
                    // Create marker
                    const marker = L.marker([artwork.latitude, artwork.longitude], {
                        icon: artworkIcon
                    }).addTo(routeLayers[index]);
                    
                    // Add to global bounds and this route's bounds
                    allBounds.extend([artwork.latitude, artwork.longitude]);
                    routeBounds[index].extend([artwork.latitude, artwork.longitude]);
                    
                    // Prepare popup content
                    const popupContent = `
                        <div class="artwork-popup">
                            <div class="artwork-popup-image">
                                <img src="${artwork.image_url}" alt="${artwork.title}">
                            </div>
                            <div class="artwork-popup-content">
                                <h3>${artwork.title}</h3>
                                <div class="artwork-description">
                                    ${artwork.description}
                                </div>
                            </div>
                        </div>
                    `;
                    
                    // Create popup
                    const popup = L.popup({
                        maxWidth: 300,
                        className: 'artwork-popup-container',
                        closeButton: true,
                        autoClose: false,
                        closeOnEscapeKey: true
                    }).setContent(popupContent);
                    
                    // Add click event
                    marker.on('click', function() {
                        popup.setLatLng(marker.getLatLng()).openOn(map);
                    });
                    
                    // Add to artwork markers array
                    artworkMarkers.push({
                        marker: marker,
                        routeIndex: index,
                        popup: popup
                    });
                });
            }
        });
        
        // Add global artwork markers (visible on all routes)
        if (routesData.artworks && routesData.artworks.length > 0) {
            routesData.artworks.forEach(function(artwork, artworkIndex) {
                // Create a custom artwork marker (neutral styling for global artworks)
                const artworkIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: `
                        <div class="artwork-marker-inner">
                            <div class="artwork-marker-image" style="background-image: url('${artwork.image_url || 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9IiNmNGY0ZjQiLz4KPHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSI4IiB5PSI4Ij4KPHBhdGggZD0iTTE5IDNINVYyMUgxOVYzWk02IDEwTDEwLjUgMTQuNUwxMyAxMkwxNyAxNkg2VjEwWiIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4KPC9zdmc+'}');"></div>
                            <div class="artwork-marker-overlay"></div>
                            <div class="artwork-marker-number">${artworkIndex + 1}</div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });
                
                // Create marker
                const marker = L.marker([artwork.latitude, artwork.longitude], {
                    icon: artworkIcon
                }).addTo(map); // Add directly to map, not to route layers
                
                // Add to global bounds
                allBounds.extend([artwork.latitude, artwork.longitude]);
                hasValidCoordinates = true;
                
                // Prepare popup content
                let popupContent = '<div class="artwork-popup">';
                if (artwork.image_url) {
                    popupContent += `
                        <div class="artwork-popup-image">
                            <img src="${artwork.image_url}" alt="${artwork.title}">
                        </div>
                    `;
                }
                popupContent += `
                    <div class="artwork-popup-content">
                        <h3>${artwork.title}</h3>
                        <div class="artwork-description">
                            ${artwork.description}
                        </div>
                `;
                
                // Add artist information if available
                if (artwork.artists && artwork.artists.length > 0) {
                    popupContent += '<div class="artwork-artists"><strong>Artist(s):</strong><ul>';
                    artwork.artists.forEach(function(artist) {
                        popupContent += `<li><a href="${artist.url}" target="_blank">${artist.title}</a></li>`;
                    });
                    popupContent += '</ul></div>';
                }
                
                popupContent += `
                    </div>
                </div>
                `;
                
                // Create popup
                const popup = L.popup({
                    maxWidth: 300,
                    className: 'artwork-popup-container',
                    closeButton: true,
                    autoClose: false,
                    closeOnEscapeKey: true
                }).setContent(popupContent);
                
                // Add click event
                marker.on('click', function() {
                    popup.setLatLng(marker.getLatLng()).openOn(map);
                });
            });
        }
        
        // Add global information point markers (visible on all routes)
        if (routesData.information_points && routesData.information_points.length > 0) {
            routesData.information_points.forEach(function(infoPoint) {
                // Create a custom information point marker
                const infoPointIcon = L.divIcon({
                    className: 'info-point-marker',
                    html: '<div class="info-point-marker-inner">i</div>',
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });
                
                // Create marker
                const marker = L.marker([infoPoint.latitude, infoPoint.longitude], {
                    icon: infoPointIcon
                }).addTo(map); // Add directly to map, not to route layers
                
                // Add to global bounds
                allBounds.extend([infoPoint.latitude, infoPoint.longitude]);
                hasValidCoordinates = true;
                
                // Prepare popup content
                let popupContent = '<div class="info-point-popup">';
                if (infoPoint.image_url) {
                    popupContent += `
                        <div class="info-point-popup-image">
                            <img src="${infoPoint.image_url}" alt="${infoPoint.title}">
                        </div>
                    `;
                }
                popupContent += `
                    <div class="info-point-popup-content">
                        <h3>${infoPoint.title}</h3>
                        <div class="info-point-excerpt">
                            ${infoPoint.excerpt}
                        </div>
                        <a href="${infoPoint.permalink}" class="info-point-link" target="_blank">Read more</a>
                    </div>
                `;
                popupContent += '</div>';
                
                // Create popup
                const popup = L.popup({
                    maxWidth: 300,
                    className: 'info-point-popup-container',
                    closeButton: true,
                    autoClose: false,
                    closeOnEscapeKey: true
                }).setContent(popupContent);
                
                // Add click event
                marker.on('click', function() {
                    popup.setLatLng(marker.getLatLng()).openOn(map);
                });
            });
        }
        
        // Set map view based on attributes or all routes
        if (routesData.mapSettings.center_lat && routesData.mapSettings.center_lng && routesData.mapSettings.zoom) {
            // Use specified center and zoom
            map.setView(
                [routesData.mapSettings.center_lat, routesData.mapSettings.center_lng],
                routesData.mapSettings.zoom
            );
        } else if (hasValidCoordinates) {
            // Fit to bounds of all routes
            map.fitBounds(allBounds, {
                padding: [30, 30]
            });
        } else {
            // Default view (Netherlands)
            map.setView([52.1326, 5.2913], 7);
        }
        
        // Add zoom to route functionality
        const zoomButtons = document.querySelectorAll('.zoom-to-route-button');
        zoomButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const routeIndex = parseInt(this.getAttribute('data-route-index'));
                
                // Make sure this route has valid bounds
                if (routeBounds[routeIndex] && routeBounds[routeIndex].isValid()) {
                    // Fit the map to this route's bounds with animation
                    map.flyToBounds(routeBounds[routeIndex], {
                        padding: [30, 30],
                        duration: 0.8 // Animation duration in seconds
                    });
                    
                    // Apply visual feedback to the button that was clicked
                    zoomButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
        
        // Add a button to zoom out to show all routes
        const legendElement = document.querySelector('.art-routes-legend');
        if (legendElement && hasValidCoordinates) {
            const allRoutesButton = document.createElement('button');
            allRoutesButton.className = 'zoom-to-all-routes-button';
            allRoutesButton.innerHTML = '<span class="dashicons dashicons-admin-site"></span>' + 
                                        '<?php _e('Show All Routes', 'wp-art-routes'); ?>';
            
            // Insert button at the top of the legend
            const legendTitle = legendElement.querySelector('h3');
            if (legendTitle) {
                legendTitle.insertAdjacentElement('afterend', allRoutesButton);
            } else {
                legendElement.insertAdjacentElement('afterbegin', allRoutesButton);
            }
            
            // Add event listener
            allRoutesButton.addEventListener('click', function() {
                if (allBounds.isValid()) {
                    map.flyToBounds(allBounds, {
                        padding: [30, 30],
                        duration: 0.8
                    });
                    
                    // Remove active state from all zoom buttons
                    zoomButtons.forEach(btn => btn.classList.remove('active'));
                }
            });
        }
        
        // Hide loading indicator
        document.getElementById('map-loading-' + mapId).style.display = 'none';
    }
</script>