/**
 * Multiple Routes Map
 *
 * Initializes a map displaying multiple routes with color coding, artworks,
 * and information points.
 * Expects artRoutesMultiMapI18n to be localized with: { showAllRoutes, readMore }
 * Per-instance data is passed via artRoutesMultiMapInstances array.
 *
 * NOTE: This is a direct extraction of existing inline script from
 * shortcode-multiple-map.php. The innerHTML usage for Leaflet popup content
 * and marker icons is intentional and matches the existing codebase patterns.
 * All dynamic data comes from server-side sanitized PHP output.
 *
 * @package WP Art Routes
 */

var artRoutesMultiMap = (function() {
    'use strict';

    var i18n = (typeof artRoutesMultiMapI18n !== 'undefined') ? artRoutesMultiMapI18n : {};

    /**
     * Initialize multiple routes map
     *
     * @param {string} mapId      - DOM element ID for the map container
     * @param {object} routesData - Map data with routes, artworks, information_points, colors, mapSettings
     */
    function init(mapId, routesData) {
        // Show loading indicator
        var loadingEl = document.getElementById('map-loading-' + mapId);
        if (loadingEl) loadingEl.style.display = 'block';

        // Map variables
        var map, routeLayers = [], routeBounds = [], artworkMarkers = [];

        // Create the map
        map = L.map(mapId);

        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Create a bounds object for all routes
        var allBounds = L.latLngBounds();
        var hasValidCoordinates = false;

        // Process each route
        routesData.routes.forEach(function(route, index) {
            // Create a layer group for this route
            routeLayers[index] = L.layerGroup().addTo(map);

            // Create bounds for this specific route
            routeBounds[index] = L.latLngBounds();

            // Add route path if it exists
            if (route.route_path && route.route_path.length > 0) {
                // Create polyline with the route's color
                L.polyline(route.route_path, {
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
                    // Leaflet divIcon requires HTML string for custom markers
                    var artworkIcon = L.divIcon({
                        className: 'artwork-marker',
                        html: '<div class="artwork-marker-inner" style="border-color: ' + route.color + ';"><div class="artwork-marker-image" style="background-image: url(\'' + artwork.image_url + '\');"></div><div class="artwork-marker-overlay" style="background-color: ' + route.color + '; opacity: 0.3;"></div><div class="artwork-marker-number">' + (artworkIndex + 1) + '</div></div>',
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    });

                    var marker = L.marker([artwork.latitude, artwork.longitude], {
                        icon: artworkIcon
                    }).addTo(routeLayers[index]);

                    allBounds.extend([artwork.latitude, artwork.longitude]);
                    routeBounds[index].extend([artwork.latitude, artwork.longitude]);

                    // Leaflet popup content as HTML string (data is server-sanitized)
                    var popupContent = '<div class="artwork-popup"><div class="artwork-popup-image"><img src="' + artwork.image_url + '" alt="' + artwork.title + '"></div><div class="artwork-popup-content"><h3>' + artwork.title + '</h3><div class="artwork-description">' + artwork.description + '</div></div></div>';

                    var popup = L.popup({
                        maxWidth: 300,
                        className: 'artwork-popup-container',
                        closeButton: true,
                        autoClose: false,
                        closeOnEscapeKey: true
                    }).setContent(popupContent);

                    marker.on('click', function() {
                        popup.setLatLng(marker.getLatLng()).openOn(map);
                    });

                    artworkMarkers.push({
                        marker: marker,
                        routeIndex: index,
                        popup: popup
                    });
                });
            }
        });

        // Add global artwork markers
        if (routesData.artworks && routesData.artworks.length > 0) {
            routesData.artworks.forEach(function(artwork, artworkIndex) {
                var fallbackImage = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQwIiBoZWlnaHQ9IjQwIiByeD0iMjAiIGZpbGw9IiNmNGY0ZjQiLz4KPHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1zbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSI4IiB5PSI4Ij4KPHBhdGggZD0iTTE5IDNINVYyMUgxOVYzWk02IDEwTDEwLjUgMTQuNUwxMyAxMkwxNyAxNkg2VjEwWiIgZmlsbD0iIzk5OTk5OSIvPgo8L3N2Zz4KPC9zdmc+';
                var imageUrl = artwork.image_url || fallbackImage;

                // Leaflet divIcon requires HTML string for custom markers
                var artworkIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'' + imageUrl + '\');"></div><div class="artwork-marker-overlay"></div><div class="artwork-marker-number">' + (artworkIndex + 1) + '</div></div>',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });

                var marker = L.marker([artwork.latitude, artwork.longitude], {
                    icon: artworkIcon
                }).addTo(map);

                allBounds.extend([artwork.latitude, artwork.longitude]);
                hasValidCoordinates = true;

                // Leaflet popup content as HTML string (data is server-sanitized)
                var popupContent = '<div class="artwork-popup">';
                if (artwork.image_url) {
                    popupContent += '<div class="artwork-popup-image"><img src="' + artwork.image_url + '" alt="' + artwork.title + '"></div>';
                }
                popupContent += '<div class="artwork-popup-content"><h3>' + artwork.title + '</h3><div class="artwork-description">' + artwork.description + '</div>';

                if (artwork.artists && artwork.artists.length > 0) {
                    popupContent += '<div class="artwork-artists"><strong>Artist(s):</strong><ul>';
                    artwork.artists.forEach(function(artist) {
                        popupContent += '<li><a href="' + artist.url + '" target="_blank">' + artist.title + '</a></li>';
                    });
                    popupContent += '</ul></div>';
                }

                popupContent += '</div></div>';

                var popup = L.popup({
                    maxWidth: 300,
                    className: 'artwork-popup-container',
                    closeButton: true,
                    autoClose: false,
                    closeOnEscapeKey: true
                }).setContent(popupContent);

                marker.on('click', function() {
                    popup.setLatLng(marker.getLatLng()).openOn(map);
                });
            });
        }

        // Add global information point markers
        if (routesData.information_points && routesData.information_points.length > 0) {
            routesData.information_points.forEach(function(infoPoint) {
                // Leaflet divIcon requires HTML string for custom markers
                var iconHtml = '<div class="info-point-marker-inner">i</div>';

                if (infoPoint.icon_url) {
                    iconHtml = '<div class="info-point-marker-inner" style="background: none; position: relative;"><div style="width: 100%; height: 100%; background: url(\'' + infoPoint.icon_url + '\') center center / contain no-repeat; border-radius: 50%; border: 2px solid #ffc107;"></div></div>';
                }

                var infoPointIcon = L.divIcon({
                    className: 'info-point-marker',
                    html: iconHtml,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15]
                });

                var marker = L.marker([infoPoint.latitude, infoPoint.longitude], {
                    icon: infoPointIcon
                }).addTo(map);

                allBounds.extend([infoPoint.latitude, infoPoint.longitude]);
                hasValidCoordinates = true;

                // Leaflet popup content as HTML string (data is server-sanitized)
                var popupContent = '<div class="info-point-popup">';
                if (infoPoint.image_url) {
                    popupContent += '<div class="info-point-popup-image"><img src="' + infoPoint.image_url + '" alt="' + infoPoint.title + '"></div>';
                }
                popupContent += '<div class="info-point-popup-content"><h3>' + infoPoint.title + '</h3><div class="info-point-excerpt">' + infoPoint.excerpt + '</div>';
                popupContent += '<a href="' + infoPoint.permalink + '" class="info-point-link" target="_blank">' + (i18n.readMore || 'Read more') + '</a>';
                popupContent += '</div></div>';

                var popup = L.popup({
                    maxWidth: 300,
                    className: 'info-point-popup-container',
                    closeButton: true,
                    autoClose: false,
                    closeOnEscapeKey: true
                }).setContent(popupContent);

                marker.on('click', function() {
                    popup.setLatLng(marker.getLatLng()).openOn(map);
                });
            });
        }

        // Set map view
        if (routesData.mapSettings && routesData.mapSettings.center_lat && routesData.mapSettings.center_lng && routesData.mapSettings.zoom) {
            map.setView(
                [routesData.mapSettings.center_lat, routesData.mapSettings.center_lng],
                routesData.mapSettings.zoom
            );
        } else if (hasValidCoordinates) {
            map.fitBounds(allBounds, { padding: [30, 30] });
        } else {
            map.setView([52.1326, 5.2913], 7);
        }

        // Add zoom to route functionality
        var zoomButtons = document.querySelectorAll('.zoom-to-route-button');
        zoomButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var routeIndex = parseInt(this.getAttribute('data-route-index'));

                if (routeBounds[routeIndex] && routeBounds[routeIndex].isValid()) {
                    map.flyToBounds(routeBounds[routeIndex], {
                        padding: [30, 30],
                        duration: 0.8
                    });

                    zoomButtons.forEach(function(btn) { btn.classList.remove('active'); });
                    this.classList.add('active');
                }
            });
        });

        // Add zoom to all routes button
        var legendElement = document.querySelector('.art-routes-legend');
        if (legendElement && hasValidCoordinates) {
            var allRoutesButton = document.createElement('button');
            allRoutesButton.className = 'zoom-to-all-routes-button';
            allRoutesButton.textContent = i18n.showAllRoutes || 'Show All Routes';

            // Prepend dashicon span
            var iconSpan = document.createElement('span');
            iconSpan.className = 'dashicons dashicons-admin-site';
            allRoutesButton.insertBefore(iconSpan, allRoutesButton.firstChild);

            var legendTitle = legendElement.querySelector('h3');
            if (legendTitle) {
                legendTitle.insertAdjacentElement('afterend', allRoutesButton);
            } else {
                legendElement.insertAdjacentElement('afterbegin', allRoutesButton);
            }

            allRoutesButton.addEventListener('click', function() {
                if (allBounds.isValid()) {
                    map.flyToBounds(allBounds, { padding: [30, 30], duration: 0.8 });
                    zoomButtons.forEach(function(btn) { btn.classList.remove('active'); });
                }
            });
        }

        // Hide loading indicator
        if (loadingEl) loadingEl.style.display = 'none';
    }

    return { init: init };
})();

// Auto-initialize from queued instances
document.addEventListener('DOMContentLoaded', function() {
    if (typeof artRoutesMultiMapInstances !== 'undefined' && Array.isArray(artRoutesMultiMapInstances)) {
        artRoutesMultiMapInstances.forEach(function(instance) {
            artRoutesMultiMap.init(instance.mapId, instance.mapData);
        });
    }
});
