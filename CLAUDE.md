# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## CRITICAL WARNING

**NEVER delete, remove, or overwrite existing project files without explicit user permission.** This includes:
- Never run `rm -rf` or similar destructive commands on the project
- Never overwrite files with empty content
- Never delete the codebase to "start fresh"
- Always preserve existing functionality when making changes

If you need to make breaking changes, ask the user first.

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

# Verify Editions system integration
./bin/verify-editions-system

# Generate POT file for translations
wp i18n make-pot . languages/wp-art-routes.pot

# Merge POT with existing translations
msgmerge --update languages/wp-art-routes-nl_NL.po languages/wp-art-routes.pot

# Compile .mo file
wp i18n make-mo languages/wp-art-routes-nl_NL.po
```

## Architecture

### Admin Menu Structure

The plugin uses a centralized menu under **Art Routes** (translatable to "Kunstroutes" in Dutch):

```
Art Routes (top-level, dashicons-location-alt)
├── Editions          (list of editions)
├── Routes            (art_route CPT)
├── Locations         (artwork CPT)
├── Info Points       (information_point CPT)
├── Import/Export     (CSV/GPX import and export)
└── Settings          (General, Terminology tabs)
```

Note: The "Add New Edition" submenu is intentionally hidden - editions should be created deliberately.

### Terminology System

The plugin uses a centralized terminology system (`includes/terminology.php`) that allows all labels to be customized. **Never hardcode labels** like "Artwork", "Artist", "Route" - always use the helper functions:

```php
// Get a label (singular or plural) - with optional edition context
wp_art_routes_label('location', false);              // "Location" (or custom)
wp_art_routes_label('location', true);               // "Locations" (or custom)
wp_art_routes_label('creator', false);               // "Artist" (or custom)
wp_art_routes_label('route', true);                  // "Routes" (or custom)
wp_art_routes_label('location', false, $edition_id); // Edition-specific override

// Get URL slug
wp_art_routes_slug('route');         // "art-route" (or custom)
wp_art_routes_slug('location');      // "artwork" (or custom)

// Get global terminology settings
wp_art_routes_get_global_terminology();

// Get default (hardcoded) terminology
wp_art_routes_get_default_terminology();

// Get merged terminology for an edition (edition → global → defaults)
wp_art_routes_get_edition_terminology($edition_id);

// Detect edition context from current page
wp_art_routes_detect_edition_context();
```

**Terminology Types:**
- `route` - Main paths users follow (singular: Route, plural: Routes)
- `location` - Main content items like artworks, performances (singular: Location, plural: Locations)
- `info_point` - Info markers along routes (singular: Info Point, plural: Info Points)
- `creator` - People/entities associated with locations (singular: Artist, plural: Artists)

**Terminology Cascade:** Edition override → Global settings → Hardcoded defaults

### Custom Post Types

Post type machine names are **fixed** (for data compatibility), but labels are dynamic:
- `art_route` - Routes with path coordinates and metadata
- `artwork` - Locations/points of interest with GPS coordinates
- `information_point` - Info markers along routes
- `edition` - Container that groups routes/locations/info points for events/time periods

### Editions System

Editions are containers that group routes, locations, and info points for specific events/time periods (e.g., "Gluren bij de Buren 2024", "Kunstroute 2025"):

```php
// Get all published editions
wp_art_routes_get_editions();

// Get full edition data (title, dates, terminology, etc.)
wp_art_routes_get_edition_data($edition_id);

// Get edition assigned to a post
wp_art_routes_get_post_edition($post_id);

// Get edition-aware label (checks edition overrides first)
wp_art_routes_edition_label('location', false, $edition_id);

// Get merged terminology for edition
wp_art_routes_get_edition_terminology($edition_id);

// Get edition-filtered content
wp_art_routes_get_edition_routes($edition_id);
wp_art_routes_get_edition_artworks($edition_id);
wp_art_routes_get_edition_information_points($edition_id);
```

**Linking Content to Editions:**
- Routes, artworks, info points have `_edition_id` meta field
- Filter by edition in admin list tables (dropdown filter)
- Edition column shown in list tables
- Edition selector meta box on edit screens

**Per-Edition Terminology:**
- Editions can override labels (Route, Location, Info Point, Creator - singular/plural)
- Empty overrides fall back to global settings, then to hardcoded defaults
- Stored in `_edition_terminology` post meta as serialized array

**Edition Meta Fields:**
- `_edition_terminology` - Array of terminology overrides
- `_edition_start_date` - Optional event start date (Y-m-d)
- `_edition_end_date` - Optional event end date (Y-m-d)

### Import/Export System

Located at Editions → Import/Export (`includes/import-export.php`):

**CSV Import (Locations & Info Points):**
- Select target edition
- Upload CSV with columns: Type, Name, Description, Latitude, Longitude, Number, Icon, Creator
- Items created as drafts for review
- Download template CSV available

**GPX Import (Routes & Locations):**
- Select target edition
- Three import modes:
  - Route path only (tracks become Routes)
  - Route path + waypoints as Locations
  - Waypoints as Locations only
- Supports GPX 1.0 and 1.1 formats
- Parses `<trk>`, `<rte>`, and `<wpt>` elements

**CSV Export:**
- Export edition's locations and info points
- Includes ID and permalink columns

**GPX Export:**
- Export edition's routes as tracks
- Export locations and info points as waypoints

### Core PHP Files (includes/)

| File | Purpose |
|------|---------|
| `terminology.php` | **Centralized terminology system** - labels, slugs, cascade logic, helper functions |
| `editions.php` | Edition CPT registration, meta boxes, terminology overrides, helper functions |
| `blocks.php` | Gutenberg block registration (Edition Map block) |
| `import-export.php` | Import/Export admin page - CSV and GPX import/export |
| `post-types.php` | Registers art_route, artwork, information_point CPTs with edition linking |
| `meta-boxes.php` | Admin meta boxes for route/artwork/info point editing, Edition selector |
| `template-functions.php` | Route data retrieval, edition-filtered queries, template loading |
| `scripts.php` | Enqueues Leaflet.js and plugin assets with i18n |
| `shortcodes.php` | `[art_route_map]`, `[art_routes_map]`, `[edition_map]`, `[art_route_icons]`, `[related_artworks]` |
| `ajax-handlers.php` | AJAX for visited artworks, artist search, GPX export |
| `settings.php` | Tabbed settings page (General, Terminology) |
| `class-gpx-handler.php` | GPX import/export with device presets |
| `class-svg-sanitizer.php` | SVG security sanitizer for custom icon uploads |

### Gutenberg Blocks

**Edition Map Block** (`wp-art-routes/edition-map`)

A dynamic server-side rendered block for displaying edition maps:

```
Block name: wp-art-routes/edition-map
```

Attributes:
- `editionId` (number) - Edition to display (0 = auto-detect from page context)
- `height` (string) - Map height, e.g., "500px"
- `showRoutes` (boolean) - Show route paths
- `showLocations` (boolean) - Show location markers
- `showInfoPoints` (boolean) - Show info point markers
- `showLegend` (boolean) - Show legend/toggle controls

Auto-detection: On Edition single pages, the block automatically uses that edition's content.

**Block Assets:**
- Editor script: `assets/js/blocks/edition-map-block.js`
- Editor styles: `assets/css/blocks/edition-map-block-editor.css`

**Routes Map Block** (`wp-art-routes/routes-map`)

Legacy block for displaying multiple routes:

Attributes:
- `editionId` (number) - Edition to filter by (0 = all)
- `height` (string) - Map height
- `showRoutes`, `showArtworks`, `showInfoPoints`, `showLegend` (boolean)

### Shortcodes

**Edition Map Shortcode:**
```
[edition_map
    edition_id="123"       (optional, auto-detects on edition pages)
    routes="all|none|45,67"
    show_locations="true"
    show_info_points="true"
    show_legend="true"
    height="500px"
]
```

**Other Shortcodes:**
- `[art_route_map]` - Single route map
- `[art_routes_map]` - Multiple routes map (supports `edition_id` parameter)
- `[art_route_icons]` - Display route icons
- `[related_artworks]` - Show related artworks

### Template System

Templates in `templates/` can be overridden by themes by copying to `{theme}/wp-art-routes/`:

- `single-edition.php` - Edition single page (map + routes grid + locations grid + info points list)
- `shortcode-edition-map.php` - Edition map shortcode template

The edition single template automatically displays:
1. Edition title and featured image
2. Edition content/description
3. Interactive map with all edition content
4. Routes grid (if any)
5. Locations grid (if any)
6. Info points list (if any)

All section headings use edition-specific terminology.

### JavaScript Files (assets/js/)

| File | Lines | Purpose |
|------|-------|---------|
| `art-route-map.js` | ~1,000 | Frontend map display, location tracking, proximity detection |
| `route-editor-admin.js` | ~1,400 | Admin route path editor with draggable points |
| `artwork-location-picker.js` | ~300 | Admin artwork GPS coordinate picker |
| `blocks/edition-map-block.js` | ~150 | Edition Map Gutenberg block editor component |

## Settings Structure

Settings are stored in `wp_art_routes_terminology` option:

```php
[
    'route' => ['singular' => 'Route', 'plural' => 'Routes', 'slug' => 'art-route'],
    'location' => ['singular' => 'Location', 'plural' => 'Locations', 'slug' => 'artwork'],
    'info_point' => ['singular' => 'Info Point', 'plural' => 'Info Points', 'slug' => 'info-point'],
    'creator' => ['singular' => 'Artist', 'plural' => 'Artists'],
]
```

Settings page has two tabs:
- **General** - Default route ID, location tracking toggle
- **Terminology** - Global label customization (singular/plural for each type)

## Meta Field Naming

**Important:** Meta keys are **fixed** and should never be renamed (for backwards compatibility).

**Routes (`art_route`):**
- `_route_path` - JSON array of [lat, lng] coordinates
- `_route_length` - Route length
- `_route_duration` - Estimated duration
- `_route_type` - Route type
- `_edition_id` - Linked edition ID

**Locations (`artwork`):**
- `_artwork_latitude` - GPS latitude
- `_artwork_longitude` - GPS longitude
- `_artwork_number` - Display number (e.g., "A1", "1")
- `_artwork_location` - Location description
- `_artwork_artist_ids` - Linked artist/creator IDs
- `_artwork_icon` - Custom icon filename
- `_wheelchair_accessible` - Accessibility flag
- `_stroller_accessible` - Accessibility flag
- `_edition_id` - Linked edition ID

**Info Points (`information_point`):**
- `_artwork_latitude` - GPS latitude (shares prefix with artwork for consistency)
- `_artwork_longitude` - GPS longitude
- `_info_point_icon` - Custom icon filename
- `_edition_id` - Linked edition ID

**Editions (`edition`):**
- `_edition_terminology` - Serialized array of terminology overrides
- `_edition_start_date` - Event start date (Y-m-d)
- `_edition_end_date` - Event end date (Y-m-d)

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
2. Artworks/Locations
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
/* translators: %s: location label (e.g., "Location", "Artwork") */
sprintf(__('Add New %s', 'wp-art-routes'), wp_art_routes_label('location'))
```

### Adding a New Setting

1. Add default value in `wp_art_routes_get_default_terminology()`
2. Add form field in appropriate tab in `settings.php`
3. Add hidden field preservation in other tabs
4. Create helper function in `terminology.php` if needed

### Adding Edition-Aware Functionality

When building features that should respect edition context:

```php
// Detect current edition from context
$edition_id = wp_art_routes_detect_edition_context();

// Get edition-specific label
$label = wp_art_routes_label('location', false, $edition_id);

// Query content for specific edition
$locations = wp_art_routes_get_edition_artworks($edition_id);
```
