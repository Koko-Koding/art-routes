# Editions System Design

**Date:** 2026-02-02
**Status:** Draft
**Author:** Brainstorming session with Claude

## Overview

Add an "Editions" layer to WP Art Routes that groups Routes, Locations, and Info Points together for cultural events (e.g., "Gluren bij de Buren 2026", "Kunstroute 2025"). Each Edition can override terminology labels and serves as the primary organizational unit for content.

## Goals

- Group related content under event/time-based containers
- Allow per-Edition terminology customization
- Provide flexible map display via Gutenberg block and shortcode
- Enable bulk import via CSV and GPX
- Maintain backwards compatibility with existing installations

## Data Model

### New Custom Post Type: Edition

```
Machine name: edition
Slug: /edition/ (customizable via settings)
Supports: title, editor, excerpt, thumbnail
Menu position: Top-level "Editions"
Dashicon: calendar-alt
```

**Edition Meta Fields:**

| Meta Key | Type | Description |
|----------|------|-------------|
| `_edition_terminology` | array | Serialized terminology overrides |
| `_edition_start_date` | string | Optional event start date (Y-m-d) |
| `_edition_end_date` | string | Optional event end date (Y-m-d) |

### Content Linking

Routes, Locations (artworks), and Info Points each receive a new meta field:

| Meta Key | Type | Description |
|----------|------|-------------|
| `_edition_id` | int | References Edition post ID (0 = no edition) |

**Relationship:** One-to-many. Each content item belongs to zero or one Edition. If content needs to appear in multiple editions, it should be duplicated (details often change year to year).

### Terminology Structure

**Per-Edition overrides (`_edition_terminology`):**
```php
[
    'route' => ['singular' => '', 'plural' => ''],
    'location' => ['singular' => '', 'plural' => ''],
    'info_point' => ['singular' => '', 'plural' => ''],
    'creator' => ['singular' => '', 'plural' => ''],
]
// Empty strings = fall back to global/defaults
```

**Global settings (`wp_art_routes_terminology` option):**
```php
[
    'route' => ['singular' => 'Route', 'plural' => 'Routes', 'slug' => 'route'],
    'location' => ['singular' => 'Location', 'plural' => 'Locations', 'slug' => 'location'],
    'info_point' => ['singular' => 'Info Point', 'plural' => 'Info Points', 'slug' => 'info-point'],
    'creator' => ['singular' => 'Artist', 'plural' => 'Artists'],
]
```

**Cascade Logic:**
1. Edition override (if set and non-empty)
2. Global setting (if set and non-empty)
3. Hardcoded default

**Hardcoded Defaults:**
- Route / Routes
- Location / Locations
- Info Point / Info Points
- Artist / Artists

## Admin Interface

### Menu Structure

```
Editions (top-level, dashicon: calendar-alt)
├── Editions
├── Routes
├── Locations
├── Info Points
├── Import/Export
└── Settings
```

### Edition Edit Screen

- Standard WordPress editor: title, content, featured image, excerpt
- **Terminology Override** meta box:
  - Route: singular / plural inputs
  - Location: singular / plural inputs
  - Info Point: singular / plural inputs
  - Creator: singular / plural inputs
  - Empty fields = use default (shown as placeholder)
- **Date Range** meta box (optional):
  - Start date picker
  - End date picker

### List Table Enhancements

**Routes, Locations, Info Points list tables:**
- New "Edition" column showing linked Edition title (or "— None —")
- Dropdown filter: "All Editions" / [specific editions] / "No Edition"
- Bulk action: "Move to Edition..." with Edition selector

**Edit screens for Routes, Locations, Info Points:**
- New **Edition** meta box (side position, high priority)
- Dropdown: "— None —" + all published Editions

### Settings Page

**Tabs:**
1. **General** - Default route ID, location tracking toggle
2. **Terminology** - Global default labels (singular/plural for each type)
3. **Icons** - Icon filename prefix, default info point icon, custom icon uploads
4. **GPX** - Creator name for exports, POI type label

## Map Display

### Gutenberg Block: `wp-art-routes/edition-map`

**Block type:** Dynamic (server-side rendered)

**Attributes:**

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `editionId` | number | 0 | Edition ID (0 = auto-detect) |
| `routeIds` | array | [] | Specific route IDs (empty = all) |
| `showRoutes` | boolean | true | Display route paths |
| `showLocations` | boolean | true | Display location markers |
| `showInfoPoints` | boolean | true | Display info point markers |
| `showLegend` | boolean | true | Display legend/controls |
| `height` | string | "500px" | Map container height |

**Editor UI:**
- Edition dropdown with "Auto-detect" option
- Multi-select checkboxes for routes (populated from selected Edition)
- Toggle switches for locations, info points, legend
- Height input field

**Auto-detect Logic:**
1. On Edition single page → use that Edition's ID
2. `editionId` explicitly set → use that value
3. Neither → show "Please select an Edition" placeholder

**Rendering:**
- Server-side rendered via `render_callback`
- Editor preview uses `ServerSideRender` component for live data
- Data changes (new locations, etc.) automatically reflected without re-editing

### Shortcode: `[edition_map]`

```
[edition_map
    edition_id="123"
    routes="all|none|45,67"
    show_locations="true"
    show_info_points="true"
    show_legend="true"
    height="500px"
]
```

Same logic as block, for classic editor users.

## Import/Export

### Admin Page Location

Editions → Import/Export

### Import Tab

**Section 1: CSV Import (Locations & Info Points)**

- Edition dropdown (required)
- File upload field
- "Download Template CSV" link
- Preview table after upload
- Confirm button

**CSV Template Columns:**
```csv
Type,Name,Description,Latitude,Longitude,Number,Icon,Creator
location,Kunstwerk Jan,Prachtig schilderij,52.0907,5.1214,A1,icon-art.svg,Jan de Kunstenaar
info_point,Parkeren,Gratis parkeren hier,52.0910,5.1220,,,
```

| Column | Required | Description |
|--------|----------|-------------|
| Type | Yes | "location" or "info_point" |
| Name | Yes | Post title |
| Description | No | Post content/body |
| Latitude | Yes | GPS latitude |
| Longitude | Yes | GPS longitude |
| Number | No | Display number (locations only) |
| Icon | No | Icon filename |
| Creator | No | Creator name (creates link if found, or note) |

**Section 2: CSV Import (Route Points)** — *Phase 2*

- Edition dropdown
- Route name & description fields
- File upload (Lat, Lng, Notes columns)
- Row order = point sequence
- Preview path on mini-map
- Confirm to create Route post

**Section 3: GPX Import**

- Edition dropdown (required)
- File upload
- Import mode:
  - "Route path only" - Track becomes Route post
  - "Route path + waypoints as locations" - Track + waypoints
  - "Waypoints as locations only" - Ignore track data
- Preview showing what will be created
- Confirm button

### Export Tab

- Edition dropdown
- Checkboxes: Routes, Locations, Info Points
- Format selection:
  - GPX (for routes with waypoints)
  - CSV (for locations/info points)
- Download button

## Edition Single Page Template

**Template file:** `templates/single-edition.php`
**Override path:** `{theme}/wp-art-routes/single-edition.php`

### Default Layout

```
┌─────────────────────────────────────────┐
│ Edition Title (h1)                      │
│ Featured Image (if set)                 │
│ Edition Description (the_content)       │
├─────────────────────────────────────────┤
│ ┌─────────────────────────────────────┐ │
│ │                                     │ │
│ │         Interactive Map             │ │
│ │   (all routes, locations, info)     │ │
│ │                                     │ │
│ └─────────────────────────────────────┘ │
├─────────────────────────────────────────┤
│ Routes (h2) — dynamic label             │
│ ┌───────┐ ┌───────┐ ┌───────┐          │
│ │Route 1│ │Route 2│ │Route 3│ ...      │
│ └───────┘ └───────┘ └───────┘          │
├─────────────────────────────────────────┤
│ Locations (h2) — dynamic label          │
│ Grid of location cards with thumbnails  │
├─────────────────────────────────────────┤
│ Info Points (h2) — dynamic label        │
│ List of info point links                │
└─────────────────────────────────────────┘
```

### Behavior

- All section headings use Edition-aware terminology
- Sections with no content are hidden
- Map auto-detects Edition context
- Responsive: grid stacks on mobile

## Terminology Helper Functions

**File:** `includes/terminology.php`

### Primary Functions

```php
/**
 * Get a label with Edition cascade.
 *
 * @param string   $type       One of: 'route', 'location', 'info_point', 'creator'
 * @param bool     $plural     Return plural form
 * @param int|null $edition_id Edition ID for override lookup
 * @return string
 */
function wp_art_routes_label($type, $plural = false, $edition_id = null)

/**
 * Get URL slug for a content type.
 *
 * @param string $type One of: 'route', 'location', 'info_point'
 * @return string
 */
function wp_art_routes_slug($type)

/**
 * Get merged terminology array for an Edition.
 *
 * @param int $edition_id
 * @return array Full terminology with Edition overrides applied
 */
function wp_art_routes_get_edition_terminology($edition_id)

/**
 * Get global default terminology.
 *
 * @return array
 */
function wp_art_routes_get_default_terminology()

/**
 * Get post type labels array for register_post_type().
 *
 * @param string $type One of: 'route', 'location', 'info_point', 'edition'
 * @return array Labels array
 */
function wp_art_routes_get_post_type_labels($type)
```

### Context Detection

Templates and blocks can auto-detect Edition context from:
1. Explicit parameter passed to function
2. Current post's `_edition_id` meta (when on a Route/Location/Info Point page)
3. Current queried object (when on Edition single page)

## File Structure

New and modified files:

```
includes/
├── terminology.php       # NEW - Terminology system
├── editions.php          # NEW - Edition CPT, meta boxes, admin filters
├── blocks.php            # NEW - Gutenberg block registration
├── import-export.php     # NEW - Import/export admin page
├── post-types.php        # MODIFY - Add edition_id meta, adjust menu
├── meta-boxes.php        # MODIFY - Add Edition selector
├── settings.php          # MODIFY - Add terminology tab
├── template-functions.php # MODIFY - Edition-aware data functions
└── shortcodes.php        # MODIFY - Add [edition_map] shortcode

templates/
├── single-edition.php    # NEW - Edition single page template
└── shortcode-edition-map.php # NEW - Edition map template

assets/
├── js/
│   └── blocks/
│       └── edition-map-block.js  # NEW - Block editor component
└── css/
    └── blocks/
        └── edition-map-block-editor.css # NEW - Block editor styles
```

## Migration & Backwards Compatibility

- Existing Routes, Locations, Info Points remain functional (edition_id defaults to 0/none)
- Existing shortcodes (`[art_route_map]`, `[art_routes_map]`) continue to work
- No database migration required; new meta fields are simply empty until set
- Old templates continue to work; Edition features are additive

## Out of Scope (Future Considerations)

- CSV import for route points (Phase 2)
- Many-to-many Edition relationships
- Dedicated Creator custom post type
- Edition archiving/unpublishing workflow
- Edition duplication/cloning feature

## Implementation Order

Suggested implementation sequence:

1. **Terminology system** (`includes/terminology.php`) - Foundation for everything
2. **Edition CPT** (`includes/editions.php`) - Basic post type with meta
3. **Edition linking** - Add `_edition_id` to existing post types, admin filters
4. **Settings page updates** - Terminology tab
5. **Edition template** - Single page display
6. **Map block & shortcode** - Display functionality
7. **Import/Export page** - CSV and GPX import

Each step should be a working increment that can be tested independently.
