# Changelog

[← Go back](README.md).

All notable changes to the WP Art Routes plugin will be documented in this file.

## [1.13.1] - 2025-07-19

### Fixed
- Minor CSS and JS improvements for map display and marker rendering. Patch release for bugfixes and style tweaks.

## [1.13.0] - 2025-07-19

### Enhanced

- **Improved Information Point Icon Selection**: Replaced the media picker with a dropdown selector for SVG icons from the assets/icons directory
- **Default Icon Support**: Information points now automatically use "WB plattegrond-Informatie.svg" as the default icon when no custom icon is selected
- **Better User Experience**: Icon selector shows cleaned-up display names (e.g., "Start" instead of "WB plattegrond-Start.svg") with live preview functionality
- **Backward Compatibility**: Maintains full compatibility with existing information points that use the old icon_url field
- **Consistent Icon Display**: All information points now display meaningful icons on maps instead of falling back to generic markers

### Technical Details

- Added new `_info_point_icon` meta field to store icon filenames
- Updated REST API to compute full icon URLs from filenames with fallback to default icon
- Enhanced template functions and AJAX handlers to support both new and legacy icon fields
- Improved map rendering to use custom SVG icons for information points across all map views

## [1.12.0] - 2025-07-17

### Changed

- Refactored marker stacking order logic for route maps: all marker z-index offsets are now defined centrally in a `markerDisplayOrder` object in `art-route-map.js`.
- Marker stacking order is now: Route start/end points (top), Artworks, Information points, Directional arrows (bottom).
- Improved maintainability and clarity for marker display order; future changes can be made by editing a single object.

### Technical Details

- All marker creation code now references the `markerDisplayOrder` object for zIndexOffset values.
- No functional changes for end users, but developers and maintainers benefit from easier configuration and updates.

## [1.11.2] - 2025-07-17

### Changed

- Standardized route length display formatting across all templates (always one decimal place, e.g. "3.2 km")
- Implemented readable duration formatting for route display (e.g. "2 hours 23 minutes")
- Improved responsiveness: added minimum height for route editor map on larger screens

### Fixed

- Route length and duration formatting now consistent for all routes and languages

## [1.11.1] - 2025-07-16

### Added

- **Automatic Duration Calculation**: Route duration is now automatically calculated based on route type and distance
  - Walking routes: 4.5 km/h average speed
  - Cycling routes: 15 km/h average speed  
  - Wheelchair-friendly routes: 3.5 km/h (slower walking pace)
  - Child-friendly routes: 3.0 km/h (slower pace for families)
- **Smart Auto-Update Logic**: Duration field updates automatically when route distance changes or route type is modified
- **Enhanced Route Editor**: Console logging for route length calculation and duration estimates for better debugging
- **Route Loading Improvements**: Added console logging when loading existing routes with total distance and point count

### Enhanced

- Duration field only auto-updates when empty or zero, preserving manually entered custom durations
- Real-time duration recalculation when changing route type in the admin interface
- Improved user experience with automatic estimates while maintaining manual override capability

## [1.11.0] - 2025-07-16

### Added

- **Custom Single Artwork Template**: Created a dedicated template for displaying individual artwork posts with enhanced features:
  - Prominently displays the artwork's featured image
  - Shows artwork number and location metadata if available
  - Lists associated artists with clickable links to their respective pages/posts
  - Includes an interactive location map showing the artwork's position
  - Fully responsive design optimized for mobile devices
- **Dutch Translation Support**: Added complete Dutch translations for all new template strings
  - Added translations for "Number:", "Location:", "Artist(s):", "Location on Map"
  - Updated and compiled Dutch translation files (.po and .mo)
  - Fixed syntax errors in existing Dutch translation file

### Enhanced

- **Template System**: Implemented proper template handling that allows theme overrides
  - Template can be customized by copying to theme's `wp-art-routes/single-artwork.php`
  - Follows WordPress best practices for template hierarchy
- **Asset Loading**: Updated script loading to include Leaflet CSS/JS on single artwork pages for map functionality
- **User Experience**: Artwork pages now provide rich, detailed information instead of basic post content

### Technical Details

- Added `wp_art_routes_single_artwork_template()` function for template redirection
- Modified `wp_art_routes_is_route_page()` to include single artwork pages
- Created responsive CSS styling with mobile breakpoints
- Integrated with existing artist association system
- Added proper escaping and sanitization for all output

## [1.10.0] - 2025-07-16

### Added

- Added "Number" field to artwork post type - optional text input for artwork identification
- Added "Location" field to artwork post type - optional text input for location description (e.g., "Near the town square")
- New fields are available in the WordPress admin when editing artworks
- Fields are accessible via REST API for external integrations
- Number and Location fields are specific to artworks only (not available for information points)

### Enhanced

- Improved artwork editing interface with better field organization
- Enhanced artwork data structure for more detailed information capture
- Better user experience for administrators managing artwork details
- **Map markers now display custom artwork numbers**: Artwork markers on route maps now show the custom number from the Number field instead of sequential array indices, making artwork identification more intuitive and consistent with organizational numbering systems

## [1.9.0] - 2025-07-14

### Added

- Added direction arrow functionality for route points
- Route points can now display custom direction arrows pointing in any direction (0-360°)
- New arrow direction field in the route point edit modal in the admin route editor
- Direction arrows are displayed both in the admin route editor and on public-facing maps
- Arrows are visually distinct with orange color and drop shadow for better visibility
- Arrow direction is saved as part of the route path data and preserved when loading existing routes

### Enhanced

- Improved route point editing interface with clearer field organization
- Enhanced visual feedback for route points with direction indicators
- Better user experience for administrators configuring route guidance

## [1.8.1] - 2025-07-11

### Changed

- Updated: Registered meta fields for artworks and information points as REST fields using `register_rest_field` for improved API access and compatibility.
- Updated: Import script to use new REST field structure for information points, supporting both new REST fields and legacy meta fields.

## [1.8.0] - 2025-07-11

- Added support for creating information points through the WordPress JSON REST API.
- Added the `@route-info-rest-client` project, which contains a Bun/Biome based REST client for importing information points via the REST API.

## [1.7.0] - 2025-07-10

### Added

- You can now upload or select a custom icon image for each information point in the WordPress admin (sidebar meta box).
- The selected icon image is saved and used as the marker for that information point on all public maps.
- If no icon is set, the default info point marker is used.
- The route editor modal also supports selecting an icon image for info points using the WordPress media library.

### Improved

- Information points with a custom icon are now visually distinct and easier to recognize on the map, improving clarity for all users.
- All features remain fully responsive and mobile-friendly.

## [1.6.2] - 2025-07-10

### Changed

- **BREAKING CHANGE**: Artworks are now also global and no longer associated with specific routes
- Removed route association meta box from artwork edit screens
- Both artworks and information points now appear on all maps regardless of the selected route
- Updated AJAX handlers to fetch all artworks globally instead of filtering by route
- Modified template functions to return all artworks for any route request

### Technical Details

- Removed artwork route association meta box and save functions
- Updated `wp_art_routes_get_associated_points()` to fetch all artworks globally
- Updated `wp_art_routes_get_route_artworks()` to return all artworks globally
- Modified save functionality to not associate any points (artworks or information points) with routes
- All points of interest are now truly global and shared across all routes

## [1.6.1] - 2025-07-10

### Changed

- **BREAKING CHANGE**: Information points are now global and no longer associated with specific routes
- Removed route association meta box from information point edit screens
- Information points now appear on all maps regardless of the selected route
- Updated AJAX handlers to fetch all information points globally instead of filtering by route
- Modified template functions to return all information points for any route request

## [1.6.0] - 2025-07-10

### Changed

- **Made artworks and information points global**: Both artworks and information points are now visible on all route maps, regardless of which specific route they were originally associated with.
- Updated `wp_art_routes_get_route_artworks()` and `wp_art_routes_get_route_information_points()` functions to return all published artworks and information points globally.
- Added new functions `wp_art_routes_get_all_artworks()` and `wp_art_routes_get_all_information_points()` for fetching global content.
- Updated multiple routes map template to display global artworks and information points on all maps.
- Improved user experience by ensuring all points of interest are visible regardless of the route being viewed.

### Technical Details

- Artworks and information points are no longer filtered by the `_artwork_route_id` meta key.
- Global markers are added directly to the map (not to route-specific layers) ensuring they remain visible when toggling between routes.
- Maintained backward compatibility with existing route editor functionality.
- Removed `wp_art_routes_render_artwork_route_meta_box` from information_point post type
- Updated `wp_art_routes_get_associated_points()` to fetch all information points globally
- Modified save functionality to only associate artworks with routes, not information points
- Updated shortcode templates to handle global information points correctly

## [1.5.0] - 2025-06-29

### Added

- Added start and end markers with popups to route paths, including enhanced route path data structure to support these markers.
- Made the Leaflet popup close button smaller for improved mobile usability.
- Enhanced route path saving and loading: now supports additional properties in JSON format for route points and improved handling for legacy data structures.
- Added edit functionality for route points: implemented modal for editing metadata and updated marker controls in the route editor.
- Added delete button functionality for route point markers in the route editor.
- Added draggable markers for route path points in the route editor for easier reordering and adjustment.
- Enhanced route path handling: supports JSON format for saving and retrieving route paths, added user geolocation logging, and improved data validation.

## [1.4.1] - 2025-04-17

### Fixed

- Fixed a bug where the popups for artworks and information points were trying to display a non-existent image when the image was not set. The popups now handle this case gracefully by not attempting to display an image if it is not available.

## [1.4.0] - 2025-04-17

### Added

- "Fit Route" button in the map editor to zoom/pan the map to show the entire route and all points.
- "My Location" button in the map editor to center the map on the user's current location.
- "Map View" section in editor controls for the new buttons.

### Changed

- Initial map view in the editor now attempts to fit the existing route/points first, falling back to user location or default view.

## [1.3.3] - 2025-04-17

### Fixed

- Refactored the way the route map editor initially pans & zooms (first, try to fit bounds on existing points, otherwise, pan to user location, otherwise, pan to the netherlands).

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

### Changed

- Initial map view in the editor now attempts to fit the existing route/points first, falling back to user location or default view.

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
