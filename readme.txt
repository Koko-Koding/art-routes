=== WP Art Routes ===
Contributors: drikusroor
Tags: maps, routes, art, leaflet, openstreetmap, interactive, geolocation, tourism
Requires at least: 5.6
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.18.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Interactive art route maps with OpenStreetMap integration for WordPress. Create custom routes with artworks and points of interest.

== Description ==

WP Art Routes is a WordPress plugin designed for organizations that organize art routes and events, allowing them to create and display interactive maps with custom routes and points of interest (artworks).

The plugin uses Leaflet.js in combination with OpenStreetMap to display maps, routes, and artwork locations. It allows users to track their progress on routes and receive notifications when they are near artworks.

= Features =

* Custom post types for Routes and Artworks
* Interactive route editor with map interface
* Artwork location picker with map integration
* Route types (walking, cycling, wheelchair-accessible, child-friendly)
* Route details (length, duration)
* Location-aware functionality showing user position on map
* Route progress tracking
* Proximity detection for nearby artworks
* Multiple routes on a single map
* Responsive design for mobile devices
* Translation-ready with complete Dutch (nl_NL) translation

= Shortcodes =

Display a specific route map:
`[art_route_map route="123" height="500px"]`

Display multiple routes on a single map:
`[art_routes_map height="600px" ids="1,2,3" exclude_ids="2"]`

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

Yes! Use the `[art_routes_map]` shortcode to display all routes on a single interactive map with toggle controls. You can also use the `ids` and `exclude_ids` attributes to include or exclude specific routes:

`[art_routes_map height="600px" ids="1,2,3" exclude_ids="2"]`

= Is the plugin mobile-friendly? =

Yes, the plugin is fully responsive and optimized for mobile devices with touch-friendly controls.

== Screenshots ==

1. Route editor interface showing interactive map with drawing tools
2. Frontend map display with route, artworks, and user location
3. Artwork management interface with location picker
4. Multiple routes map with toggle controls
5. Mobile-responsive map interface

== Changelog ==

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

= 1.18.0 =
This version adds developer tools for translation management. No breaking changes for end users.

= 1.17.0 =
Major enhancement to route editing with new point insertion feature. Existing routes remain fully compatible.

= 1.15.0 =
New map controls provide better user experience. All existing functionality preserved.