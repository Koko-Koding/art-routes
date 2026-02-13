<?php

/**
 * Register Custom Post Types and Taxonomies
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types for routes and artworks
 */
function art_routes_register_post_types()
{
    // Register Routes post type
    register_post_type('art_route', [
        'labels' => [
            'name' => __('Routes', 'art-routes'),
            'singular_name' => __('Route', 'art-routes'),
            'add_new' => __('Add New Route', 'art-routes'),
            'add_new_item' => __('Add New Route', 'art-routes'),
            'edit_item' => __('Edit Route', 'art-routes'),
            'view_item' => __('View Route', 'art-routes'),
            'search_items' => __('Search Routes', 'art-routes'),
            'not_found' => __('No routes found', 'art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-location-alt',
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'art-route'],
        'show_in_menu' => 'edit.php?post_type=edition',
    ]);

    // Register Artworks post type
    register_post_type('artwork', [
        'labels' => [
            'name' => __('Artworks', 'art-routes'),
            'singular_name' => __('Artwork', 'art-routes'),
            'add_new' => __('Add New Artwork', 'art-routes'),
            'add_new_item' => __('Add New Artwork', 'art-routes'),
            'edit_item' => __('Edit Artwork', 'art-routes'),
            'view_item' => __('View Artwork', 'art-routes'),
            'search_items' => __('Search Artworks', 'art-routes'),
            'not_found' => __('No artworks found', 'art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-format-image',
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'artwork'],
        'show_in_menu' => 'edit.php?post_type=edition', // Add under the main Editions menu
    ]);

    // Register Information Points post type
    register_post_type('information_point', [
        'labels' => [
            'name' => __('Information Points', 'art-routes'),
            'singular_name' => __('Information Point', 'art-routes'),
            'add_new' => __('Add New Info Point', 'art-routes'),
            'add_new_item' => __('Add New Info Point', 'art-routes'),
            'edit_item' => __('Edit Info Point', 'art-routes'),
            'view_item' => __('View Info Point', 'art-routes'),
            'search_items' => __('Search Info Points', 'art-routes'),
            'not_found' => __('No info points found', 'art-routes'),
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'menu_icon' => 'dashicons-info', // Use an info icon
        'show_in_rest' => true,
        'rewrite' => ['slug' => 'info-point'],
        'show_in_menu' => 'edit.php?post_type=edition', // Add under the main Editions menu
    ]);
}
add_action('init', 'art_routes_register_post_types');

/**
 * Register REST API meta for artworks and information points
 */
function art_routes_register_artwork_meta()
{
    // Register meta for artwork post type
    register_post_meta('artwork', '_artwork_latitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('artwork', '_artwork_longitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Register new meta fields for artwork number and location
    register_post_meta('artwork', '_artwork_number', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('artwork', '_artwork_location', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Register artwork icon field (filename)
    register_post_meta('artwork', '_artwork_icon', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);

    // Register accessibility meta fields for artworks
    register_post_meta('artwork', '_wheelchair_accessible', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('artwork', '_stroller_accessible', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'art_routes_register_artwork_meta');

/**
 * Register _edition_id meta for all content post types
 */
function art_routes_register_edition_id_meta() {
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
add_action('init', 'art_routes_register_edition_id_meta');

/**
 * Register REST API meta for information points
 */
function art_routes_register_information_point_meta()
{
    register_post_meta('information_point', '_artwork_latitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    register_post_meta('information_point', '_artwork_longitude', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    // Register the new icon field (filename instead of URL)
    register_post_meta('information_point', '_info_point_icon', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
    // Keep the old field for backward compatibility during transition
    register_post_meta('information_point', '_info_point_icon_url', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ]);
}
add_action('init', 'art_routes_register_information_point_meta');

/**
 * Register REST fields for artwork meta data
 */
function art_routes_register_artwork_rest_fields()
{
    register_rest_field('artwork', 'latitude', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_latitude', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_latitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork latitude coordinate', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'longitude', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_longitude', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_longitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork longitude coordinate', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'number', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_number', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_number', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork number', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('artwork', 'location', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_location', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_location', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork location description', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Artwork icon field (filename)
    register_rest_field('artwork', 'icon', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_icon', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_icon', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Artwork icon filename', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Icon URL (computed from filename)
    register_rest_field('artwork', 'icon_url', [
        'get_callback' => function ($post) {
            $icon_filename = get_post_meta($post['id'], '_artwork_icon', true);
            if (!empty($icon_filename)) {
                return art_routes_get_icon_url($icon_filename);
            }
            // No default icon for artworks - they will use their featured image or a generic marker
            return '';
        },
        'schema' => [
            'description' => __('Artwork icon URL', 'art-routes'),
            'type' => 'string',
            'format' => 'uri',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Accessibility fields (expose as non-underscore fields for frontend)
    register_rest_field('artwork', 'wheelchair_accessible', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_wheelchair_accessible', true);
        },
        'schema' => [
            'description' => __('Wheelchair accessible', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
    register_rest_field('artwork', 'stroller_accessible', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_stroller_accessible', true);
        },
        'schema' => [
            'description' => __('Stroller accessible', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);
}
add_action('rest_api_init', 'art_routes_register_artwork_rest_fields');

/**
 * Register REST fields for information point meta data
 */
function art_routes_register_information_point_rest_fields()
{
    register_rest_field('information_point', 'latitude', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_latitude', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_latitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point latitude coordinate', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    register_rest_field('information_point', 'longitude', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_artwork_longitude', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_artwork_longitude', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point longitude coordinate', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // New icon field (filename)
    register_rest_field('information_point', 'icon', [
        'get_callback' => function ($post) {
            return get_post_meta($post['id'], '_info_point_icon', true);
        },
        'update_callback' => function ($value, $post) {
            return update_post_meta($post->ID, '_info_point_icon', sanitize_text_field($value));
        },
        'schema' => [
            'description' => __('Information point icon filename', 'art-routes'),
            'type' => 'string',
            'context' => ['view', 'edit'],
        ],
    ]);

    // Icon URL (computed from filename)
    register_rest_field('information_point', 'icon_url', [
        'get_callback' => function ($post) {
            $icon_filename = get_post_meta($post['id'], '_info_point_icon', true);
            if (!empty($icon_filename)) {
                return art_routes_get_icon_url($icon_filename);
            }
            // Fallback to old icon_url field for backward compatibility
            $old_icon_url = get_post_meta($post['id'], '_info_point_icon_url', true);
            if (!empty($old_icon_url)) {
                return $old_icon_url;
            }
            // Default icon if no icon is set
            return art_routes_get_icon_url(art_routes_get_default_info_icon());
        },
        'schema' => [
            'description' => __('Information point icon URL', 'art-routes'),
            'type' => 'string',
            'format' => 'uri',
            'context' => ['view', 'edit'],
        ],
    ]);
}
add_action('rest_api_init', 'art_routes_register_information_point_rest_fields');

// Add custom column to show artwork number in admin list
add_filter('manage_artwork_posts_columns', function ($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['artwork_number'] = __('Number', 'art-routes');
            $new_columns['artwork_artists'] = __('Artists', 'art-routes');
        }
    }
    return $new_columns;
});

add_action('manage_artwork_posts_custom_column', function ($column, $post_id) {
    if ($column === 'artwork_number') {
        $number = get_post_meta($post_id, '_artwork_number', true);
        echo esc_html($number);
    } elseif ($column === 'artwork_artists') {
        $artist_ids = get_post_meta($post_id, '_artwork_artist_ids', true);
        if (!is_array($artist_ids)) {
            $artist_ids = empty($artist_ids) ? array() : array($artist_ids);
        }
        $artist_titles = [];
        foreach ($artist_ids as $artist_id) {
            $artist_post = get_post($artist_id);
            if ($artist_post) {
                $artist_titles[] = esc_html($artist_post->post_title);
            }
        }
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $artist_titles items are escaped with esc_html() when added to array
        echo implode(', ', $artist_titles);
    }
}, 10, 2);

// Make the artwork number column sortable
add_filter('manage_edit-artwork_sortable_columns', function ($columns) {
    $columns['artwork_number'] = 'artwork_number';
    return $columns;
});

add_action('pre_get_posts', function ($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    $orderby = $query->get('orderby');
    if ('artwork_number' === $orderby && $query->get('post_type') === 'artwork') {
        $query->set('meta_key', '_artwork_number');
        $query->set('orderby', 'meta_value');
    }
});

/**
 * Add Edition column to admin list tables for routes, artworks, and info points
 *
 * @param array $columns Existing columns.
 * @return array Modified columns.
 */
function art_routes_add_edition_column($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        if ($key === 'title') {
            $new_columns['edition'] = __('Edition', 'art-routes');
        }
    }
    return $new_columns;
}
add_filter('manage_art_route_posts_columns', 'art_routes_add_edition_column');
add_filter('manage_information_point_posts_columns', 'art_routes_add_edition_column');

// For artwork, we need to insert edition after the existing title-related columns
add_filter('manage_artwork_posts_columns', function($columns) {
    $new_columns = [];
    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;
        // Insert edition after artwork_artists (which comes after title)
        if ($key === 'artwork_artists') {
            $new_columns['edition'] = __('Edition', 'art-routes');
        }
    }
    return $new_columns;
}, 11); // Priority 11 to run after the artwork_number/artwork_artists columns are added

/**
 * Render Edition column content
 *
 * @param string $column  Column name.
 * @param int    $post_id Post ID.
 */
function art_routes_render_edition_column($column, $post_id) {
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
            echo '<span style="color:#999;">— ' . esc_html__('None', 'art-routes') . ' —</span>';
        }
    }
}
add_action('manage_art_route_posts_custom_column', 'art_routes_render_edition_column', 10, 2);
add_action('manage_artwork_posts_custom_column', 'art_routes_render_edition_column', 10, 2);
add_action('manage_information_point_posts_custom_column', 'art_routes_render_edition_column', 10, 2);

/**
 * Add Edition filter dropdown to admin list tables
 */
function art_routes_add_edition_filter() {
    global $typenow;

    if (!in_array($typenow, ['art_route', 'artwork', 'information_point'])) {
        return;
    }

    $editions = art_routes_get_editions();
    $current_edition = isset($_GET['edition_filter']) ? absint(wp_unslash($_GET['edition_filter'])) : 0;

    ?>
    <select name="edition_filter">
        <option value="0"><?php esc_html_e('All Editions', 'art-routes'); ?></option>
        <option value="-1" <?php selected($current_edition, -1); ?>><?php esc_html_e('No Edition', 'art-routes'); ?></option>
        <?php foreach ($editions as $edition) : ?>
            <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($current_edition, $edition->ID); ?>>
                <?php echo esc_html($edition->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}
add_action('restrict_manage_posts', 'art_routes_add_edition_filter');

/**
 * Filter admin list queries by edition
 *
 * @param WP_Query $query The query object.
 */
function art_routes_filter_by_edition($query) {
    global $pagenow, $typenow;

    if (!is_admin() || $pagenow !== 'edit.php') {
        return;
    }

    if (!in_array($typenow, ['art_route', 'artwork', 'information_point'])) {
        return;
    }

    if (!isset($_GET['edition_filter']) || wp_unslash($_GET['edition_filter']) === '0') {
        return;
    }

    $edition_filter = intval(wp_unslash($_GET['edition_filter']));

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
add_action('pre_get_posts', 'art_routes_filter_by_edition');
