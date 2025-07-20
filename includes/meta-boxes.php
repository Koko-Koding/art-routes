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

    // Info Point Location meta box (reuses artwork location rendering)
    add_meta_box(
        'info_point_location',
        __('Info Point Location', 'wp-art-routes'),
        'wp_art_routes_render_info_point_location_meta_box', // Use dedicated function for info points
        'information_point', // Apply to the new CPT
        'normal',
        'high'
    );
    
    // Artwork Artist Association meta box
    add_meta_box(
        'artwork_artists',
        __('Artist(s)', 'wp-art-routes'),
        'wp_art_routes_render_artwork_artists_meta_box',
        'artwork',
        'normal',
        'default'
    );

    // Artwork Icon meta box
    add_meta_box(
        'artwork_icon',
        __('Artwork Icon', 'wp-art-routes'),
        'wp_art_routes_render_artwork_icon_meta_box',
        'artwork',
        'side',
        'default'
    );

    // Info Point Icon meta box
    add_meta_box(
        'info_point_icon',
        __('Info Point Icon', 'wp-art-routes'),
        'wp_art_routes_render_info_point_icon_meta_box',
        'information_point',
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
        <input type="number" id="route_length" name="route_length" value="<?php echo esc_attr($length); ?>" step="0.01" min="0" style="width: 100px;" />
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
    $number = get_post_meta($post->ID, '_artwork_number', true);
    $location = get_post_meta($post->ID, '_artwork_location', true);
    
    ?>
    <p>
        <label for="artwork_number">
            <?php _e('Number', 'wp-art-routes'); ?>:
        </label>
        <input type="text" id="artwork_number" name="artwork_number" value="<?php echo esc_attr($number); ?>" class="regular-text" />
        <span class="description"><?php _e('Optional artwork number for identification', 'wp-art-routes'); ?></span>
    </p>
    
    <p>
        <label for="artwork_location">
            <?php _e('Location', 'wp-art-routes'); ?>:
        </label>
        <input type="text" id="artwork_location" name="artwork_location" value="<?php echo esc_attr($location); ?>" class="regular-text" />
        <span class="description"><?php _e('Optional location description (e.g., "Near the town square")', 'wp-art-routes'); ?></span>
    </p>
    
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
 * Render Info Point Location meta box
 */
function wp_art_routes_render_info_point_location_meta_box($post) {
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
 * Render Artwork Artist Association meta box
 */
function wp_art_routes_render_artwork_artists_meta_box($post) {
    // Add nonce for security
    wp_nonce_field('save_artwork_artists', 'artwork_artists_nonce');
    
    // Get saved artist associations
    $artist_ids = get_post_meta($post->ID, '_artwork_artist_ids', true);
    
    if (!is_array($artist_ids)) {
        $artist_ids = empty($artist_ids) ? array() : array($artist_ids);
    }
    
    // Enqueue the WordPress media scripts
    wp_enqueue_script('jquery-ui-autocomplete');
    
    // Get all available post types except some internal ones
    $excluded_post_types = array('revision', 'attachment', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'art_route', 'artwork');
    $post_types = get_post_types(array('public' => true), 'objects');
    
    ?>
    <div class="artist-association-container">
        <p><?php _e('Connect this artwork to one or more posts representing the artist(s).', 'wp-art-routes'); ?></p>
        
        <div class="artist-search">
            <label for="artist_search"><?php _e('Search for content:', 'wp-art-routes'); ?></label>
            <input type="text" id="artist_search" placeholder="<?php esc_attr_e('Start typing to search...', 'wp-art-routes'); ?>" class="regular-text" />
            
            <div class="post-type-filter">
                <label><?php _e('Filter by post type:', 'wp-art-routes'); ?></label>
                <select id="post_type_filter">
                    <option value=""><?php _e('All post types', 'wp-art-routes'); ?></option>
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
            <h4><?php _e('Selected Artist(s):', 'wp-art-routes'); ?></h4>
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
                            echo ' <a href="#" class="remove-artist">' . __('Remove', 'wp-art-routes') . '</a>';
                            echo '<input type="hidden" name="artwork_artist_ids[]" value="' . esc_attr($artist_id) . '">';
                            echo '</li>';
                        }
                    }
                }
                ?>
            </ul>
            <p class="description"><?php _e('These posts will be associated with this artwork as artists.', 'wp-art-routes'); ?></p>
        </div>
    </div>
    
    <style>
        .artist-association-container {
            margin-bottom: 20px;
        }
        .artist-search {
            margin-bottom: 15px;
        }
        .post-type-filter {
            margin-top: 10px;
        }
        #selected_artists_list {
            margin-top: 10px;
        }
        #selected_artists_list li {
            margin-bottom: 5px;
            padding: 5px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .remove-artist {
            color: #a00;
            text-decoration: none;
        }
        .post-type-label {
            color: #666;
            font-style: italic;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Autocomplete for artist search
        $('#artist_search').autocomplete({
            source: function(request, response) {
                var postType = $('#post_type_filter').val();
                
                $.ajax({
                    url: ajaxurl,
                    dataType: 'json',
                    data: {
                        action: 'search_posts_for_artist',
                        term: request.term,
                        post_type: postType,
                        nonce: '<?php echo wp_create_nonce('artist_search_nonce'); ?>'
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                // Add the selected artist to the list
                addArtistToList(ui.item);
                
                // Clear the search field
                setTimeout(function() {
                    $('#artist_search').val('');
                }, 100);
                
                return false;
            }
        }).autocomplete('instance')._renderItem = function(ul, item) {
            return $('<li>')
                .append('<div>' + item.label + ' <span class="post-type-label">(' + item.post_type_label + ')</span></div>')
                .appendTo(ul);
        };
        
        // Function to add artist to the selected list
        function addArtistToList(item) {
            // Check if already added
            if ($('#selected_artists_list li[data-id="' + item.id + '"]').length === 0) {
                var artistItem = $('<li data-id="' + item.id + '"></li>');
                artistItem.append('<span class="artist-title">' + item.label + '</span>');
                artistItem.append(' <span class="post-type-label">(' + item.post_type_label + ')</span>');
                artistItem.append(' <a href="#" class="remove-artist"><?php _e('Remove', 'wp-art-routes'); ?></a>');
                artistItem.append('<input type="hidden" name="artwork_artist_ids[]" value="' + item.id + '">');
                
                $('#selected_artists_list').append(artistItem);
            }
        }
        
        // Remove artist from the list
        $(document).on('click', '.remove-artist', function(e) {
            e.preventDefault();
            $(this).parent('li').remove();
        });
    });
    </script>
    <?php
}

/**
 * Render Artwork Icon meta box
 */
function wp_art_routes_render_artwork_icon_meta_box($post) {
    wp_nonce_field('save_artwork_icon', 'artwork_icon_nonce');
    
    // Get the currently selected icon
    $selected_icon = get_post_meta($post->ID, '_artwork_icon', true);
    
    // Get available SVG icons from the assets/icons directory
    $icons_dir = plugin_dir_path(dirname(__FILE__)) . 'assets/icons/';
    $icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
    $available_icons = [];
    
    if (is_dir($icons_dir)) {
        $files = scandir($icons_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $available_icons[] = $file;
            }
        }
        sort($available_icons);
    }
    
    ?>
    <div id="artwork-icon-meta-box">
        <p>
            <label for="artwork_icon_select">
                <?php _e('Select Icon:', 'wp-art-routes'); ?>
            </label>
        </p>
        
        <select id="artwork_icon_select" name="artwork_icon" style="width: 100%;">
            <option value=""><?php _e('-- No Icon --', 'wp-art-routes'); ?></option>
            <?php foreach ($available_icons as $icon_file) : 
                $icon_name = pathinfo($icon_file, PATHINFO_FILENAME);
                $display_name = str_replace(['WB plattegrond-', '-'], ['', ' '], $icon_name);
                $display_name = ucwords(trim($display_name));
            ?>
                <option value="<?php echo esc_attr($icon_file); ?>" <?php selected($selected_icon, $icon_file); ?>>
                    <?php echo esc_html($display_name); ?> (<?php echo esc_html($icon_file); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        
        <div id="icon-preview-container" style="margin-top: 15px;">
            <?php if ($selected_icon && in_array($selected_icon, $available_icons)) : ?>
                <p><strong><?php _e('Preview:', 'wp-art-routes'); ?></strong></p>
                <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                    <img id="icon-preview" src="<?php echo esc_url($icons_url . $selected_icon); ?>" 
                         style="width: 40px; height: 40px; object-fit: contain;" 
                         alt="<?php echo esc_attr($selected_icon); ?>" />
                </div>
            <?php else : ?>
                <div id="icon-preview" style="display: none;">
                    <p><strong><?php _e('Preview:', 'wp-art-routes'); ?></strong></p>
                    <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                        <img style="width: 40px; height: 40px; object-fit: contain;" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        const iconsUrl = '<?php echo esc_js($icons_url); ?>';
        
        $('#artwork_icon_select').on('change', function() {
            const selectedIcon = $(this).val();
            const $previewContainer = $('#icon-preview');
            
            if (selectedIcon) {
                const iconUrl = iconsUrl + selectedIcon;
                $previewContainer.show();
                $previewContainer.find('img').attr('src', iconUrl).attr('alt', selectedIcon);
            } else {
                $previewContainer.hide();
            }
        });
    });
    </script>
    
    <p class="description">
        <?php _e('Select an icon for this artwork. The icon will be displayed as a marker on the map.', 'wp-art-routes'); ?>
    </p>
    <?php
}

/**
 * Render Info Point Icon meta box
 */
function wp_art_routes_render_info_point_icon_meta_box($post) {
    wp_nonce_field('save_info_point_icon', 'info_point_icon_nonce');
    
    // Get the currently selected icon
    $selected_icon = get_post_meta($post->ID, '_info_point_icon', true);
    
    // Get available SVG icons from the assets/icons directory
    $icons_dir = plugin_dir_path(dirname(__FILE__)) . 'assets/icons/';
    $icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
    $available_icons = [];
    
    if (is_dir($icons_dir)) {
        $files = scandir($icons_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $available_icons[] = $file;
            }
        }
        sort($available_icons);
    }
    
    ?>
    <div id="info-point-icon-meta-box">
        <p>
            <label for="info_point_icon_select">
                <?php _e('Select Icon:', 'wp-art-routes'); ?>
            </label>
        </p>
        
        <select id="info_point_icon_select" name="info_point_icon" style="width: 100%;">
            <option value=""><?php _e('-- No Icon --', 'wp-art-routes'); ?></option>
            <?php foreach ($available_icons as $icon_file) : 
                $icon_name = pathinfo($icon_file, PATHINFO_FILENAME);
                $display_name = str_replace(['WB plattegrond-', '-'], ['', ' '], $icon_name);
                $display_name = ucwords(trim($display_name));
            ?>
                <option value="<?php echo esc_attr($icon_file); ?>" <?php selected($selected_icon, $icon_file); ?>>
                    <?php echo esc_html($display_name); ?> (<?php echo esc_html($icon_file); ?>)
                </option>
            <?php endforeach; ?>
        </select>
        
        <div id="icon-preview-container" style="margin-top: 15px;">
            <?php if ($selected_icon && in_array($selected_icon, $available_icons)) : ?>
                <p><strong><?php _e('Preview:', 'wp-art-routes'); ?></strong></p>
                <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                    <img id="icon-preview" src="<?php echo esc_url($icons_url . $selected_icon); ?>" 
                         style="width: 40px; height: 40px; object-fit: contain;" 
                         alt="<?php echo esc_attr($selected_icon); ?>" />
                </div>
            <?php else : ?>
                <div id="icon-preview" style="display: none;">
                    <p><strong><?php _e('Preview:', 'wp-art-routes'); ?></strong></p>
                    <div style="padding: 10px; border: 1px solid #ddd; background: #f9f9f9; display: inline-block;">
                        <img style="width: 40px; height: 40px; object-fit: contain;" alt="" />
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        const iconsUrl = '<?php echo esc_js($icons_url); ?>';
        
        $('#info_point_icon_select').on('change', function() {
            const selectedIcon = $(this).val();
            const $previewContainer = $('#icon-preview');
            
            if (selectedIcon) {
                const iconUrl = iconsUrl + selectedIcon;
                $previewContainer.show();
                $previewContainer.find('img').attr('src', iconUrl).attr('alt', selectedIcon);
            } else {
                $previewContainer.hide();
            }
        });
    });
    </script>
    
    <p class="description">
        <?php _e('Select an icon for this information point. The icon will be displayed as a marker on the map.', 'wp-art-routes'); ?>
    </p>
    <?php
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
        $raw = trim(stripslashes($_POST['route_path']));
        $json = json_decode($raw, true);
        if (is_array($json) && isset($json[0]['lat']) && isset($json[0]['lng'])) {
            // Already valid JSON format
            $to_save = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } else {
            // Try to parse as old format and convert
            $lines = explode("\n", $raw);
            $points = [];
            foreach ($lines as $line) {
                $parts = explode(',', $line);
                if (count($parts) >= 2) {
                    $lat = trim($parts[0]);
                    $lng = trim($parts[1]);
                    if (is_numeric($lat) && is_numeric($lng)) {
                        $points[] = [ 'lat' => (float)$lat, 'lng' => (float)$lng ];
                    }
                }
            }
            $to_save = json_encode($points, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }
        update_post_meta($post_id, '_route_path', $to_save);
    }
}
add_action('save_post_art_route', 'wp_art_routes_save_route_path');

/**
 * Save artwork location meta box data
 */
function wp_art_routes_save_artwork_location($post_id) {
    // Verify nonce
    // Use a dynamic nonce name based on post type
    $post_type = get_post_type($post_id);
    if ($post_type !== 'artwork' && $post_type !== 'information_point') {
        return; // Only save for these post types
    }
    $nonce_action = 'save_artwork_location'; // Nonce action remains the same as it's tied to the rendering function
    $nonce_name = 'artwork_location_nonce';

    if (!isset($_POST[$nonce_name]) || !wp_verify_nonce($_POST[$nonce_name], $nonce_action)) {
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
    
    // Only save number and location for artwork post type
    if ($post_type === 'artwork') {
        // Save artwork number
        if (isset($_POST['artwork_number'])) {
            update_post_meta($post_id, '_artwork_number', sanitize_text_field($_POST['artwork_number']));
        }
        
        // Save location description
        if (isset($_POST['artwork_location'])) {
            update_post_meta($post_id, '_artwork_location', sanitize_text_field($_POST['artwork_location']));
        }
    }
}
add_action('save_post_artwork', 'wp_art_routes_save_artwork_location');
add_action('save_post_information_point', 'wp_art_routes_save_artwork_location');

/**
 * Save artwork artist associations
 */
function wp_art_routes_save_artwork_artists($post_id) {
    // Verify nonce
    if (!isset($_POST['artwork_artists_nonce']) || !wp_verify_nonce($_POST['artwork_artists_nonce'], 'save_artwork_artists')) {
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
    if (isset($_POST['artwork_artist_ids']) && is_array($_POST['artwork_artist_ids'])) {
        // Sanitize the array of IDs
        $artist_ids = array_map('intval', $_POST['artwork_artist_ids']);
        update_post_meta($post_id, '_artwork_artist_ids', $artist_ids);
    } else {
        // If no artists selected, save empty array
        update_post_meta($post_id, '_artwork_artist_ids', array());
    }
}
add_action('save_post_artwork', 'wp_art_routes_save_artwork_artists');

/**
 * Save artwork icon meta box data
 */
function wp_art_routes_save_artwork_icon($post_id) {
    if (!isset($_POST['artwork_icon_nonce']) || !wp_verify_nonce($_POST['artwork_icon_nonce'], 'save_artwork_icon')) {
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
        $selected_icon = sanitize_text_field($_POST['artwork_icon']);
        if (!empty($selected_icon)) {
            update_post_meta($post_id, '_artwork_icon', $selected_icon);
        } else {
            delete_post_meta($post_id, '_artwork_icon');
        }
    }
}
add_action('save_post_artwork', 'wp_art_routes_save_artwork_icon');

/**
 * Save info point icon meta box data
 */
function wp_art_routes_save_info_point_icon($post_id) {
    if (!isset($_POST['info_point_icon_nonce']) || !wp_verify_nonce($_POST['info_point_icon_nonce'], 'save_info_point_icon')) {
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
        $selected_icon = sanitize_text_field($_POST['info_point_icon']);
        if (!empty($selected_icon)) {
            update_post_meta($post_id, '_info_point_icon', $selected_icon);
        } else {
            delete_post_meta($post_id, '_info_point_icon');
        }
    }
}
add_action('save_post_information_point', 'wp_art_routes_save_info_point_icon');
