/**
 * Art Route Map JavaScript
 * Handles the OpenStreetMap integration, location tracking, and artwork markers
 */

(function($) {
    // Map variables
    let map, userMarker, routeLayer, completedRouteLayer, userToRouteLayer;
    let userPosition = null;
    let watchId = null;
    let artworkMarkers = [];
    let routePath = [];
    let completedPath = [];
    let initialLocationSet = false;
    // Add toast container and queue
    let toastQueue = [];
    let toastDisplaying = false;
    // Flag to indicate whether to show completed route
    let showCompletedRoute = true;
    // Flag to indicate whether to show artwork toasts
    let showArtworkToasts = true;
    
    // Initialize the map when the DOM is ready
    $(document).ready(function() {
        initMap();
        // Create toast container
        $('body').append('<div id="art-route-toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;"></div>');
    });
    
    /**
     * Initialize the OpenStreetMap with Leaflet
     */
    function initMap() {
        // Show loading indicator
        $('#map-loading').show();
        
        // Create the map centered on default position (will be updated with user's position)
        map = L.map('art-route-map').setView([52.1326, 5.2913], 13); // Default center on Netherlands
        
        // Add the OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Get route data from WordPress
        routePath = artRouteData.route_path;
        
        // Check if we should show the completed route
        showCompletedRoute = artRouteData.show_completed_route !== undefined ? artRouteData.show_completed_route : true;
        
        // Check if we should show artwork toasts
        showArtworkToasts = artRouteData.show_artwork_toasts !== undefined ? artRouteData.show_artwork_toasts : true;
        
        // Add the route to the map
        drawRoute();
        
        // Add artwork markers
        addArtworkMarkers();
        
        // Get user location
        getUserLocation();
        
        // Remove modal close handler and replace with toast handling
        
        // Retry location button
        $('#retry-location').on('click', function() {
            $('#location-error').hide();
            getUserLocation();
        });
        
        // Hide loading indicator
        $('#map-loading').hide();
    }
    
    /**
     * Get the user's current location
     */
    function getUserLocation() {
        if (navigator.geolocation) {
            // Hide any previous errors
            $('#location-error').hide();
            
            // Watch the user's position and update in real-time
            watchId = navigator.geolocation.watchPosition(
                updateUserPosition,
                handleLocationError,
                { 
                    enableHighAccuracy: true,
                    maximumAge: 10000, 
                    timeout: 10000 
                }
            );
        } else {
            // Geolocation not supported
            $('#location-error').show().find('p').text('Je browser ondersteunt geen locatievoorzieningen.');
        }
    }
    
    /**
     * Update the user's position on the map
     */
    function updateUserPosition(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        
        // Store current position
        userPosition = [latitude, longitude];
        
        // Create or update user marker
        if (!userMarker) {
            // Create a custom marker for the user
            const userIcon = L.divIcon({
                className: 'user-marker',
                html: '<div class="user-marker-inner"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            
            userMarker = L.marker([latitude, longitude], {
                icon: userIcon,
                zIndexOffset: 1000
            }).addTo(map);
            
            // Now fit both the route and user location in view when user is first located
            if (routeLayer) {
                // Create a bounds object that includes both the route and the user's position
                const combinedBounds = routeLayer.getBounds();
                combinedBounds.extend([latitude, longitude]);
                
                // Fit the map to these combined bounds with some padding
                map.fitBounds(combinedBounds, {
                    padding: [50, 50],
                    maxZoom: 19  // Limit how far it can zoom out
                });
            } else {
                // If there's no route, just center on user
                map.setView([latitude, longitude], 19);
            }
            
            // Set flag for initial location
            initialLocationSet = true;
        } else {
            // Update existing marker
            userMarker.setLatLng([latitude, longitude]);
        }
        
        // Update completed route path if feature is enabled
        if (showCompletedRoute) {
            updateCompletedRoute();
        }
        
        // Only check artwork proximity if not first location update
        if (initialLocationSet) {
            checkArtworkProximity();
        }
    }
    
    /**
     * Handle location errors
     */
    function handleLocationError(error) {
        let errorMessage;
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Locatietoegang is geweigerd. Sta locatietoegang toe in je browser.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Locatie-informatie is niet beschikbaar.';
                break;
            case error.TIMEOUT:
                errorMessage = 'Het verzoek om je locatie is verlopen.';
                break;
            case error.UNKNOWN_ERROR:
                errorMessage = 'Er is een onbekende fout opgetreden bij het ophalen van je locatie.';
                break;
        }
        
        // Show error message
        $('#location-error').show().find('p').text(errorMessage);
    }
    
    /**
     * Draw the route on the map
     */
    function drawRoute() {
        if (routePath && routePath.length > 0) {
            // Create a polyline for the route
            routeLayer = L.polyline(routePath, {
                color: '#3388FF',
                weight: 4,
                opacity: 0.7,
                lineJoin: 'round'
            }).addTo(map);
            
            // Fit the map to the route bounds (only initially if user location not yet available)
            if (!userPosition) {
                map.fitBounds(routeLayer.getBounds(), {
                    padding: [50, 50],
                    maxZoom: 16  // Limit how far it can zoom out
                });
            }
            
            // Initialize completed route layer (empty at first) if feature is enabled
            if (showCompletedRoute) {
                completedRouteLayer = L.polyline([], {
                    color: '#32CD32', // Green color for completed segments
                    weight: 4,
                    opacity: 0.8,
                    lineJoin: 'round'
                }).addTo(map);
                
                // Create a separate layer for the line connecting user to route
                userToRouteLayer = L.polyline([], {
                    color: '#32CD32', // Same base color as completed route but different style
                    weight: 3,
                    opacity: 0.5, // More transparent
                    dashArray: '6, 8', // Dashed line
                    lineJoin: 'round'
                }).addTo(map);
            }
        }
    }
    
    /**
     * Add artwork markers to the map
     */
    function addArtworkMarkers() {
        const artworks = artRouteData.artworks;
        
        if (artworks && artworks.length > 0) {
            artworks.forEach(function(artwork, index) {
                // Create a custom artwork marker with image background
                const artworkIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: `
                        <div class="artwork-marker-inner">
                            <div class="artwork-marker-image" style="background-image: url('${artwork.image_url}');"></div>
                            <div class="artwork-marker-overlay"></div>
                            <div class="artwork-marker-number">${index + 1}</div>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });
                
                // Create the marker
                const marker = L.marker([artwork.latitude, artwork.longitude], {
                    icon: artworkIcon
                }).addTo(map);
                
                // Add click event
                marker.on('click', function() {
                    // Create a copy of the artwork with userClicked flag
                    const clickedArtwork = {...artwork, userClicked: true};
                    showArtworkDetails(clickedArtwork);
                });
                
                // Add to array for proximity checking
                artworkMarkers.push({
                    marker: marker,
                    artwork: artwork,
                    visited: false
                });
            });
        }
    }
    
    /**
     * Show artwork details as a toast
     */
    function showArtworkDetails(artwork) {
        // Skip showing toasts if disabled AND not clicked by user
        if (!showArtworkToasts && !artwork.userClicked) {
            return;
        }
        
        // Create a toast instead of showing modal
        const toast = $(`
            <div class="art-route-toast" style="
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                margin-bottom: 10px;
                max-width: 320px;
                overflow: hidden;
                transform: translateX(400px);
                transition: transform 0.3s ease;">
                <div style="position: relative;">
                    <img src="${artwork.image_url}" alt="${artwork.title}" style="width: 100%; height: 160px; object-fit: cover;">
                    <div style="position: absolute; top: 10px; right: 10px; cursor: pointer; background-color: rgba(255,255,255,0.7); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;" class="toast-close">
                        &times;
                    </div>
                </div>
                <div style="padding: 16px;">
                    <h3 style="margin: 0 0 8px; font-size: 18px;">${artwork.title}</h3>
                    <div style="font-size: 14px; max-height: 100px; overflow-y: auto;">
                        ${artwork.description}
                    </div>
                    <div style="margin-top: 12px;">
                        <a href="${artwork.artist_url || '#'}" target="_blank" style="text-decoration: none; color: #3388FF;">
                            ${artwork.artist_url ? 'Meer informatie' : ''}
                        </a>
                    </div>
                </div>
            </div>
        `);

        // Add to queue
        toastQueue.push(toast);
        
        // If no toast is displaying, show this one
        if (!toastDisplaying) {
            showNextToast();
        }
    }
    
    /**
     * Display the next toast in queue
     */
    function showNextToast() {
        if (toastQueue.length === 0) {
            toastDisplaying = false;
            return;
        }
        
        toastDisplaying = true;
        const toast = toastQueue.shift();
        
        // Add to container
        $('#art-route-toast-container').append(toast);
        
        // Animate in
        setTimeout(function() {
            toast.css('transform', 'translateX(0)');
        }, 50);
        
        // Add close handler
        toast.find('.toast-close').on('click', function() {
            hideToast(toast);
        });
        
        // Auto dismiss after 12 seconds
        setTimeout(function() {
            hideToast(toast);
        }, 12000);
    }
    
    /**
     * Hide a toast element
     */
    function hideToast(toast) {
        toast.css('transform', 'translateX(400px)');
        
        setTimeout(function() {
            toast.remove();
            showNextToast();
        }, 300);
    }
    
    /**
     * Update the completed route based on user position
     */
    function updateCompletedRoute() {
        // Only proceed if showing completed route is enabled
        if (!showCompletedRoute) {
            return;
        }
        
        if (!userPosition || !routePath || routePath.length === 0) {
            return;
        }
        
        // Find the closest point on the route
        let closestPoint = findClosestPointOnRoute(userPosition, routePath);
        let closestIndex = closestPoint.index;
        
        // Update completed path (all points up to the closest one)
        completedPath = routePath.slice(0, closestIndex + 1);
        
        // Update the completed route layer
        completedRouteLayer.setLatLngs(completedPath);
        
        // Create a separate line from closest point to user position
        userToRouteLayer.setLatLngs([
            routePath[closestIndex],
            userPosition
        ]);
        
        // Calculate and update progress
        const totalDistance = calculateRouteDistance(routePath);
        const completedDistance = calculateRouteDistance(completedPath);
        const progressPercent = Math.min(Math.round((completedDistance / totalDistance) * 100), 100);
        
        // Update progress UI only if showing completed route is enabled
        $('.route-progress').show();
        $('.progress-fill').css('width', `${progressPercent}%`);
        $('#progress-percentage').text(progressPercent);
        
        // Check if route is complete
        if (progressPercent >= 98) {
            alert(artRouteData.i18n.routeComplete);
        }
    }
    
    /**
     * Find the closest point on the route to the user's position
     */
    function findClosestPointOnRoute(position, route) {
        let closestIndex = 0;
        let closestDistance = Infinity;
        
        for (let i = 0; i < route.length; i++) {
            const distance = calculateDistance(
                position[0], position[1],
                route[i][0], route[i][1]
            );
            
            if (distance < closestDistance) {
                closestDistance = distance;
                closestIndex = i;
            }
        }
        
        return {
            point: route[closestIndex],
            index: closestIndex,
            distance: closestDistance
        };
    }
    
    /**
     * Calculate distance between two points (Haversine formula)
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
     * Calculate total distance of a route
     */
    function calculateRouteDistance(route) {
        let totalDistance = 0;
        
        for (let i = 0; i < route.length - 1; i++) {
            totalDistance += calculateDistance(
                route[i][0], route[i][1],
                route[i+1][0], route[i+1][1]
            );
        }
        
        return totalDistance;
    }
    
    /**
     * Check if user is close to any artwork markers
     */
    function checkArtworkProximity() {
        if (!userPosition) return;
        
        const proximityThreshold = 50; // meters
        
        artworkMarkers.forEach(function(item) {
            if (item.visited) return;
            
            const markerPosition = item.marker.getLatLng();
            const distance = calculateDistance(
                userPosition[0], userPosition[1],
                markerPosition.lat, markerPosition.lng
            );
            
            if (distance <= proximityThreshold) {
                // Mark as visited
                item.visited = true;
                
                // Update marker style
                const icon = item.marker.options.icon;
                const newIconHtml = icon.options.html.replace('artwork-marker-inner', 'artwork-marker-inner visited');
                
                const newIcon = L.divIcon({
                    className: 'artwork-marker',
                    html: newIconHtml,
                    iconSize: icon.options.iconSize,
                    iconAnchor: icon.options.iconAnchor
                });
                
                item.marker.setIcon(newIcon);
                
                // Show artwork details as toast instead of modal (if toasts are enabled)
                if (showArtworkToasts) {
                    showArtworkDetails(item.artwork);
                }
                
                // Save visited status
                saveVisitedArtwork(item.artwork.id);
            }
        });
    }
    
    /**
     * Save visited artwork to server
     */
    function saveVisitedArtwork(artworkId) {
        $.ajax({
            url: artRouteData.ajax_url,
            type: 'POST',
            data: {
                action: 'wp_art_routes_mark_artwork_visited',
                artwork_id: artworkId,
                nonce: artRouteData.nonce
            },
            success: function(response) {
                console.log('Artwork visit saved');
            }
        });
    }
    
    /**
     * Clean up resources on page leave
     */
    $(window).on('beforeunload', function() {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
        }
    });
    
})(jQuery);