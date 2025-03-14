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
    register_setting(
        'wp_art_routes_options',
        'wp_art_routes_default_route_id',
        [
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ]
    );

    register_setting(
        'wp_art_routes_options',
        'wp_art_routes_enable_location_tracking',
        [
            'type' => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'default' => true,
        ]
    );
}
add_action('admin_init', 'wp_art_routes_register_settings');

/**
 * Add settings page to admin menu
 */
function wp_art_routes_add_settings_page() {
    add_submenu_page(
        'edit.php?post_type=art_route',
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

    // Save settings if form was submitted
    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'wp_art_routes_messages',
            'wp_art_routes_message',
            __('Settings saved.', 'wp-art-routes'),
            'updated'
        );
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php settings_errors('wp_art_routes_messages'); ?>
        
        <form method="post" action="options.php">
            <?php
            settings_fields('wp_art_routes_options');
            ?>
            
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
                        
                        // Get all routes
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
    </div>
    <?php
}