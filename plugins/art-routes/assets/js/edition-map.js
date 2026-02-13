/**
 * Edition Map
 *
 * Unified edition map initialization for both shortcode and single-edition templates.
 * Expects artRoutesEditionMapI18n to be localized with: { viewDetails, readMore }
 * Per-instance data is passed via artRoutesEditionMapInstances array.
 *
 * @package WP Art Routes
 */

var artRoutesEditionMap = (function() {
    'use strict';

    var i18n = (typeof artRoutesEditionMapI18n !== 'undefined') ? artRoutesEditionMapI18n : {};

    /**
     * Initialize an edition map instance
     *
     * @param {string} mapId   - DOM element ID for the map container
     * @param {object} mapData - Map data (routes, artworks, info_points, colors)
     * @param {object} options - Options: { variant: 'shortcode'|'single' }
     */
    function init(mapId, mapData, options) {
        options = options || {};
        var variant = options.variant || 'shortcode';

        // Show loading indicator
        var loadingEl = document.getElementById('map-loading-' + mapId);
        if (loadingEl) loadingEl.style.display = 'block';

        // Map variables
        var map;
        var routeLayers = [];
        var routeBounds = [];
        var artworkLayerGroup;
        var infoPointLayerGroup;

        // Create the map
        map = L.map(mapId);

        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create bounds for all content
        var allBounds = L.latLngBounds();
        var hasValidCoordinates = false;

        // Default route colors
        var routeColors = (mapData.colors && mapData.colors.length > 0) ? mapData.colors : [
            '#e6194B', '#3cb44b', '#4363d8', '#f58231', '#911eb4',
            '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990'
        ];

        // Create layer groups for artworks and info points
        artworkLayerGroup = L.layerGroup().addTo(map);
        infoPointLayerGroup = L.layerGroup().addTo(map);

        // Add routes
        if (mapData.routes && mapData.routes.length > 0) {
            mapData.routes.forEach(function(route, index) {
                var color = routeColors[index % routeColors.length];
                routeLayers[index] = L.layerGroup().addTo(map);
                routeBounds[index] = L.latLngBounds();

                if (route.route_path && route.route_path.length > 0) {
                    var polyline = L.polyline(route.route_path, {
                        color: color,
                        weight: 4,
                        opacity: 0.7,
                        lineJoin: 'round'
                    }).addTo(routeLayers[index]);

                    // Add popup with route title
                    var popupHtml = '<strong>' + route.title + '</strong>';
                    if (route.url) {
                        popupHtml += '<br><a href="' + route.url + '">' + (i18n.viewDetails || 'View details') + '</a>';
                    }
                    polyline.bindPopup(popupHtml);

                    route.route_path.forEach(function(point) {
                        var lat, lng;
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
                var artworkMarkerHtml;
                if (artwork.icon_url && artwork.icon_url.trim() !== '') {
                    artworkMarkerHtml = '<div class="artwork-marker-inner"><div class="artwork-marker-icon" style="background-image: url(\'' + artwork.icon_url + '\'); background-size: contain; background-repeat: no-repeat; background-position: center; width: 100%; height: 100%; border-radius: 50%;"></div></div>';
                } else {
                    var displayNumber = artwork.number && artwork.number.trim() !== '' ? artwork.number : '';
                    artworkMarkerHtml = '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'' + (artwork.image_url || '') + '\');"></div><div class="artwork-marker-overlay"></div><div class="artwork-marker-number">' + displayNumber + '</div></div>';
                }

                var artworkIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: artworkMarkerHtml,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                var marker = L.marker([artwork.latitude, artwork.longitude], {
                    icon: artworkIcon
                }).addTo(artworkLayerGroup);

                var popupContent = '<div class="artwork-popup">';
                if (artwork.image_url) {
                    popupContent += '<div class="artwork-popup-image"><img src="' + artwork.image_url + '" alt="' + artwork.title + '"></div>';
                }
                popupContent += '<div class="artwork-popup-content"><h3>' + artwork.title + '</h3>';
                if (artwork.excerpt) {
                    popupContent += '<div class="artwork-excerpt">' + artwork.excerpt + '</div>';
                }
                popupContent += '<a href="' + artwork.permalink + '" class="artwork-link">' + (i18n.viewDetails || 'View details') + '</a>';
                popupContent += '</div></div>';

                marker.bindPopup(popupContent, { maxWidth: 300 });

                allBounds.extend([artwork.latitude, artwork.longitude]);
                hasValidCoordinates = true;
            });
        }

        // Add information point markers
        if (mapData.info_points && mapData.info_points.length > 0) {
            mapData.info_points.forEach(function(infoPoint) {
                var iconHtml = '<div class="info-point-marker-inner">i</div>';
                if (infoPoint.icon_url) {
                    iconHtml = '<div class="info-point-marker-inner" style="background: url(\'' + infoPoint.icon_url + '\') center center / contain no-repeat;"></div>';
                }

                var infoPointIcon = L.divIcon({
                    className: 'info-point-marker',
                    html: iconHtml,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                var marker = L.marker([infoPoint.latitude, infoPoint.longitude], {
                    icon: infoPointIcon
                }).addTo(infoPointLayerGroup);

                var popupContent = '<div class="info-point-popup">';
                if (infoPoint.image_url) {
                    popupContent += '<div class="info-point-popup-image"><img src="' + infoPoint.image_url + '" alt="' + infoPoint.title + '"></div>';
                }
                popupContent += '<div class="info-point-popup-content"><h3>' + infoPoint.title + '</h3>';
                if (infoPoint.excerpt) {
                    popupContent += '<div class="info-point-excerpt">' + infoPoint.excerpt + '</div>';
                }
                popupContent += '<a href="' + infoPoint.permalink + '" class="info-point-link">' + (i18n.readMore || 'Read more') + '</a>';
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

        // Store map reference and layer groups for controls (shortcode variant)
        if (variant === 'shortcode') {
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
                    var routeIndex = parseInt(this.getAttribute('data-route-index'));
                    var mapRef = window['editionMap_' + mapId];

                    if (mapRef && mapRef.routeBounds[routeIndex] && mapRef.routeBounds[routeIndex].isValid()) {
                        mapRef.map.flyToBounds(mapRef.routeBounds[routeIndex], {
                            padding: [30, 30],
                            duration: 0.8
                        });

                        document.querySelectorAll('.edition-zoom-to-route-button[data-map-id="' + mapId + '"]').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                        this.classList.add('active');
                    }
                });
            });

            // Add zoom to all routes functionality
            var zoomAllButton = document.querySelector('.edition-map-zoom-all[data-map-id="' + mapId + '"]');
            if (zoomAllButton) {
                zoomAllButton.addEventListener('click', function() {
                    var mapRef = window['editionMap_' + mapId];
                    if (mapRef && mapRef.allBounds.isValid()) {
                        mapRef.map.flyToBounds(mapRef.allBounds, {
                            padding: [30, 30],
                            duration: 0.8
                        });

                        document.querySelectorAll('.edition-zoom-to-route-button[data-map-id="' + mapId + '"]').forEach(function(btn) {
                            btn.classList.remove('active');
                        });
                    }
                });
            }

            // Toggle route visibility
            var toggleRoutesCheckbox = document.querySelector('.edition-toggle-routes[data-map-id="' + mapId + '"]');
            if (toggleRoutesCheckbox) {
                toggleRoutesCheckbox.addEventListener('change', function() {
                    var mapRef = window['editionMap_' + mapId];
                    if (mapRef) {
                        var checked = this.checked;
                        mapRef.routeLayers.forEach(function(layer) {
                            if (checked) {
                                layer.addTo(mapRef.map);
                            } else {
                                mapRef.map.removeLayer(layer);
                            }
                        });
                    }
                });
            }

            // Toggle locations visibility
            var toggleLocationsCheckbox = document.querySelector('.edition-toggle-locations[data-map-id="' + mapId + '"]');
            if (toggleLocationsCheckbox) {
                toggleLocationsCheckbox.addEventListener('change', function() {
                    var mapRef = window['editionMap_' + mapId];
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
            var toggleInfoPointsCheckbox = document.querySelector('.edition-toggle-info-points[data-map-id="' + mapId + '"]');
            if (toggleInfoPointsCheckbox) {
                toggleInfoPointsCheckbox.addEventListener('change', function() {
                    var mapRef = window['editionMap_' + mapId];
                    if (mapRef) {
                        if (this.checked) {
                            mapRef.infoPointLayerGroup.addTo(mapRef.map);
                        } else {
                            mapRef.map.removeLayer(mapRef.infoPointLayerGroup);
                        }
                    }
                });
            }
        }

        // Hide loading indicator
        if (loadingEl) loadingEl.style.display = 'none';
    }

    return { init: init };
})();

// Auto-initialize from queued instances
document.addEventListener('DOMContentLoaded', function() {
    if (typeof artRoutesEditionMapInstances !== 'undefined' && Array.isArray(artRoutesEditionMapInstances)) {
        artRoutesEditionMapInstances.forEach(function(instance) {
            artRoutesEditionMap.init(instance.mapId, instance.mapData, instance.options || {});
        });
    }
});
