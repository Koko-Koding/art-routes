# Editions System Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add an Editions layer that groups Routes, Locations, and Info Points for cultural events, with per-edition terminology overrides and a dynamic map block.

**Architecture:** Edition is a new CPT that owns content via `_edition_id` meta field on existing post types. A terminology system provides cascading labels (Edition → Global → Defaults). The Edition Map block is server-side rendered for dynamic data.

**Tech Stack:** WordPress CPT/meta APIs, PHP, Gutenberg blocks with ServerSideRender, existing Leaflet.js map infrastructure.

---

## Task 1: Create Terminology System Foundation

**Files:**
- Create: `includes/terminology.php`
- Modify: `wp-art-routes.php:44-51` (add require)

**Step 1: Create the terminology file with helper functions**

Create `includes/terminology.php`:

```php
<?php
/**
 * Terminology System for WP Art Routes
 *
 * Provides customizable labels with cascade: Edition → Global → Defaults
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get hardcoded default terminology
 *
 * @return array
 */
function wp_art_routes_get_default_terminology() {
    return [
        'route' => [
            'singular' => __('Route', 'wp-art-routes'),
            'plural' => __('Routes', 'wp-art-routes'),
            'slug' => 'route',
        ],
        'location' => [
            'singular' => __('Location', 'wp-art-routes'),
            'plural' => __('Locations', 'wp-art-routes'),
            'slug' => 'location',
        ],
        'info_point' => [
            'singular' => __('Info Point', 'wp-art-routes'),
            'plural' => __('Info Points', 'wp-art-routes'),
            'slug' => 'info-point',
        ],
        'creator' => [
            'singular' => __('Artist', 'wp-art-routes'),
            'plural' => __('Artists', 'wp-art-routes'),
        ],
    ];
}

/**
 * Get global terminology settings
 *
 * @return array
 */
function wp_art_routes_get_global_terminology() {
    $defaults = wp_art_routes_get_default_terminology();
    $saved = get_option('wp_art_routes_terminology', []);

    // Merge saved values with defaults
    $merged = $defaults;
    foreach ($saved as $type => $values) {
        if (isset($merged[$type]) && is_array($values)) {
            foreach ($values as $key => $value) {
                if (!empty($value)) {
                    $merged[$type][$key] = $value;
                }
            }
        }
    }

    return $merged;
}

/**
 * Get terminology for a specific Edition (merged with global/defaults)
 *
 * @param int $edition_id
 * @return array
 */
function wp_art_routes_get_edition_terminology($edition_id) {
    $base = wp_art_routes_get_global_terminology();

    if (!$edition_id) {
        return $base;
    }

    $edition_overrides = get_post_meta($edition_id, '_edition_terminology', true);

    if (!is_array($edition_overrides)) {
        return $base;
    }

    // Merge edition overrides with base
    foreach ($edition_overrides as $type => $values) {
        if (isset($base[$type]) && is_array($values)) {
            foreach ($values as $key => $value) {
                if (!empty($value)) {
                    $base[$type][$key] = $value;
                }
            }
        }
    }

    return $base;
}

/**
 * Get a label with Edition cascade
 *
 * @param string   $type       One of: 'route', 'location', 'info_point', 'creator'
 * @param bool     $plural     Return plural form
 * @param int|null $edition_id Edition ID for override lookup
 * @return string
 */
function wp_art_routes_label($type, $plural = false, $edition_id = null) {
    $key = $plural ? 'plural' : 'singular';
    $defaults = wp_art_routes_get_default_terminology();

    // 1. Check Edition override
    if ($edition_id) {
        $edition_terms = get_post_meta($edition_id, '_edition_terminology', true);
        if (is_array($edition_terms) && !empty($edition_terms[$type][$key])) {
            return $edition_terms[$type][$key];
        }
    }

    // 2. Check global settings
    $global = get_option('wp_art_routes_terminology', []);
    if (!empty($global[$type][$key])) {
        return $global[$type][$key];
    }

    // 3. Hardcoded defaults
    return isset($defaults[$type][$key]) ? $defaults[$type][$key] : '';
}

/**
 * Get URL slug for a content type
 *
 * @param string $type One of: 'route', 'location', 'info_point'
 * @return string
 */
function wp_art_routes_slug($type) {
    $global = wp_art_routes_get_global_terminology();
    return isset($global[$type]['slug']) ? $global[$type]['slug'] : $type;
}

/**
 * Try to auto-detect Edition context
 *
 * @return int|null Edition ID or null
 */
function wp_art_routes_detect_edition_context() {
    // 1. Check if we're on an Edition single page
    if (is_singular('edition')) {
        return get_the_ID();
    }

    // 2. Check if current post has an edition_id
    if (is_singular(['art_route', 'artwork', 'information_point'])) {
        $edition_id = get_post_meta(get_the_ID(), '_edition_id', true);
        if ($edition_id) {
            return (int) $edition_id;
        }
    }

    return null;
}
```

**Step 2: Add require statement to main plugin file**

In `wp-art-routes.php`, after line 51 (after `require_once ... settings.php`), add:

```php
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/terminology.php';
```

**Step 3: Verify the file loads without errors**

Run: Visit WordPress admin dashboard - no PHP errors should appear.

**Step 4: Commit**

```bash
git add includes/terminology.php wp-art-routes.php
git commit -m "feat: Add terminology system foundation

Introduces wp_art_routes_label() and related helpers for
customizable labels with Edition → Global → Defaults cascade."
```

---

## Task 2: Create Edition Custom Post Type

**Files:**
- Create: `includes/editions.php`
- Modify: `wp-art-routes.php` (add require)

**Step 1: Create editions.php with CPT registration**

Create `includes/editions.php`:

```php
<?php
/**
 * Edition Custom Post Type and Related Functionality
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Edition post type
 */
function wp_art_routes_register_edition_post_type() {
    register_post_type('edition', [
        'labels' => [
            'name' => __('Editions', 'wp-art-routes'),
            'singular_name' => __('Edition', 'wp-art-routes'),
            'add_new' => __('Add New Edition', 'wp-art-routes'),
            'add_new_item' => __('Add New Edition', 'wp-art-routes'),
            'edit_item' => __('Edit Edition', 'wp-art-routes'),
            'view_item' => __('View Edition', 'wp-art-routes'),
            'search_items' => __('Search Editions', 'wp-art-routes'),
            'not_found' => __('No editions found', 'wp-art-routes'),
            'not_found_in_trash' => __('No editions found in Trash', 'wp-art-routes'),
            'all_items' => __('Editions', 'wp-art-routes'),
            'menu_name' => __('Editions', 'wp-art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-calendar-alt',
        'menu_position' => 5,
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'edition'],
    ]);
}
add_action('init', 'wp_art_routes_register_edition_post_type');

/**
 * Register Edition meta fields
 */
function wp_art_routes_register_edition_meta() {
    register_post_meta('edition', '_edition_terminology', [
        'type' => 'array',
        'single' => true,
        'show_in_rest' => false, // Complex array, handle manually
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);

    register_post_meta('edition', '_edition_start_date', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);

    register_post_meta('edition', '_edition_end_date', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'wp_art_routes_register_edition_meta');

/**
 * Add Edition meta boxes
 */
function wp_art_routes_add_edition_meta_boxes() {
    add_meta_box(
        'edition_terminology',
        __('Terminology Overrides', 'wp-art-routes'),
        'wp_art_routes_render_edition_terminology_meta_box',
        'edition',
        'normal',
        'high'
    );

    add_meta_box(
        'edition_dates',
        __('Event Dates', 'wp-art-routes'),
        'wp_art_routes_render_edition_dates_meta_box',
        'edition',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'wp_art_routes_add_edition_meta_boxes');

/**
 * Render Terminology Override meta box
 */
function wp_art_routes_render_edition_terminology_meta_box($post) {
    wp_nonce_field('save_edition_terminology', 'edition_terminology_nonce');

    $terminology = get_post_meta($post->ID, '_edition_terminology', true);
    if (!is_array($terminology)) {
        $terminology = [];
    }

    $defaults = wp_art_routes_get_global_terminology();
    $types = [
        'route' => __('Route', 'wp-art-routes'),
        'location' => __('Location', 'wp-art-routes'),
        'info_point' => __('Info Point', 'wp-art-routes'),
        'creator' => __('Creator/Artist', 'wp-art-routes'),
    ];

    echo '<p class="description">' . __('Override labels for this edition. Leave empty to use global defaults.', 'wp-art-routes') . '</p>';
    echo '<table class="form-table">';

    foreach ($types as $type => $label) {
        $singular = isset($terminology[$type]['singular']) ? $terminology[$type]['singular'] : '';
        $plural = isset($terminology[$type]['plural']) ? $terminology[$type]['plural'] : '';
        $default_singular = $defaults[$type]['singular'];
        $default_plural = $defaults[$type]['plural'];

        echo '<tr>';
        echo '<th scope="row">' . esc_html($label) . '</th>';
        echo '<td>';
        echo '<label>' . __('Singular:', 'wp-art-routes') . ' ';
        echo '<input type="text" name="edition_terminology[' . esc_attr($type) . '][singular]" ';
        echo 'value="' . esc_attr($singular) . '" placeholder="' . esc_attr($default_singular) . '" class="regular-text">';
        echo '</label><br><br>';
        echo '<label>' . __('Plural:', 'wp-art-routes') . ' ';
        echo '<input type="text" name="edition_terminology[' . esc_attr($type) . '][plural]" ';
        echo 'value="' . esc_attr($plural) . '" placeholder="' . esc_attr($default_plural) . '" class="regular-text">';
        echo '</label>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}

/**
 * Render Event Dates meta box
 */
function wp_art_routes_render_edition_dates_meta_box($post) {
    wp_nonce_field('save_edition_dates', 'edition_dates_nonce');

    $start_date = get_post_meta($post->ID, '_edition_start_date', true);
    $end_date = get_post_meta($post->ID, '_edition_end_date', true);

    ?>
    <p>
        <label for="edition_start_date"><?php _e('Start Date:', 'wp-art-routes'); ?></label><br>
        <input type="date" id="edition_start_date" name="edition_start_date"
               value="<?php echo esc_attr($start_date); ?>" class="widefat">
    </p>
    <p>
        <label for="edition_end_date"><?php _e('End Date:', 'wp-art-routes'); ?></label><br>
        <input type="date" id="edition_end_date" name="edition_end_date"
               value="<?php echo esc_attr($end_date); ?>" class="widefat">
    </p>
    <p class="description"><?php _e('Optional. Specify the date range for this event.', 'wp-art-routes'); ?></p>
    <?php
}

/**
 * Save Edition terminology meta
 */
function wp_art_routes_save_edition_terminology($post_id) {
    if (!isset($_POST['edition_terminology_nonce']) ||
        !wp_verify_nonce($_POST['edition_terminology_nonce'], 'save_edition_terminology')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['edition_terminology']) && is_array($_POST['edition_terminology'])) {
        $terminology = [];
        foreach ($_POST['edition_terminology'] as $type => $values) {
            $terminology[sanitize_key($type)] = [
                'singular' => sanitize_text_field($values['singular'] ?? ''),
                'plural' => sanitize_text_field($values['plural'] ?? ''),
            ];
        }
        update_post_meta($post_id, '_edition_terminology', $terminology);
    }
}
add_action('save_post_edition', 'wp_art_routes_save_edition_terminology');

/**
 * Save Edition dates meta
 */
function wp_art_routes_save_edition_dates($post_id) {
    if (!isset($_POST['edition_dates_nonce']) ||
        !wp_verify_nonce($_POST['edition_dates_nonce'], 'save_edition_dates')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['edition_start_date'])) {
        update_post_meta($post_id, '_edition_start_date', sanitize_text_field($_POST['edition_start_date']));
    }

    if (isset($_POST['edition_end_date'])) {
        update_post_meta($post_id, '_edition_end_date', sanitize_text_field($_POST['edition_end_date']));
    }
}
add_action('save_post_edition', 'wp_art_routes_save_edition_dates');

/**
 * Get all editions
 *
 * @return array Array of edition posts
 */
function wp_art_routes_get_editions() {
    return get_posts([
        'post_type' => 'edition',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
}

/**
 * Get Edition data by ID
 *
 * @param int $edition_id
 * @return array|null
 */
function wp_art_routes_get_edition_data($edition_id) {
    $edition = get_post($edition_id);

    if (!$edition || $edition->post_type !== 'edition') {
        return null;
    }

    return [
        'id' => $edition->ID,
        'title' => $edition->post_title,
        'description' => $edition->post_content,
        'excerpt' => $edition->post_excerpt,
        'image' => get_the_post_thumbnail_url($edition->ID, 'large'),
        'start_date' => get_post_meta($edition->ID, '_edition_start_date', true),
        'end_date' => get_post_meta($edition->ID, '_edition_end_date', true),
        'terminology' => wp_art_routes_get_edition_terminology($edition->ID),
        'permalink' => get_permalink($edition->ID),
    ];
}
```

**Step 2: Add require statement to main plugin file**

In `wp-art-routes.php`, after the terminology.php require, add:

```php
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/editions.php';
```

**Step 3: Verify Edition CPT appears in admin**

Run: Visit WordPress admin → "Editions" should appear as a top-level menu item.

**Step 4: Commit**

```bash
git add includes/editions.php wp-art-routes.php
git commit -m "feat: Add Edition custom post type

Registers edition CPT with terminology override and date meta boxes.
Includes helper functions for getting edition data."
```

---

## Task 3: Add Edition Linking to Existing Post Types

**Files:**
- Modify: `includes/post-types.php`
- Modify: `includes/meta-boxes.php`

**Step 1: Register _edition_id meta for existing post types**

In `includes/post-types.php`, add after line 156 (after `wp_art_routes_register_artwork_meta` function ends):

```php
/**
 * Register _edition_id meta for all content post types
 */
function wp_art_routes_register_edition_id_meta() {
    $post_types = ['art_route', 'artwork', 'information_point'];

    foreach ($post_types as $post_type) {
        register_post_meta($post_type, '_edition_id', [
            'type' => 'integer',
            'single' => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'absint',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ]);
    }
}
add_action('init', 'wp_art_routes_register_edition_id_meta');
```

**Step 2: Add Edition selector meta box**

In `includes/meta-boxes.php`, add after line 96 (after `wp_art_routes_add_meta_boxes` function, before the closing brace):

```php
    // Edition selector meta box for all content types
    $edition_post_types = ['art_route', 'artwork', 'information_point'];
    foreach ($edition_post_types as $post_type) {
        add_meta_box(
            'edition_selector',
            __('Edition', 'wp-art-routes'),
            'wp_art_routes_render_edition_selector_meta_box',
            $post_type,
            'side',
            'high'
        );
    }
```

**Step 3: Add render function for Edition selector**

In `includes/meta-boxes.php`, add after the last function (at the end of the file):

```php
/**
 * Render Edition selector meta box
 */
function wp_art_routes_render_edition_selector_meta_box($post) {
    wp_nonce_field('save_edition_selector', 'edition_selector_nonce');

    $current_edition_id = get_post_meta($post->ID, '_edition_id', true);
    $editions = wp_art_routes_get_editions();

    ?>
    <p>
        <select name="edition_id" id="edition_id" class="widefat">
            <option value="0"><?php _e('— No Edition —', 'wp-art-routes'); ?></option>
            <?php foreach ($editions as $edition) : ?>
                <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($current_edition_id, $edition->ID); ?>>
                    <?php echo esc_html($edition->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class="description">
        <?php _e('Assign this content to an edition.', 'wp-art-routes'); ?>
    </p>
    <?php
}

/**
 * Save Edition selector meta
 */
function wp_art_routes_save_edition_selector($post_id) {
    if (!isset($_POST['edition_selector_nonce']) ||
        !wp_verify_nonce($_POST['edition_selector_nonce'], 'save_edition_selector')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['edition_id'])) {
        $edition_id = absint($_POST['edition_id']);
        if ($edition_id > 0) {
            update_post_meta($post_id, '_edition_id', $edition_id);
        } else {
            delete_post_meta($post_id, '_edition_id');
        }
    }
}
add_action('save_post_art_route', 'wp_art_routes_save_edition_selector');
add_action('save_post_artwork', 'wp_art_routes_save_edition_selector');
add_action('save_post_information_point', 'wp_art_routes_save_edition_selector');
```

**Step 4: Verify Edition selector appears**

Run: Edit any Route, Artwork, or Info Point → "Edition" dropdown should appear in sidebar.

**Step 5: Commit**

```bash
git add includes/post-types.php includes/meta-boxes.php
git commit -m "feat: Add Edition linking to Routes, Locations, Info Points

Adds _edition_id meta field and selector dropdown to all content types."
```

---

## Task 4: Add Edition Column and Filters to Admin List Tables

**Files:**
- Modify: `includes/post-types.php`

**Step 1: Add Edition column to list tables**

In `includes/post-types.php`, add at the end of the file:

```php
/**
 * Add Edition column to content list tables
 */
function wp_art_routes_add_edition_column($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['edition'] = __('Edition', 'wp-art-routes');
        }
    }
    return $new_columns;
}
add_filter('manage_art_route_posts_columns', 'wp_art_routes_add_edition_column');
add_filter('manage_artwork_posts_columns', 'wp_art_routes_add_edition_column');
add_filter('manage_information_point_posts_columns', 'wp_art_routes_add_edition_column');

/**
 * Render Edition column content
 */
function wp_art_routes_render_edition_column($column, $post_id) {
    if ($column === 'edition') {
        $edition_id = get_post_meta($post_id, '_edition_id', true);
        if ($edition_id) {
            $edition = get_post($edition_id);
            if ($edition) {
                echo '<a href="' . esc_url(get_edit_post_link($edition_id)) . '">';
                echo esc_html($edition->post_title);
                echo '</a>';
            } else {
                echo '—';
            }
        } else {
            echo '<span style="color:#999;">— ' . __('None', 'wp-art-routes') . ' —</span>';
        }
    }
}
add_action('manage_art_route_posts_custom_column', 'wp_art_routes_render_edition_column', 10, 2);
add_action('manage_artwork_posts_custom_column', 'wp_art_routes_render_edition_column', 10, 2);
add_action('manage_information_point_posts_custom_column', 'wp_art_routes_render_edition_column', 10, 2);

/**
 * Add Edition filter dropdown to list tables
 */
function wp_art_routes_add_edition_filter() {
    global $typenow;

    if (!in_array($typenow, ['art_route', 'artwork', 'information_point'])) {
        return;
    }

    $editions = wp_art_routes_get_editions();
    $current_edition = isset($_GET['edition_filter']) ? absint($_GET['edition_filter']) : 0;

    ?>
    <select name="edition_filter">
        <option value="0"><?php _e('All Editions', 'wp-art-routes'); ?></option>
        <option value="-1" <?php selected($current_edition, -1); ?>><?php _e('No Edition', 'wp-art-routes'); ?></option>
        <?php foreach ($editions as $edition) : ?>
            <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($current_edition, $edition->ID); ?>>
                <?php echo esc_html($edition->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'wp_art_routes_add_edition_filter');

/**
 * Filter posts by Edition
 */
function wp_art_routes_filter_by_edition($query) {
    global $pagenow, $typenow;

    if (!is_admin() || $pagenow !== 'edit.php') {
        return;
    }

    if (!in_array($typenow, ['art_route', 'artwork', 'information_point'])) {
        return;
    }

    if (!isset($_GET['edition_filter']) || $_GET['edition_filter'] === '0') {
        return;
    }

    $edition_filter = intval($_GET['edition_filter']);

    if ($edition_filter === -1) {
        // No edition
        $query->set('meta_query', [
            'relation' => 'OR',
            [
                'key' => '_edition_id',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key' => '_edition_id',
                'value' => '',
                'compare' => '=',
            ],
            [
                'key' => '_edition_id',
                'value' => '0',
                'compare' => '=',
            ],
        ]);
    } else {
        // Specific edition
        $query->set('meta_key', '_edition_id');
        $query->set('meta_value', $edition_filter);
    }
}
add_action('pre_get_posts', 'wp_art_routes_filter_by_edition');
```

**Step 2: Verify column and filter appear**

Run: Visit Routes/Artworks/Info Points list → "Edition" column and filter dropdown should appear.

**Step 3: Commit**

```bash
git add includes/post-types.php
git commit -m "feat: Add Edition column and filter to admin list tables

Shows Edition in content lists with filter dropdown for easy filtering."
```

---

## Task 5: Restructure Admin Menu

**Files:**
- Modify: `includes/post-types.php`
- Modify: `includes/settings.php`

**Step 1: Update post type registrations to use Editions menu**

In `includes/post-types.php`, modify the `register_post_type` calls:

For `art_route` (around line 18-35), change:
```php
'menu_icon' => 'dashicons-location-alt',
```
to:
```php
'menu_icon' => 'dashicons-location-alt',
'show_in_menu' => 'edit.php?post_type=edition',
```

For `artwork` (around line 55), change:
```php
'show_in_menu' => 'edit.php?post_type=art_route',
```
to:
```php
'show_in_menu' => 'edit.php?post_type=edition',
```

For `information_point` (around line 76), change:
```php
'show_in_menu' => 'edit.php?post_type=art_route',
```
to:
```php
'show_in_menu' => 'edit.php?post_type=edition',
```

**Step 2: Update settings page parent menu**

In `includes/settings.php`, modify `wp_art_routes_add_settings_page` function (around line 40-50):

Change:
```php
add_submenu_page(
    'edit.php?post_type=art_route',
```
to:
```php
add_submenu_page(
    'edit.php?post_type=edition',
```

**Step 3: Verify menu structure**

Run: Visit WordPress admin → Menu should show:
- Editions (top-level)
  - Editions
  - Routes
  - Locations (Artworks)
  - Info Points
  - Settings

**Step 4: Commit**

```bash
git add includes/post-types.php includes/settings.php
git commit -m "refactor: Restructure admin menu under Editions

Moves Routes, Locations, Info Points, and Settings under Editions menu."
```

---

## Task 6: Add Terminology Settings Tab

**Files:**
- Modify: `includes/settings.php`

**Step 1: Rewrite settings page with tabs**

Replace the entire contents of `includes/settings.php` with:

```php
<?php
/**
 * Settings Page for WP Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register plugin settings
 */
function wp_art_routes_register_settings() {
    // General settings
    register_setting('wp_art_routes_options', 'wp_art_routes_default_route_id', [
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'default' => 0,
    ]);

    register_setting('wp_art_routes_options', 'wp_art_routes_enable_location_tracking', [
        'type' => 'boolean',
        'sanitize_callback' => 'wp_validate_boolean',
        'default' => true,
    ]);

    // Terminology settings
    register_setting('wp_art_routes_terminology_options', 'wp_art_routes_terminology', [
        'type' => 'array',
        'sanitize_callback' => 'wp_art_routes_sanitize_terminology',
        'default' => [],
    ]);
}
add_action('admin_init', 'wp_art_routes_register_settings');

/**
 * Sanitize terminology settings
 */
function wp_art_routes_sanitize_terminology($input) {
    if (!is_array($input)) {
        return [];
    }

    $sanitized = [];
    $allowed_types = ['route', 'location', 'info_point', 'creator'];

    foreach ($allowed_types as $type) {
        if (isset($input[$type]) && is_array($input[$type])) {
            $sanitized[$type] = [
                'singular' => sanitize_text_field($input[$type]['singular'] ?? ''),
                'plural' => sanitize_text_field($input[$type]['plural'] ?? ''),
            ];
            if (isset($input[$type]['slug'])) {
                $sanitized[$type]['slug'] = sanitize_title($input[$type]['slug']);
            }
        }
    }

    return $sanitized;
}

/**
 * Add settings page to admin menu
 */
function wp_art_routes_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=edition',
        __('Art Routes Settings', 'wp-art-routes'),
        __('Settings', 'wp-art-routes'),
        'manage_options',
        'wp-art-routes-settings',
        'wp_art_routes_render_settings_page'
    );
}
add_action('admin_menu', 'wp_art_routes_add_settings_page');

/**
 * Render the settings page
 */
function wp_art_routes_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
    $tabs = [
        'general' => __('General', 'wp-art-routes'),
        'terminology' => __('Terminology', 'wp-art-routes'),
    ];

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php settings_errors('wp_art_routes_messages'); ?>

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                <a href="<?php echo esc_url(add_query_arg('tab', $tab_key, admin_url('edit.php?post_type=edition&page=wp-art-routes-settings'))); ?>"
                   class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($tab_label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="tab-content" style="margin-top: 20px;">
            <?php
            switch ($current_tab) {
                case 'terminology':
                    wp_art_routes_render_terminology_tab();
                    break;
                default:
                    wp_art_routes_render_general_tab();
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Render General settings tab
 */
function wp_art_routes_render_general_tab() {
    ?>
    <form method="post" action="options.php">
        <?php settings_fields('wp_art_routes_options'); ?>

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="wp_art_routes_default_route_id">
                        <?php _e('Default Route', 'wp-art-routes'); ?>
                    </label>
                </th>
                <td>
                    <?php
                    $default_route_id = get_option('wp_art_routes_default_route_id', 0);
                    $routes = get_posts([
                        'post_type' => 'art_route',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                    ]);

                    if (!empty($routes)) {
                        echo '<select name="wp_art_routes_default_route_id" id="wp_art_routes_default_route_id">';
                        echo '<option value="0">' . __('Select a default route', 'wp-art-routes') . '</option>';
                        foreach ($routes as $route) {
                            echo '<option value="' . esc_attr($route->ID) . '" ' . selected($default_route_id, $route->ID, false) . '>';
                            echo esc_html($route->post_title);
                            echo '</option>';
                        }
                        echo '</select>';
                        echo '<p class="description">' . __('This route will be used when no specific route is selected.', 'wp-art-routes') . '</p>';
                    } else {
                        echo '<p>' . __('No routes available. Please create a route first.', 'wp-art-routes') . '</p>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php _e('Location Tracking', 'wp-art-routes'); ?></th>
                <td>
                    <label for="wp_art_routes_enable_location_tracking">
                        <input type="checkbox" name="wp_art_routes_enable_location_tracking" id="wp_art_routes_enable_location_tracking" value="1" <?php checked(get_option('wp_art_routes_enable_location_tracking', true)); ?> />
                        <?php _e('Enable location tracking for users', 'wp-art-routes'); ?>
                    </label>
                    <p class="description"><?php _e('When enabled, users will be prompted to share their location to track progress on routes.', 'wp-art-routes'); ?></p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
    <?php
}

/**
 * Render Terminology settings tab
 */
function wp_art_routes_render_terminology_tab() {
    $defaults = wp_art_routes_get_default_terminology();
    $saved = get_option('wp_art_routes_terminology', []);

    $types = [
        'route' => __('Route', 'wp-art-routes'),
        'location' => __('Location (Artwork)', 'wp-art-routes'),
        'info_point' => __('Info Point', 'wp-art-routes'),
        'creator' => __('Creator (Artist)', 'wp-art-routes'),
    ];

    ?>
    <form method="post" action="options.php">
        <?php settings_fields('wp_art_routes_terminology_options'); ?>

        <p><?php _e('Customize the labels used throughout the plugin. Leave fields empty to use defaults.', 'wp-art-routes'); ?></p>

        <table class="form-table" role="presentation">
            <?php foreach ($types as $type => $label) :
                $singular = isset($saved[$type]['singular']) ? $saved[$type]['singular'] : '';
                $plural = isset($saved[$type]['plural']) ? $saved[$type]['plural'] : '';
                $default_singular = $defaults[$type]['singular'];
                $default_plural = $defaults[$type]['plural'];
            ?>
            <tr>
                <th scope="row"><?php echo esc_html($label); ?></th>
                <td>
                    <p>
                        <label>
                            <?php _e('Singular:', 'wp-art-routes'); ?>
                            <input type="text" name="wp_art_routes_terminology[<?php echo esc_attr($type); ?>][singular]"
                                   value="<?php echo esc_attr($singular); ?>"
                                   placeholder="<?php echo esc_attr($default_singular); ?>"
                                   class="regular-text">
                        </label>
                    </p>
                    <p>
                        <label>
                            <?php _e('Plural:', 'wp-art-routes'); ?>
                            <input type="text" name="wp_art_routes_terminology[<?php echo esc_attr($type); ?>][plural]"
                                   value="<?php echo esc_attr($plural); ?>"
                                   placeholder="<?php echo esc_attr($default_plural); ?>"
                                   class="regular-text">
                        </label>
                    </p>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <?php submit_button(); ?>
    </form>
    <?php
}
```

**Step 2: Verify settings tabs work**

Run: Visit Editions → Settings → Both "General" and "Terminology" tabs should work.

**Step 3: Commit**

```bash
git add includes/settings.php
git commit -m "feat: Add Terminology settings tab

Allows global customization of Route, Location, Info Point, Creator labels."
```

---

## Task 7: Add Edition-Filtered Data Functions

**Files:**
- Modify: `includes/template-functions.php`

**Step 1: Add edition-filtered data retrieval functions**

At the end of `includes/template-functions.php`, add:

```php
/**
 * Get routes for a specific Edition
 *
 * @param int $edition_id
 * @return array
 */
function wp_art_routes_get_edition_routes($edition_id) {
    if (!$edition_id) {
        return [];
    }

    $routes = get_posts([
        'post_type' => 'art_route',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $result = [];
    foreach ($routes as $route) {
        $result[] = wp_art_routes_get_route_data($route->ID);
    }

    return $result;
}

/**
 * Get artworks for a specific Edition
 *
 * @param int $edition_id
 * @return array
 */
function wp_art_routes_get_edition_artworks($edition_id) {
    if (!$edition_id) {
        return [];
    }

    $artworks = get_posts([
        'post_type' => 'artwork',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $result = [];
    foreach ($artworks as $artwork) {
        $latitude = get_post_meta($artwork->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($artwork->ID, '_artwork_longitude', true);

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            continue;
        }

        $icon_filename = get_post_meta($artwork->ID, '_artwork_icon', true);
        $icon_url = '';
        if (!empty($icon_filename)) {
            $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
            $icon_url = $icons_url . $icon_filename;
        }

        $artist_ids = get_post_meta($artwork->ID, '_artwork_artist_ids', true);
        $artists = [];
        if (is_array($artist_ids) && !empty($artist_ids)) {
            foreach ($artist_ids as $artist_id) {
                $artist_post = get_post($artist_id);
                if ($artist_post) {
                    $post_type_obj = get_post_type_object($artist_post->post_type);
                    $artists[] = [
                        'id' => $artist_id,
                        'title' => $artist_post->post_title,
                        'url' => get_permalink($artist_id),
                        'post_type' => $artist_post->post_type,
                        'post_type_label' => $post_type_obj ? $post_type_obj->labels->singular_name : $artist_post->post_type,
                    ];
                }
            }
        }

        $result[] = [
            'id' => $artwork->ID,
            'title' => $artwork->post_title,
            'description' => $artwork->post_content,
            'excerpt' => $artwork->post_excerpt,
            'image_url' => get_the_post_thumbnail_url($artwork->ID, 'large'),
            'latitude' => (float)$latitude,
            'longitude' => (float)$longitude,
            'number' => get_post_meta($artwork->ID, '_artwork_number', true),
            'location' => get_post_meta($artwork->ID, '_artwork_location', true),
            'permalink' => get_permalink($artwork->ID),
            'icon_url' => $icon_url ? esc_url($icon_url) : '',
            'wheelchair_accessible' => get_post_meta($artwork->ID, '_wheelchair_accessible', true),
            'stroller_accessible' => get_post_meta($artwork->ID, '_stroller_accessible', true),
            'artists' => $artists,
        ];
    }

    return $result;
}

/**
 * Get information points for a specific Edition
 *
 * @param int $edition_id
 * @return array
 */
function wp_art_routes_get_edition_information_points($edition_id) {
    if (!$edition_id) {
        return [];
    }

    $info_points = get_posts([
        'post_type' => 'information_point',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $result = [];
    foreach ($info_points as $info_post) {
        $latitude = get_post_meta($info_post->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($info_post->ID, '_artwork_longitude', true);

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            continue;
        }

        $icon_filename = get_post_meta($info_post->ID, '_info_point_icon', true);
        $icon_url = '';
        if (!empty($icon_filename)) {
            $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
            $icon_url = $icons_url . $icon_filename;
        } else {
            $icon_url = get_post_meta($info_post->ID, '_info_point_icon_url', true);
            if (empty($icon_url)) {
                $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
                $icon_url = $icons_url . 'WB plattegrond-Informatie.svg';
            }
        }

        $result[] = [
            'id' => $info_post->ID,
            'title' => $info_post->post_title,
            'excerpt' => has_excerpt($info_post->ID) ? get_the_excerpt($info_post->ID) : wp_trim_words($info_post->post_content, 30, '...'),
            'image_url' => get_the_post_thumbnail_url($info_post->ID, 'medium'),
            'permalink' => get_permalink($info_post->ID),
            'latitude' => (float)$latitude,
            'longitude' => (float)$longitude,
            'icon_url' => $icon_url,
        ];
    }

    return $result;
}
```

**Step 2: Commit**

```bash
git add includes/template-functions.php
git commit -m "feat: Add Edition-filtered data retrieval functions

Adds functions to get routes, artworks, info points for specific editions."
```

---

## Task 8: Create Edition Single Template

**Files:**
- Create: `templates/single-edition.php`
- Modify: `includes/template-functions.php`

**Step 1: Create single-edition.php template**

Create `templates/single-edition.php`:

```php
<?php
/**
 * Template for displaying single Edition
 *
 * Override by copying to: {theme}/wp-art-routes/single-edition.php
 */

get_header();

while (have_posts()) :
    the_post();

    $edition_id = get_the_ID();
    $edition_data = wp_art_routes_get_edition_data($edition_id);
    $routes = wp_art_routes_get_edition_routes($edition_id);
    $artworks = wp_art_routes_get_edition_artworks($edition_id);
    $info_points = wp_art_routes_get_edition_information_points($edition_id);

    // Get terminology for this edition
    $route_label = wp_art_routes_label('route', true, $edition_id);
    $location_label = wp_art_routes_label('location', true, $edition_id);
    $info_point_label = wp_art_routes_label('info_point', true, $edition_id);
?>

<article id="edition-<?php the_ID(); ?>" <?php post_class('edition-single'); ?>>
    <header class="edition-header">
        <h1 class="edition-title"><?php the_title(); ?></h1>

        <?php if ($edition_data['start_date'] || $edition_data['end_date']) : ?>
            <div class="edition-dates">
                <?php
                if ($edition_data['start_date'] && $edition_data['end_date']) {
                    printf(
                        __('%s - %s', 'wp-art-routes'),
                        date_i18n(get_option('date_format'), strtotime($edition_data['start_date'])),
                        date_i18n(get_option('date_format'), strtotime($edition_data['end_date']))
                    );
                } elseif ($edition_data['start_date']) {
                    printf(__('Starting %s', 'wp-art-routes'), date_i18n(get_option('date_format'), strtotime($edition_data['start_date'])));
                } elseif ($edition_data['end_date']) {
                    printf(__('Until %s', 'wp-art-routes'), date_i18n(get_option('date_format'), strtotime($edition_data['end_date'])));
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (has_post_thumbnail()) : ?>
            <div class="edition-featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="edition-content">
        <?php the_content(); ?>
    </div>

    <?php if (!empty($routes) || !empty($artworks) || !empty($info_points)) : ?>
        <!-- Map Section -->
        <section class="edition-map-section">
            <div id="edition-map" class="edition-map" style="height: 500px;"></div>

            <?php
            // Display map controls
            wp_art_routes_display_map_controls([
                'show_route' => !empty($routes),
                'artworks_checked' => !empty($artworks),
                'info_points_checked' => !empty($info_points),
            ]);
            ?>
        </section>

        <?php
        // Enqueue map scripts with edition data
        wp_art_routes_enqueue_edition_map_scripts($edition_id, $routes, $artworks, $info_points);
        ?>
    <?php endif; ?>

    <?php if (!empty($routes)) : ?>
        <!-- Routes Section -->
        <section class="edition-routes-section">
            <h2><?php echo esc_html($route_label); ?></h2>
            <div class="edition-routes-grid">
                <?php foreach ($routes as $route) : ?>
                    <div class="route-card">
                        <?php if (!empty($route['image'])) : ?>
                            <div class="route-card-image">
                                <a href="<?php echo esc_url(get_permalink($route['id'])); ?>">
                                    <img src="<?php echo esc_url($route['image']); ?>" alt="<?php echo esc_attr($route['title']); ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="route-card-content">
                            <h3 class="route-card-title">
                                <a href="<?php echo esc_url(get_permalink($route['id'])); ?>">
                                    <?php echo esc_html($route['title']); ?>
                                </a>
                            </h3>
                            <?php if (!empty($route['length']) || !empty($route['duration'])) : ?>
                                <div class="route-card-meta">
                                    <?php if (!empty($route['length'])) : ?>
                                        <span class="route-length"><?php echo esc_html(wp_art_routes_format_length($route['length'])); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($route['duration'])) : ?>
                                        <span class="route-duration"><?php echo esc_html(wp_art_routes_format_duration($route['duration'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($route['excerpt'])) : ?>
                                <p class="route-card-excerpt"><?php echo esc_html($route['excerpt']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($artworks)) : ?>
        <!-- Locations Section -->
        <section class="edition-locations-section">
            <h2><?php echo esc_html($location_label); ?></h2>
            <div class="edition-locations-grid">
                <?php foreach ($artworks as $artwork) : ?>
                    <div class="location-card">
                        <?php if (!empty($artwork['image_url'])) : ?>
                            <div class="location-card-image">
                                <a href="<?php echo esc_url($artwork['permalink']); ?>">
                                    <img src="<?php echo esc_url($artwork['image_url']); ?>" alt="<?php echo esc_attr($artwork['title']); ?>">
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="location-card-content">
                            <?php if (!empty($artwork['number'])) : ?>
                                <span class="location-number"><?php echo esc_html($artwork['number']); ?></span>
                            <?php endif; ?>
                            <h3 class="location-card-title">
                                <a href="<?php echo esc_url($artwork['permalink']); ?>">
                                    <?php echo esc_html($artwork['title']); ?>
                                </a>
                            </h3>
                            <?php if (!empty($artwork['artists'])) : ?>
                                <p class="location-artists">
                                    <?php
                                    $artist_links = array_map(function($artist) {
                                        return '<a href="' . esc_url($artist['url']) . '">' . esc_html($artist['title']) . '</a>';
                                    }, $artwork['artists']);
                                    echo implode(', ', $artist_links);
                                    ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($info_points)) : ?>
        <!-- Info Points Section -->
        <section class="edition-info-points-section">
            <h2><?php echo esc_html($info_point_label); ?></h2>
            <ul class="edition-info-points-list">
                <?php foreach ($info_points as $info_point) : ?>
                    <li class="info-point-item">
                        <a href="<?php echo esc_url($info_point['permalink']); ?>">
                            <?php echo esc_html($info_point['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</article>

<?php
endwhile;

get_footer();
```

**Step 2: Add template include filter and enqueue function**

At the end of `includes/template-functions.php`, add:

```php
/**
 * Handle template redirection for Edition posts
 */
function wp_art_routes_single_edition_template($template) {
    if (is_singular('edition')) {
        $located = locate_template('wp-art-routes/single-edition.php');
        if (empty($located)) {
            $located = WP_ART_ROUTES_PLUGIN_DIR . 'templates/single-edition.php';
        }
        if (file_exists($located)) {
            return $located;
        }
    }
    return $template;
}
add_filter('template_include', 'wp_art_routes_single_edition_template', 99);

/**
 * Enqueue map scripts for Edition template
 */
function wp_art_routes_enqueue_edition_map_scripts($edition_id, $routes, $artworks, $info_points) {
    // Prepare route paths for the map
    $map_routes = [];
    foreach ($routes as $route) {
        $map_routes[] = [
            'id' => $route['id'],
            'title' => $route['title'],
            'path' => $route['route_path'],
        ];
    }

    // Localize script data
    wp_localize_script('wp-art-routes-map', 'wpArtRoutesEditionData', [
        'editionId' => $edition_id,
        'routes' => $map_routes,
        'artworks' => $artworks,
        'informationPoints' => $info_points,
        'mapContainerId' => 'edition-map',
    ]);
}
```

**Step 3: Verify edition template loads**

Run: Create an Edition, add some routes/artworks to it, visit the Edition page.

**Step 4: Commit**

```bash
git add templates/single-edition.php includes/template-functions.php
git commit -m "feat: Add Edition single page template

Displays map with routes/locations/info points plus content grids below."
```

---

## Task 9: Create Edition Map Shortcode

**Files:**
- Modify: `includes/shortcodes.php`
- Create: `templates/shortcode-edition-map.php`

**Step 1: Add edition_map shortcode**

In `includes/shortcodes.php`, add after line 21 (after `add_shortcode('related_artworks'...`):

```php
    add_shortcode('edition_map', 'wp_art_routes_edition_map_shortcode');
```

Then add the shortcode function after `wp_art_routes_related_artworks_shortcode` function:

```php
/**
 * Shortcode to display an Edition map
 *
 * Usage: [edition_map edition_id="123" routes="all" show_locations="true" show_info_points="true" show_legend="true" height="500px"]
 */
function wp_art_routes_edition_map_shortcode($atts) {
    $atts = shortcode_atts([
        'edition_id' => 0,           // 0 = auto-detect
        'routes' => 'all',           // all, none, or comma-separated IDs
        'show_locations' => 'true',
        'show_info_points' => 'true',
        'show_legend' => 'true',
        'height' => '500px',
    ], $atts);

    // Convert string booleans
    $atts['show_locations'] = ($atts['show_locations'] === 'true');
    $atts['show_info_points'] = ($atts['show_info_points'] === 'true');
    $atts['show_legend'] = ($atts['show_legend'] === 'true');

    // Auto-detect edition if not specified
    $edition_id = intval($atts['edition_id']);
    if (!$edition_id) {
        $edition_id = wp_art_routes_detect_edition_context();
    }

    // Still no edition? Show placeholder
    if (!$edition_id) {
        return '<div class="edition-map-placeholder"><p>' . __('Please select an Edition.', 'wp-art-routes') . '</p></div>';
    }

    // Get edition data
    $edition = wp_art_routes_get_edition_data($edition_id);
    if (!$edition) {
        return '<div class="edition-map-error"><p>' . __('Edition not found.', 'wp-art-routes') . '</p></div>';
    }

    // Get routes based on parameter
    $routes = [];
    if ($atts['routes'] !== 'none') {
        $all_routes = wp_art_routes_get_edition_routes($edition_id);
        if ($atts['routes'] === 'all') {
            $routes = $all_routes;
        } else {
            $route_ids = array_map('intval', explode(',', $atts['routes']));
            $routes = array_filter($all_routes, function($route) use ($route_ids) {
                return in_array($route['id'], $route_ids);
            });
        }
    }

    // Get locations and info points
    $artworks = $atts['show_locations'] ? wp_art_routes_get_edition_artworks($edition_id) : [];
    $info_points = $atts['show_info_points'] ? wp_art_routes_get_edition_information_points($edition_id) : [];

    ob_start();
    wp_art_routes_get_template_part('shortcode-edition-map', [
        'atts' => $atts,
        'edition' => $edition,
        'routes' => $routes,
        'artworks' => $artworks,
        'info_points' => $info_points,
    ]);
    return ob_get_clean();
}
```

**Step 2: Create shortcode template**

Create `templates/shortcode-edition-map.php`:

```php
<?php
/**
 * Template for Edition Map shortcode
 *
 * Variables: $atts, $edition, $routes, $artworks, $info_points
 */

if (!defined('ABSPATH')) {
    exit;
}

$map_id = 'edition-map-' . $edition['id'] . '-' . wp_rand();
$height = esc_attr($atts['height']);

// Prepare routes data for JS
$js_routes = [];
foreach ($routes as $route) {
    $js_routes[] = [
        'id' => $route['id'],
        'title' => $route['title'],
        'path' => $route['route_path'],
        'color' => '', // Will be assigned by JS
    ];
}
?>

<div class="edition-map-container">
    <div id="<?php echo esc_attr($map_id); ?>" class="edition-map" style="height: <?php echo $height; ?>;"></div>

    <?php if ($atts['show_legend'] && (!empty($routes) || !empty($artworks) || !empty($info_points))) : ?>
        <?php
        wp_art_routes_display_map_controls([
            'show_route' => !empty($routes),
            'show_artworks' => !empty($artworks),
            'show_info_points' => !empty($info_points),
            'show_user_location' => true,
            'show_navigation' => true,
        ]);
        ?>
    <?php endif; ?>
</div>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof wpArtRoutesInitEditionMap === 'function') {
            wpArtRoutesInitEditionMap(
                '<?php echo esc_js($map_id); ?>',
                <?php echo json_encode($js_routes); ?>,
                <?php echo json_encode($artworks); ?>,
                <?php echo json_encode($info_points); ?>
            );
        }
    });
})();
</script>
```

**Step 3: Commit**

```bash
git add includes/shortcodes.php templates/shortcode-edition-map.php
git commit -m "feat: Add [edition_map] shortcode

Displays map for specific edition with route/location/info point filtering."
```

---

## Task 10: Create Edition Map Gutenberg Block

**Files:**
- Create: `includes/blocks.php`
- Create: `assets/js/blocks/edition-map-block.js`
- Modify: `wp-art-routes.php`

**Step 1: Create blocks.php**

Create `includes/blocks.php`:

```php
<?php
/**
 * Gutenberg Blocks Registration
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Edition Map block
 */
function wp_art_routes_register_blocks() {
    // Register block script
    wp_register_script(
        'wp-art-routes-edition-map-block',
        WP_ART_ROUTES_PLUGIN_URL . 'assets/js/blocks/edition-map-block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-server-side-render', 'wp-data'],
        WP_ART_ROUTES_VERSION,
        true
    );

    // Localize block data
    $editions = wp_art_routes_get_editions();
    $editions_options = [['value' => 0, 'label' => __('Auto-detect', 'wp-art-routes')]];
    foreach ($editions as $edition) {
        $editions_options[] = [
            'value' => $edition->ID,
            'label' => $edition->post_title,
        ];
    }

    wp_localize_script('wp-art-routes-edition-map-block', 'wpArtRoutesBlockData', [
        'editions' => $editions_options,
        'i18n' => [
            'blockTitle' => __('Edition Map', 'wp-art-routes'),
            'blockDescription' => __('Display an interactive map for an Edition', 'wp-art-routes'),
            'editionLabel' => __('Edition', 'wp-art-routes'),
            'autoDetect' => __('Auto-detect', 'wp-art-routes'),
            'showRoutes' => __('Show Routes', 'wp-art-routes'),
            'showLocations' => __('Show Locations', 'wp-art-routes'),
            'showInfoPoints' => __('Show Info Points', 'wp-art-routes'),
            'showLegend' => __('Show Legend', 'wp-art-routes'),
            'heightLabel' => __('Map Height', 'wp-art-routes'),
            'selectEdition' => __('Please select an Edition', 'wp-art-routes'),
        ],
    ]);

    // Register block
    register_block_type('wp-art-routes/edition-map', [
        'editor_script' => 'wp-art-routes-edition-map-block',
        'render_callback' => 'wp_art_routes_render_edition_map_block',
        'attributes' => [
            'editionId' => [
                'type' => 'number',
                'default' => 0,
            ],
            'showRoutes' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showLocations' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showInfoPoints' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showLegend' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'height' => [
                'type' => 'string',
                'default' => '500px',
            ],
        ],
    ]);
}
add_action('init', 'wp_art_routes_register_blocks');

/**
 * Server-side render callback for Edition Map block
 */
function wp_art_routes_render_edition_map_block($attributes) {
    $routes_param = $attributes['showRoutes'] ? 'all' : 'none';

    return wp_art_routes_edition_map_shortcode([
        'edition_id' => $attributes['editionId'],
        'routes' => $routes_param,
        'show_locations' => $attributes['showLocations'] ? 'true' : 'false',
        'show_info_points' => $attributes['showInfoPoints'] ? 'true' : 'false',
        'show_legend' => $attributes['showLegend'] ? 'true' : 'false',
        'height' => $attributes['height'],
    ]);
}
```

**Step 2: Create block editor script**

Create `assets/js/blocks/edition-map-block.js`:

```javascript
(function(wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
    const { Fragment, createElement } = wp.element;
    const ServerSideRender = wp.serverSideRender;

    const blockData = window.wpArtRoutesBlockData || {};
    const { editions = [], i18n = {} } = blockData;

    registerBlockType('wp-art-routes/edition-map', {
        title: i18n.blockTitle || 'Edition Map',
        description: i18n.blockDescription || 'Display an interactive map for an Edition',
        icon: 'location-alt',
        category: 'widgets',
        keywords: ['map', 'edition', 'route', 'art'],

        attributes: {
            editionId: {
                type: 'number',
                default: 0,
            },
            showRoutes: {
                type: 'boolean',
                default: true,
            },
            showLocations: {
                type: 'boolean',
                default: true,
            },
            showInfoPoints: {
                type: 'boolean',
                default: true,
            },
            showLegend: {
                type: 'boolean',
                default: true,
            },
            height: {
                type: 'string',
                default: '500px',
            },
        },

        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { editionId, showRoutes, showLocations, showInfoPoints, showLegend, height } = attributes;

            return createElement(
                Fragment,
                null,
                createElement(
                    InspectorControls,
                    null,
                    createElement(
                        PanelBody,
                        { title: i18n.blockTitle || 'Edition Map Settings', initialOpen: true },
                        createElement(SelectControl, {
                            label: i18n.editionLabel || 'Edition',
                            value: editionId,
                            options: editions,
                            onChange: function(value) {
                                setAttributes({ editionId: parseInt(value, 10) });
                            },
                        }),
                        createElement(TextControl, {
                            label: i18n.heightLabel || 'Map Height',
                            value: height,
                            onChange: function(value) {
                                setAttributes({ height: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: i18n.showRoutes || 'Show Routes',
                            checked: showRoutes,
                            onChange: function(value) {
                                setAttributes({ showRoutes: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: i18n.showLocations || 'Show Locations',
                            checked: showLocations,
                            onChange: function(value) {
                                setAttributes({ showLocations: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: i18n.showInfoPoints || 'Show Info Points',
                            checked: showInfoPoints,
                            onChange: function(value) {
                                setAttributes({ showInfoPoints: value });
                            },
                        }),
                        createElement(ToggleControl, {
                            label: i18n.showLegend || 'Show Legend',
                            checked: showLegend,
                            onChange: function(value) {
                                setAttributes({ showLegend: value });
                            },
                        })
                    )
                ),
                createElement(ServerSideRender, {
                    block: 'wp-art-routes/edition-map',
                    attributes: attributes,
                })
            );
        },

        save: function() {
            // Server-side rendered
            return null;
        },
    });
})(window.wp);
```

**Step 3: Add require to main plugin file**

In `wp-art-routes.php`, after the editions.php require, add:

```php
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/blocks.php';
```

**Step 4: Verify block appears in editor**

Run: Edit any page in Gutenberg → Search for "Edition Map" block → Should appear and be configurable.

**Step 5: Commit**

```bash
git add includes/blocks.php assets/js/blocks/edition-map-block.js wp-art-routes.php
git commit -m "feat: Add Edition Map Gutenberg block

Server-side rendered block with edition selection, route/location toggles."
```

---

## Task 11: Add Import/Export Admin Page (CSV Import)

**Files:**
- Create: `includes/import-export.php`
- Modify: `wp-art-routes.php`

**Step 1: Create import-export.php**

Create `includes/import-export.php`:

```php
<?php
/**
 * Import/Export Admin Page
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Import/Export page to admin menu
 */
function wp_art_routes_add_import_export_page() {
    add_submenu_page(
        'edit.php?post_type=edition',
        __('Import/Export', 'wp-art-routes'),
        __('Import/Export', 'wp-art-routes'),
        'manage_options',
        'wp-art-routes-import-export',
        'wp_art_routes_render_import_export_page'
    );
}
add_action('admin_menu', 'wp_art_routes_add_import_export_page');

/**
 * Render Import/Export page
 */
function wp_art_routes_render_import_export_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'import';
    $tabs = [
        'import' => __('Import', 'wp-art-routes'),
        'export' => __('Export', 'wp-art-routes'),
    ];

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['csv_import_nonce']) && wp_verify_nonce($_POST['csv_import_nonce'], 'csv_import')) {
            wp_art_routes_handle_csv_import();
        }
    }

    ?>
    <div class="wrap">
        <h1><?php _e('Import/Export', 'wp-art-routes'); ?></h1>

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                <a href="<?php echo esc_url(add_query_arg('tab', $tab_key, admin_url('edit.php?post_type=edition&page=wp-art-routes-import-export'))); ?>"
                   class="nav-tab <?php echo $current_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($tab_label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="tab-content" style="margin-top: 20px;">
            <?php
            if ($current_tab === 'export') {
                wp_art_routes_render_export_tab();
            } else {
                wp_art_routes_render_import_tab();
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Render Import tab
 */
function wp_art_routes_render_import_tab() {
    $editions = wp_art_routes_get_editions();
    ?>
    <div class="import-section">
        <h2><?php _e('CSV Import (Locations & Info Points)', 'wp-art-routes'); ?></h2>

        <p><?php _e('Import locations and information points from a CSV file.', 'wp-art-routes'); ?></p>

        <p>
            <a href="<?php echo esc_url(admin_url('admin-ajax.php?action=wp_art_routes_download_csv_template')); ?>" class="button">
                <?php _e('Download Template CSV', 'wp-art-routes'); ?>
            </a>
        </p>

        <form method="post" enctype="multipart/form-data" class="import-form">
            <?php wp_nonce_field('csv_import', 'csv_import_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="import_edition"><?php _e('Target Edition', 'wp-art-routes'); ?></label>
                    </th>
                    <td>
                        <select name="import_edition" id="import_edition" required>
                            <option value=""><?php _e('— Select Edition —', 'wp-art-routes'); ?></option>
                            <?php foreach ($editions as $edition) : ?>
                                <option value="<?php echo esc_attr($edition->ID); ?>">
                                    <?php echo esc_html($edition->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('All imported items will be assigned to this edition.', 'wp-art-routes'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="csv_file"><?php _e('CSV File', 'wp-art-routes'); ?></label>
                    </th>
                    <td>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                        <p class="description"><?php _e('Select a CSV file with columns: Type, Name, Description, Latitude, Longitude, Number, Icon, Creator', 'wp-art-routes'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Import CSV', 'wp-art-routes')); ?>
        </form>

        <hr>

        <h2><?php _e('GPX Import', 'wp-art-routes'); ?></h2>
        <p><?php _e('GPX import functionality will be added in a future update.', 'wp-art-routes'); ?></p>
    </div>
    <?php
}

/**
 * Render Export tab
 */
function wp_art_routes_render_export_tab() {
    $editions = wp_art_routes_get_editions();
    ?>
    <div class="export-section">
        <h2><?php _e('Export Edition Data', 'wp-art-routes'); ?></h2>

        <form method="get" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
            <input type="hidden" name="action" value="wp_art_routes_export_edition">
            <?php wp_nonce_field('edition_export', 'export_nonce'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="export_edition"><?php _e('Edition', 'wp-art-routes'); ?></label>
                    </th>
                    <td>
                        <select name="edition_id" id="export_edition" required>
                            <option value=""><?php _e('— Select Edition —', 'wp-art-routes'); ?></option>
                            <?php foreach ($editions as $edition) : ?>
                                <option value="<?php echo esc_attr($edition->ID); ?>">
                                    <?php echo esc_html($edition->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Export Format', 'wp-art-routes'); ?></th>
                    <td>
                        <label>
                            <input type="radio" name="export_format" value="csv" checked>
                            <?php _e('CSV (Locations & Info Points)', 'wp-art-routes'); ?>
                        </label><br>
                        <label>
                            <input type="radio" name="export_format" value="gpx">
                            <?php _e('GPX (Routes with waypoints)', 'wp-art-routes'); ?>
                        </label>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('Export', 'wp-art-routes'), 'primary', 'submit', true); ?>
        </form>
    </div>
    <?php
}

/**
 * Handle CSV import
 */
function wp_art_routes_handle_csv_import() {
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        add_settings_error('wp_art_routes_import', 'upload_error', __('Error uploading file.', 'wp-art-routes'), 'error');
        return;
    }

    $edition_id = isset($_POST['import_edition']) ? absint($_POST['import_edition']) : 0;
    if (!$edition_id) {
        add_settings_error('wp_art_routes_import', 'no_edition', __('Please select an edition.', 'wp-art-routes'), 'error');
        return;
    }

    $file = $_FILES['csv_file']['tmp_name'];
    $handle = fopen($file, 'r');

    if (!$handle) {
        add_settings_error('wp_art_routes_import', 'file_error', __('Could not read file.', 'wp-art-routes'), 'error');
        return;
    }

    // Read header row
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        add_settings_error('wp_art_routes_import', 'empty_file', __('File is empty.', 'wp-art-routes'), 'error');
        return;
    }

    // Normalize header
    $header = array_map('strtolower', array_map('trim', $header));

    // Required columns
    $required = ['type', 'name', 'latitude', 'longitude'];
    $missing = array_diff($required, $header);
    if (!empty($missing)) {
        fclose($handle);
        add_settings_error('wp_art_routes_import', 'missing_columns',
            sprintf(__('Missing required columns: %s', 'wp-art-routes'), implode(', ', $missing)), 'error');
        return;
    }

    $imported = 0;
    $errors = 0;
    $row_num = 1;

    while (($row = fgetcsv($handle)) !== false) {
        $row_num++;
        $data = array_combine($header, array_pad($row, count($header), ''));

        $type = strtolower(trim($data['type']));
        $name = trim($data['name']);
        $lat = floatval($data['latitude']);
        $lng = floatval($data['longitude']);

        if (empty($name) || !$lat || !$lng) {
            $errors++;
            continue;
        }

        // Determine post type
        $post_type = ($type === 'info_point' || $type === 'information_point') ? 'information_point' : 'artwork';

        // Create post
        $post_id = wp_insert_post([
            'post_title' => $name,
            'post_content' => isset($data['description']) ? $data['description'] : '',
            'post_type' => $post_type,
            'post_status' => 'draft', // Import as drafts for review
        ]);

        if (is_wp_error($post_id)) {
            $errors++;
            continue;
        }

        // Set meta
        update_post_meta($post_id, '_artwork_latitude', $lat);
        update_post_meta($post_id, '_artwork_longitude', $lng);
        update_post_meta($post_id, '_edition_id', $edition_id);

        if ($post_type === 'artwork') {
            if (!empty($data['number'])) {
                update_post_meta($post_id, '_artwork_number', sanitize_text_field($data['number']));
            }
            if (!empty($data['icon'])) {
                update_post_meta($post_id, '_artwork_icon', sanitize_text_field($data['icon']));
            }
        } else {
            if (!empty($data['icon'])) {
                update_post_meta($post_id, '_info_point_icon', sanitize_text_field($data['icon']));
            }
        }

        $imported++;
    }

    fclose($handle);

    if ($imported > 0) {
        add_settings_error('wp_art_routes_import', 'import_success',
            sprintf(__('Successfully imported %d items as drafts. %d errors.', 'wp-art-routes'), $imported, $errors), 'updated');
    } else {
        add_settings_error('wp_art_routes_import', 'import_failed',
            __('No items were imported. Please check your CSV format.', 'wp-art-routes'), 'error');
    }
}

/**
 * AJAX handler for CSV template download
 */
function wp_art_routes_download_csv_template() {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="import-template.csv"');

    $output = fopen('php://output', 'w');

    // Header row
    fputcsv($output, ['Type', 'Name', 'Description', 'Latitude', 'Longitude', 'Number', 'Icon', 'Creator']);

    // Example rows
    fputcsv($output, ['location', 'Example Artwork', 'Description of the artwork', '52.0907', '5.1214', 'A1', 'icon.svg', 'Artist Name']);
    fputcsv($output, ['info_point', 'Parking Area', 'Free parking available', '52.0910', '5.1220', '', '', '']);

    fclose($output);
    exit;
}
add_action('wp_ajax_wp_art_routes_download_csv_template', 'wp_art_routes_download_csv_template');

/**
 * AJAX handler for edition export
 */
function wp_art_routes_export_edition() {
    if (!current_user_can('manage_options')) {
        wp_die(__('Unauthorized', 'wp-art-routes'));
    }

    if (!isset($_GET['export_nonce']) || !wp_verify_nonce($_GET['export_nonce'], 'edition_export')) {
        wp_die(__('Security check failed', 'wp-art-routes'));
    }

    $edition_id = isset($_GET['edition_id']) ? absint($_GET['edition_id']) : 0;
    $format = isset($_GET['export_format']) ? sanitize_key($_GET['export_format']) : 'csv';

    if (!$edition_id) {
        wp_die(__('Please select an edition', 'wp-art-routes'));
    }

    $edition = get_post($edition_id);
    if (!$edition) {
        wp_die(__('Edition not found', 'wp-art-routes'));
    }

    if ($format === 'csv') {
        wp_art_routes_export_edition_csv($edition_id, $edition->post_title);
    } else {
        wp_art_routes_export_edition_gpx($edition_id, $edition->post_title);
    }

    exit;
}
add_action('wp_ajax_wp_art_routes_export_edition', 'wp_art_routes_export_edition');

/**
 * Export edition as CSV
 */
function wp_art_routes_export_edition_csv($edition_id, $edition_title) {
    $filename = sanitize_file_name($edition_title) . '-export.csv';

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // BOM for Excel UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header
    fputcsv($output, ['Type', 'Name', 'Description', 'Latitude', 'Longitude', 'Number', 'Icon', 'Artists']);

    // Artworks
    $artworks = wp_art_routes_get_edition_artworks($edition_id);
    foreach ($artworks as $artwork) {
        $artist_names = array_map(function($a) { return $a['title']; }, $artwork['artists']);
        fputcsv($output, [
            'location',
            $artwork['title'],
            wp_strip_all_tags($artwork['description']),
            $artwork['latitude'],
            $artwork['longitude'],
            $artwork['number'],
            '', // icon
            implode('; ', $artist_names),
        ]);
    }

    // Info Points
    $info_points = wp_art_routes_get_edition_information_points($edition_id);
    foreach ($info_points as $info_point) {
        fputcsv($output, [
            'info_point',
            $info_point['title'],
            wp_strip_all_tags($info_point['excerpt']),
            $info_point['latitude'],
            $info_point['longitude'],
            '',
            '',
            '',
        ]);
    }

    fclose($output);
}

/**
 * Export edition as GPX
 */
function wp_art_routes_export_edition_gpx($edition_id, $edition_title) {
    $filename = sanitize_file_name($edition_title) . '-export.gpx';

    header('Content-Type: application/gpx+xml');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $routes = wp_art_routes_get_edition_routes($edition_id);
    $artworks = wp_art_routes_get_edition_artworks($edition_id);
    $info_points = wp_art_routes_get_edition_information_points($edition_id);

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<gpx version="1.1" creator="WP Art Routes" xmlns="http://www.topografix.com/GPX/1/1">' . "\n";
    echo '<metadata><name>' . esc_html($edition_title) . '</name><time>' . gmdate('c') . '</time></metadata>' . "\n";

    // Waypoints for artworks
    foreach ($artworks as $artwork) {
        echo '<wpt lat="' . esc_attr($artwork['latitude']) . '" lon="' . esc_attr($artwork['longitude']) . '">';
        echo '<name>' . esc_html($artwork['title']) . '</name>';
        echo '<type>Artwork</type>';
        echo '</wpt>' . "\n";
    }

    // Waypoints for info points
    foreach ($info_points as $info_point) {
        echo '<wpt lat="' . esc_attr($info_point['latitude']) . '" lon="' . esc_attr($info_point['longitude']) . '">';
        echo '<name>' . esc_html($info_point['title']) . '</name>';
        echo '<type>Info Point</type>';
        echo '</wpt>' . "\n";
    }

    // Routes
    foreach ($routes as $route) {
        if (empty($route['route_path'])) {
            continue;
        }
        echo '<trk>';
        echo '<name>' . esc_html($route['title']) . '</name>';
        echo '<trkseg>';
        foreach ($route['route_path'] as $point) {
            $lat = is_array($point) && isset($point['lat']) ? $point['lat'] : (is_array($point) ? $point[0] : 0);
            $lng = is_array($point) && isset($point['lng']) ? $point['lng'] : (is_array($point) ? $point[1] : 0);
            echo '<trkpt lat="' . esc_attr($lat) . '" lon="' . esc_attr($lng) . '"></trkpt>';
        }
        echo '</trkseg>';
        echo '</trk>' . "\n";
    }

    echo '</gpx>';
}
```

**Step 2: Add require to main plugin file**

In `wp-art-routes.php`, after blocks.php require, add:

```php
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/import-export.php';
```

**Step 3: Verify import/export page works**

Run: Visit Editions → Import/Export → Both tabs should appear with forms.

**Step 4: Commit**

```bash
git add includes/import-export.php wp-art-routes.php
git commit -m "feat: Add Import/Export admin page

CSV import for locations/info points, CSV and GPX export for editions."
```

---

## Task 12: Final Integration and Testing

**Step 1: Flush rewrite rules**

Add to `includes/editions.php` activation code or manually flush:

```bash
# In WordPress admin: Settings → Permalinks → Save (to flush rewrites)
```

**Step 2: Manual testing checklist**

1. [ ] Create a new Edition with terminology overrides
2. [ ] Create Routes, assign to Edition
3. [ ] Create Artworks, assign to Edition
4. [ ] Create Info Points, assign to Edition
5. [ ] Visit Edition single page - map and content should display
6. [ ] Add Edition Map block to a page - should render
7. [ ] Use [edition_map] shortcode - should render
8. [ ] Import CSV file - items created as drafts
9. [ ] Export Edition as CSV - downloads file
10. [ ] Export Edition as GPX - downloads file
11. [ ] Filter admin lists by Edition - should filter correctly

**Step 3: Final commit**

```bash
git add -A
git commit -m "feat: Complete Editions system implementation

- Edition CPT with terminology overrides
- _edition_id linking for Routes, Locations, Info Points
- Admin menu restructure under Editions
- Edition Map block and shortcode
- Edition single page template
- CSV import/export
- GPX export"
```

---

## Summary

This plan implements the Editions system in 12 tasks:

1. Terminology system foundation
2. Edition CPT
3. Edition linking to content
4. Admin list table columns/filters
5. Menu restructure
6. Terminology settings tab
7. Edition-filtered data functions
8. Edition single template
9. Edition map shortcode
10. Edition map Gutenberg block
11. Import/Export page
12. Final integration

Each task is incremental and testable. The system maintains backwards compatibility - existing content works without editions assigned.
