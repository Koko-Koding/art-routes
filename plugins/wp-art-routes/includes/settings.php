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
 * Sanitize icon filename input
 *
 * Validates the filename exists in available icons rather than
 * sanitizing the filename (which would break filenames with spaces).
 *
 * @param string $input The icon filename to validate
 * @return string The validated filename or empty string
 */
function wp_art_routes_sanitize_icon_filename($input) {
    if (empty($input)) {
        return '';
    }

    // Get list of available icons
    $available_icons = wp_art_routes_get_available_icons();

    // Only allow filenames that exist in available icons
    if (in_array($input, $available_icons, true)) {
        return $input;
    }

    // Invalid filename - return empty
    return '';
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
            'sanitize_callback' => 'wp_art_routes_sanitize_icon_filename',
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
        __('Art Routes Settings', 'art-routes'),
        __('Settings', 'art-routes'),
        'manage_options',
        'wp-art-routes-settings',
        'wp_art_routes_render_settings_page',
        101 // Position: last in menu
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
    $current_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'general';

    // Define available tabs
    $tabs = [
        'general' => __('General', 'art-routes'),
        'terminology' => __('Terminology', 'art-routes'),
        'custom_icons' => __('Custom Icons', 'art-routes'),
    ];

    // Show success message if settings were updated
    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'wp_art_routes_messages',
            'wp_art_routes_message',
            __('Settings saved.', 'art-routes'),
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
                case 'custom_icons':
                    wp_art_routes_render_custom_icons_tab();
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
                        <?php esc_html_e('Default Route', 'art-routes'); ?>
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
                        echo '<option value="0">' . esc_html__('Select a default route', 'art-routes') . '</option>';

                        foreach ($routes as $route) {
                            echo '<option value="' . esc_attr($route->ID) . '" ' . selected($default_route_id, $route->ID, false) . '>';
                            echo esc_html($route->post_title);
                            echo '</option>';
                        }

                        echo '</select>';
                        echo '<p class="description">' . esc_html__('This route will be used when no specific route is selected.', 'art-routes') . '</p>';
                    } else {
                        echo '<p>' . esc_html__('No routes available. Please create a route first.', 'art-routes') . '</p>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Location Tracking', 'art-routes'); ?></th>
                <td>
                    <label for="wp_art_routes_enable_location_tracking">
                        <input type="checkbox" name="wp_art_routes_enable_location_tracking" id="wp_art_routes_enable_location_tracking" value="1" <?php checked(get_option('wp_art_routes_enable_location_tracking', true)); ?> />
                        <?php esc_html_e('Enable location tracking for users', 'art-routes'); ?>
                    </label>
                    <p class="description"><?php esc_html_e('When enabled, users will be prompted to share their location to track progress on routes.', 'art-routes'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp_art_routes_default_location_icon">
                        <?php esc_html_e('Default Location Icon', 'art-routes'); ?>
                    </label>
                </th>
                <td>
                    <?php
                    $default_location_icon = get_option('wp_art_routes_default_location_icon', '');
                    $available_icons = wp_art_routes_get_available_icons();
                    ?>
                    <select name="wp_art_routes_default_location_icon" id="wp_art_routes_default_location_icon">
                        <option value=""><?php esc_html_e('No default icon (gray circle)', 'art-routes'); ?></option>
                        <?php foreach ($available_icons as $icon_filename) : ?>
                            <option value="<?php echo esc_attr($icon_filename); ?>" <?php selected($default_location_icon, $icon_filename); ?>>
                                <?php echo esc_html(wp_art_routes_get_icon_display_name($icon_filename)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($default_location_icon)) : ?>
                        <span style="margin-left: 10px; vertical-align: middle;">
                            <img src="<?php echo esc_url(wp_art_routes_get_icon_url($default_location_icon)); ?>" alt="" style="width: 24px; height: 24px; vertical-align: middle;">
                        </span>
                    <?php endif; ?>
                    <p class="description">
                        <?php esc_html_e('Select a default icon for locations that do not have an icon assigned (e.g., imported via GPX).', 'art-routes'); ?>
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
            'label' => __('Route', 'art-routes'),
            'description' => __('The main paths users follow (e.g., "Art Route", "Trail", "Walk")', 'art-routes'),
            'has_slug' => true,
        ],
        'location' => [
            'label' => __('Location', 'art-routes'),
            'description' => __('Points of interest along routes (e.g., "Artwork", "Performance", "Venue")', 'art-routes'),
            'has_slug' => true,
        ],
        'info_point' => [
            'label' => __('Info Point', 'art-routes'),
            'description' => __('Information markers along routes (e.g., "Info Point", "Landmark", "Stop")', 'art-routes'),
            'has_slug' => true,
        ],
        'creator' => [
            'label' => __('Creator', 'art-routes'),
            'description' => __('People or entities associated with locations (e.g., "Artist", "Performer", "Author")', 'art-routes'),
            'has_slug' => false,
        ],
    ];
    ?>
    <form method="post" action="options.php">
        <?php settings_fields('wp_art_routes_terminology_options'); ?>

        <p class="description" style="margin-bottom: 20px;">
            <?php esc_html_e('Customize the labels used throughout the plugin. Leave fields empty to use the default values shown as placeholders.', 'art-routes'); ?>
        </p>

        <?php foreach ($terminology_types as $type => $config) : ?>
            <h3><?php echo esc_html($config['label']); ?></h3>
            <p class="description"><?php echo esc_html($config['description']); ?></p>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="wp_art_routes_terminology_<?php echo esc_attr($type); ?>_singular">
                            <?php esc_html_e('Singular', 'art-routes'); ?>
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
                            <?php esc_html_e('Plural', 'art-routes'); ?>
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
                                <?php esc_html_e('URL Slug', 'art-routes'); ?>
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
                                <?php esc_html_e('The URL-friendly version (lowercase, no spaces). After changing, go to Settings > Permalinks and click Save to refresh URL rules.', 'art-routes'); ?>
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

/**
 * Render the Custom Icons settings tab
 */
function wp_art_routes_render_custom_icons_tab() {
    $custom_icons = wp_art_routes_get_custom_icons();
    $custom_icons_url = wp_art_routes_get_custom_icons_url();
    ?>
    <div class="custom-icons-wrapper">
        <h2><?php esc_html_e('Upload Custom Icons', 'art-routes'); ?></h2>
        <p class="description">
            <?php esc_html_e('Upload custom icons (SVG, PNG, JPG, or WebP) to use for locations and info points. Uploaded icons will appear in all icon selection dropdowns.', 'art-routes'); ?>
        </p>

        <div class="custom-icons-upload-form" style="margin: 20px 0; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
            <form id="custom-icon-upload-form" enctype="multipart/form-data">
                <?php wp_nonce_field('wp_art_routes_upload_icon', 'upload_icon_nonce'); ?>
                <p>
                    <label for="custom_icon_file">
                        <strong><?php esc_html_e('Select Icon File:', 'art-routes'); ?></strong>
                    </label>
                </p>
                <p>
                    <input type="file" name="custom_icon_file" id="custom_icon_file" accept=".svg,.png,.jpg,.jpeg,.webp" required />
                </p>
                <p class="description">
                    <?php esc_html_e('Recommended: SVG files for best quality at any size. PNG/JPG supported for compatibility.', 'art-routes'); ?>
                </p>
                <p>
                    <button type="submit" class="button button-primary" id="upload-icon-btn">
                        <?php esc_html_e('Upload Icon', 'art-routes'); ?>
                    </button>
                    <span id="upload-status" style="margin-left: 10px;"></span>
                </p>
            </form>
        </div>

        <h2><?php esc_html_e('Uploaded Custom Icons', 'art-routes'); ?></h2>

        <?php if (empty($custom_icons)) : ?>
            <p class="description" id="no-custom-icons-message">
                <?php esc_html_e('No custom icons uploaded yet.', 'art-routes'); ?>
            </p>
        <?php endif; ?>

        <div id="custom-icons-grid" class="custom-icons-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px; margin-top: 20px;">
            <?php foreach ($custom_icons as $icon) : ?>
                <div class="custom-icon-item" data-filename="<?php echo esc_attr($icon); ?>" style="background: #fff; border: 1px solid #ddd; border-radius: 4px; padding: 15px; text-align: center;">
                    <div class="icon-preview" style="height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                        <img src="<?php echo esc_url($custom_icons_url . rawurlencode($icon)); ?>" alt="<?php echo esc_attr($icon); ?>" style="max-width: 100%; max-height: 60px;">
                    </div>
                    <div class="icon-filename" style="font-size: 12px; word-break: break-all; margin-bottom: 10px;">
                        <?php echo esc_html($icon); ?>
                    </div>
                    <button type="button" class="button button-small button-link-delete delete-custom-icon" data-filename="<?php echo esc_attr($icon); ?>">
                        <?php esc_html_e('Delete', 'art-routes'); ?>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php
}

/**
 * Handle custom icon upload via AJAX
 */
function wp_art_routes_ajax_upload_custom_icon() {
    // Verify nonce
    if (!isset($_POST['upload_icon_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['upload_icon_nonce'])), 'wp_art_routes_upload_icon')) {
        wp_send_json_error(['message' => __('Security check failed.', 'art-routes')]);
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permission denied.', 'art-routes')]);
    }

    // Check if file was uploaded
    if (!isset($_FILES['custom_icon_file']) || $_FILES['custom_icon_file']['error'] !== UPLOAD_ERR_OK) {
        wp_send_json_error(['message' => __('No file uploaded or upload error.', 'art-routes')]);
    }

    $file = $_FILES['custom_icon_file'];

    // Validate file extension
    $allowed_extensions = ['svg', 'png', 'jpg', 'jpeg', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions, true)) {
        wp_send_json_error(['message' => __('Invalid file type. Allowed: SVG, PNG, JPG, WebP.', 'art-routes')]);
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowed_mimes = [
        'image/svg+xml' => 'svg',
        'image/png' => 'png',
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/webp' => 'webp',
        'text/plain' => 'svg', // Some servers report SVG as text/plain
        'text/html' => 'svg',  // Some servers report SVG as text/html
        'application/xml' => 'svg', // Some servers report SVG as application/xml
    ];

    $mime_valid = false;
    foreach ($allowed_mimes as $mime => $exts) {
        if ($mime_type === $mime) {
            $exts = (array) $exts;
            if (in_array($ext, $exts, true)) {
                $mime_valid = true;
                break;
            }
        }
    }

    if (!$mime_valid) {
        wp_send_json_error(['message' => __('Invalid file type detected.', 'art-routes')]);
    }

    // For SVG files, sanitize the content
    if ($ext === 'svg') {
        // Use WP_Filesystem for reading the file
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }
        $svg_content = $wp_filesystem->get_contents($file['tmp_name']);

        // Load the SVG sanitizer if available
        if (class_exists('WP_Art_Routes_SVG_Sanitizer')) {
            $sanitizer = new WP_Art_Routes_SVG_Sanitizer();
            $sanitized = $sanitizer->sanitize($svg_content);

            if ($sanitized === false) {
                wp_send_json_error(['message' => __('SVG file failed security validation.', 'art-routes')]);
            }

            $svg_content = $sanitized;
        }
    }

    // Get the custom icons directory
    $custom_icons_dir = wp_art_routes_get_custom_icons_dir();
    if (!$custom_icons_dir) {
        wp_send_json_error(['message' => __('Could not create icons directory.', 'art-routes')]);
    }

    // Generate safe filename
    $filename = sanitize_file_name($file['name']);

    // Check if file already exists, add number suffix if so
    $base = pathinfo($filename, PATHINFO_FILENAME);
    $counter = 1;
    while (file_exists($custom_icons_dir . $filename)) {
        $filename = $base . '-' . $counter . '.' . $ext;
        $counter++;
    }

    // Save the file
    $destination = $custom_icons_dir . $filename;

    // Ensure WP_Filesystem is available
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }

    if ($ext === 'svg' && isset($svg_content)) {
        // Write sanitized SVG content using WP_Filesystem
        if (!$wp_filesystem->put_contents($destination, $svg_content, FS_CHMOD_FILE)) {
            wp_send_json_error(['message' => __('Failed to save file.', 'art-routes')]);
        }
    } else {
        // For non-SVG files, read the uploaded file and write to destination using WP_Filesystem
        // This replaces move_uploaded_file() which is forbidden by WordPress Plugin Check
        $file_content = $wp_filesystem->get_contents($file['tmp_name']);
        if ($file_content === false) {
            wp_send_json_error(['message' => __('Failed to read uploaded file.', 'art-routes')]);
        }
        if (!$wp_filesystem->put_contents($destination, $file_content, FS_CHMOD_FILE)) {
            wp_send_json_error(['message' => __('Failed to save file.', 'art-routes')]);
        }
    }

    // Return success
    wp_send_json_success([
        'message' => __('Icon uploaded successfully.', 'art-routes'),
        'filename' => $filename,
        'url' => wp_art_routes_get_custom_icons_url() . rawurlencode($filename),
    ]);
}
add_action('wp_ajax_wp_art_routes_upload_custom_icon', 'wp_art_routes_ajax_upload_custom_icon');

/**
 * Handle custom icon deletion via AJAX
 */
function wp_art_routes_ajax_delete_custom_icon() {
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wp_art_routes_delete_icon')) {
        wp_send_json_error(['message' => __('Security check failed.', 'art-routes')]);
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => __('Permission denied.', 'art-routes')]);
    }

    // Get filename
    $filename = isset($_POST['filename']) ? sanitize_file_name(wp_unslash($_POST['filename'])) : '';

    if (empty($filename)) {
        wp_send_json_error(['message' => __('No filename provided.', 'art-routes')]);
    }

    // Verify this is a custom icon (not a built-in one)
    if (!wp_art_routes_is_custom_icon($filename)) {
        wp_send_json_error(['message' => __('Cannot delete built-in icons.', 'art-routes')]);
    }

    // Get the file path
    $custom_icons_dir = wp_art_routes_get_custom_icons_dir();
    $filepath = $custom_icons_dir . $filename;

    // Delete the file
    if (file_exists($filepath)) {
        wp_delete_file($filepath);
        // Verify file was deleted
        if (file_exists($filepath)) {
            wp_send_json_error(['message' => __('Failed to delete file.', 'art-routes')]);
        }
    }

    wp_send_json_success(['message' => __('Icon deleted successfully.', 'art-routes')]);
}
add_action('wp_ajax_wp_art_routes_delete_custom_icon', 'wp_art_routes_ajax_delete_custom_icon');
