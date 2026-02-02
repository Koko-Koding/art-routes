# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Art Routes is a flexible WordPress plugin for organizations managing cultural location-based events (art routes, theater trails, music festivals, heritage walks, etc.). It provides interactive map-based route management using Leaflet.js and OpenStreetMap. The plugin is designed for non-technical users on mobile devices, so prioritize simplicity and responsive design.

**Key Design Principles:**
- Backwards compatible: existing installations must work unchanged
- Target audience: non-technical Dutch users organizing cultural events
- All labels and terminology are customizable via settings

## Common Commands

```bash
# Build release package (creates zip in build/)
./bin/build-release

# Compile translations (.po to .mo)
./bin/translate

# Validate GPX export files
./bin/validate-gpx

# Generate POT file for translations
wp i18n make-pot . languages/wp-art-routes.pot

# Merge POT with existing translations
msgmerge --update languages/wp-art-routes-nl_NL.po languages/wp-art-routes.pot

# Compile .mo file
wp i18n make-mo languages/wp-art-routes-nl_NL.po
```

## Architecture

### Terminology System

The plugin uses a centralized terminology system (`includes/terminology.php`) that allows all labels to be customized. **Never hardcode labels** like "Artwork", "Artist", "Route" - always use the helper functions:

```php
// Get a label (singular or plural)
wp_art_routes_label('point_of_interest', false);  // "Artwork" (or custom)
wp_art_routes_label('point_of_interest', true);   // "Artworks" (or custom)
wp_art_routes_label('creator', false);            // "Artist" (or custom)
wp_art_routes_label('route', true);               // "Routes" (or custom)

// Get URL slug
wp_art_routes_slug('route');                      // "art-route" (or custom)
wp_art_routes_slug('point_of_interest');          // "artwork" (or custom)

// Get specific terminology value
wp_art_routes_get_term('gpx', 'creator');         // GPX creator name
wp_art_routes_get_term('icons', 'prefix');        // Icon filename prefix

// Get post type labels array for register_post_type()
wp_art_routes_get_post_type_labels('route');
```

**Terminology Types:**
- `route` - Main paths users follow
- `point_of_interest` - Main content items (artworks, performances, etc.)
- `information_point` - Info markers along routes
- `creator` - People/entities associated with points (artists, performers, etc.)

### Custom Post Types

Post type machine names are **fixed** (for data compatibility), but labels are dynamic:
- `art_route` - Routes with path coordinates and metadata
- `artwork` - Points of interest with GPS coordinates
- `information_point` - Info markers along routes
- `edition` - Container that groups routes/locations/info points for events/time periods

### Editions System

Editions are containers that group routes, locations, and info points for specific events/time periods (e.g., "Gluren bij de Buren 2024"):

```php
// Get all editions
wp_art_routes_get_editions();

// Get edition for a post
wp_art_routes_get_post_edition($post_id);

// Get edition-aware label (checks overrides first)
wp_art_routes_edition_label('point_of_interest', false, $edition_id);

// Get merged terminology for edition
wp_art_routes_get_edition_terminology($edition_id);
```

**Linking Content to Editions:**
- Routes, artworks, info points have `_edition_id` meta field
- Filter by edition in admin list tables
- Shortcodes support `edition_id` parameter: `[art_routes_map edition_id="123"]`
- Auto-detected on edition single pages

**Per-Edition Terminology:**
- Editions can override labels (POI, Creator, Info Point singular/plural)
- Empty overrides fall back to global settings
- Stored in `_edition_terminology` post meta

### Core PHP Files (includes/)

| File | Purpose |
|------|---------|
| `terminology.php` | **Centralized terminology system** - labels, slugs, presets, helper functions |
| `editions.php` | Edition CPT, edition meta fields, admin filters, terminology overrides |
| `blocks.php` | Gutenberg block registration and render callbacks |
| `post-types.php` | Registers custom post types with dynamic labels |
| `meta-boxes.php` | Admin meta boxes for route/artwork editing |
| `template-functions.php` | Route data retrieval, map controls, GPX export UI |
| `scripts.php` | Enqueues Leaflet.js and plugin assets with i18n |
| `shortcodes.php` | `[art_route_map]`, `[art_routes_map]`, `[art_route_icons]`, `[related_artworks]` |
| `ajax-handlers.php` | AJAX for visited artworks, artist search, GPX import/export |
| `settings.php` | Tabbed settings page (General, Terminology, Icons, GPX) |
| `class-gpx-handler.php` | GPX import/export with device presets |
| `class-svg-sanitizer.php` | SVG security sanitizer for custom icon uploads |

### Gutenberg Blocks

The plugin provides Gutenberg blocks as visual alternatives to shortcodes:

**Art Routes Map Block**

Block name: `wp-art-routes/routes-map`

Attributes:
- `editionId` (number) - Edition to filter by (0 = all)
- `height` (string) - Map height, e.g., "500px"
- `showRoutes` (boolean) - Show route paths
- `showArtworks` (boolean) - Show location markers
- `showInfoPoints` (boolean) - Show info point markers
- `showLegend` (boolean) - Show legend/toggle controls

The block uses server-side rendering via `wp_art_routes_multiple_map_shortcode()`.

**Block Assets:**
- Editor script: `assets/js/blocks/routes-map-block.js`
- Editor styles: `assets/css/blocks/routes-map-block-editor.css`

### JavaScript Files (assets/js/)

| File | Lines | Purpose |
|------|-------|---------|
| `art-route-map.js` | ~1,000 | Frontend map display, location tracking, proximity detection |
| `route-editor-admin.js` | ~1,400 | Admin route path editor with draggable points |
| `artwork-location-picker.js` | ~300 | Admin artwork GPS coordinate picker |

### Template System

Templates in `templates/` can be overridden by themes by copying to `{theme}/wp-art-routes/`.

## Settings Structure

Settings are stored in `wp_art_routes_terminology` option:

```php
[
    'route' => ['singular' => 'Route', 'plural' => 'Routes', 'slug' => 'art-route'],
    'point_of_interest' => ['singular' => 'Artwork', 'plural' => 'Artworks', 'slug' => 'artwork'],
    'information_point' => ['singular' => 'Info Point', 'plural' => 'Info Points', 'slug' => 'info-point'],
    'creator' => ['singular' => 'Artist', 'plural' => 'Artists'],
    'gpx' => ['creator' => 'WP Art Routes Plugin', 'poi_type' => 'Artwork'],
    'icons' => ['prefix' => 'WB plattegrond-', 'default_info_icon' => 'WB plattegrond-Informatie.svg'],
]
```

**Available Presets:** Art Routes (default), Theater Trail, Music Festival, Heritage Walk, Custom

## Meta Field Naming

**Important:** Meta keys are **fixed** and should never be renamed (for backwards compatibility).

Routes: `_route_path`, `_route_length`, `_route_duration`, `_route_type`, `_edition_id`
Artworks: `_artwork_latitude`, `_artwork_longitude`, `_artwork_number`, `_artwork_location`, `_artwork_artist_ids`, `_artwork_icon`, `_wheelchair_accessible`, `_stroller_accessible`, `_edition_id`
Info Points: `_artwork_latitude`, `_artwork_longitude`, `_info_point_icon`, `_edition_id`
Editions: `_edition_terminology`

## Icon System

Icons are stored in `assets/icons/`. The system supports:
- Built-in SVG icons (filtered by configurable prefix)
- Custom uploaded icons (stored in `wp-content/uploads/wp-art-routes-icons/`)

```php
// Get available icons (respects prefix setting)
wp_art_routes_get_available_icons();

// Get icon URL (handles both built-in and custom icons)
wp_art_routes_get_icon_url($filename);

// Get display name for icon dropdown
wp_art_routes_get_icon_display_name($filename);

// Get default info point icon
wp_art_routes_get_default_info_icon();
```

## GPX Handler

The `WP_Art_Routes_GPX_Handler` class provides:

```php
// Export route to GPX
WP_Art_Routes_GPX_Handler::export($route_data, $preset);
// Presets: 'standard', 'garmin', 'komoot', 'osmand', 'google_maps'

// Import GPX file
WP_Art_Routes_GPX_Handler::import($file_path);

// Get available device presets
WP_Art_Routes_GPX_Handler::get_device_presets();
```

## Map Marker Stacking Order

Controlled via `markerDisplayOrder` object in `art-route-map.js`:
1. Route start/end points (highest)
2. Artworks
3. Information points
4. Directional arrows (lowest)

## Release Workflow

When making changes:
1. Update version in `wp-art-routes.php` (uses `wenb-X.Y.Z` format)
2. Add entry to `CHANGELOG.md`
3. Update `README.md` if user-facing
4. Update translation files if strings changed (run commands above)
5. Run `./bin/build-release` to create distribution zip

## External Dependencies

- Leaflet.js 1.9.4 (loaded from cdn.jsdelivr.net)
- OpenStreetMap tiles
- jQuery (WordPress bundled)

No composer or npm dependencies for the main plugin. The `@bin/route-info-rest-client/` directory contains a separate Bun/TypeScript utility project for data import.

## Common Patterns

### Adding a New Translatable String

Always use WordPress i18n functions with the `wp-art-routes` text domain:

```php
// Simple string
__('My string', 'wp-art-routes')

// String with placeholder - add translators comment
/* translators: %s: point of interest label (e.g., "Artwork", "Performance") */
sprintf(__('Add New %s', 'wp-art-routes'), wp_art_routes_label('point_of_interest'))
```

### Adding a New Setting

1. Add default value in `wp_art_routes_get_default_terminology()`
2. Add form field in appropriate tab in `settings.php`
3. Add hidden field preservation in other tabs
4. Create helper function in `terminology.php` if needed
