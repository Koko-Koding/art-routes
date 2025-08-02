# Changelog

[← Go back](README.md).

All notable changes to the WP Art Routes plugin will be documented in this file.

## [1.21.2] - 2025-08-03

### Fixed

- Add missing toggle visibility functions

## [1.21.1] - 2025-08-03

### Fixed

- Refactored code to remove unused parameters and improve readability.
- Enhanced security by sanitizing and unslashing nonce values, using strict in_array, removing extract(), and adding nonce verification for forms and templates.
- Replaced deprecated json_encode() with wp_json_encode() for better compatibility.
- Renamed single-information_point.php to single-information-point.php for consistency.
- Updated PHP_CodeSniffer to version 3.7.2 and fixed code style issues.
- Updated Docker volume path syntax for consistency.
- Updated VSCode and development environment settings for improved formatting and consistency.
- Escaped output in meta boxes, settings, and templates for improved security.
- Enhanced shortcode functionality to support ids and exclude_ids attributes for displaying multiple routes.
- Loaded Leaflet assets from the local plugin directory instead of CDN for WordPress compliance.
- Added .editorconfig, .gitattributes, and updated .gitignore for consistent coding standards.
- Removed unnecessary fields and text domain loading from plugin header.
- Updated documentation and copilot instructions to emphasize conventional commit messages.

## [1.21.0] - 2025-07-28

### Added

- **Docker-based Code Quality Tools**: Comprehensive development environment with automated WordPress coding standards enforcement
  - Added `bin/dev-tools` script for easy access to code quality tools
  - Integrated PHP_CodeSniffer (PHPCS) with WordPress coding standards
  - Added `Dockerfile` and `docker-compose.yml` for containerized development
  - Included `phpcs.xml` configuration tailored for WordPress plugin development
  - Automatic fixing of unsafe printing functions (`_e()` → `esc_html_e()`)
  - Security-focused code analysis and validation
  - Internationalization compliance checking
  - Translation file compilation tools

### Enhanced

- **Development Workflow**: Streamlined code quality management
  - `bin/dev-tools check` - Comprehensive code analysis with WordPress standards
  - `bin/dev-tools fix` - Automatic resolution of coding standard violations
  - `bin/dev-tools security` - Security-focused vulnerability scanning
  - `bin/dev-tools compile` - Translation file compilation
  - Fixed 6,371+ code quality issues automatically across all plugin files
  - Improved output escaping and input sanitization throughout codebase

- **Documentation**: Enhanced coding guidelines and development instructions
  - Updated `.github/copilot-instructions.md` with code quality tool documentation
  - Added comprehensive usage examples and best practices
  - Clear guidelines for pre-commit code quality checks

### Technical Details

- Automatically resolved unsafe printing functions across templates and includes
- Enhanced security through proper output escaping and nonce verification
- Improved code formatting and WordPress coding standard compliance
- Containerized development environment for consistent code quality across all contributors
- Zero-configuration setup for automated code quality enforcement

## [1.20.1] - 2025-07-28

### Fixed

- **Information Point Detail Pages**: Fixed issue where information point single pages were not displaying location maps
  - Created dedicated single template (`templates/single-information-point.php`) for information point detail pages
  - Added template loading logic to automatically use the new template for information point pages
  - Updated script loading conditions to include information point pages for proper Leaflet map functionality
  - Information point detail pages now display an interactive map showing the point's exact location
  - Template includes responsive design and consistent styling with artwork pages
  - Uses custom blue circular marker with "i" icon to distinguish from artwork markers

### Enhanced

- **Template System**: Information points now have the same rich detail page experience as artworks
  - Featured image display
  - Full content with proper formatting
  - Interactive location map with custom markers
  - Mobile-responsive design
- **User Experience**: Improved navigation and information discovery for information points
  - Consistent template hierarchy that allows theme overrides
  - Professional map display with proper popup functionality

### Technical Details

- Added `wp_art_routes_single_information_point_template()` function for template redirection
- Modified `wp_art_routes_is_route_page()` to include single information point pages
- Created responsive CSS styling with mobile breakpoints matching artwork template design
- Maintained backward compatibility with existing information point functionality

## [1.20.0] - 2025-07-27

### Changed

- **BREAKING CHANGE: Icon System Migration**: Completely replaced SVG icon system with WordPress Dashicons for professional appearance and better performance
  - **Artwork Icons**: Admin dropdown now offers curated selection of relevant dashicons (art, image, camera, gallery, heart, star, location, marker, etc.) instead of SVG files
  - **Information Point Icons**: Admin dropdown now offers curated selection of relevant dashicons (info, info-outline, location, marker, flag, warning, megaphone, etc.) instead of SVG files
  - **Map Rendering**: All map markers now display dashicons instead of SVG images with proper styling and colors
  - **REST API**: Updated to use `icon_class` field containing dashicon class names; `icon_url` field deprecated but maintained for backward compatibility
  - **Template Functions**: Updated to pass `icon_class` instead of `icon_url` to frontend JavaScript
  - **Admin Interface**: Meta boxes completely redesigned with dashicon previews and better user experience

### Enhanced

- **Performance Improvements**: Dashicons load faster than SVG files and are cached by WordPress core
- **Consistent Styling**: All icons now use consistent WordPress design language with proper colors and sizing
- **Better Accessibility**: Dashicons provide better contrast and screen reader support
- **Mobile Optimization**: Improved touch targets and icon scaling on mobile devices
- **Professional Appearance**: More suitable for business and organizational websites compared to custom SVG files

### Technical Details

- **Backward Compatibility**: Existing installations will fall back to default dashicons (dashicons-art for artworks, dashicons-info for information points)
- **Database Migration**: No database migration required - new icon selections will overwrite old SVG references
- **CSS Updates**: Complete redesign of marker styling to properly display and animate dashicons
- **JavaScript Updates**: Updated all map rendering code to use dashicon HTML instead of SVG URLs
- **Admin Updates**: Replaced SVG file dropdowns with curated dashicon selection interfaces

### Migration Notes

- **For Users**: Simply edit your artworks and information points to select new dashicon-based icons from the dropdown
- **For Developers**: Update any custom code referencing `icon_url` to use `icon_class` instead
- **For Theme Developers**: Dashicons are loaded by WordPress core, no additional CSS required

## [1.19.0] - 2025-07-27

### Added

- **Build and Distribution System**: Added comprehensive build tooling for plugin distribution
  - New `bin/build-release` script to create WordPress.org-ready zip packages
  - Automated version extraction and zip file creation with proper file filtering
  - Distribution documentation (`DISTRIBUTION.md`, `WORDPRESS-ORG-PREPARATION.md`)
  - Build artifacts are excluded from version control via `.gitignore`
- **Development Infrastructure**: Enhanced project setup for better maintainability
  - Added `.gitignore` file to exclude build artifacts, development files, and system files
  - Improved plugin metadata and description for better WordPress.org compatibility

### Fixed

- **Map Display**: Fixed fallback value for artwork display number to use empty string instead of undefined
  - Prevents display issues when artwork numbers are not set
  - Improves map marker rendering consistency

### Technical Details

- Added automated build script that creates distribution-ready zip files
- Enhanced plugin documentation for WordPress.org submission process
- Improved development workflow with proper gitignore configuration

## [1.18.0] - 2025-07-26

### Added

- Add translate script & Dockerfile to make it easy for the developer to generate .mo files from .po translation files.

## [1.17.1] - 2025-07-24

### Added

- **New Icon**: Added new icon image to the assets/icons directory for enhanced visual options

## [1.17.0] - 2025-07-23

### Added

- **Route Point Insertion Feature**: New insert button for adding route points between existing points
  - Added green "+" button next to edit and delete buttons on route point markers
  - Insert button allows users to add a new route point after the current point
  - New points are automatically positioned at the midpoint between current and next point
  - For last points, new points are created with a small geographical offset
  - Maintains the same object structure as existing route points with proper metadata support
  - Provides immediate visual feedback and marks changes as unsaved
  - Significantly improves route editing workflow by allowing precise point insertion

### Enhanced

- **Route Editor User Experience**: Improved route point management capabilities
  - Better precision when fine-tuning route paths
  - Eliminates need to delete and redraw route segments for minor adjustments
  - Streamlined workflow for adding intermediate waypoints

## [1.16.1] - 2025-07-23

### Changed

- **Route Length is also updated through button**

## [1.16.0] - 2025-07-23

### Changed

- **Manual Duration Calculation**: Duration estimation is now a manual action rather than automatic
  - Removed automatic duration calculation when route type changes or route distance updates
  - Added "Calculate" button next to the duration field for manual duration estimation
  - Duration field can now be manually overridden without being automatically recalculated
  - Users have full control over when duration is calculated and can enter custom values

### Enhanced

- **Translation Support**: Added Dutch translations for new duration calculation button
  - "Calculate" → "Bereken"
  - "Calculate estimated duration based on route distance and type" → "Bereken geschatte duur op basis van route afstand en type"

## [1.15.1] - 2025-07-20

### Changed

- **Professional Icon System**: Replaced informal emoji icons with WordPress Dashicons for a more professional appearance
  - Artwork toggle: Changed from 🎨 to `dashicons-art`
  - Information points toggle: Changed from ℹ️ to `dashicons-info`
  - Route toggle: Changed from 🛣️ to `dashicons-route`
  - User location toggle: Changed from 📍 to `dashicons-location`
  - Navigation buttons: Updated "Go to My Location" (🧭 → `dashicons-location-alt`) and "Go to Route" (🗺️ → `dashicons-admin-site`)
  - Control panel title: Changed from ⚙️ to `dashicons-admin-generic`

### Enhanced

- **Consistent Visual Design**: All map controls now use WordPress's standard icon system for better integration
- **Improved Accessibility**: Dashicons provide better contrast and recognition compared to emoji
- **Professional Appearance**: More suitable for business and organizational websites
- **Better Browser Compatibility**: Dashicons load reliably across all browsers without emoji font dependencies

### Technical Details

- Updated CSS to properly style Dashicons with appropriate sizing and spacing
- Maintained all existing functionality while improving visual presentation
- Enhanced mobile responsiveness with proper icon scaling

## [1.15.0] - 2025-07-20

### Added

- **Map Visibility Toggle Controls**: Added comprehensive toggle controls for map elements to reduce visual clutter
  - Toggle button to show/hide artwork markers
  - Toggle button to show/hide information point markers  
  - Toggle button to show/hide route path and related elements
  - Toggle button to show/hide user location marker
- **Modern Control Interface**: Designed responsive toggle controls with:
  - Clean grid layout that adapts to different screen sizes
  - Visual feedback with hover states and checked indicators
  - Emoji icons for intuitive identification of each control type
  - Smooth animations and transitions for better user experience
- **Smart State Management**: Toggle controls intelligently handle:
  - Automatic popup closure when hiding marker types
  - Proper layer management in Leaflet map
  - Persistent state during user interaction
  - Fallback styling for browsers without modern CSS support

### Enhanced

- **Improved User Experience**: Users can now customize their map view based on their needs
  - Reduce visual clutter when focusing on specific elements
  - Better performance on crowded maps with many markers
  - More control over information density
- **Mobile-Friendly Design**: Toggle controls are fully responsive and touch-friendly
  - Single-column layout on mobile devices
  - Appropriate sizing for touch interaction
  - Maintains usability across all device sizes
- **Accessibility Improvements**: Added proper ARIA labels and keyboard navigation support

### Technical Details

- Added visibility state management system in JavaScript
- Implemented efficient marker show/hide functionality using Leaflet's layer management
- Enhanced CSS with modern grid layout and fallbacks for older browsers
- Updated translation files with new control labels in Dutch and English
- Maintained backward compatibility with existing map functionality

## [1.14.0] - 2025-01-19

### Added

- **Artwork Icon Selection**: Added the ability to select custom SVG icons for artwork markers on maps
- **Icon Dropdown Interface**: Artworks now have an icon selection meta box in the admin with a dropdown of available SVG icons from the assets/icons directory
- **Map Rendering Support**: Map markers now check for custom artwork icons and display them instead of the default image+number combination when available
- **Consistent Icon Experience**: Artwork icon selection follows the same pattern as information points, providing a unified user experience
- **Route Editor Integration**: The route editor admin interface now supports icon selection for both artworks and information points when adding or editing points

### Enhanced

- **Template Functions**: Updated artwork data structures to include icon_url information for proper map rendering
- **AJAX Handlers**: Enhanced to include artwork icon data in route point management and map data loading
- **REST API Fields**: Added artwork icon support to REST API endpoints for external integrations
- **Backward Compatibility**: Maintained full compatibility with existing artworks that don't have custom icons

### Technical Details

- Added `_artwork_icon` meta field to store icon filenames for artworks
- Updated template functions to build icon URLs from filenames with proper fallbacks
- Enhanced JavaScript map rendering to prioritize custom icons over default artwork display
- Improved admin interface consistency between artwork and information point icon selection
- Added proper nonce verification and sanitization for artwork icon meta box saving

## [1.13.2] - 2025-07-20

### Fixed

- **Route Point Duplication Issue**: Fixed race conditions in route editor that could cause duplicate route points to be added when clicking on the map
- **Improved Marker Cleanup**: Enhanced marker removal system to properly clean up event handlers and prevent memory leaks
- **Debounced Route Updates**: Added debouncing to route information updates to prevent rapid multiple calls that could cause rendering issues
- **Duplicate Point Prevention**: Added coordinate comparison to prevent adding route points at identical locations
- **Enhanced State Management**: Improved reset functionality to properly clean up all timeouts and markers when closing the editor

### Technical Details

- Added proper timeout reference management for route point marker event handlers
- Implemented debouncing mechanism for `updateRouteInfo()` function calls
- Enhanced `drawRoutePointMarkers()` function with more thorough cleanup procedures
- Added unique marker identification and improved drag handler stability
- Strengthened `resetEditorState()` function to handle all cleanup scenarios

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
