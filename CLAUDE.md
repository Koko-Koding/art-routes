# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Art Routes is a WordPress plugin for organizations managing art routes and events. It provides interactive map-based route management using Leaflet.js and OpenStreetMap. The plugin is designed for non-technical users on mobile devices, so prioritize simplicity and responsive design.

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

### Custom Post Types
- `art_route` - Routes with path coordinates and metadata
- `artwork` - Points of interest with GPS coordinates
- `information_point` - Info markers along routes

### Core PHP Files (includes/)
| File | Purpose |
|------|---------|
| `post-types.php` | Registers custom post types |
| `meta-boxes.php` | Admin meta boxes for route/artwork editing |
| `template-functions.php` | Route data retrieval (`wp_art_routes_get_route_data()`, etc.) |
| `scripts.php` | Enqueues Leaflet.js and plugin assets |
| `shortcodes.php` | `[art_route_map]`, `[art_routes_map]`, `[art_route_icons]`, `[related_artworks]` |
| `ajax-handlers.php` | AJAX for visited artworks, artist search, GPX export |
| `settings.php` | Plugin settings page |

### JavaScript Files (assets/js/)
| File | Lines | Purpose |
|------|-------|---------|
| `art-route-map.js` | ~1,000 | Frontend map display, location tracking, proximity detection |
| `route-editor-admin.js` | ~1,400 | Admin route path editor with draggable points |
| `artwork-location-picker.js` | ~300 | Admin artwork GPS coordinate picker |

### Template System
Templates in `templates/` can be overridden by themes by copying to `{theme}/wp-art-routes/`.

## Meta Field Naming

Routes: `_route_path`, `_route_length`, `_route_duration`, `_route_type`
Artworks: `_artwork_latitude`, `_artwork_longitude`, `_artwork_number`, `_artwork_location`, `_artwork_artist_ids`, `_wheelchair_accessible`, `_stroller_accessible`

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
4. Update translation files if strings changed
5. Run `./bin/build-release` to create distribution zip

## External Dependencies

- Leaflet.js 1.9.4 (loaded from cdn.jsdelivr.net)
- OpenStreetMap tiles
- jQuery (WordPress bundled)

No composer or npm dependencies for the main plugin. The `@bin/route-info-rest-client/` directory contains a separate Bun/TypeScript utility project for data import.
