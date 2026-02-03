# Changelog

[← Go back](README.md).

All notable changes to the WP Art Routes plugin will be documented in this file.

**Versioning Note:** Versions 1.19.1 through 1.31.0 used a `wenb-` prefix (e.g., `wenb-1.25.0`) when the plugin was specific to Woest & Bijster. As of version 2.0.0, the plugin uses standard semantic versioning and is suitable for any organization running location-based cultural events.

## [2.0.0] - 2026-02-03

### Changed

- **Standalone Release**: Plugin now uses standard semantic versioning (removed `wenb-` prefix)
- Ready for use by any organization, not just Woest & Bijster
- No functional changes from wenb-1.31.0

---

## Historical Releases (wenb- prefix)

## [wenb-1.31.0] - 2026-02-03

### Added

- **Edition Settings in Dashboard**: The Edition Dashboard now includes a collapsible "Edition Settings" section with:
  - Event dates (start and end date)
  - Default location icon selector with live preview
  - Terminology overrides (Route, Location, Info Point, Creator - singular/plural)
- Settings changes are saved via AJAX without page reload
- All settings previously only available on the WordPress edit page are now accessible from the dashboard

## [wenb-1.30.1] - 2026-02-03

### Fixed

- **Custom Icons 404 Error**: Fixed custom uploaded icons returning 404 errors on maps
- Centralized all icon URL generation to use `wp_art_routes_get_icon_url()` which properly checks both custom uploads directory and built-in icons directory
- Updated icon URL building in: template-functions.php, edition-dashboard.php, ajax-handlers.php, editions.php, post-types.php, meta-boxes.php, settings.php
- Icon selection dropdowns now include custom uploaded icons in all admin areas (artwork, info point, and edition settings)

## [wenb-1.30.0] - 2026-02-03

### Added

- **Custom Icon Uploads**: New "Custom Icons" tab in Settings page allows uploading custom SVG, PNG, JPG, and WebP icons
- Icons are stored in `wp-content/uploads/wp-art-routes-icons/`
- Custom icons appear in all icon selection dropdowns alongside built-in icons
- SVG sanitizer prevents XSS attacks from malicious SVG files
- Upload and delete icons directly from the admin interface

## [wenb-1.29.0] - 2026-02-03

### Added

- **Edition Default Icon**: Each edition can now have its own default location icon (Edition Settings meta box)
- Icon fallback chain: Location icon → Edition default icon → Global default icon

## [wenb-1.28.1] - 2026-02-03

### Fixed

- **Edition Map Icons Not Showing**: Fixed icons not appearing on edition map shortcode - was using `image_url` instead of `icon_url` for artwork markers
- **URL Encoding in Edition Dashboard**: Fixed missing `rawurlencode()` for icon URLs in the Edition Dashboard AJAX handler

## [wenb-1.28.0] - 2026-02-03

### Added

- **Create Edition During Import**: CSV and GPX import now allows creating a new edition directly from the import form
- **Dashboard Link After Import**: Successful imports now show a button linking to the edition's dashboard page
- **Publish/Draft Buttons in Dashboard**: Added explicit publish/draft toggle buttons in each row of the Edition Dashboard for easier status management
- **Edition Delete Modal**: Custom confirmation modal when deleting editions with options to keep or delete linked content

### Fixed

- **Default Location Icon**: Fixed icon not appearing for locations without assigned icons
- **Icon Filenames with Spaces**: Fixed URL encoding for icon files containing spaces (e.g., "WB plattegrond-10.svg")
- **Edition Delete Modal Loading**: Fixed modal stuck on "Loading..." due to Thickbox content copying issue
- **Icon Settings**: Fixed icon selection being corrupted by sanitize_file_name (now validates against available icons instead)
- **Dashboard Icon Updates**: Fixed icon changes not reflecting immediately in Edition Dashboard after saving

## [wenb-1.27.1] - 2026-02-03

### Fixed

- **Critical Fix**: Fixed fatal error on Settings page caused by missing icon helper functions (load order issue)
- Added missing `wp_art_routes_get_available_icons()`, `wp_art_routes_get_icon_url()`, `wp_art_routes_get_icon_display_name()`, and `wp_art_routes_get_default_info_icon()` helper functions to terminology.php
- Fixed file load order so terminology.php loads before settings.php

## [wenb-1.27.0] - 2026-02-03

### Added

- **Default Location Icon Setting**: New setting in Settings → General to select a default icon for locations without icons (e.g., imported via GPX)
- **Import Duplicate Detection**: Both CSV and GPX imports now detect and skip duplicate items
  - Locations/Info Points: Skipped if coordinates within ~2 meters OR title matches existing item in same edition
  - Routes: Skipped if title matches existing route in same edition
  - Import results now show count of created and skipped items

### Fixed

- **Edition 404 Fix**: Plugin now automatically flushes rewrite rules when version changes, fixing 404 errors on edition pages after plugin updates

## [wenb-1.26.0] - 2026-02-02

### Added

- **Edition Dashboard**: New comprehensive admin page for bulk managing edition content
  - Overview map showing all routes (polylines), locations, and info points (markers)
  - Draft items displayed at 50% opacity on map
  - Collapsible sections for Routes, Locations, Info Points with counts
  - Inline editing for title, number, and GPS coordinates
  - Status toggle (click badge to instantly publish/draft)
  - Icon selector dropdown for locations and info points
  - Bulk actions: Publish, Draft, Delete selected items
  - Quick selection buttons: Select All, Select None, Select Drafts
  - Section collapse state persisted per edition in localStorage

### Changed

- **Edition Map Block**: Improved editor preview showing current settings instead of blank
  - Displays selected edition name or "Auto-detect from page"
  - Shows visible content summary (Routes, Locations, Info Points, Legend)
  - Shows configured map height
  - Visual gradient design with hint that interactive map appears on publish

### Fixed

- GPX import now correctly validates file type using extension check instead of MIME type
- Renamed top-level admin menu from "Editions" to "Art Routes" for clarity

## [wenb-1.25.0] - 2026-02-02

### Added

- **Editions System**: New organizational layer for grouping content by events/time periods
  - Edition custom post type for cultural events (e.g., "Gluren bij de Buren 2026", "Kunstroute 2025")
  - Per-edition terminology overrides (Route, Location, Info Point, Creator labels)
  - Edition linking via `_edition_id` meta field on Routes, Locations, and Info Points
  - Edition column and dropdown filter in admin list tables
  - Edition selector meta box on content edit screens
  - Edition single page template with map, routes grid, locations grid, and info points list

- **Terminology System**: Centralized customizable labels with cascade logic
  - Global terminology settings (General → Terminology tab)
  - Edition-specific overrides that fall back to global, then to defaults
  - Helper functions: `wp_art_routes_label()`, `wp_art_routes_slug()`, `wp_art_routes_get_edition_terminology()`

- **Edition Map Block**: New Gutenberg block (`wp-art-routes/edition-map`)
  - Server-side rendered for dynamic content
  - Auto-detects edition on single edition pages
  - Configurable: show/hide routes, locations, info points, legend
  - Custom height setting

- **Edition Map Shortcode**: `[edition_map]` with full attribute support
  - `edition_id`, `routes`, `show_locations`, `show_info_points`, `show_legend`, `height`
  - Auto-detection on edition single pages

- **Import/Export Admin Page** (Editions → Import/Export)
  - CSV import for Locations and Info Points with template download
  - GPX import with three modes: route path only, route + waypoints, waypoints only
  - CSV export for edition content
  - GPX export with routes as tracks and locations/info points as waypoints

- **Admin Menu Restructure**: All content types now under Editions top-level menu
  - Editions, Routes, Locations, Info Points, Import/Export, Settings

- **Verification Script**: `./bin/verify-editions-system` to validate integration

### Changed

- Settings page reorganized with tabbed interface (General, Terminology)
- Menu structure consolidated under Editions for better organization

### Fixed

- Edition menu now shows "Editions" instead of "All Editions"
- Removed "Add New Edition" from submenu (editions created deliberately)
- Leaflet.js properly enqueued for edition_map shortcode and Edition Map block
- Map container existence check before initialization

## [wenb-1.24.3] - 2025-10-14

### Fixed

- **GPX Export Validation**: Fixed GPX file generation to properly sanitize content for XML format
  - Added new `wp_art_routes_sanitize_for_gpx()` helper function for proper content sanitization
  - Removes WordPress shortcodes (`[video]`, `[audio]`, etc.) from GPX descriptions
  - Properly handles HTML entities like `&nbsp;`, `&mdash;`, etc. by converting them to valid characters
  - Strips all HTML tags and normalizes whitespace in descriptions
  - Ensures all special XML characters are properly escaped
  - Exported GPX files now pass strict XML validation and are compatible with all GPS software
  - Fixes "Entity 'nbsp' not defined" XML parser errors

### Technical Details

- Implemented comprehensive content sanitization pipeline: shortcode removal → HTML stripping → entity decoding → character replacement → XML escaping
- Used `strip_shortcodes()`, `html_entity_decode()`, and `htmlspecialchars()` with proper XML flags
- Updated `wp_art_routes_generate_gpx()` to use new sanitization function for all text content

## [wenb-1.24.2] - 2025-09-14

### Fixed

- **GPX Element Order Compliance**: Fixed GPX export to follow strict GPX 1.1 specification element ordering
  - Moved waypoints (`<wpt>`) before track elements (`<trk>`) per GPX schema requirements
  - Ensures proper element sequence: metadata, waypoints, routes, tracks, extensions
  - Resolves Garmin BaseCamp validation errors about invalid elements after tracks
  - Improved compatibility with professional GPS software and GPX validators

## [wenb-1.24.1] - 2025-09-14

### Fixed

- **GPX Export Compatibility**: Updated GPX export format to be fully compatible with Garmin BaseCamp and other GPS software
  - Added `standalone="no"` attribute to XML declaration
  - Added `xmlns:xsi` namespace declaration for XML Schema instance
  - Added `xsi:schemaLocation` attribute with proper GPX 1.1 schema reference
  - GPX files now follow the exact format structure that works with professional GPS software

## [wenb-1.24.0] - 2025-09-05

### Added

- **GPX Export Feature**: Added the ability to export routes as GPX files for use with GPS devices and mapping applications
  - Export button appears on all route pages (shortcode maps and single route pages)
  - GPX files include the complete route path as a track for navigation
  - All artworks are included as individual waypoints with names, descriptions, and numbering
  - Information points are included as waypoints with proper descriptions
  - Files are automatically named after the route title for easy identification
  - Secure download with proper nonce verification and file headers
  - Compatible with all standard GPS devices and mapping applications that support GPX format

### Enhanced

- **Route Portability**: Routes can now be used offline and with external GPS applications
- **Mobile GPS Integration**: Perfect for users who want to follow routes using dedicated GPS apps
- **Professional Export**: Clean GPX XML format following GPX 1.1 specifications
- **Multi-language Support**: Added Dutch and English translations for export functionality

### Technical Details

- Added `wp_art_routes_ajax_export_gpx()` AJAX handler for secure file generation
- Implemented `wp_art_routes_generate_gpx()` function with proper coordinate validation
- Enhanced route path processing to handle both legacy and modern coordinate formats
- Added responsive CSS styling for the export button with hover effects
- Updated translation files with new GPX export strings

## [wenb-1.23.0] - 2025-08-24

### Added

- Shortcode `[related_artworks]` to display artworks related to the current post/artist, with featured image, title, excerpt, and link.
- New template and responsive CSS for related artworks section, styled to match the "artwork-artists" section.

### Fixed

- Meta query for related artworks now correctly matches post IDs in `_artwork_artist_ids` (fixes missing related artworks).

### Changed

- Improved visual consistency for related artworks section.

## [wenb-1.22.0] - 2025-08-24

### Added

- Show related artists, pages and/or posts column in the artworks admin overview page for easier management and overview.
- Enhanced artwork content display with excerpt truncation for improved readability in listings and popups.

### Fixed

- Handled NaN progress percentage in route progress calculation to prevent display errors.

### Changed

- Updated icon dimensions and layout for better responsiveness across devices.
- Made route tiles bigger on larger screens for improved usability.
- Moved route info REST project to the bin directory for better project organization.

## [wenb-1.21.0] - 2025-08-17

### Added

- Artworks in map data now include `wheelchair_accessible` and `stroller_accessible` fields, allowing accessibility icons and text to be shown in marker popups and details.

## [wenb-1.20.2] - 2025-08-17

### Added

- The "Number" column in the Artworks admin overview is now sortable. You can click the column header to sort artworks by their number, making it easier to organize and find artworks by their identification number.

### Technical Details

- Added a custom column for artwork number to the admin list table for the artwork post type.
- Made the column sortable using meta_value ordering for the_artwork_number field.

## [wenb-1.20.1] - 2025-08-08

### Fixed

- The "Go to Route" button and initial map fit now only focus on the route bounds and never include the user location. This prevents the map from zooming out to show both the route and the user's position, improving usability for all users.

## [wenb-1.20.0] - 2025-08-08

### Added

- Show "Read more" link in information point popups if the content is cut off due to length restrictions. This allows users to access the full content of the information point directly from the popup.

## [wenb-1.19.2] - 2025-08-08

### Fixed

- Map display option buttons (Show Artworks, Show Information Points, Show Route, Show My Location) now work as intended. Added missing toggle functions for map element visibility in JavaScript.

### Technical Details

- Implemented `toggleArtworkVisibility`, `toggleInfoPointVisibility`, `toggleRouteVisibility`, and `toggleUserLocationVisibility` in `assets/js/art-route-map.js` to support map display toggles.

## [1.19.1] - 2025-07-27

### Added

- Prepare plugin for generalization of plugin and distribution

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
