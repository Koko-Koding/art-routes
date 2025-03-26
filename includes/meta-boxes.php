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
function wp_art_routes_add_meta_boxes() {
    // Route Details meta box
    add_meta_box(
        'art_route_details',
        __('Route Details', 'wp-art-routes'),
        'wp_art_routes_render_route_details_meta_box',
        'art_route',
        'normal',
        'high'
    );
    
    // Route Path meta box
    add_meta_box(
        'art_route_path',
        __('Route Path', 'wp-art-routes'),
        'wp_art_routes_render_route_path_meta_box',
        'art_route',
        'normal',
        'high'
    );
    
    // Artwork Location meta box
    add_meta_box(
        'artwork_location',
        __('Artwork Location', 'wp-art-routes'),
        'wp_art_routes_render_artwork_location_meta_box',
        'artwork',
        'normal',
        'high'
    );
    
    // Artwork Route Association meta box
    add_meta_box(
        'artwork_route',
        __('Artwork Route', 'wp-art-routes'),
        'wp_art_routes_render_artwork_route_meta_box',
        'artwork',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'wp_art_routes_add_meta_boxes');

/**
 * Render Route Details meta box
 */
function wp_art_routes_render_route_details_meta_box($post) {
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
        'walking' => __('Walking route', 'wp-art-routes'),
        'cycling' => __('Bicycle route', 'wp-art-routes'),
        'wheelchair' => __('Wheelchair friendly', 'wp-art-routes'),
        'children' => __('Child-friendly route', 'wp-art-routes'),
    ];
    
    ?>
    <p>
        <label for="route_length">
            <?php _e('Route Length (km)', 'wp-art-routes'); ?>:
        </label>
        <input type="number" id="route_length" name="route_length" value="<?php echo esc_attr($length); ?>" step="0.1" min="0" style="width: 100px;" />
    </p>
    
    <p>
        <label for="route_duration">
            <?php _e('Duration (minutes)', 'wp-art-routes'); ?>:
        </label>
        <input type="number" id="route_duration" name="route_duration" value="<?php echo esc_attr($duration); ?>" min="0" style="width: 100px;" />
    </p>
    
    <p>
        <label for="route_type">
            <?php _e('Route Type', 'wp-art-routes'); ?>:
        </label>
        <select id="route_type" name="route_type">
            <option value=""><?php _e('Select Type', 'wp-art-routes'); ?></option>
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
            <?php _e('Show completed route path', 'wp-art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php _e('When checked, users will see which part of the route they have already traversed.', 'wp-art-routes'); ?></span>
    </p>
    
    <p>
        <label for="show_artwork_toasts">
            <input type="checkbox" id="show_artwork_toasts" name="show_artwork_toasts" value="1" <?php checked($show_artwork_toasts, '1'); ?> />
            <?php _e('Show artwork notifications', 'wp-art-routes'); ?>
        </label>
        <br>
        <span class="description"><?php _e('When checked, users will receive a notification when they pass near an artwork.', 'wp-art-routes'); ?></span>
    </p>
    <?php
}

/**
 * Render Route Path meta box
 */
function wp_art_routes_render_route_path_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_route_path', 'route_path_nonce');
    
    // Get saved path
    $path = get_post_meta($post->ID, '_route_path', true);
    
    // Instructions
    echo '<p>' . __('Enter the route path as a series of latitude,longitude points. One point per line.', 'wp-art-routes') . '</p>';
    echo '<p>' . __('Example: 52.3702, 4.8952', 'wp-art-routes') . '</p>';
    
    // Text area for path
    echo '<textarea id="route_path" name="route_path" rows="10" class="large-text">' . esc_textarea($path) . '</textarea>';
    
    // Add button to open map modal
    echo '<p><button type="button" class="button" id="open_route_map">' . __('Use Map to Create Route', 'wp-art-routes') . '</button></p>';
}

/**
 * Render Artwork Location meta box
 */
function wp_art_routes_render_artwork_location_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_artwork_location', 'artwork_location_nonce');
    
    // Get saved values
    $latitude = get_post_meta($post->ID, '_artwork_latitude', true);
    $longitude = get_post_meta($post->ID, '_artwork_longitude', true);
    
    ?>
    <p>
        <label for="artwork_latitude">
            <?php _e('Latitude', 'wp-art-routes'); ?>:
        </label>
        <input type="text" id="artwork_latitude" name="artwork_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" />
    </p>
    
    <p>
        <label for="artwork_longitude">
            <?php _e('Longitude', 'wp-art-routes'); ?>:
        </label>
        <input type="text" id="artwork_longitude" name="artwork_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" />
    </p>
    
    <div id="artwork_location_map" style="width: 100%; height: 300px; margin-top: 10px;"></div>
    <p><button type="button" class="button" id="pick_artwork_location"><?php _e('Pick Location on Map', 'wp-art-routes'); ?></button></p>
    <?php
}

/**
 * Render Artwork Route Association meta box
 */
function wp_art_routes_render_artwork_route_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_artwork_route', 'artwork_route_nonce');
    
    // Get saved route ID
    $route_id = get_post_meta($post->ID, '_artwork_route_id', true);
    
    // Get all routes
    $routes = get_posts([
        'post_type' => 'art_route',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);
    
    if (empty($routes)) {
        echo '<p>' . __('No routes available. Please create a route first.', 'wp-art-routes') . '</p>';
        return;
    }
    
    echo '<select name="artwork_route_id" id="artwork_route_id">';
    echo '<option value="">' . __('Select a Route', 'wp-art-routes') . '</option>';
    
    foreach ($routes as $route) {
        echo '<option value="' . esc_attr($route->ID) . '" ' . selected($route_id, $route->ID, false) . '>';
        echo esc_html($route->post_title);
        echo '</option>';
    }
    
    echo '</select>';
}

/**
 * Save route details meta box data
 */
function wp_art_routes_save_route_details($post_id) {
    // Verify nonce
    if (!isset($_POST['route_details_nonce']) || !wp_verify_nonce($_POST['route_details_nonce'], 'save_route_details')) {
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
        update_post_meta($post_id, '_route_length', sanitize_text_field($_POST['route_length']));
    }
    
    // Save route duration
    if (isset($_POST['route_duration'])) {
        update_post_meta($post_id, '_route_duration', sanitize_text_field($_POST['route_duration']));
    }
    
    // Save route type
    if (isset($_POST['route_type'])) {
        update_post_meta($post_id, '_route_type', sanitize_text_field($_POST['route_type']));
    }
    
    // Save show completed route setting (checkbox)
    $show_completed_route = isset($_POST['show_completed_route']) ? '1' : '0';
    update_post_meta($post_id, '_show_completed_route', $show_completed_route);
    
    // Save show artwork toasts setting (checkbox)
    $show_artwork_toasts = isset($_POST['show_artwork_toasts']) ? '1' : '0';
    update_post_meta($post_id, '_show_artwork_toasts', $show_artwork_toasts);
}
add_action('save_post_art_route', 'wp_art_routes_save_route_details');

/**
 * Save route path meta box data
 */
function wp_art_routes_save_route_path($post_id) {
    // Verify nonce
    if (!isset($_POST['route_path_nonce']) || !wp_verify_nonce($_POST['route_path_nonce'], 'save_route_path')) {
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
    
    // Save route path
    if (isset($_POST['route_path'])) {
        update_post_meta($post_id, '_route_path', sanitize_textarea_field($_POST['route_path']));
    }
}
add_action('save_post_art_route', 'wp_art_routes_save_route_path');

/**
 * Save artwork location meta box data
 */
function wp_art_routes_save_artwork_location($post_id) {
    // Verify nonce
    if (!isset($_POST['artwork_location_nonce']) || !wp_verify_nonce($_POST['artwork_location_nonce'], 'save_artwork_location')) {
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
    
    // Save latitude
    if (isset($_POST['artwork_latitude'])) {
        update_post_meta($post_id, '_artwork_latitude', sanitize_text_field($_POST['artwork_latitude']));
    }
    
    // Save longitude
    if (isset($_POST['artwork_longitude'])) {
        update_post_meta($post_id, '_artwork_longitude', sanitize_text_field($_POST['artwork_longitude']));
    }
}
add_action('save_post_artwork', 'wp_art_routes_save_artwork_location');

/**
 * Save artwork route association
 */
function wp_art_routes_save_artwork_route($post_id) {
    // Verify nonce
    if (!isset($_POST['artwork_route_nonce']) || !wp_verify_nonce($_POST['artwork_route_nonce'], 'save_artwork_route')) {
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
    
    // Save route association
    if (isset($_POST['artwork_route_id'])) {
        update_post_meta($post_id, '_artwork_route_id', sanitize_text_field($_POST['artwork_route_id']));
    }
}
add_action('save_post_artwork', 'wp_art_routes_save_artwork_route');