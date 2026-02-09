/**
 * Artwork Location Picker JavaScript
 * Handles the map modal for picking artwork locations in the WordPress admin
 */

(function($) {
    // Map variables
    let pickerMap, locationMarker;
    let selectedLocation = null;
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Add modal to body if not already there
        if ($('#artwork-location-modal').length === 0) {
            $('body').append(artworkLocationModalHTML);
        }
        
        // Setup event handlers
        setupLocationPickerEvents();
    });
    
    /**
     * Set up event handlers for the location picker
     */
    function setupLocationPickerEvents() {
        // Open the location picker modal
        $('#pick_artwork_location').on('click', function(e) {
            e.preventDefault();
            openLocationPickerModal();
        });
        
        // Close modal events
        $('.location-picker-modal .close-modal, #cancel-location').on('click', function() {
            closeLocationPickerModal();
        });
        
        // Close modal if clicking outside the content
        $(document).on('click', '.location-picker-modal', function(e) {
            if ($(e.target).hasClass('location-picker-modal')) {
                closeLocationPickerModal();
            }
        });
        
        // Search location
        $('#search-artwork-location').on('click', function() {
            searchArtworkLocation();
        });
        
        $('#location-search').on('keypress', function(e) {
            if (e.which === 13) {
                searchArtworkLocation();
                e.preventDefault();
            }
        });
        
        // Save location
        $('#save-location').on('click', function() {
            saveArtworkLocation();
        });
    }
    
    /**
     * Open the location picker modal
     */
    function openLocationPickerModal() {
        // Show the modal
        $('#artwork-location-modal').show();
        
        // Initialize map if not already initialized
        if (!pickerMap) {
            initLocationPickerMap();
        } else {
            // Reset the map view if already initialized
            pickerMap.invalidateSize();
        }
        
        // Load existing location if available
        loadExistingLocation();
    }
    
    /**
     * Close the location picker modal
     */
    function closeLocationPickerModal() {
        $('#artwork-location-modal').hide();
    }
    
    /**
     * Initialize the location picker map
     */
    function initLocationPickerMap() {
        // Create the map
        pickerMap = L.map('location-picker-map').setView([52.1326, 5.2913], 8); // Default center on Netherlands
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(pickerMap);
        
        // Add scale control
        L.control.scale().addTo(pickerMap);
        
        // Add click handler for setting location
        pickerMap.on('click', onMapClick);
    }
    
    /**
     * Handle map clicks to set location
     */
    function onMapClick(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Store selected location
        selectedLocation = [lat, lng];
        
        // Update the coordinates display
        updateCoordinatesDisplay();
        
        // Update or add marker
        if (locationMarker) {
            locationMarker.setLatLng(e.latlng);
        } else {
            const locationIcon = L.divIcon({
                className: 'artwork-location-marker',
                html: '<div class="artwork-location-dot"></div>',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });
            
            locationMarker = L.marker([lat, lng], {
                icon: locationIcon,
                draggable: true
            }).addTo(pickerMap);
            
            // Update coordinates when marker is dragged
            locationMarker.on('dragend', function(event) {
                const marker = event.target;
                const position = marker.getLatLng();
                selectedLocation = [position.lat, position.lng];
                updateCoordinatesDisplay();
            });
        }
    }
    
    /**
     * Update the coordinates display
     */
    function updateCoordinatesDisplay() {
        if (selectedLocation) {
            $('#selected-coordinates').text(
                `Lat: ${selectedLocation[0].toFixed(6)}, Lng: ${selectedLocation[1].toFixed(6)}`
            );
        } else {
            $('#selected-coordinates').text('None');
        }
    }
    
    /**
     * Search for a location
     */
    function searchArtworkLocation() {
        const searchValue = $('#location-search').val().trim();
        
        if (!searchValue) return;
        
        // Show loading indicator
        $('#search-artwork-location').text('Searching...').prop('disabled', true);
        
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
                    pickerMap.setView([lat, lon], 14);
                    
                    // Add a temporary highlight
                    const searchHighlight = L.circle([lat, lon], {
                        color: '#FF5722',
                        fillColor: '#FF5722',
                        fillOpacity: 0.5,
                        radius: 50
                    }).addTo(pickerMap);
                    
                    // Remove highlight after 3 seconds
                    setTimeout(function() {
                        pickerMap.removeLayer(searchHighlight);
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
                $('#search-artwork-location').text('Search').prop('disabled', false);
            }
        });
    }
    
    /**
     * Save the selected location to the form fields
     */
    function saveArtworkLocation() {
        if (!selectedLocation) {
            alert('Please select a location on the map first.');
            return;
        }
        
        // Update the form fields
        $('#artwork_latitude').val(selectedLocation[0].toFixed(6));
        $('#artwork_longitude').val(selectedLocation[1].toFixed(6));
        
        // Also update the small map in the meta box if it exists
        if (window.locationMap && window.locationMarker) {
            window.locationMarker.setLatLng(selectedLocation);
            window.locationMap.setView(selectedLocation, 14);
        } else if (window.locationMap) {
            // Create marker if it doesn't exist
            window.locationMarker = L.marker(selectedLocation).addTo(window.locationMap);
            window.locationMap.setView(selectedLocation, 14);
        }
        
        // Close the modal
        closeLocationPickerModal();
        
        // Show success message
        alert('Location saved successfully.');
    }
    
    /**
     * Load existing location from form fields
     */
    function loadExistingLocation() {
        const lat = $('#artwork_latitude').val().trim();
        const lng = $('#artwork_longitude').val().trim();
        
        if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
            selectedLocation = [parseFloat(lat), parseFloat(lng)];
            
            // Update the coordinates display
            updateCoordinatesDisplay();
            
            // Add marker for existing location
            if (locationMarker) {
                locationMarker.setLatLng(selectedLocation);
            } else {
                const locationIcon = L.divIcon({
                    className: 'artwork-location-marker',
                    html: '<div class="artwork-location-dot"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });
                
                locationMarker = L.marker(selectedLocation, {
                    icon: locationIcon,
                    draggable: true
                }).addTo(pickerMap);
                
                // Update coordinates when marker is dragged
                locationMarker.on('dragend', function(event) {
                    const marker = event.target;
                    const position = marker.getLatLng();
                    selectedLocation = [position.lat, position.lng];
                    updateCoordinatesDisplay();
                });
            }
            
            // Center map on existing location
            pickerMap.setView(selectedLocation, 14);
        }
    }
    
})(jQuery);