# Changelog

All notable changes to the WP Art Routes plugin will be documented in this file.

## [1.2.4] - 2025-03-27
### Removed
- Removed the artist taxonomy and all related features for artwork
- Removed artist links from artwork popups and modals
- Simplified the artwork display by focusing only on the artwork itself

## [1.2.3] - 2025-03-26
- Added proper internationalization (i18n) support
- Added complete Dutch (nl_NL) translation
- Updated plugin file to load translation files

## [1.2.2] - 2025-03-26

### Changed
- Replaced route toggle checkboxes with "Zoom to Route" buttons in the multiple routes display
- Added a "Show All Routes" button to easily return to the overview of all routes
- Improved the user experience with smooth animations when zooming to specific routes
- Enhanced the visual feedback for the currently selected route

## [1.2.1] - 2025-03-26

### Enhanced
- Improved the legend display for multiple routes map with:
  - Route excerpts instead of full content for better readability
  - Distance and duration information with icons
  - Route type indicators with appropriate icons
  - Direct links to individual route pages
  - Improved mobile-friendly styling

## [1.2.0] - 2025-03-26

### Added
- New `[art_routes_map]` shortcode to display multiple routes on a single map
- Color-coding for different routes when displayed together
- Route toggle functionality to show/hide specific routes on the multi-route map
- Legend/control panel for multi-route maps with route information
- Responsive design for the multi-route map display on mobile devices
- Customization options for multi-route maps (height, zoom, center coordinates)

## [1.1.3] - 2023-10-XX

### Changed
- Increased size and improved styling (white icon on dark background) of artwork balloon's close button for better accessibility on smaller devices.

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