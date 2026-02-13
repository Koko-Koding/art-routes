/**
 * Single Artwork Map
 *
 * Initializes a small map on the single artwork page showing the artwork location.
 * Expects wpArtRoutesSingleArtwork to be localized with: { latitude, longitude, title, thumbnailUrl }
 *
 * @package WP Art Routes
 */

jQuery(document).ready(function($) {
    if (typeof wpArtRoutesSingleArtwork === 'undefined') {
        return;
    }

    var data = wpArtRoutesSingleArtwork;
    var mapContainer = document.getElementById('artwork-single-map');
    if (!mapContainer) {
        return;
    }

    // Initialize map
    var map = L.map('artwork-single-map').setView([data.latitude, data.longitude], 15);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Leaflet divIcon requires HTML string for custom marker (data is server-sanitized)
    var artworkIcon = L.divIcon({
        className: 'artwork-marker',
        html: '<div class="artwork-marker-inner"><div class="artwork-marker-image" style="background-image: url(\'' + data.thumbnailUrl + '\');"></div><div class="artwork-marker-overlay"></div></div>',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    L.marker([data.latitude, data.longitude], {
        icon: artworkIcon
    }).addTo(map)
    .bindPopup('<strong>' + data.title + '</strong>')
    .openPopup();
});
