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
    
    // Add route data to JS array
    $js_data['routes'][] = [
        'id' => $route['id'],
        'title' => $route['title'],
        'description' => $route['description'],
        'route_path' => $route['route_path'],
        'artworks' => $route['artworks'],
        'color' => $color,
        'length' => $route['length'],
        'duration' => $route['duration'],
        'type' => $route['type'],
    ];
}

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
                        <label class="route-toggle">
                            <input type="checkbox" class="route-toggle-checkbox" data-route-index="<?php echo esc_attr($index); ?>" checked>
                            <span class="route-color-indicator" style="background-color: <?php echo esc_attr($route['color']); ?>;"></span>
                            <span class="route-title"><?php echo esc_html($route['title']); ?></span>
                        </label>
                        <?php if ($atts['show_description'] && !empty($route['description'])): ?>
                            <div class="route-description">
                                <?php echo wp_kses_post($route['description']); ?>
                            </div>
                        <?php endif; ?>
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
        let map, routeLayers = [], artworkMarkers = [];
        
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
            
            // Add route path if it exists
            if (route.route_path && route.route_path.length > 0) {
                // Create polyline with the route's color
                const routePolyline = L.polyline(route.route_path, {
                    color: route.color,
                    weight: 4,
                    opacity: 0.7,
                    lineJoin: 'round'
                }).addTo(routeLayers[index]);
                
                // Add points to bounds
                route.route_path.forEach(function(point) {
                    allBounds.extend(point);
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
                    
                    // Add to bounds
                    allBounds.extend([artwork.latitude, artwork.longitude]);
                    
                    // Prepare popup content
                    const popupContent = `
                        <div class="artwork-popup">
                            <div class="artwork-popup-image">
                                <img src="${artwork.image_url}" alt="${artwork.title}">
                            </div>
                            <div class="artwork-popup-content">
                                <h3>${artwork.title}</h3>
                                <p class="artwork-artist">${artwork.artist}</p>
                                <div class="artwork-description">
                                    ${artwork.description}
                                </div>
                                ${artwork.artist_url ? `<a href="${artwork.artist_url}" target="_blank" class="artwork-link">Meer informatie</a>` : ''}
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
        
        // Add toggle functionality for routes
        const toggleCheckboxes = document.querySelectorAll('.route-toggle-checkbox');
        toggleCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const routeIndex = parseInt(this.getAttribute('data-route-index'));
                if (this.checked) {
                    map.addLayer(routeLayers[routeIndex]);
                } else {
                    map.removeLayer(routeLayers[routeIndex]);
                }
            });
        });
        
        // Hide loading indicator
        document.getElementById('map-loading-' + mapId).style.display = 'none';
    }
</script>

<style>
    /* Container layout */
    .art-routes-container {
        display: flex;
        flex-direction: column;
        margin-bottom: 30px;
    }
    
    @media (min-width: 768px) {
        .art-routes-container {
            flex-direction: row;
            flex-wrap: nowrap;
        }
        
        .art-routes-legend {
            width: 30%;
            min-width: 250px;
            max-width: 300px;
            margin-right: 20px;
        }
        
        .art-routes-map-container {
            flex: 1;
        }
    }
    
    /* Legend styling */
    .art-routes-legend {
        margin-bottom: 20px;
    }
    
    .art-routes-legend h3 {
        margin-top: 0;
        margin-bottom: 10px;
    }
    
    .route-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .route-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .route-item:last-child {
        border-bottom: none;
    }
    
    .route-toggle {
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .route-toggle-checkbox {
        margin-right: 8px;
    }
    
    .route-color-indicator {
        display: inline-block;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
    }
    
    .route-title {
        font-weight: 500;
    }
    
    .route-description {
        margin-top: 5px;
        font-size: 0.9em;
        color: #666;
    }
    
    /* Map styling */
    .art-routes-map {
        width: 100%;
        min-height: 400px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
    
    .art-routes-map-title {
        margin-top: 0;
        margin-bottom: 15px;
    }
    
    /* Loading indicator */
    .map-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.8);
        padding: 15px 20px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
    }
    
    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 0 auto 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Artwork markers */
    .artwork-marker-inner {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
        border: 3px solid; /* The color will be set dynamically */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    .artwork-marker-image {
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
    }
    
    .artwork-marker-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .artwork-marker-number {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        text-shadow: 0 0 3px rgba(0, 0, 0, 0.7);
    }
    
    /* Popup styling */
    .artwork-popup {
        max-width: 300px;
    }
    
    .artwork-popup-image img {
        width: 100%;
        height: auto;
        max-height: 150px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .artwork-popup-content {
        margin-top: 10px;
    }
    
    .artwork-popup-content h3 {
        margin-top: 0;
        margin-bottom: 5px;
    }
    
    .artwork-artist {
        font-style: italic;
        margin-top: 0;
        margin-bottom: 10px;
    }
    
    .artwork-description {
        margin-bottom: 10px;
        font-size: 0.9em;
    }
    
    .artwork-link {
        display: inline-block;
        margin-top: 5px;
        color: #3498db;
        text-decoration: none;
    }
    
    .artwork-link:hover {
        text-decoration: underline;
    }

    /* Make sure the Leaflet container has a relative position */
    .art-routes-map-container {
        position: relative;
    }
</style>