/**
 * Route Editor Admin JavaScript
 * Handles the map modal for drawing routes in the WordPress admin
 */

(function($) {
    // Map variables
    let editorMap, drawingLayer, searchControl, routePoints = [];
    let isDrawing = false;
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Add modal to body if not already there
        if ($('#route-editor-modal').length === 0) {
            $('body').append(routeEditorModalHTML);
        }
        
        // Setup event handlers
        setupEventHandlers();
    });
    
    /**
     * Set up event handlers for the route editor
     */
    function setupEventHandlers() {
        // Open the map modal
        $('#open_route_map').on('click', function(e) {
            e.preventDefault();
            openRouteEditorModal();
        });
        
        // Close modal
        $('.route-editor-modal .close-modal, #cancel-route').on('click', function() {
            closeRouteEditorModal();
        });
        
        // Close modal if clicking outside the content
        $(document).on('click', '.route-editor-modal', function(e) {
            if ($(e.target).hasClass('route-editor-modal')) {
                closeRouteEditorModal();
            }
        });
        
        // Drawing controls
        $('#start-drawing').on('click', startDrawing);
        $('#stop-drawing').on('click', stopDrawing);
        $('#clear-route').on('click', clearRoute);
        
        // Search location
        $('#search-location').on('click', searchLocation);
        $('#route-search').on('keypress', function(e) {
            if (e.which === 13) {
                searchLocation();
                e.preventDefault();
            }
        });
        
        // Save route
        $('#save-route').on('click', saveRoute);
    }
    
    /**
     * Open the route editor modal and initialize the map
     */
    function openRouteEditorModal() {
        // Show the modal
        $('#route-editor-modal').show();
        
        // Initialize map if not already initialized
        if (!editorMap) {
            initEditorMap();
        } else {
            // Reset the map view if already initialized
            editorMap.invalidateSize();
        }
        
        // Load existing route if available
        loadExistingRoute();
    }
    
    /**
     * Close the route editor modal
     */
    function closeRouteEditorModal() {
        $('#route-editor-modal').hide();
        stopDrawing();
    }
    
    /**
     * Initialize the map for route editing
     */
    function initEditorMap() {
        // Create the map
        editorMap = L.map('route-editor-map').setView([52.1326, 5.2913], 8); // Default center on Netherlands
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(editorMap);
        
        // Add drawing layer for the route
        drawingLayer = L.polyline([], {
            color: '#3388FF',
            weight: 4
        }).addTo(editorMap);
        
        // Add search control
        setupSearchControl();
        
        // Add click handler for drawing points
        editorMap.on('click', onMapClick);
    }
    
    /**
     * Set up the search control for finding locations
     */
    function setupSearchControl() {
        // Use browser's Geolocation API to center on user's location
        editorMap.locate({setView: true, maxZoom: 13});
        
        // Add scale control
        L.control.scale().addTo(editorMap);
    }
    
    /**
     * Handle map clicks when drawing a route
     */
    function onMapClick(e) {
        if (!isDrawing) return;
        
        // Add point to the route
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        routePoints.push([lat, lng]);
        
        // Update the drawing layer
        drawingLayer.setLatLngs(routePoints);
        
        // Update point count
        updateRouteInfo();
        
        // Add marker for visual feedback
        L.marker([lat, lng], {
            icon: L.divIcon({
                className: 'route-point-marker',
                html: '<div class="route-point-dot"></div>',
                iconSize: [10, 10],
                iconAnchor: [5, 5]
            })
        }).addTo(editorMap);
    }
    
    /**
     * Start drawing mode
     */
    function startDrawing() {
        isDrawing = true;
        $('#start-drawing').addClass('active');
        $('#stop-drawing').removeClass('active');
        
        // Update UI
        $('#drawing-instructions').text('Click on the map to add points to your route. Click "Stop Drawing" when finished.');
        editorMap.getContainer().style.cursor = 'crosshair';
    }
    
    /**
     * Stop drawing mode
     */
    function stopDrawing() {
        isDrawing = false;
        $('#start-drawing').removeClass('active');
        $('#stop-drawing').addClass('active');
        
        // Update UI
        $('#drawing-instructions').text('Drawing paused. Click "Start Drawing" to continue adding points.');
        editorMap.getContainer().style.cursor = '';
    }
    
    /**
     * Clear the current route
     */
    function clearRoute() {
        // Clear the route points
        routePoints = [];
        
        // Clear the drawing layer
        drawingLayer.setLatLngs([]);
        
        // Remove all route point markers
        editorMap.eachLayer(function(layer) {
            if (layer instanceof L.Marker) {
                editorMap.removeLayer(layer);
            }
        });
        
        // Reset point count
        updateRouteInfo();
        
        // Show message
        $('#drawing-instructions').text('Route cleared. Click "Start Drawing" to begin a new route.');
    }
    
    /**
     * Search for a location and center the map on it
     */
    function searchLocation() {
        const searchValue = $('#route-search').val().trim();
        
        if (!searchValue) return;
        
        // Show loading indicator
        $('#search-location').text('Searching...').prop('disabled', true);
        
        // Use Nominatim for geocoding (OpenStreetMap's service)
        $.ajax({
            url: 'https://nominatim.openstreetmap.org/search',
            type: 'GET',
            data: {
                q: searchValue,
                format: 'json',
                limit: 1
            },
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    const result = data[0];
                    const lat = parseFloat(result.lat);
                    const lon = parseFloat(result.lon);
                    
                    // Center map on result
                    editorMap.setView([lat, lon], 14);
                    
                    // Add a temporary highlight
                    const searchHighlight = L.circle([lat, lon], {
                        color: '#FF5722',
                        fillColor: '#FF5722',
                        fillOpacity: 0.5,
                        radius: 50
                    }).addTo(editorMap);
                    
                    // Remove highlight after 3 seconds
                    setTimeout(function() {
                        editorMap.removeLayer(searchHighlight);
                    }, 3000);
                } else {
                    alert('Location not found. Please try a different search term.');
                }
            },
            error: function() {
                alert('Error searching for location. Please try again.');
            },
            complete: function() {
                // Reset button
                $('#search-location').text('Search').prop('disabled', false);
            }
        });
    }
    
    /**
     * Save the route to the textarea
     */
    function saveRoute() {
        if (routePoints.length < 2) {
            alert('Please add at least 2 points to create a route.');
            return;
        }
        
        // Format points for storage
        let formattedPoints = '';
        
        for (let i = 0; i < routePoints.length; i++) {
            formattedPoints += routePoints[i][0] + ', ' + routePoints[i][1] + '\n';
        }
        
        // Set the value in the textarea
        $('#route_path').val(formattedPoints.trim());
        
        // Update route length field if available
        const routeLength = calculateRouteLength();
        if ($('#route_length').length) {
            $('#route_length').val(routeLength.toFixed(2));
        }
        
        // Close the modal
        closeRouteEditorModal();
        
        // Show success message
        alert('Route saved successfully with ' + routePoints.length + ' points.');
    }
    
    /**
     * Calculate the total route length in kilometers
     */
    function calculateRouteLength() {
        let totalDistance = 0;
        
        for (let i = 0; i < routePoints.length - 1; i++) {
            totalDistance += calculateDistance(
                routePoints[i][0], routePoints[i][1],
                routePoints[i+1][0], routePoints[i+1][1]
            );
        }
        
        // Convert meters to kilometers
        return totalDistance / 1000;
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth radius in meters
        const φ1 = lat1 * Math.PI/180;
        const φ2 = lat2 * Math.PI/180;
        const Δφ = (lat2-lat1) * Math.PI/180;
        const Δλ = (lon2-lon1) * Math.PI/180;

        const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
                Math.cos(φ1) * Math.cos(φ2) *
                Math.sin(Δλ/2) * Math.sin(Δλ/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

        return R * c; // in meters
    }
    
    /**
     * Update the route information display
     */
    function updateRouteInfo() {
        // Update point count
        $('#point-count').text(routePoints.length);
        
        // Update distance
        const distance = calculateRouteLength();
        $('#route-distance').text(distance.toFixed(2));
    }
    
    /**
     * Load existing route from textarea
     */
    function loadExistingRoute() {
        // Clear any existing route
        clearRoute();
        
        // Get existing route from textarea
        const routeText = $('#route_path').val().trim();
        
        if (!routeText) return;
        
        // Parse the route
        const lines = routeText.split('\n');
        let validPoints = [];
        
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line) continue;
            
            const parts = line.split(',');
            if (parts.length >= 2) {
                const lat = parseFloat(parts[0].trim());
                const lng = parseFloat(parts[1].trim());
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    validPoints.push([lat, lng]);
                    
                    // Add marker
                    L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'route-point-marker',
                            html: '<div class="route-point-dot"></div>',
                            iconSize: [10, 10],
                            iconAnchor: [5, 5]
                        })
                    }).addTo(editorMap);
                }
            }
        }
        
        if (validPoints.length > 0) {
            // Set the points
            routePoints = validPoints;
            
            // Update the drawing layer
            drawingLayer.setLatLngs(routePoints);
            
            // Update info
            updateRouteInfo();
            
            // Fit map to bounds
            editorMap.fitBounds(drawingLayer.getBounds());
        }
    }
    
})(jQuery);