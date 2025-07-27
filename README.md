# WP Art Routes

Interactive art route maps with OpenStreetMap integration for WordPress.

## Description

WP Art Routes is a WordPress plugin designed for organizations that organize art routes and events, allowing them to create and display interactive maps with custom routes and points of interest (artworks).

The plugin uses Leaflet.js in combination with OpenStreetMap to display maps, routes, and artwork locations. It allows users to track their progress on routes and receive notifications when they are near artworks.

## Features

- Custom post types for Routes and Artworks
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

📋 See the [CHANGELOG](CHANGELOG.md) for a detailed list of changes and updates.

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
5. Set route properties (type, length, duration - duration is automatically calculated based on route type and distance)
6. Publish the route

**Note:** The duration field is automatically calculated based on your route type and distance using realistic average speeds (walking: 4.5 km/h, cycling: 15 km/h, wheelchair-friendly: 3.5 km/h, child-friendly: 3.0 km/h). You can override this with a custom duration if needed.

### Creating Artworks

1. Navigate to "Artworks" in the WordPress admin menu
2. Click "Add New Artwork"
3. Fill in the artwork details (title, description)
4. **Optional:** Add a Number for artwork identification (e.g., "A1", "001")
5. **Optional:** Add a Location description (e.g., "Near the town square")
6. Use the Location Picker to place the artwork on the map
7. **Optional:** Associate the artwork with artists by linking to existing posts/pages
8. Set a featured image for the artwork
9. Publish the artwork

**Note:** The Number field, when provided, will be displayed on the map markers instead of sequential numbers, making it easier to identify specific artworks according to your numbering system.

### Artwork Display

Each artwork has its own dedicated page that displays:

- **Featured Image:** Prominently shown at the top of the page
- **Artwork Details:** Title, description, and metadata (number, location)
- **Associated Artists:** Clickable links to artist pages/posts
- **Interactive Map:** Shows the artwork's location with a custom marker
- **Responsive Design:** Optimized for both desktop and mobile viewing

The artwork template can be customized by themes by copying `templates/single-artwork.php` to `wp-art-routes/single-artwork.php` in your active theme directory.

### Displaying Maps

Use the shortcode `[art_route_map]` to display a specific route map:

```shortcode
[art_route_map route="123" height="500px"]
```

Or use `[art_routes_map]` to display multiple routes on a single map:

```shortcode
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

### Translation Automation

Use the `bin/translate` script to automatically compile all `.po` files to `.mo` files. The script will use local tools (`msgfmt` or Poedit) if available, or fallback to Docker.

#### Docker Usage

Build the Docker image:

```sh
docker build -t po-compiler .
```

Run the translation script:

```sh
./bin/translate
```

### Commands

Make sure you have wp-cli installed and available in your PATH. You can use the following command to generate the `.po` and `.mo` files:

```bash
# Generate the .pot file
wp i18n make-pot . languages/wp-art-routes.pot

# To merge the .pot file with existing translations
msgmerge --update languages/wp-art-routes-nl_NL.po languages/wp-art-routes.pot

# Generate the .po file for Dutch
wp i18n make-po languages/wp-art-routes.pot languages/wp-art-routes-nl_NL.po

# Generate the .mo file for Dutch
wp i18n make-mo languages/wp-art-routes-nl_NL.po
```

## Requirements

- WordPress 5.6 or higher
- PHP 7.4 or higher
- Browser with geolocation support for tracking features

## License

This plugin is licensed under the GPL v2 or later.

## Editor Description

### Route Editor Features

- **Map View:**
  - **Fit Route:** Click this to automatically zoom and pan the map to fit the entire drawn route and all added points.
  - **My Location:** Click this to attempt to center the map on your current browser location.
- **Search:** Enter a location name or address and click "Search" to pan the map to that area.

- **Route Point Management:**
  - **Draggable Points:** All route points can be dragged to new positions for precise route adjustment
  - **Edit Points:** Click the pencil (✎) button on any route point to edit metadata (start/end markers, direction arrows, notes)
  - **Insert Points:** Click the green plus (+) button to insert a new route point after the current one
    - New points are automatically positioned at the midpoint between current and next point
    - For the last point, new points are created with a small geographical offset
  - **Delete Points:** Click the red × button to remove unwanted route points (minimum 2 points required)

### Marker Stacking Order (zIndexOffset)

The display order of map markers (which markers appear on top) is now centrally managed in the plugin’s JavaScript. All marker types use the `markerDisplayOrder` object in `assets/js/art-route-map.js` to set their stacking order via Leaflet’s `zIndexOffset` property.

**Default order (top to bottom):**

- Route start/end points (highest)
- Artworks
- Information points
- Directional arrows (lowest)

To change the stacking order, simply edit the values in the `markerDisplayOrder` object at the top of `art-route-map.js`. All marker creation code references this object, making future adjustments easy and consistent.

## Version 1.13.2

Patch release: Minor CSS and JS improvements for map display and marker rendering. See CHANGELOG.md for details.
