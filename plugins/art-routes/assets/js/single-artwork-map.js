/**
 * Single Artwork Map
 *
 * Initializes a small map on the single artwork page showing the artwork location.
 * Renders the marker in the same style as edition and route page maps.
 * Expects artRoutesSingleArtwork to be localized with:
 *   { latitude, longitude, title, thumbnailUrl, iconUrl, number }
 *
 * @package Art_Routes
 */

jQuery(document).ready(function($) {
    if (typeof artRoutesSingleArtwork === 'undefined') {
        return;
    }

    var data = artRoutesSingleArtwork;
    var mapContainer = document.getElementById('artwork-single-map');
    if (!mapContainer) {
        return;
    }

    // Initialize map
    var map = L.map('artwork-single-map').setView([data.latitude, data.longitude], 15);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Build marker HTML matching the style used on edition and route pages
    var markerHtml;
    if (data.iconUrl && data.iconUrl.trim() !== '') {
        // Custom icon marker (same as edition/route pages)
        markerHtml = '<div class="artwork-marker-inner">' +
            '<div class="artwork-marker-icon" style="background-image: url(\'' + data.iconUrl + '\'); background-size: contain; background-repeat: no-repeat; background-position: center; width: 100%; height: 100%; border-radius: 50%;"></div>' +
            '</div>';
    } else {
        // Fallback: thumbnail image with overlay and number
        var displayNumber = (data.number && data.number.trim() !== '') ? data.number : '';
        markerHtml = '<div class="artwork-marker-inner">' +
            '<div class="artwork-marker-image" style="background-image: url(\'' + (data.thumbnailUrl || '') + '\');"></div>' +
            '<div class="artwork-marker-overlay"></div>' +
            '<div class="artwork-marker-number">' + displayNumber + '</div>' +
            '</div>';
    }

    // Leaflet divIcon requires HTML string for custom marker (data is server-sanitized)
    var artworkIcon = L.divIcon({
        className: 'artwork-marker',
        html: markerHtml,
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    L.marker([data.latitude, data.longitude], {
        icon: artworkIcon
    }).addTo(map)
    .bindPopup('<strong>' + data.title + '</strong>')
    .openPopup();
});
