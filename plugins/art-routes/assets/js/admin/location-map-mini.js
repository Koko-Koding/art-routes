/**
 * Location Map Mini
 *
 * Small inline map in the artwork/info point location meta box.
 * Allows clicking to set coordinates.
 *
 * @package Art_Routes
 */

jQuery(document).ready(function($) {
    var mapContainer = document.getElementById('artwork_location_map');
    if (!mapContainer) {
        return;
    }

    // Initialize small map for location in the meta box
    window.locationMap = L.map('artwork_location_map').setView([52.1326, 5.2913], 8);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(window.locationMap);

    // Get saved coordinates
    var lat = $('#artwork_latitude').val();
    var lng = $('#artwork_longitude').val();

    // Add marker if coordinates exist
    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
        window.locationMarker = L.marker([lat, lng]).addTo(window.locationMap);
        window.locationMap.setView([lat, lng], 14);
    }

    // Handle map click events on the small map too
    window.locationMap.on('click', function(e) {
        // Update form fields
        $('#artwork_latitude').val(e.latlng.lat.toFixed(6));
        $('#artwork_longitude').val(e.latlng.lng.toFixed(6));

        // Update or add marker
        if (window.locationMarker) {
            window.locationMarker.setLatLng(e.latlng);
        } else {
            window.locationMarker = L.marker(e.latlng).addTo(window.locationMap);
        }
    });
});
