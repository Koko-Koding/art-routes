# WP Art Routes

A WordPress plugin for creating interactive art routes with OpenStreetMap integration.

## Description

WP Art Routes allows you to create and manage art routes and artworks on your WordPress site. It provides an interactive map interface for users to explore art routes and discover artworks along the way.

### Features

- Custom post types for routes and artworks
- Interactive maps using OpenStreetMap/Leaflet.js (version 1.9.4)
- Route editor for creating custom paths
- Artwork location picker with map interface
- Artist taxonomy for categorizing artworks
- Route types (walking, cycling, wheelchair-accessible, children routes)
- Route details (length in km, duration in minutes)
- Location-aware functionality showing user position on map
- Route progress tracking with visualization of completed segments
- Proximity detection for nearby artworks with toast notifications
- Shortcode for embedding maps on any page (`[art_route_map]`)
- Custom page template for full-page map display
- Mobile-friendly responsive design
- Plugin settings page for global configuration

## Installation

1. Upload the `wp-art-routes` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings at Routes > Settings
4. Start creating art routes and artworks through the WordPress admin panel

## Usage

### Creating an Art Route

1. Go to **Routes > Add New**
2. Enter a title and description for your route
3. Fill in the route details (length, duration, type)
4. Use the "Use Map to Create Route" button to draw your route on the map
5. Configure route options (show completed route path, show artwork notifications)
6. Publish your route

### Adding Artworks

1. Go to **Artworks > Add New**
2. Enter a title and description for the artwork
3. Use the location picker to set the artwork's location on the map
4. Select the route this artwork belongs to
5. Add an artist (or create a new one)
6. Set a featured image for the artwork
7. Publish the artwork

### Displaying Art Routes

#### Using the Shortcode

You can display an art route map on any page or post using the shortcode:

```
[art_route_map route_id="123" height="500px" show_title="true" show_description="true"]
```

Parameters:
- `route_id`: The ID of the route to display (required)
- `height`: The height of the map (default: 600px)
- `show_title`: Whether to show the route title (default: true)
- `show_description`: Whether to show the route description (default: true)

#### Using the Page Template

1. Create a new page
2. Set the template to "Art Route Map Template"
3. Publish the page
4. To display a specific route, add `?route_id=123` to the page URL

### Plugin Settings

1. Navigate to **Routes > Settings** in the WordPress admin
2. Set a default route to use when no specific route is selected
3. Enable or disable location tracking for users
4. Save your settings

## Customization

### Template Overrides

You can override the plugin templates by creating a `wp-art-routes` directory in your theme and copying the template files from the plugin's `templates` directory.

### CSS Customization

The plugin includes CSS for styling the maps and UI elements. You can override these styles in your theme's stylesheet.

## Transitioning from Theme to Plugin

If you previously had art routes functionality in your theme, follow these steps:

1. Activate the WP Art Routes plugin
2. Existing routes and artworks will continue to work
3. Remove the old art routes code from your theme
4. Update any shortcodes to use the new format if needed

## Credits

- Maps provided by [OpenStreetMap](https://www.openstreetmap.org/) 
- Map library: [Leaflet](https://leafletjs.com/) version 1.9.4

## License

This plugin is licensed under the GPL v2 or later.