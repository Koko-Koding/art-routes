# WP Art Routes

Interactive art route maps with OpenStreetMap integration for WordPress.

## Description

WP Art Routes is a WordPress plugin designed for organizations that organize art routes and events, allowing them to create and display interactive maps with custom routes and points of interest (artworks).

The plugin uses Leaflet.js in combination with OpenStreetMap to display maps, routes, and artwork locations. It allows users to track their progress on routes and receive notifications when they are near artworks.

## Features

- Custom post types for Routes and Artworks
- Artist taxonomy for categorizing artworks
- Interactive route editor with map interface
- Artwork location picker with map integration
- Route types (walking, cycling, wheelchair-accessible, child-friendly)
- Route details (length, duration)
- Location-aware functionality showing user position on map
- Route progress tracking 
- Proximity detection for nearby artworks
- Multiple routes on a single map
- Responsive design for mobile devices
- Translation-ready with complete Dutch (nl_NL) translation

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wp-art-routes` directory, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->Art Routes Settings screen to configure the plugin.

## Usage

### Creating Routes

1. Navigate to "Routes" in the WordPress admin menu
2. Click "Add New Route"
3. Fill in the route details (title, description)
4. Use the Route Editor to draw the route path
5. Set route properties (type, length, duration)
6. Publish the route

### Creating Artworks

1. Navigate to "Artworks" in the WordPress admin menu
2. Click "Add New Artwork"
3. Fill in the artwork details (title, description)
4. Use the Location Picker to place the artwork on the map
5. Associate the artwork with a route
6. Publish the artwork

### Displaying Maps

Use the shortcode `[art_route_map]` to display a specific route map:

```
[art_route_map route="123" height="500px"]
```

Or use `[art_routes_map]` to display multiple routes on a single map:

```
[art_routes_map height="600px"]
```

## Shortcode Parameters

### [art_route_map]

- `route`: ID of the route to display (required)
- `height`: Height of the map (default: "400px")
- `zoom`: Initial zoom level (default: 13)
- `center_lat`: Center latitude (default: auto)
- `center_lng`: Center longitude (default: auto)

### [art_routes_map]

- `height`: Height of the map (default: "500px")
- `zoom`: Initial zoom level (default: 12)
- `center_lat`: Center latitude (default: auto)
- `center_lng`: Center longitude (default: auto)

## Translations

The plugin is fully translation-ready and includes the following translations:

- English (default)
- Dutch (nl_NL)

To add new translations:
1. Copy the `wp-art-routes.pot` file from the `languages` folder
2. Rename it to `wp-art-routes-{language_code}.po` (e.g., `wp-art-routes-fr_FR.po` for French)
3. Translate all strings using a POT/PO editor like Poedit
4. Generate the `.mo` file and place both files in the `languages` folder

## Requirements

- WordPress 5.6 or higher
- PHP 7.2 or higher
- Browser with geolocation support for tracking features

## License

This plugin is licensed under the GPL v2 or later.