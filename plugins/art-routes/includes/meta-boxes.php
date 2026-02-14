<?php

/**
 * Meta Boxes for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register meta boxes for routes and artworks
 */
function art_routes_add_meta_boxes()
{
    // Route Details meta box
    add_meta_box(
        'art_route_details',
        __('Route Details', 'art-routes'),
        'art_routes_render_route_details_meta_box',
        'artro_route',
        'normal',
        'high'
    );

    // Route Path meta box
    add_meta_box(
        'art_route_path',
        __('Route Path', 'art-routes'),
        'art_routes_render_route_path_meta_box',
        'artro_route',
        'normal',
        'high'
    );

    // Artwork Location meta box
    add_meta_box(
        'artwork_location',
        __('Artwork Location', 'art-routes'),
        'art_routes_render_artwork_location_meta_box',
        'artro_artwork',
        'normal',
        'high'
    );

    // Info Point Location meta box (reuses artwork location rendering)
    add_meta_box(
        'info_point_location',
        __('Info Point Location', 'art-routes'),
        'art_routes_render_info_point_location_meta_box', // Use dedicated function for info points
        'artro_info_point', // Apply to the new CPT
        'normal',
        'high'
    );

    // Artwork Artist Association meta box
    add_meta_box(
        'artwork_artists',
        __('Artist(s)', 'art-routes'),
        'art_routes_render_artwork_artists_meta_box',
        'artro_artwork',
        'normal',
        'default'
    );

    // Artwork Icon meta box
    add_meta_box(
        'artwork_icon',
        __('Artwork Icon', 'art-routes'),
        'art_routes_render_artwork_icon_meta_box',
        'artro_artwork',
        'side',
        'default'
    );

    // Info Point Icon meta box
    add_meta_box(
        'info_point_icon',
        __('Info Point Icon', 'art-routes'),
        'art_routes_render_info_point_icon_meta_box',
        'artro_info_point',
        'side',
        'default'
    );

    // Route Icon meta box
    add_meta_box(
        'route_icon',
        __('Route Icon', 'art-routes'),
        'art_routes_render_route_icon_meta_box',
        'artro_route',
        'side',
        'default'
    );

    // Edition selector for all content types
    $edition_post_types = ['artro_route', 'artro_artwork', 'artro_info_point'];
    foreach ($edition_post_types as $post_type) {
        add_meta_box(
            'edition_selector',
            __('Edition', 'art-routes'),
            'art_routes_render_edition_selector_meta_box',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'art_routes_add_meta_boxes');

/**
 * Render Route Details meta box
 */
function art_routes_render_route_details_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_route_details', 'route_details_nonce');

    // Get saved values
    $length = get_post_meta($post->ID, '_route_length', true);
    $duration = get_post_meta($post->ID, '_route_duration', true);
    $type = get_post_meta($post->ID, '_route_type', true);
    $show_completed_route = get_post_meta($post->ID, '_show_completed_route', true);
    $show_artwork_toasts = get_post_meta($post->ID, '_show_artwork_toasts', true);

    // Default to true if not set
    if ($show_completed_route === '') {
        $show_completed_route = '1';
    }

    // Default to true if not set
    if ($show_artwork_toasts === '') {
        $show_artwork_toasts = '1';
    }

    // Route types
    $route_types = [
        'walking' => __('Walking route', 'art-routes'),
        'cycling' => __('Bicycle route', 'art-routes'),
        'wheelchair' => __('Wheelchair friendly', 'art-routes'),
        'children' => __('Child-friendly route', 'art-routes'),
    ];

?>
    <p>
        <label for="route_length">
            <?php esc_html_e('Route Length (km)', 'art-routes'); ?>:
        </label>
        <input type="number" id="route_length" name="route_length" value="<?php echo esc_attr($length); ?>" step="0.01" min="0" style="width: 100px;" />
    </p>

    <p>
        <label for="route_duration">
            <?php esc_html_e('Duration (minutes)', 'art-routes'); ?>:
        </label>
        <input type="number" id="route_duration" name="route_duration" value="<?php echo esc_attr($duration); ?>" min="0" style="width: 100px;" />
        <button type="button" id="calculate-duration" class="button button-secondary" style="margin-left: 10px;" title="<?php esc_html_e('Calculate estimated duration based on route distance and type', 'art-routes'); ?>">
            <?php esc_html_e('Calculate', 'art-routes'); ?>
        </button>
    </p>

    <p>
        <label for="route_type">
            <?php esc_html_e('Route Type', 'art-routes'); ?>:
        </label>
        <select id="route_type" name="route_type">
            <option value=""><?php esc_html_e('Select Type', 'art-routes'); ?></option>
            <?php foreach ($route_types as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($type, $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="show_completed_route">
            <input type="checkbox" id="show_completed_route" name="show_completed_route" value="1" <?php checked($show_completed_route, '1'); ?> />
            <?php esc_html_e('Show completed route path', 'art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php esc_html_e('When checked, users will see which part of the route they have already traversed.', 'art-routes'); ?></span>
    </p>

    <p>
        <label for="show_artwork_toasts">
            <input type="checkbox" id="show_artwork_toasts" name="show_artwork_toasts" value="1" <?php checked($show_artwork_toasts, '1'); ?> />
            <?php esc_html_e('Show artwork notifications', 'art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php esc_html_e('When checked, users will receive a notification when they pass near an artwork.', 'art-routes'); ?></span>
    </p>
<?php
}

/**
 * Render Route Path meta box
 */
function art_routes_render_route_path_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_route_path', 'route_path_nonce');

    // Get saved path
    $path = get_post_meta($post->ID, '_route_path', true);

    // Instructions
    echo '<p>' . esc_html__('Enter the route path as a series of latitude,longitude points. One point per line.', 'art-routes') . '</p>';
    echo '<p>' . esc_html__('Example: 52.3702, 4.8952', 'art-routes') . '</p>';

    // Text area for path
    echo '<textarea id="route_path" name="route_path" rows="10" class="large-text">' . esc_textarea($path) . '</textarea>';

    // Add button to open map modal
    echo '<p><button type="button" class="button" id="open_route_map">' . esc_html__('Use Map to Create Route', 'art-routes') . '</button></p>';
}

/**
 * Render Artwork Location meta box
 */
function art_routes_render_artwork_location_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_artwork_location', 'artwork_location_nonce');

    // Get saved values
    $latitude = get_post_meta($post->ID, '_artwork_latitude', true);
    $longitude = get_post_meta($post->ID, '_artwork_longitude', true);
    $number = get_post_meta($post->ID, '_artwork_number', true);
    $location = get_post_meta($post->ID, '_artwork_location', true);
    $wheelchair_accessible = get_post_meta($post->ID, '_wheelchair_accessible', true);
    $stroller_accessible = get_post_meta($post->ID, '_stroller_accessible', true);

?>
    <p>
        <label for="artwork_number">
            <?php esc_html_e('Number', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_number" name="artwork_number" value="<?php echo esc_attr($number); ?>" class="regular-text" />
        <span class="description"><?php esc_html_e('Optional artwork number for identification', 'art-routes'); ?></span>
    </p>

    <p>
        <label for="artwork_location">
            <?php esc_html_e('Location', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_location" name="artwork_location" value="<?php echo esc_attr($location); ?>" class="regular-text" />
        <span class="description"><?php esc_html_e('Optional location description (e.g., "Near the town square")', 'art-routes'); ?></span>
    </p>

    <p>
        <label for="wheelchair_accessible">
            <input type="checkbox" id="wheelchair_accessible" name="wheelchair_accessible" value="1" <?php checked($wheelchair_accessible, '1'); ?> />
            <?php esc_html_e('Wheelchair accessible', 'art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php esc_html_e('Check if this artwork is accessible by wheelchair.', 'art-routes'); ?></span>
    </p>
    <p>
        <label for="stroller_accessible">
            <input type="checkbox" id="stroller_accessible" name="stroller_accessible" value="1" <?php checked($stroller_accessible, '1'); ?> />
            <?php esc_html_e('Stroller accessible', 'art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php esc_html_e('Check if this artwork is accessible by stroller.', 'art-routes'); ?></span>
    </p>

    <p>
        <label for="artwork_latitude">
            <?php esc_html_e('Latitude', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_latitude" name="artwork_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" />
    </p>

    <p>
        <label for="artwork_longitude">
            <?php esc_html_e('Longitude', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_longitude" name="artwork_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" />
    </p>

    <div id="artwork_location_map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
    <p><button type="button" class="button" id="pick_artwork_location"><?php esc_html_e('Pick Location on Map', 'art-routes'); ?></button></p>
<?php
}

/**
 * Render Info Point Location meta box
 */
function art_routes_render_info_point_location_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_artwork_location', 'artwork_location_nonce');

    // Get saved values
    $latitude = get_post_meta($post->ID, '_artwork_latitude', true);
    $longitude = get_post_meta($post->ID, '_artwork_longitude', true);

?>
    <p>
        <label for="artwork_latitude">
            <?php esc_html_e('Latitude', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_latitude" name="artwork_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" />
    </p>

    <p>
        <label for="artwork_longitude">
            <?php esc_html_e('Longitude', 'art-routes'); ?>:
        </label>
        <input type="text" id="artwork_longitude" name="artwork_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" />
    </p>

    <div id="artwork_location_map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
    <p><button type="button" class="button" id="pick_artwork_location"><?php esc_html_e('Pick Location on Map', 'art-routes'); ?></button></p>
<?php
}

/**
 * Render Artwork Artist Association meta box
 */
function art_routes_render_artwork_artists_meta_box($post)
{
    // Add nonce for security
    wp_nonce_field('save_artwork_artists', 'artwork_artists_nonce');

    // Get saved artist associations
    $artist_ids = get_post_meta($post->ID, '_artwork_artist_ids', true);

    if (!is_array($artist_ids)) {
        $artist_ids = empty($artist_ids) ? array() : array($artist_ids);
    }

    // Get all available post types except some internal ones
    $excluded_post_types = array('revision', 'attachment', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'artro_route', 'artro_artwork');
    $post_types = get_post_types(array('public' => true), 'objects');

?>
    <div class="artist-association-container">
        <p><?php esc_html_e('Connect this artwork to one or more posts representing the artist(s).', 'art-routes'); ?></p>

        <div class="artist-search">
            <label for="artist_search"><?php esc_html_e('Search for content:', 'art-routes'); ?></label>
            <input type="text" id="artist_search" placeholder="<?php esc_attr_e('Start typing to search...', 'art-routes'); ?>" class="regular-text" />

            <div class="post-type-filter">
                <label><?php esc_html_e('Filter by post type:', 'art-routes'); ?></label>
                <select id="post_type_filter">
                    <option value=""><?php esc_html_e('All post types', 'art-routes'); ?></option>
                    <?php foreach ($post_types as $type) :
                        if (!in_array($type->name, $excluded_post_types)) : ?>
                            <option value="<?php echo esc_attr($type->name); ?>">
                                <?php echo esc_html($type->labels->singular_name); ?>
                            </option>
                    <?php endif;
                    endforeach; ?>
                </select>
            </div>
        </div>

        <div class="selected-artists">
            <h4><?php esc_html_e('Selected Artist(s):', 'art-routes'); ?></h4>
            <ul id="selected_artists_list">
                <?php
                if (!empty($artist_ids)) {
                    foreach ($artist_ids as $artist_id) {
                        $artist = get_post($artist_id);
                        if ($artist) {
                            $post_type_obj = get_post_type_object($artist->post_type);
                            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : $artist->post_type;

                            echo '<li data-id="' . esc_attr($artist_id) . '">';
                            echo '<span class="artist-title">' . esc_html($artist->post_title) . '</span>';
                            echo ' <span class="post-type-label">(' . esc_html($post_type_label) . ')</span>';
                            echo ' <a href="#" class="remove-artist">' . esc_html__('Remove', 'art-routes') . '</a>';
                            echo '<input type="hidden" name="artwork_artist_ids[]" value="' . esc_attr($artist_id) . '">';
                            echo '</li>';
                        }
                    }
                }
                ?>
            </ul>
            <p class="description"><?php esc_html_e('These posts will be associated with this artwork as artists.', 'art-routes'); ?></p>
        </div>
    </div>
<?php
}

/**
 * Render Artwork Icon meta box
 */
function art_routes_render_artwork_icon_meta_box($post)
{
    wp_nonce_field('save_artwork_icon', 'artwork_icon_nonce');

    // Get the currently selected icon
    $selected_icon = get_post_meta($post->ID, '_artwork_icon', true);

    // Get available icons (includes both built-in and custom uploaded)
    $available_icons = art_routes_get_available_icons();

    // Build icon URL map for JavaScript
    $icon_urls = [];
    foreach ($available_icons as $icon_file) {
        $icon_urls[$icon_file] = art_routes_get_icon_url($icon_file);
    }

?>
    <div id="artwork-icon-meta-box">
        <p>
            <label for="artwork_icon_select">
                <?php esc_html_e('Select Icon:', 'art-routes'); ?>
            </label>
        </p>

        <select id="artwork_icon_select" name="artwork_icon" style="width: 100%;">
            <option value=""><?php esc_html_e('-- No Icon --', 'art-routes'); ?></option>
            <?php foreach ($available_icons as $icon_file) : ?>
                <option value="<?php echo esc_attr($icon_file); ?>" <?php selected($selected_icon, $icon_file); ?>>
                    <?php echo esc_html(art_routes_get_icon_display_name($icon_file)); ?> (<?php echo esc_html($icon_file); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <div id="icon-preview-container" style="margin-top: 15px;">
            <?php if ($selected_icon && in_array($selected_icon, $available_icons, true)) : ?>
                <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                    <img id="icon-preview" src="<?php echo esc_url(art_routes_get_icon_url($selected_icon)); ?>"
                        style="width: 40px; height: 40px; object-fit: contain;"
                        alt="<?php echo esc_attr($selected_icon); ?>" />
                </div>
            <?php else : ?>
                <div id="icon-preview" style="display: none;">
                    <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                    <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                        <img style="width: 40px; height: 40px; object-fit: contain;" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Push icon config for the shared icon-preview.js
    wp_add_inline_script('art-routes-icon-preview-js',
        'var artRoutesIconConfigs = artRoutesIconConfigs || [];' .
        'artRoutesIconConfigs.push(' . wp_json_encode([
            'selectId'  => 'artwork_icon_select',
            'previewId' => 'icon-preview',
            'iconUrls'  => $icon_urls,
        ]) . ');',
        'before'
    );
    ?>

    <p class="description">
        <?php esc_html_e('Select an icon for this artwork. The icon will be displayed as a marker on the map.', 'art-routes'); ?>
    </p>
<?php
}

/**
 * Render Info Point Icon meta box
 */
function art_routes_render_info_point_icon_meta_box($post)
{
    wp_nonce_field('save_info_point_icon', 'info_point_icon_nonce');

    // Get the currently selected icon
    $selected_icon = get_post_meta($post->ID, '_info_point_icon', true);

    // Get available icons (includes both built-in and custom uploaded)
    $available_icons = art_routes_get_available_icons();

    // Build icon URL map for JavaScript
    $icon_urls = [];
    foreach ($available_icons as $icon_file) {
        $icon_urls[$icon_file] = art_routes_get_icon_url($icon_file);
    }

?>
    <div id="info-point-icon-meta-box">
        <p>
            <label for="info_point_icon_select">
                <?php esc_html_e('Select Icon:', 'art-routes'); ?>
            </label>
        </p>

        <select id="info_point_icon_select" name="info_point_icon" style="width: 100%;">
            <option value=""><?php esc_html_e('-- No Icon --', 'art-routes'); ?></option>
            <?php foreach ($available_icons as $icon_file) : ?>
                <option value="<?php echo esc_attr($icon_file); ?>" <?php selected($selected_icon, $icon_file); ?>>
                    <?php echo esc_html(art_routes_get_icon_display_name($icon_file)); ?> (<?php echo esc_html($icon_file); ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <div id="icon-preview-container" style="margin-top: 15px;">
            <?php if ($selected_icon && in_array($selected_icon, $available_icons, true)) : ?>
                <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                    <img id="icon-preview" src="<?php echo esc_url(art_routes_get_icon_url($selected_icon)); ?>"
                        style="width: 40px; height: 40px; object-fit: contain;"
                        alt="<?php echo esc_attr($selected_icon); ?>" />
                </div>
            <?php else : ?>
                <div id="icon-preview" style="display: none;">
                    <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                    <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                        <img style="width: 40px; height: 40px; object-fit: contain;" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php
    // Push icon config for the shared icon-preview.js
    wp_add_inline_script('art-routes-icon-preview-js',
        'var artRoutesIconConfigs = artRoutesIconConfigs || [];' .
        'artRoutesIconConfigs.push(' . wp_json_encode([
            'selectId'  => 'info_point_icon_select',
            'previewId' => 'icon-preview',
            'iconUrls'  => $icon_urls,
        ]) . ');',
        'before'
    );
    ?>

    <p class="description">
        <?php esc_html_e('Select an icon for this information point. The icon will be displayed as a marker on the map.', 'art-routes'); ?>
    </p>
<?php
}

/**
 * Render Route Icon meta box
 */
function art_routes_render_route_icon_meta_box($post)
{
    wp_nonce_field('save_route_icon', 'route_icon_nonce');

    // Get the currently selected icon
    $selected_icon = get_post_meta($post->ID, '_route_icon', true);

    // Only allow these route marker icons
    $allowed_icons = [
        'start.svg',
        'end.svg',
        'marker.svg',
    ];

    // Build icon URL map for JavaScript
    $icon_urls = [];
    foreach ($allowed_icons as $icon_file) {
        $icon_urls[$icon_file] = art_routes_get_icon_url($icon_file);
    }

?>
    <div id="route-icon-meta-box">
        <p>
            <label for="route_icon_select">
                <?php esc_html_e('Select Icon:', 'art-routes'); ?>
            </label>
        </p>
        <select id="route_icon_select" name="route_icon" style="width: 100%;">
            <option value=""><?php esc_html_e('-- No Icon --', 'art-routes'); ?></option>
            <?php foreach ($allowed_icons as $icon_file) : ?>
                <option value="<?php echo esc_attr($icon_file); ?>" <?php selected($selected_icon, $icon_file); ?>>
                    <?php echo esc_html(art_routes_get_icon_display_name($icon_file)); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="route-icon-preview-container" style="margin-top: 15px;">
            <?php if ($selected_icon && in_array($selected_icon, $allowed_icons, true)) : ?>
                <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                    <img id="route-icon-preview" src="<?php echo esc_url(art_routes_get_icon_url($selected_icon)); ?>"
                        style="width: 40px; height: 40px; object-fit: contain;"
                        alt="<?php echo esc_attr($selected_icon); ?>" />
                </div>
            <?php else : ?>
                <div id="route-icon-preview" style="display: none;">
                    <p><strong><?php esc_html_e('Preview:', 'art-routes'); ?></strong></p>
                    <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                        <img style="width: 40px; height: 40px; object-fit: contain;" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    // Push icon config for the shared icon-preview.js
    wp_add_inline_script('art-routes-icon-preview-js',
        'var artRoutesIconConfigs = artRoutesIconConfigs || [];' .
        'artRoutesIconConfigs.push(' . wp_json_encode([
            'selectId'  => 'route_icon_select',
            'previewId' => 'route-icon-preview',
            'iconUrls'  => $icon_urls,
        ]) . ');',
        'before'
    );
    ?>
    <p class="description">
        <?php esc_html_e('Select an icon for this route. The icon will be shown on the route overview page.', 'art-routes'); ?>
    </p>
<?php
}

/**
 * Save route details meta box data
 */
function art_routes_save_route_details($post_id)
{
    // Verify nonce
    if (!isset($_POST['route_details_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['route_details_nonce'])), 'save_route_details')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save route length
    if (isset($_POST['route_length'])) {
        update_post_meta($post_id, '_route_length', sanitize_text_field(wp_unslash($_POST['route_length'])));
    }

    // Save route duration
    if (isset($_POST['route_duration'])) {
        update_post_meta($post_id, '_route_duration', sanitize_text_field(wp_unslash($_POST['route_duration'])));
    }

    // Save route type
    if (isset($_POST['route_type'])) {
        update_post_meta($post_id, '_route_type', sanitize_text_field(wp_unslash($_POST['route_type'])));
    }

    // Save show completed route setting (checkbox)
    $show_completed_route = isset($_POST['show_completed_route']) ? '1' : '0';
    update_post_meta($post_id, '_show_completed_route', $show_completed_route);

    // Save show artwork toasts setting (checkbox)
    $show_artwork_toasts = isset($_POST['show_artwork_toasts']) ? '1' : '0';
    update_post_meta($post_id, '_show_artwork_toasts', $show_artwork_toasts);
}
add_action('save_post_artro_route', 'art_routes_save_route_details');

/**
 * Save route path meta box data
 */
function art_routes_save_route_path($post_id)
{
    // Verify nonce
    if (!isset($_POST['route_path_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['route_path_nonce'])), 'save_route_path')) {
        return;
    }
    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    // Save route path - JSON coordinate data, sanitized by validating each point individually
    if (isset($_POST['route_path'])) {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON coordinate data; each value is validated as float and range-checked below
        $raw = trim(wp_unslash($_POST['route_path']));
        $json = json_decode($raw, true);
        $points = [];
        if (is_array($json) && !empty($json) && isset($json[0]['lat']) && isset($json[0]['lng'])) {
            // Valid JSON format - validate and sanitize each coordinate pair
            foreach ($json as $point) {
                if (!isset($point['lat']) || !isset($point['lng'])) {
                    continue;
                }
                $lat = (float) $point['lat'];
                $lng = (float) $point['lng'];
                if ($lat >= -90 && $lat <= 90 &&
                    $lng >= -180 && $lng <= 180) {
                    $points[] = ['lat' => $lat, 'lng' => $lng];
                }
            }
        } else {
            // Try to parse as old format and convert
            $lines = explode("\n", $raw);
            foreach ($lines as $line) {
                $parts = explode(',', sanitize_text_field($line));
                if (count($parts) >= 2) {
                    $lat = (float) trim($parts[0]);
                    $lng = (float) trim($parts[1]);
                    if ($lat >= -90 && $lat <= 90 &&
                        $lng >= -180 && $lng <= 180) {
                        $points[] = ['lat' => $lat, 'lng' => $lng];
                    }
                }
            }
        }
        $to_save = wp_json_encode($points, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        update_post_meta($post_id, '_route_path', $to_save);
    }
}
add_action('save_post_artro_route', 'art_routes_save_route_path');

/**
 * Save artwork location meta box data
 */
function art_routes_save_artwork_location($post_id)
{
    // Verify nonce
    // Use a dynamic nonce name based on post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'artro_artwork' && $post_type !== 'artro_info_point') {
        return; // Only save for these post types
    }
    $nonce_action = 'save_artwork_location'; // Nonce action remains the same as it's tied to the rendering function
    $nonce_name = 'artwork_location_nonce';

    if (!isset($_POST[$nonce_name]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[$nonce_name])), $nonce_action)) {
        return;
    }

    // Save latitude
    if (isset($_POST['artwork_latitude'])) {
        update_post_meta($post_id, '_artwork_latitude', sanitize_text_field(wp_unslash($_POST['artwork_latitude'])));
    }

    // Save longitude
    if (isset($_POST['artwork_longitude'])) {
        update_post_meta($post_id, '_artwork_longitude', sanitize_text_field(wp_unslash($_POST['artwork_longitude'])));
    }

    // Only save number and location for artwork post type
    if ($post_type === 'artro_artwork') {
        // Save artwork number
        if (isset($_POST['artwork_number'])) {
            update_post_meta($post_id, '_artwork_number', sanitize_text_field(wp_unslash($_POST['artwork_number'])));
        }

        // Save location description
        if (isset($_POST['artwork_location'])) {
            update_post_meta($post_id, '_artwork_location', sanitize_text_field(wp_unslash($_POST['artwork_location'])));
        }

        // Save wheelchair accessible setting (checkbox)
        $wheelchair_accessible = isset($_POST['wheelchair_accessible']) ? '1' : '0';
        update_post_meta($post_id, '_wheelchair_accessible', $wheelchair_accessible);

        // Save stroller accessible setting (checkbox)
        $stroller_accessible = isset($_POST['stroller_accessible']) ? '1' : '0';
        update_post_meta($post_id, '_stroller_accessible', $stroller_accessible);
    }
}
add_action('save_post_artro_artwork', 'art_routes_save_artwork_location');
add_action('save_post_artro_info_point', 'art_routes_save_artwork_location');

/**
 * Save artwork artist associations
 */
function art_routes_save_artwork_artists($post_id)
{
    // Verify nonce
    if (!isset($_POST['artwork_artists_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['artwork_artists_nonce'])), 'save_artwork_artists')) {
        return;
    }

    // Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save artist associations
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- IDs are cast to int via array_map('intval')
    if (isset($_POST['artwork_artist_ids']) && is_array($_POST['artwork_artist_ids'])) {
        // Sanitize the array of IDs
        $artist_ids = array_map('intval', wp_unslash($_POST['artwork_artist_ids']));
        update_post_meta($post_id, '_artwork_artist_ids', $artist_ids);
    } else {
        // If no artists selected, save empty array
        update_post_meta($post_id, '_artwork_artist_ids', array());
    }
}
add_action('save_post_artro_artwork', 'art_routes_save_artwork_artists');

/**
 * Save artwork icon meta box data
 */
function art_routes_save_artwork_icon($post_id)
{
    if (!isset($_POST['artwork_icon_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['artwork_icon_nonce'])), 'save_artwork_icon')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the selected icon filename
    if (isset($_POST['artwork_icon'])) {
        $selected_icon = sanitize_text_field(wp_unslash($_POST['artwork_icon']));
        if (!empty($selected_icon)) {
            update_post_meta($post_id, '_artwork_icon', $selected_icon);
        } else {
            delete_post_meta($post_id, '_artwork_icon');
        }
    }
}
add_action('save_post_artro_artwork', 'art_routes_save_artwork_icon');

/**
 * Save info point icon meta box data
 */
function art_routes_save_info_point_icon($post_id)
{
    if (!isset($_POST['info_point_icon_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['info_point_icon_nonce'])), 'save_info_point_icon')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save the selected icon filename
    if (isset($_POST['info_point_icon'])) {
        $selected_icon = sanitize_text_field(wp_unslash($_POST['info_point_icon']));
        if (!empty($selected_icon)) {
            update_post_meta($post_id, '_info_point_icon', $selected_icon);
        } else {
            delete_post_meta($post_id, '_info_point_icon');
        }
    }
}
add_action('save_post_artro_info_point', 'art_routes_save_info_point_icon');

/**
 * Save route icon meta box data
 */
function art_routes_save_route_icon($post_id)
{
    if (!isset($_POST['route_icon_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['route_icon_nonce'])), 'save_route_icon')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    // Only allow route marker icons
    $allowed_icons = [
        'start.svg',
        'end.svg',
        'marker.svg',
    ];
    $route_icon = isset($_POST['route_icon']) ? sanitize_text_field(wp_unslash($_POST['route_icon'])) : '';
    if (!empty($route_icon) && in_array($route_icon, $allowed_icons, true)) {
        update_post_meta($post_id, '_route_icon', $route_icon);
    } else {
        delete_post_meta($post_id, '_route_icon');
    }
}
add_action('save_post_artro_route', 'art_routes_save_route_icon');

/**
 * Render Edition selector meta box
 */
function art_routes_render_edition_selector_meta_box($post) {
    wp_nonce_field('save_edition_selector', 'edition_selector_nonce');

    $current_edition_id = get_post_meta($post->ID, '_edition_id', true);
    $editions = art_routes_get_editions();

    ?>
    <p>
        <select name="edition_id" id="edition_id" class="widefat">
            <option value="0"><?php esc_html_e('— No Edition —', 'art-routes'); ?></option>
            <?php foreach ($editions as $edition) : ?>
                <option value="<?php echo esc_attr($edition->ID); ?>" <?php selected($current_edition_id, $edition->ID); ?>>
                    <?php echo esc_html($edition->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class="description">
        <?php esc_html_e('Assign this content to an edition.', 'art-routes'); ?>
    </p>
    <?php
}

/**
 * Save Edition selector
 */
function art_routes_save_edition_selector($post_id) {
    if (!isset($_POST['edition_selector_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['edition_selector_nonce'])), 'save_edition_selector')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['edition_id'])) {
        $edition_id = absint(wp_unslash($_POST['edition_id']));
        if ($edition_id > 0) {
            update_post_meta($post_id, '_edition_id', $edition_id);
        } else {
            delete_post_meta($post_id, '_edition_id');
        }
    }
}
add_action('save_post_artro_route', 'art_routes_save_edition_selector');
add_action('save_post_artro_artwork', 'art_routes_save_edition_selector');
add_action('save_post_artro_info_point', 'art_routes_save_edition_selector');
