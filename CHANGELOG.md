# Changelog

All notable changes to the WP Art Routes plugin will be documented in this file.

## [1.3.2] - 2025-04-17

### Fixed

- Resolved a JavaScript TypeError (`undefined is not an object (evaluating 'editorMap.getContainer')`) in the route editor admin script (`route-editor-admin.js`) by ensuring the map object is initialized before attaching event handlers to its container.

## [1.3.1] - 2025-04-17

### Changed

- Modified route editor to load associated artworks and information points that are in 'draft' status.
- Added visual indicators (opacity, border, popup text) in the route editor map for draft points (both newly added and previously saved drafts), warning users they are not yet public.

## [1.3.0] - 2025-04-17

### Added

- Added new "Information Point" post type to allow adding general points of interest to routes, distinct from artworks.
- Implemented functionality to display Information Points on the route map with a unique icon.
- Added meta boxes for managing Information Point details (title, description, location).
- Updated route editor to allow adding and managing Information Points alongside artworks.
- Updated map display logic in `art-route-map.js` to handle Information Points.
- Added specific CSS styles for Information Points in `art-route-map.css` and `route-editor-admin.css`.

## [1.2.5] - 2025-03-27

### Improved

- De-duplicated CSS styles between the single route map and multiple routes map
- Moved all styles from shortcode-multiple-map.php to the main art-route-map.css file
- Better organization of styles with clear section comments
- Improved maintainability and consistency across different map displays
- Replace artist taxonomy with a many-to-many relationship between artworks and any post type

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

## [1.1.3] - 2025-03-16

### Changed

- Increased size and improved styling (white icon on dark background) of artwork balloon's close button for better accessibility on smaller devices.

## [1.1.2] - 2025-03-16

### Fixed

- Fixed issue with artwork popups not reopening after being closed by using a different popup creation approach
- Improved popup handling to ensure consistent behavior across all browsers

## [1.1.1] - 2025-03-16

### Fixed

- Fixed issue where artwork popups couldn't be reopened after being closed when clicking an artwork marker

## [1.1.0] - 2025-03-16

### Added

- Artwork balloons that display within the map when clicking on artwork markers
- Better styling for artwork information display in map popups

### Changed

- Clicking an artwork icon now shows information in a map balloon instead of a toast notification
- Improved popup balloon styling for better readability

### Fixed

- Various UI issues with popup display and interaction

## [1.0.0] - 2025-03-16

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
