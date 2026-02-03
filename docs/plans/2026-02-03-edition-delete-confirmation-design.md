# Edition Delete Confirmation Dialog

## Overview

When deleting editions, users need to choose whether to also delete linked content (routes, locations, info points) or keep them. This design adds a confirmation modal to the Editions list table.

## User Flow

When a user clicks "Trash" on an edition or uses bulk delete:

1. Intercept the action before it executes
2. Fetch counts of linked content via AJAX
3. If no linked content exists, delete directly (no modal needed)
4. If linked content exists, show modal with options:
   - **Delete Edition Only**: Remove edition, clear `_edition_id` from linked content
   - **Delete Everything**: Remove edition AND permanently delete all linked content
   - **Cancel**: Close modal, no action

For bulk actions, show combined counts across all selected editions.

## Modal Design

```
┌─────────────────────────────────────────────────────────┐
│  Delete Edition: "Gluren bij de Buren 2024"             │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  This edition contains:                                 │
│  • 3 Routes                                             │
│  • 12 Locations                                         │
│  • 5 Info Points                                        │
│                                                         │
│  What would you like to do?                             │
│                                                         │
│  ┌───────────────────┐  ┌───────────────────┐          │
│  │ Delete Edition    │  │ Delete Everything │          │
│  │ Only              │  │                   │          │
│  └───────────────────┘  └───────────────────┘          │
│                                                         │
│  [Cancel]                                               │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

Button styling:
- "Delete Edition Only" - Secondary button (less destructive)
- "Delete Everything" - Red/destructive button
- "Cancel" - Link style

## Technical Implementation

### Files

| File | Purpose |
|------|---------|
| `includes/editions.php` | AJAX handlers for deletion modes |
| `assets/js/edition-delete-modal.js` | Modal logic and event handling |
| `assets/css/edition-delete-modal.css` | Modal styling |

### AJAX Endpoints

**`wp_art_routes_get_edition_content_counts`**
- Input: `edition_ids` (array of IDs)
- Output: `{routes: 3, locations: 12, info_points: 5}`
- Counts all content linked to the specified editions

**`wp_art_routes_delete_edition_only`**
- Input: `edition_ids` (array of IDs)
- Action: Delete editions, clear `_edition_id` meta from all linked content
- Output: `{success: true, deleted: 2}`

**`wp_art_routes_delete_edition_all`**
- Input: `edition_ids` (array of IDs)
- Action: Delete editions AND permanently delete all linked content
- Output: `{success: true, deleted_editions: 2, deleted_routes: 3, deleted_locations: 12, deleted_info_points: 5}`

### JavaScript Flow

1. On page load, attach click handlers to:
   - `.row-actions .submitdelete` links (individual trash)
   - `#doaction` / `#doaction2` buttons when "trash" is selected (bulk)

2. On delete click:
   - Prevent default action
   - Collect edition ID(s) from link href or checked checkboxes
   - AJAX call to get content counts
   - If counts are all zero, proceed with direct delete
   - Otherwise, show Thickbox modal with counts and options

3. On modal button click:
   - AJAX call to appropriate delete endpoint
   - Show spinner/loading state
   - On success, reload page
   - On error, show error message

### PHP Implementation

```php
// Get content counts for editions
function wp_art_routes_ajax_get_edition_content_counts() {
    check_ajax_referer('wp_art_routes_edition_delete', 'nonce');

    if (!current_user_can('delete_posts')) {
        wp_send_json_error('Permission denied');
    }

    $edition_ids = array_map('absint', $_POST['edition_ids']);

    $counts = [
        'routes' => 0,
        'locations' => 0,
        'info_points' => 0,
    ];

    foreach ($edition_ids as $edition_id) {
        $counts['routes'] += count(wp_art_routes_get_edition_routes($edition_id));
        $counts['locations'] += count(wp_art_routes_get_edition_artworks($edition_id));
        $counts['info_points'] += count(wp_art_routes_get_edition_information_points($edition_id));
    }

    wp_send_json_success($counts);
}

// Delete edition only, clear links
function wp_art_routes_ajax_delete_edition_only() {
    // Verify nonce and permissions
    // For each edition:
    //   - Get all linked content
    //   - delete_post_meta for _edition_id on each
    //   - wp_delete_post($edition_id, true)
    // Return success with count
}

// Delete edition and all content
function wp_art_routes_ajax_delete_edition_all() {
    // Verify nonce and permissions
    // For each edition:
    //   - Get all linked content
    //   - wp_delete_post for each content item (force delete)
    //   - wp_delete_post($edition_id, true)
    // Return success with counts
}
```

## Edge Cases

| Case | Handling |
|------|----------|
| Edition with no content | Skip modal, delete directly |
| Multiple editions selected | Show combined counts, process all with chosen action |
| Mixed content statuses | Delete all regardless of draft/publish |
| User lacks permissions | AJAX returns error, show message |

## Out of Scope (YAGNI)

- Move linked content to trash (permanent delete only)
- Undo functionality
- Per-item selection in modal
- Integration with single edition edit screen
- Reassign content to different edition in modal

## Testing

1. Delete edition with no content - should delete directly
2. Delete edition with content, choose "Edition Only" - edition gone, content remains with no edition
3. Delete edition with content, choose "Everything" - all deleted
4. Bulk delete multiple editions - combined counts shown
5. Cancel - no changes made
6. Test as non-admin user - should be denied
