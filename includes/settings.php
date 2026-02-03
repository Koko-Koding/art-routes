<?php
/**
 * Settings Page for WP Art Routes Plugin
 *
 * Provides a tabbed interface for:
 * - General settings (default route, location tracking)
 * - Terminology settings (customizable labels)
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitize terminology input
 *
 * @param mixed $input The input to sanitize
 * @return array Sanitized terminology array
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
            // Only route, location, and info_point have slugs
            if (isset($input[$type]['slug'])) {
                $sanitized[$type]['slug'] = sanitize_title($input[$type]['slug']);
            }
        }
    }

    return $sanitized;
}

/**
 * Register plugin settings
 */
function wp_art_routes_register_settings() {
    // General settings
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

    register_setting(
        'wp_art_routes_options',
        'wp_art_routes_default_location_icon',
        [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_file_name',
            'default' => '',
        ]
    );

    // Terminology settings
    register_setting(
        'wp_art_routes_terminology_options',
        'wp_art_routes_terminology',
        [
            'type' => 'array',
            'sanitize_callback' => 'wp_art_routes_sanitize_terminology',
            'default' => [],
        ]
    );
}
add_action('admin_init', 'wp_art_routes_register_settings');

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
 * Render the settings page with tabs
 */
function wp_art_routes_render_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Determine current tab
    $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';

    // Define available tabs
    $tabs = [
        'general' => __('General', 'wp-art-routes'),
        'terminology' => __('Terminology', 'wp-art-routes'),
    ];

    // Show success message if settings were updated
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

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_slug => $tab_label) : ?>
                <a href="<?php echo esc_url(add_query_arg(['tab' => $tab_slug], admin_url('edit.php?post_type=edition&page=wp-art-routes-settings'))); ?>"
                   class="nav-tab <?php echo $current_tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
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
                case 'general':
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
 * Render the General settings tab
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

                    // Get all routes
                    $routes = get_posts([
                        'post_type' => 'art_route',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC',
                    ]);

                    if (!empty($routes)) {
                        echo '<select name="wp_art_routes_default_route_id" id="wp_art_routes_default_route_id">';
                        echo '<option value="0">' . esc_html__('Select a default route', 'wp-art-routes') . '</option>';

                        foreach ($routes as $route) {
                            echo '<option value="' . esc_attr($route->ID) . '" ' . selected($default_route_id, $route->ID, false) . '>';
                            echo esc_html($route->post_title);
                            echo '</option>';
                        }

                        echo '</select>';
                        echo '<p class="description">' . esc_html__('This route will be used when no specific route is selected.', 'wp-art-routes') . '</p>';
                    } else {
                        echo '<p>' . esc_html__('No routes available. Please create a route first.', 'wp-art-routes') . '</p>';
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
            <tr>
                <th scope="row">
                    <label for="wp_art_routes_default_location_icon">
                        <?php _e('Default Location Icon', 'wp-art-routes'); ?>
                    </label>
                </th>
                <td>
                    <?php
                    $default_location_icon = get_option('wp_art_routes_default_location_icon', '');
                    $available_icons = wp_art_routes_get_available_icons();
                    $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
                    ?>
                    <select name="wp_art_routes_default_location_icon" id="wp_art_routes_default_location_icon">
                        <option value=""><?php esc_html_e('No default icon (gray circle)', 'wp-art-routes'); ?></option>
                        <?php foreach ($available_icons as $icon_filename) : ?>
                            <option value="<?php echo esc_attr($icon_filename); ?>" <?php selected($default_location_icon, $icon_filename); ?>>
                                <?php echo esc_html(wp_art_routes_get_icon_display_name($icon_filename)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($default_location_icon)) : ?>
                        <span style="margin-left: 10px; vertical-align: middle;">
                            <img src="<?php echo esc_url($icons_url . $default_location_icon); ?>" alt="" style="width: 24px; height: 24px; vertical-align: middle;">
                        </span>
                    <?php endif; ?>
                    <p class="description">
                        <?php _e('Select a default icon for locations that do not have an icon assigned (e.g., imported via GPX).', 'wp-art-routes'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>
    </form>
    <?php
}

/**
 * Render the Terminology settings tab
 */
function wp_art_routes_render_terminology_tab() {
    // Get default terminology for placeholders
    $defaults = wp_art_routes_get_default_terminology();

    // Get saved terminology values
    $saved = get_option('wp_art_routes_terminology', []);

    // Helper function to get saved value or empty string
    $get_saved = function ($type, $key) use ($saved) {
        return isset($saved[$type][$key]) ? $saved[$type][$key] : '';
    };

    // Define the terminology fields
    $terminology_types = [
        'route' => [
            'label' => __('Route', 'wp-art-routes'),
            'description' => __('The main paths users follow (e.g., "Art Route", "Trail", "Walk")', 'wp-art-routes'),
            'has_slug' => true,
        ],
        'location' => [
            'label' => __('Location', 'wp-art-routes'),
            'description' => __('Points of interest along routes (e.g., "Artwork", "Performance", "Venue")', 'wp-art-routes'),
            'has_slug' => true,
        ],
        'info_point' => [
            'label' => __('Info Point', 'wp-art-routes'),
            'description' => __('Information markers along routes (e.g., "Info Point", "Landmark", "Stop")', 'wp-art-routes'),
            'has_slug' => true,
        ],
        'creator' => [
            'label' => __('Creator', 'wp-art-routes'),
            'description' => __('People or entities associated with locations (e.g., "Artist", "Performer", "Author")', 'wp-art-routes'),
            'has_slug' => false,
        ],
    ];
    ?>
    <form method="post" action="options.php">
        <?php settings_fields('wp_art_routes_terminology_options'); ?>

        <p class="description" style="margin-bottom: 20px;">
            <?php _e('Customize the labels used throughout the plugin. Leave fields empty to use the default values shown as placeholders.', 'wp-art-routes'); ?>
        </p>

        <?php foreach ($terminology_types as $type => $config) : ?>
            <h3><?php echo esc_html($config['label']); ?></h3>
            <p class="description"><?php echo esc_html($config['description']); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_singular">
                            <?php _e('Singular', 'wp-art-routes'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text"
                               name="wp_art_routes_terminology[<?php echo esc_attr($type); ?>][singular]"
                               id="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_singular"
                               value="<?php echo esc_attr($get_saved($type, 'singular')); ?>"
                               placeholder="<?php echo esc_attr($defaults[$type]['singular']); ?>"
                               class="regular-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_plural">
                            <?php _e('Plural', 'wp-art-routes'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text"
                               name="wp_art_routes_terminology[<?php echo esc_attr($type); ?>][plural]"
                               id="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_plural"
                               value="<?php echo esc_attr($get_saved($type, 'plural')); ?>"
                               placeholder="<?php echo esc_attr($defaults[$type]['plural']); ?>"
                               class="regular-text" />
                    </td>
                </tr>
                <?php if ($config['has_slug']) : ?>
                    <tr>
                        <th scope="row">
                            <label for="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_slug">
                                <?php _e('URL Slug', 'wp-art-routes'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="text"
                                   name="wp_art_routes_terminology[<?php echo esc_attr($type); ?>][slug]"
                                   id="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_slug"
                                   value="<?php echo esc_attr($get_saved($type, 'slug')); ?>"
                                   placeholder="<?php echo esc_attr($defaults[$type]['slug']); ?>"
                                   class="regular-text" />
                            <p class="description">
                                <?php _e('The URL-friendly version (lowercase, no spaces). After changing, go to Settings > Permalinks and click Save to refresh URL rules.', 'wp-art-routes'); ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>

            <hr style="margin: 30px 0;" />
        <?php endforeach; ?>

        <?php submit_button(); ?>
    </form>
    <?php
}
