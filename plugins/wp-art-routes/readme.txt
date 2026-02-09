=== Art Routes ===
Contributors: drikusroor
Tags: maps, routes, art, leaflet, openstreetmap
Requires at least: 5.6
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Interactive art route maps with OpenStreetMap integration for WordPress. Create custom routes with artworks and points of interest.

== Description ==

Art Routes is a WordPress plugin designed for organizations that organize art routes and events, allowing them to create and display interactive maps with custom routes and points of interest (artworks).

The plugin uses Leaflet.js in combination with OpenStreetMap to display maps, routes, and artwork locations. It allows users to track their progress on routes and receive notifications when they are near artworks.

= Features =

* **Editions system** - Organize content by events/time periods (e.g., "Art Festival 2024")
* **Edition Dashboard** - Bulk manage routes, locations, and info points with overview map
* Custom post types for Routes, Locations, and Info Points
* Interactive route editor with map interface
* Location picker with map integration
* Route types (walking, cycling, wheelchair-accessible, child-friendly)
* Route details (length, duration)
* **GPX Import/Export** - Import routes and export for GPS devices
* **CSV Import/Export** - Bulk import locations and info points
* **Custom icon uploads** - Upload your own SVG/PNG icons
* Location-aware functionality showing user position on map
* Route progress tracking
* Proximity detection for nearby locations
* Multiple routes on a single map
* **Customizable terminology** - Rename "Location" to "Artwork", "Creator" to "Artist", etc.
* Responsive design for mobile devices
* Translation-ready with complete Dutch (nl_NL) translation

= Shortcodes =

Display a specific route map:
`[art_route_map route="123" height="500px"]`

Display multiple routes on a single map:
`[art_routes_map height="600px"]`

= Requirements =

* WordPress 5.6 or higher
* PHP 7.4 or higher
* Browser with geolocation support for tracking features

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-art-routes` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Art Routes Settings screen to configure the plugin.
4. Create your first route by going to Routes -> Add New Route.

== Frequently Asked Questions ==

= How do I create a route? =

1. Navigate to "Routes" in the WordPress admin menu
2. Click "Add New Route"
3. Fill in the route details (title, description)
4. Use the Route Editor to draw the route path
5. Set route properties (type, length, duration)
6. Publish the route

= How do I add artworks to a route? =

1. Navigate to "Artworks" in the WordPress admin menu
2. Click "Add New Artwork"
3. Fill in the artwork details and use the Location Picker to place it on the map
4. Artworks are now global and will appear on all route maps

= Can I display multiple routes on one map? =

Yes! Use the `[art_routes_map]` shortcode to display all routes on a single interactive map with toggle controls.

= Is the plugin mobile-friendly? =

Yes, the plugin is fully responsive and optimized for mobile devices with touch-friendly controls.

== Screenshots ==

1. Route editor interface showing interactive map with drawing tools
2. Frontend map display with route, artworks, and user location
3. Artwork management interface with location picker
4. Multiple routes map with toggle controls
5. Mobile-responsive map interface

== Changelog ==

= 2.2.3 =
* Added complete Dutch translations for Edition and Information Point labels
* Reorganized admin menu: entity types grouped together, utility pages at bottom

= 2.2.2 =
* Fixed map marker icons not displaying on edition single pages

= 2.2.1 =
* Fixed all remaining WordPress Plugin Check errors (0 errors now)
* Updated tested WordPress version to 6.9
* Removed deprecated load_plugin_textdomain() call
* Fixed output escaping in templates and includes
* Replaced forbidden file functions with WP_Filesystem API

= 2.2.0 =
* Renamed plugin from "WP Art Routes" to "Art Routes" for WordPress.org compliance
* Updated tested WordPress version to 6.7

= 2.1.2 =
* Fixed all WordPress Plugin Check errors (escaping, sanitization, i18n)
* Renamed legacy icons to remove spaces from filenames

= 2.1.1 =
* WordPress.org compliance: Bundled Leaflet.js locally instead of CDN

= 2.1.0 =
* NEW: 25+ new SVG map marker icons (art, museum, music, cafe, park, numbered markers 1-20, etc.)
* Legacy icons moved to subfolder (backwards compatible)
* Improved icon display names in dropdowns
* Default info icon now uses cleaner info.svg

= 2.0.0 =
* Standalone release with standard semantic versioning (removed wenb- prefix)
* Ready for use by any organization running location-based cultural events
* No functional changes from 1.31.0

= 1.31.0 =
* Edition Settings now available in Edition Dashboard (dates, default icon, terminology)
* AJAX-based settings save without page reload

= 1.30.0 =
* NEW: Custom icon uploads - upload SVG/PNG/JPG/WebP icons via Settings
* SVG sanitizer for security

= 1.29.0 =
* Edition default icon setting - each edition can have its own default location icon

= 1.28.0 =
* Create editions during CSV/GPX import
* Dashboard link after successful import
* Publish/Draft toggle buttons in Edition Dashboard

= 1.27.0 =
* Default location icon setting for locations without icons
* Import duplicate detection (skips existing items)
* Auto-flush rewrite rules on version change

= 1.26.0 =
* NEW: Edition Dashboard for bulk content management
* Overview map, inline editing, bulk actions
* Improved Edition Map Block editor preview

= 1.25.0 =
* NEW: Editions system for grouping content by events/time periods
* Per-edition terminology overrides
* Edition Map Block and shortcode
* Import/Export admin page (CSV and GPX)
* Centralized terminology system

= 1.24.0 =
* NEW: GPX Export feature for routes
* Export routes with waypoints for GPS devices

= 1.23.0 =
* Related artworks shortcode

= 1.22.0 =
* Related artists column in artworks admin

= 1.21.0 =
* Wheelchair and stroller accessibility fields

= 1.19.0 =
* Build and distribution system
* Plugin prepared for standalone use

= 1.18.0 =
* Added translate script & Dockerfile for easy translation file generation
* Enhanced developer workflow for managing translations

= 1.17.1 =
* Added new icon image to assets/icons directory

= 1.17.0 =
* NEW: Route point insertion feature - add points between existing route points
* Enhanced route editor user experience with precise point management
* Improved workflow for fine-tuning route paths

= 1.16.0 =
* Changed duration calculation to manual action with Calculate button
* Users now have full control over duration estimation
* Enhanced Dutch translations for new features

= 1.15.0 =
* NEW: Map visibility toggle controls for artworks, info points, routes, and user location
* Modern responsive control interface with visual feedback
* Smart state management and accessibility improvements

= 1.14.0 =
* NEW: Artwork icon selection - custom SVG icons for artwork markers
* Consistent icon experience across artworks and information points
* Enhanced map rendering with custom artwork icons

See CHANGELOG.md for complete version history.

== Upgrade Notice ==

= 2.2.0 =
Plugin renamed from "WP Art Routes" to "Art Routes" for WordPress.org trademark compliance. No functional changes.

= 2.0.0 =
Standalone release with standard semantic versioning. No functional changes - safe to upgrade.

= 1.25.0 =
Major feature release: Editions system for organizing content by events. New Edition Dashboard and Import/Export tools.

= 1.24.0 =
New GPX export feature for routes. Export to GPS devices and mapping applications.

= 1.18.0 =
This version adds developer tools for translation management. No breaking changes for end users.