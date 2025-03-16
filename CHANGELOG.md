# Changelog

All notable changes to the WP Art Routes plugin will be documented in this file.

## [1.1.2] - 2023-07-18

### Fixed
- Fixed issue with artwork popups not reopening after being closed by using a different popup creation approach
- Improved popup handling to ensure consistent behavior across all browsers

## [1.1.1] - 2023-07-17

### Fixed
- Fixed issue where artwork popups couldn't be reopened after being closed when clicking an artwork marker

## [1.1.0] - 2023-07-15

### Added
- Artwork balloons that display within the map when clicking on artwork markers
- Better styling for artwork information display in map popups

### Changed
- Clicking an artwork icon now shows information in a map balloon instead of a toast notification
- Improved popup balloon styling for better readability

### Fixed
- Various UI issues with popup display and interaction

## [1.0.0] - 2023-07-01

### Added
- Initial release of WP Art Routes plugin
- Custom post types for routes and artworks
- Interactive maps using OpenStreetMap/Leaflet.js
- Route editor for creating custom paths
- Artwork location picker with map interface
- Artist taxonomy for categorizing artworks
- Route types (walking, cycling, wheelchair-accessible, children routes)
- Route details (length in km, duration in minutes)
- Location-aware functionality showing user position on map
- Route progress tracking with visualization of completed segments
- Proximity detection for nearby artworks with toast notifications
- Shortcode for embedding maps on any page
- Custom page template for full-page map display
- Plugin settings page for global configuration