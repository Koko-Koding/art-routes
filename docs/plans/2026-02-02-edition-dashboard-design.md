# Edition Dashboard Design

## Overview

A comprehensive admin dashboard for managing all content within an edition from a single page. Solves the pain point of bulk-managing imported items (e.g., publishing 86 draft locations) and provides ongoing edition maintenance.

## Use Cases

- **Post-import review**: Quickly review and publish/discard items after CSV/GPX import
- **Ongoing maintenance**: Update GPS coordinates, reorder items, fix data
- **Pre-event preparation**: Verify everything is published and coordinates are correct

## Page Structure

```
┌─────────────────────────────────────────────────┐
│ Edition Dashboard                               │
│ [Dropdown: Select Edition ▼]  [View Frontend]   │
├─────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────┐ │
│ │ Overview Map (static, all items plotted)    │ │
│ │ - Routes: blue polylines                    │ │
│ │ - Locations: green numbered markers         │ │
│ │ - Info Points: orange info markers          │ │
│ │ - Draft items: 50% opacity                  │ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│ ▼ Routes (3)         (2 published, 1 draft)     │
│   [table with bulk actions]                     │
├─────────────────────────────────────────────────┤
│ ▶ Locations (86)     (12 published, 74 drafts)  │
│   (collapsed)                                   │
├─────────────────────────────────────────────────┤
│ ▶ Info Points (12)   (12 published, 0 drafts)   │
│   (collapsed)                                   │
└─────────────────────────────────────────────────┘
```

**URL**: `/wp-admin/admin.php?page=wp-art-routes-edition-dashboard&edition_id=123`

## Table Design

Each section (Routes, Locations, Info Points) contains a table:

```
▼ Locations (86)                           [Bulk Actions ▼] [Apply]
┌──┬────┬─────────────┬────────┬───────┬───────┬──────┬─────────┐
│☑ │ #  │ Title       │ Status │ Lat   │ Lng   │ Icon │ Actions │
├──┼────┼─────────────┼────────┼───────┼───────┼──────┼─────────┤
│☐ │ A1 │ Sculpture X │ ●Draft │ 52.09 │ 5.12  │ 🎨   │ ✏️ 🗑️   │
│☑ │ A2 │ Painting Y  │ ●Draft │ 52.10 │ 5.13  │ 🎨   │ ✏️ 🗑️   │
│☑ │ A3 │ Mural Z     │ ●Pub   │ 52.11 │ 5.14  │ 🎨   │ ✏️ 🗑️   │
└──┴────┴─────────────┴────────┴───────┴───────┴──────┴─────────┘
[Select All] [Select None] [Select Drafts]    Showing 86 items
```

### Editable Fields

| Field | Edit Method |
|-------|-------------|
| Title | Click to edit inline |
| Number | Click to edit inline |
| Status | Toggle button (draft ↔ publish) |
| Latitude | Click to edit inline |
| Longitude | Click to edit inline |
| Icon | Dropdown on click |

### Bulk Actions

- Publish selected
- Set to draft
- Delete selected

### Quick Selection Buttons

- Select All
- Select None
- Select Drafts (most common post-import action)

### Actions Column

- ✏️ Edit: Opens full WordPress edit screen
- 🗑️ Delete: With confirmation dialog

## Map Component

- Uses Leaflet.js (already loaded by plugin)
- Height: ~300px
- Auto-fits bounds to show all content
- View-only (no click interactions in v1)
- Legend showing marker types

### Marker Styles

| Type | Style |
|------|-------|
| Routes | Blue polylines |
| Locations | Green numbered markers |
| Info Points | Orange info icon markers |
| Draft items | 50% opacity |

## Technical Architecture

### New Files

```
includes/
  edition-dashboard.php    # Admin page, HTML rendering, AJAX handlers

assets/js/
  edition-dashboard.js     # Tables, inline editing, bulk actions, map

assets/css/
  edition-dashboard.css    # Styling
```

### AJAX Endpoints

| Action | Purpose |
|--------|---------|
| `wp_art_routes_dashboard_get_items` | Fetch all items for edition |
| `wp_art_routes_dashboard_update_item` | Update single field |
| `wp_art_routes_dashboard_bulk_action` | Bulk publish/draft/delete |

### Data Flow

1. Page loads with edition dropdown (server-rendered)
2. On edition select → JS fetches items via AJAX
3. JS renders tables and initializes map
4. Inline edits → immediate AJAX save → visual feedback
5. Bulk actions → AJAX → refresh affected rows

### Security

- All AJAX handlers verify `manage_options` capability
- Nonce verification on all requests
- Server-side input sanitization

## UX Details

### Inline Edit Behavior

1. Click cell → transforms to input
2. Enter or blur → saves
3. Escape → cancels
4. Visual states:
   - Editing: blue border
   - Saving: spinner
   - Saved: brief green flash
   - Error: red border + message

### Coordinate Validation

- Latitude: -90 to 90
- Longitude: -180 to 180
- Invalid values show inline error, don't save

### Empty States

- No edition selected: "Select an edition to manage its content"
- Empty edition: "This edition has no content yet. Import data or create items manually."

### Section State

- All sections collapsed by default
- Expand/collapse state saved to localStorage per edition

## Menu Placement

Add "Dashboard" submenu under Art Routes menu, positioned between "Editions" and "Routes".
