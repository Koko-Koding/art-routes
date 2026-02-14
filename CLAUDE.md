# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## CRITICAL WARNING

**NEVER delete, remove, or overwrite existing project files without explicit user permission.** This includes:
- Never run `rm -rf` or similar destructive commands on the project
- Never overwrite files with empty content
- Never delete the codebase to "start fresh"
- Always preserve existing functionality when making changes

If you need to make breaking changes, ask the user first.

## Monorepo Structure

This is a monorepo containing two WordPress plugins:

```
art-routes/
├── plugins/
│   ├── art-routes/           # Free plugin (WordPress.org)
│   │   ├── art-routes.php, includes/, assets/, languages/,
│   │   ├── templates/, readme.txt, README.md, CHANGELOG.md,
│   │   └── DISTRIBUTION.md, Dockerfile, icon-*.png/webp
│   └── art-routes-pro/          # Pro add-on (premium)
│       ├── art-routes-pro.php, includes/, assets/, lib/
├── bin/                         # Build & dev scripts (shared)
├── docs/, gpx/, @bin/, publish-plugin/
├── .claude/skills/, .github/
├── build/                       # gitignored output
├── CLAUDE.md                    # This file
└── README.md                    # Monorepo overview
```

**Development setup:** The repo lives at `~/repos/art-routes/`. Symlinks in Local Sites point back here:
- `<Local Sites>/plugins/art-routes` → `~/repos/art-routes/plugins/art-routes`
- `<Local Sites>/plugins/art-routes-pro` → `~/repos/art-routes/plugins/art-routes-pro`

Run `./bin/setup-dev` to create/verify symlinks.

## Project Overview

**Art Routes (Free)** is a flexible WordPress plugin for organizations managing cultural location-based events (art routes, theater trails, music festivals, heritage walks, etc.). It provides interactive map-based route management using Leaflet.js and OpenStreetMap.

**Art Routes Pro** is a premium add-on that requires the free plugin. It adds QR codes, visitor analytics, PDF exports, and more.

**Primary Language:** PHP (WordPress plugin). Use PHP for all new code unless otherwise specified.

**Key Design Principles:**
- Backwards compatible: existing installations must work unchanged
- Target audience: non-technical Dutch users organizing cultural events
- All labels and terminology are customizable via settings

## Common Commands

```bash
# Build free plugin for distribution (creates zip in build/)
./bin/build-free

# Build pro plugin for distribution (creates zip in build/)
./bin/build-pro

# Create/verify Local Sites symlinks
./bin/setup-dev

# Run Plugin Check on free plugin release build
./bin/plugin-check

# Compile translations (.po to .mo) for all plugins
./bin/translate

# Validate GPX export files
./bin/validate-gpx

# Verify Editions system integration
./bin/verify-editions-system

# Generate POT file for translations (run from plugins/art-routes/)
cd plugins/art-routes && wp i18n make-pot . languages/art-routes.pot

# Merge POT with existing translations
msgmerge --update plugins/art-routes/languages/art-routes-nl_NL.po plugins/art-routes/languages/art-routes.pot

# Compile .mo file
wp i18n make-mo plugins/art-routes/languages/art-routes-nl_NL.po
```

## Free Plugin Architecture

### Admin Menu Structure

The plugin uses a centralized menu under **Art Routes** (translatable to "Kunstroutes" in Dutch):

```
Art Routes (top-level, dashicons-location-alt)
├── Editions          (list of editions)
├── Routes            (artro_route CPT)
├── Locations         (artro_artwork CPT)
├── Info Points       (artro_info_point CPT)
├── Import/Export     (CSV/GPX import and export)
└── Settings          (General, Terminology tabs)
```

Note: The "Add New Edition" submenu is intentionally hidden - editions should be created deliberately.

### Terminology System

The plugin uses a centralized terminology system (`plugins/art-routes/includes/terminology.php`) that allows all labels to be customized. **Never hardcode labels** like "Artwork", "Artist", "Route" - always use the helper functions:

```php
// Get a label (singular or plural) - with optional edition context
art_routes_label('location', false);              // "Location" (or custom)
art_routes_label('location', true);               // "Locations" (or custom)
art_routes_label('creator', false);               // "Artist" (or custom)
art_routes_label('route', true);                  // "Routes" (or custom)
art_routes_label('location', false, $edition_id); // Edition-specific override

// Get URL slug
art_routes_slug('route');         // "art-route" (or custom)
art_routes_slug('location');      // "artwork" (or custom)

// Get global terminology settings
art_routes_get_global_terminology();

// Get default (hardcoded) terminology
art_routes_get_default_terminology();

// Get merged terminology for an edition (edition → global → defaults)
art_routes_get_edition_terminology($edition_id);

// Detect edition context from current page
art_routes_detect_edition_context();
```

**Terminology Types:**
- `route` - Main paths users follow (singular: Route, plural: Routes)
- `location` - Main content items like artworks, performances (singular: Location, plural: Locations)
- `info_point` - Info markers along routes (singular: Info Point, plural: Info Points)
- `creator` - People/entities associated with locations (singular: Artist, plural: Artists)

**Terminology Cascade:** Edition override → Global settings → Hardcoded defaults

### Custom Post Types

Post type machine names use the `artro_` prefix (for WordPress.org compliance), labels are dynamic:
- `artro_route` - Routes with path coordinates and metadata
- `artro_artwork` - Locations/points of interest with GPS coordinates
- `artro_info_point` - Info markers along routes
- `artro_edition` - Container that groups routes/locations/info points for events/time periods

A one-time database migration (`art_routes_migrate_cpt_names()`) automatically converts old generic CPT names to the new `artro_` prefixed names on plugin update.

### Editions System

Editions are containers that group routes, locations, and info points for specific events/time periods (e.g., "Gluren bij de Buren 2024", "Kunstroute 2025"):

```php
// Get all published editions
art_routes_get_editions();

// Get full edition data (title, dates, terminology, etc.)
art_routes_get_edition_data($edition_id);

// Get edition assigned to a post
art_routes_get_post_edition($post_id);

// Get edition-aware label (checks edition overrides first)
art_routes_edition_label('location', false, $edition_id);

// Get merged terminology for edition
art_routes_get_edition_terminology($edition_id);

// Get edition-filtered content
art_routes_get_edition_routes($edition_id);
art_routes_get_edition_artworks($edition_id);
art_routes_get_edition_information_points($edition_id);
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

Located at Editions → Import/Export (`plugins/art-routes/includes/import-export.php`):

**CSV Import (Locations & Info Points):**
- Select target edition or create a new edition during import
- Upload CSV with columns: Type, Name, Description, Latitude, Longitude, Number, Icon, Creator
- Items created as drafts for review
- Download template CSV available
- After successful import, shows link to edition's dashboard

**GPX Import (Routes & Locations):**
- Select target edition or create a new edition during import
- Three import modes:
  - Route path only (tracks become Routes)
  - Route path + waypoints as Locations
  - Waypoints as Locations only
- Supports GPX 1.0 and 1.1 formats
- Parses `<trk>`, `<rte>`, and `<wpt>` elements
- **Duplicate detection:** Skips items that already exist in the edition (by name or coordinates within ~2 meters)
- After successful import, shows link to edition's dashboard

**Duplicate Detection (CSV & GPX Import):**
Both import methods include automatic duplicate detection:
- **Locations:** Skipped if coordinates are within ~2 meters of existing location OR if title matches
- **Routes:** Skipped if title matches an existing route in the same edition
- **Info Points:** Skipped if coordinates are within ~2 meters OR if title matches
- Import results show count of created and skipped items

Helper functions for duplicate detection:
```php
art_routes_find_duplicate_location($lat, $lon, $edition_id, $tolerance);
art_routes_find_duplicate_location_by_name($name, $edition_id);
art_routes_find_duplicate_route($name, $edition_id);
art_routes_find_duplicate_info_point($lat, $lon, $edition_id, $tolerance);
art_routes_find_duplicate_info_point_by_name($name, $edition_id);
```

**CSV Export:**
- Export edition's locations and info points
- Includes ID and permalink columns

**GPX Export:**
- Export edition's routes as tracks
- Export locations and info points as waypoints

### Edition Dashboard

Located at Editions → Dashboard (`plugins/art-routes/includes/edition-dashboard.php`):

**Features:**
- Overview map showing all edition content (routes as polylines, locations/info points as markers)
- Draft items shown at 50% opacity on map
- Collapsible sections for Routes, Locations, Info Points, Edition Settings
- Inline editing for title, number, coordinates
- Status toggle (click badge to publish/draft)
- Publish/Draft toggle buttons in each row for quick status changes
- Icon selector dropdown
- Bulk actions: publish, draft, delete selected
- Quick selection: Select All, Select None, Select Drafts

**Edition Settings Section:**
- Event dates (start/end)
- Default location icon with live preview
- Terminology overrides (Route, Location, Info Point, Creator - singular/plural)
- AJAX-based save without page reload

**JavaScript:** `plugins/art-routes/assets/js/edition-dashboard.js`
**CSS:** `plugins/art-routes/assets/css/edition-dashboard.css`

**AJAX Endpoints:**
| Action | Purpose |
|--------|---------|
| `art_routes_dashboard_get_items` | Fetch all routes/locations/info points/settings for edition |
| `art_routes_dashboard_update_item` | Update single field (title, status, number, lat, lng, icon) |
| `art_routes_dashboard_bulk_action` | Bulk publish/draft/delete |
| `art_routes_dashboard_save_settings` | Save edition settings (dates, icon, terminology) |

### Core PHP Files (plugins/art-routes/includes/)

| File | Purpose |
|------|---------|
| `terminology.php` | **Centralized terminology system** - labels, slugs, cascade logic, helper functions |
| `editions.php` | Edition CPT registration, meta boxes, terminology overrides, helper functions |
| `edition-dashboard.php` | Edition Dashboard admin page - bulk management UI with map |
| `blocks.php` | Gutenberg block registration (Edition Map block) |
| `import-export.php` | Import/Export admin page - CSV and GPX import/export |
| `post-types.php` | Registers artro_route, artro_artwork, artro_info_point CPTs with edition linking |
| `meta-boxes.php` | Admin meta boxes for route/artwork/info point editing, Edition selector |
| `template-functions.php` | Route data retrieval, edition-filtered queries, template loading |
| `scripts.php` | Enqueues Leaflet.js and plugin assets with i18n |
| `shortcodes.php` | `[art_route_map]`, `[art_routes_map]`, `[art_routes_edition_map]`, `[art_route_icons]`, `[art_routes_related_artworks]` |
| `ajax-handlers.php` | AJAX for visited artworks, artist search, GPX export |
| `settings.php` | Tabbed settings page (General, Terminology) |
| `class-gpx-handler.php` | GPX import/export with device presets |
| `class-svg-sanitizer.php` | SVG security sanitizer for custom icon uploads |

### Gutenberg Blocks

**Edition Map Block** (`art-routes/edition-map`)

A dynamic server-side rendered block for displaying edition maps:

```
Block name: art-routes/edition-map
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
- Editor script: `plugins/art-routes/assets/js/blocks/edition-map-block.js`
- Editor styles: `plugins/art-routes/assets/css/blocks/edition-map-block-editor.css`

**Routes Map Block** (`art-routes/routes-map`)

Legacy block for displaying multiple routes:

Attributes:
- `editionId` (number) - Edition to filter by (0 = all)
- `height` (string) - Map height
- `showRoutes`, `showArtworks`, `showInfoPoints`, `showLegend` (boolean)

### Shortcodes

**Edition Map Shortcode:**
```
[art_routes_edition_map
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
- `[art_routes_related_artworks]` - Show related artworks

### Template System

Templates in `plugins/art-routes/templates/` can be overridden by themes by copying to `{theme}/art-routes/`:

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

### JavaScript Files (plugins/art-routes/assets/js/)

| File | Lines | Purpose |
|------|-------|---------|
| `art-route-map.js` | ~1,000 | Frontend map display, location tracking, proximity detection |
| `route-editor-admin.js` | ~1,400 | Admin route path editor with draggable points |
| `artwork-location-picker.js` | ~300 | Admin artwork GPS coordinate picker |
| `blocks/edition-map-block.js` | ~150 | Edition Map Gutenberg block editor component |

## Pro Plugin Architecture

**Main file:** `plugins/art-routes-pro/art-routes-pro.php`
**Text domain:** `art-routes-pro`
**Requires:** Art Routes (free) to be installed and activated

### Pro Plugin Files (plugins/art-routes-pro/)

| File | Purpose |
|------|---------|
| `art-routes-pro.php` | Main plugin file, dependency check, bootstrap |
| `includes/class-license.php` | License key validation and management |
| `includes/class-updater.php` | Auto-update mechanism |
| `includes/class-pro-features.php` | Feature registry and initialization |
| `includes/features/class-feature-qr-codes.php` | QR code generation for locations |
| `lib/class-qr-generator.php` | QR code image generation library |
| `assets/css/admin.css` | Pro admin styles |
| `assets/css/qr-codes.css` | QR code display styles |

### Pro Plugin Constants
```php
ART_ROUTES_PRO_VERSION  // '1.0.0'
ART_ROUTES_PRO_FILE     // __FILE__
ART_ROUTES_PRO_DIR      // plugin_dir_path(__FILE__)
ART_ROUTES_PRO_URL      // plugin_dir_url(__FILE__)
```

## Settings Structure

Settings are stored in `art_routes_terminology` option:

```php
[
    'route' => ['singular' => 'Route', 'plural' => 'Routes', 'slug' => 'art-route'],
    'location' => ['singular' => 'Location', 'plural' => 'Locations', 'slug' => 'artwork'],
    'info_point' => ['singular' => 'Info Point', 'plural' => 'Info Points', 'slug' => 'info-point'],
    'creator' => ['singular' => 'Artist', 'plural' => 'Artists'],
]
```

Settings page has three tabs:
- **General** - Default route ID, location tracking toggle, default location icon
- **Terminology** - Global label customization (singular/plural for each type)
- **Custom Icons** - Upload custom SVG/PNG/JPG/WebP icons, manage uploaded icons

Additional settings stored separately:
- `art_routes_default_location_icon` - Default icon filename for locations without icons (used as fallback for imported locations)
- `art_routes_version` - Stored version for auto-flushing rewrite rules on updates

## Meta Field Naming

**Important:** Meta keys are **fixed** and should never be renamed (for backwards compatibility).

**Routes (`artro_route`):**
- `_route_path` - JSON array of [lat, lng] coordinates
- `_route_length` - Route length
- `_route_duration` - Estimated duration
- `_route_type` - Route type
- `_edition_id` - Linked edition ID

**Locations (`artro_artwork`):**
- `_artwork_latitude` - GPS latitude
- `_artwork_longitude` - GPS longitude
- `_artwork_number` - Display number (e.g., "A1", "1")
- `_artwork_location` - Location description
- `_artwork_artist_ids` - Linked artist/creator IDs
- `_artwork_icon` - Custom icon filename
- `_wheelchair_accessible` - Accessibility flag
- `_stroller_accessible` - Accessibility flag
- `_edition_id` - Linked edition ID

**Info Points (`artro_info_point`):**
- `_artwork_latitude` - GPS latitude (shares prefix with artwork for consistency)
- `_artwork_longitude` - GPS longitude
- `_info_point_icon` - Custom icon filename
- `_edition_id` - Linked edition ID

**Editions (`artro_edition`):**
- `_edition_terminology` - Serialized array of terminology overrides
- `_edition_start_date` - Event start date (Y-m-d)
- `_edition_end_date` - Event end date (Y-m-d)
- `_edition_default_location_icon` - Default icon filename for locations in this edition

## Icon System

The plugin supports two sources of icons:
- **Built-in icons:** SVG files in `plugins/art-routes/assets/icons/`
- **Custom uploaded icons:** SVG/PNG/JPG/WebP files in `wp-content/uploads/art-routes-icons/`

Custom icons can be uploaded via Settings → Custom Icons tab. SVG files are sanitized to prevent XSS attacks (`class-svg-sanitizer.php`).

**Always use `art_routes_get_icon_url()` for icon URLs** - it handles both built-in and custom icons correctly.

**Icon Fallback Logic:**
- **Locations:** Uses `_artwork_icon` meta → edition default (`_edition_default_location_icon`) → global default (`art_routes_default_location_icon`) → no icon (gray circle)
- **Info Points:** Uses `_info_point_icon` meta → falls back to `_info_point_icon_url` (legacy) → falls back to `WB plattegrond-Informatie.svg`

```php
// Get available icons (respects prefix setting)
art_routes_get_available_icons();

// Get icon URL (handles both built-in and custom icons)
art_routes_get_icon_url($filename);

// Get display name for icon dropdown
art_routes_get_icon_display_name($filename);

// Get default info point icon
art_routes_get_default_info_icon();
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

## Activation & Updates

**Activation Hook:** Registers all CPTs and flushes rewrite rules.

**Automatic Rewrite Flush:** The plugin automatically flushes rewrite rules when the version changes (stored in `art_routes_version` option). This ensures permalinks work correctly after plugin updates without requiring manual flush via Settings → Permalinks.

**CPT Name Migration:** On version upgrade, `art_routes_migrate_cpt_names()` converts old generic CPT names (`art_route`, `artwork`, `information_point`, `edition`) to the new `artro_` prefixed names in the database. Gated by `art_routes_cpt_migration_done` option flag (runs once).

## Release Workflow

### Free Plugin (Art Routes)

1. Update version in `plugins/art-routes/art-routes.php` (2 places: header + constant)
2. Add entry to `plugins/art-routes/CHANGELOG.md`
3. Update `plugins/art-routes/readme.txt`:
   - Update `Stable tag:` to new version
   - Add changelog entry (condensed version)
   - Update `Upgrade Notice` section if significant
4. Update `plugins/art-routes/README.md` if user-facing features changed
5. Update translation files if strings changed
6. Run `./bin/build-free` to create distribution zip
7. Tag: `vX.Y.Z`

### Pro Plugin (Art Routes Pro)

1. Update version in `plugins/art-routes-pro/art-routes-pro.php` (2 places: header + constant)
2. Run `./bin/build-pro` to create distribution zip
3. Tag: `pro-vX.Y.Z`

### Pre-Release Compliance Checklist (Free Plugin)

**IMPORTANT:** Always run these checks BEFORE tagging a release to avoid hotfix releases:

1. **CDN Compliance:** All JS/CSS must be bundled locally (no external CDNs except Google Fonts)
   - Leaflet.js is bundled in `plugins/art-routes/assets/lib/leaflet/`
   - Verify no new CDN links were added: `grep -r "cdn\." --include="*.php" --include="*.js" plugins/art-routes/`

2. **WordPress Plugin Check:** Run `./bin/plugin-check`
   - Fix all errors before release (warnings can be reviewed case-by-case)

3. **WordPress.org Requirements:**
   - Plugin name must NOT contain "WP" (trademark restriction) - use "Art Routes"
   - Maximum 5 tags in readme.txt
   - "Tested up to" should be current WordPress version
   - All user input must be sanitized, all output must be escaped

### WordPress.org Submission

For WordPress.org plugin directory submission:
- Submission URL: https://wordpress.org/plugins/developers/add/
- Plugin slug will be `art-routes` (not `art-routes`)
- After approval, use SVN to publish (instructions in memory/wordpress-org-submission.md)

## External Dependencies

- Leaflet.js 1.9.4 (bundled locally in plugins/art-routes/assets/lib/leaflet/)
- OpenStreetMap tiles
- jQuery (WordPress bundled)

No composer or npm dependencies for the main plugin. The `@bin/route-info-rest-client/` directory contains a separate Bun/TypeScript utility project for data import.

## Common Patterns

### Adding a New Translatable String

Always use WordPress i18n functions with the `art-routes` text domain:

```php
// Simple string
__('My string', 'art-routes')

// String with placeholder - add translators comment
/* translators: %s: location label (e.g., "Location", "Artwork") */
sprintf(__('Add New %s', 'art-routes'), art_routes_label('location'))
```

### Adding a New Setting

1. Add default value in `art_routes_get_default_terminology()`
2. Add form field in appropriate tab in `plugins/art-routes/includes/settings.php`
3. Add hidden field preservation in other tabs
4. Create helper function in `plugins/art-routes/includes/terminology.php` if needed

### Adding Edition-Aware Functionality

When building features that should respect edition context:

```php
// Detect current edition from context
$edition_id = art_routes_detect_edition_context();

// Get edition-specific label
$label = art_routes_label('location', false, $edition_id);

// Query content for specific edition
$locations = art_routes_get_edition_artworks($edition_id);
```

### WordPress Enqueue Guidelines

- **NEVER** use inline `<script>` or `<style>` tags in PHP files
- Use `wp_enqueue_script()` / `wp_enqueue_style()` for all JS/CSS
- Use `wp_localize_script()` for global config and i18n strings
- Use `wp_add_inline_script()` for small per-instance data (map coordinates, etc.)
- Use `wp_add_inline_style()` only for truly dynamic CSS (e.g., user-configured colors)
- Admin scripts: enqueue in `admin_enqueue_scripts` hook, check `$hook` parameter
- Frontend scripts: register early, enqueue from templates when needed
