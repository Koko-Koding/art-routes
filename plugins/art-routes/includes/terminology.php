<?php
/**
 * Terminology System for Art Routes Plugin
 *
 * Provides customizable labels with cascade priority:
 * Edition override -> Global setting -> Hardcoded default
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get hardcoded default terminology values
 *
 * These are the fallback values when no global or edition-specific
 * terminology has been configured.
 *
 * @return array Default terminology settings
 */
function art_routes_get_default_terminology() {
    return [
        'route' => [
            'singular' => __('Route', 'art-routes'),
            'plural'   => __('Routes', 'art-routes'),
            'slug'     => 'route',
        ],
        'location' => [
            'singular' => __('Location', 'art-routes'),
            'plural'   => __('Locations', 'art-routes'),
            'slug'     => 'location',
        ],
        'info_point' => [
            'singular' => __('Info Point', 'art-routes'),
            'plural'   => __('Info Points', 'art-routes'),
            'slug'     => 'info-point',
        ],
        'creator' => [
            'singular' => __('Artist', 'art-routes'),
            'plural'   => __('Artists', 'art-routes'),
        ],
    ];
}

/**
 * Get global terminology settings from database
 *
 * Retrieves the 'art_routes_terminology' option and merges it
 * with the hardcoded defaults to ensure all keys are present.
 *
 * @return array Global terminology settings merged with defaults
 */
function art_routes_get_global_terminology() {
    $defaults = art_routes_get_default_terminology();
    $saved = get_option('art_routes_terminology', []);

    // Deep merge saved settings with defaults
    $merged = $defaults;

    if (is_array($saved)) {
        foreach ($saved as $type => $values) {
            if (isset($merged[$type]) && is_array($values)) {
                foreach ($values as $key => $value) {
                    // Only use saved value if it's not empty
                    if (!empty($value)) {
                        $merged[$type][$key] = $value;
                    }
                }
            }
        }
    }

    return $merged;
}

/**
 * Get terminology settings for a specific edition
 *
 * Retrieves the edition's '_edition_terminology' meta and merges it
 * with global settings. Empty overrides fall back to global settings.
 *
 * @param int $edition_id The edition post ID
 * @return array Edition terminology settings merged with global settings
 */
function art_routes_get_edition_terminology($edition_id) {
    $global = art_routes_get_global_terminology();

    if (empty($edition_id)) {
        return $global;
    }

    $edition_terminology = get_post_meta($edition_id, '_edition_terminology', true);

    // Deep merge edition settings with global settings
    $merged = $global;

    if (is_array($edition_terminology)) {
        foreach ($edition_terminology as $type => $values) {
            if (isset($merged[$type]) && is_array($values)) {
                foreach ($values as $key => $value) {
                    // Only use edition value if it's not empty
                    if (!empty($value)) {
                        $merged[$type][$key] = $value;
                    }
                }
            }
        }
    }

    return $merged;
}

/**
 * Get a terminology label
 *
 * Main helper function for retrieving terminology labels with cascade:
 * 1. Edition override (if edition_id provided)
 * 2. Global settings
 * 3. Hardcoded defaults
 *
 * @param string   $type       The terminology type: 'route', 'location', 'info_point', 'creator'
 * @param bool     $plural     Whether to return the plural form (default: false)
 * @param int|null $edition_id Optional edition ID to check for overrides
 * @return string The label for the requested type
 */
function art_routes_label($type, $plural = false, $edition_id = null) {
    // Get the appropriate terminology based on whether edition is provided
    if ($edition_id) {
        $terminology = art_routes_get_edition_terminology($edition_id);
    } else {
        $terminology = art_routes_get_global_terminology();
    }

    // Get the label key (singular or plural)
    $key = $plural ? 'plural' : 'singular';

    // Return the label if it exists
    if (isset($terminology[$type][$key])) {
        return $terminology[$type][$key];
    }

    // Fallback to defaults if type doesn't exist
    $defaults = art_routes_get_default_terminology();
    if (isset($defaults[$type][$key])) {
        return $defaults[$type][$key];
    }

    // Last resort: return the type name itself, capitalized
    return ucfirst(str_replace('_', ' ', $type));
}

/**
 * Get URL slug for a terminology type
 *
 * Retrieves the URL slug from global terminology settings.
 * Note: Slugs are only available for route, location, and info_point types.
 *
 * @param string $type The terminology type: 'route', 'location', 'info_point'
 * @return string The URL slug for the type
 */
function art_routes_slug($type) {
    $terminology = art_routes_get_global_terminology();

    // Return the slug if it exists
    if (isset($terminology[$type]['slug'])) {
        return $terminology[$type]['slug'];
    }

    // Fallback to defaults
    $defaults = art_routes_get_default_terminology();
    if (isset($defaults[$type]['slug'])) {
        return $defaults[$type]['slug'];
    }

    // Last resort: sanitize the type name as a slug
    return sanitize_title(str_replace('_', '-', $type));
}

/**
 * Auto-detect edition context
 *
 * Attempts to determine the current edition from:
 * 1. Current Edition single page (is_singular('edition'))
 * 2. Current post's _edition_id meta
 *
 * @return int|null Edition ID if detected, null otherwise
 */
function art_routes_detect_edition_context() {
    // Check if we're on an edition single page
    if (is_singular('edition')) {
        return get_the_ID();
    }

    // Check if current post has an edition assigned
    $current_post_id = get_the_ID();
    if ($current_post_id) {
        $edition_id = get_post_meta($current_post_id, '_edition_id', true);
        if (!empty($edition_id)) {
            return (int) $edition_id;
        }
    }

    return null;
}

/**
 * Get available icons from both built-in and custom uploaded directories
 *
 * @return array Array of icon filenames (SVG, PNG, JPG, WEBP)
 */
function art_routes_get_available_icons() {
    $available_icons = [];

    // Get built-in icons
    $icons_dir = plugin_dir_path(dirname(__FILE__)) . 'assets/icons/';
    if (is_dir($icons_dir)) {
        $files = scandir($icons_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'svg') {
                $available_icons[] = $file;
            }
        }
    }

    // Get custom uploaded icons
    $upload_dir = wp_upload_dir();
    $custom_icons_dir = $upload_dir['basedir'] . '/art-routes-icons/';
    if (is_dir($custom_icons_dir)) {
        $files = scandir($custom_icons_dir);
        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['svg', 'png', 'jpg', 'jpeg', 'webp'], true)) {
                // Avoid duplicates (custom icons override built-in with same name)
                if (!in_array($file, $available_icons, true)) {
                    $available_icons[] = $file;
                }
            }
        }
    }

    sort($available_icons);
    return $available_icons;
}

/**
 * Get custom uploaded icons only
 *
 * @return array Array of custom icon filenames
 */
function art_routes_get_custom_icons() {
    $custom_icons = [];

    $upload_dir = wp_upload_dir();
    $custom_icons_dir = $upload_dir['basedir'] . '/art-routes-icons/';

    if (is_dir($custom_icons_dir)) {
        $files = scandir($custom_icons_dir);
        foreach ($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, ['svg', 'png', 'jpg', 'jpeg', 'webp'], true)) {
                $custom_icons[] = $file;
            }
        }
        sort($custom_icons);
    }

    return $custom_icons;
}

/**
 * Check if an icon is a custom uploaded icon
 *
 * @param string $filename The icon filename
 * @return bool True if custom icon, false if built-in
 */
function art_routes_is_custom_icon($filename) {
    $upload_dir = wp_upload_dir();
    $custom_icons_dir = $upload_dir['basedir'] . '/art-routes-icons/';

    return file_exists($custom_icons_dir . $filename);
}

/**
 * Get the custom icons directory path
 *
 * Creates the directory if it doesn't exist.
 *
 * @return string|false The directory path or false on failure
 */
function art_routes_get_custom_icons_dir() {
    $upload_dir = wp_upload_dir();
    $custom_icons_dir = $upload_dir['basedir'] . '/art-routes-icons/';

    if (!is_dir($custom_icons_dir)) {
        if (!wp_mkdir_p($custom_icons_dir)) {
            return false;
        }
    }

    return $custom_icons_dir;
}

/**
 * Get the custom icons URL
 *
 * @return string The custom icons URL
 */
function art_routes_get_custom_icons_url() {
    $upload_dir = wp_upload_dir();
    return $upload_dir['baseurl'] . '/art-routes-icons/';
}

/**
 * Get the full URL for an icon file
 *
 * Handles both built-in icons and custom uploaded icons.
 * Automatically URL-encodes the filename for filenames with spaces.
 *
 * @param string $filename The icon filename
 * @return string The full URL to the icon (URL-encoded)
 */
function art_routes_get_icon_url($filename) {
    if (empty($filename)) {
        return '';
    }

    // Check if it's a custom uploaded icon
    $upload_dir = wp_upload_dir();
    $custom_icons_dir = $upload_dir['basedir'] . '/art-routes-icons/';
    $custom_icons_url = $upload_dir['baseurl'] . '/art-routes-icons/';

    if (file_exists($custom_icons_dir . $filename)) {
        return $custom_icons_url . rawurlencode($filename);
    }

    // Default to built-in icons
    return plugin_dir_url(dirname(__FILE__)) . 'assets/icons/' . rawurlencode($filename);
}

/**
 * Get display name for an icon file
 *
 * Converts the filename to a human-readable display name by:
 * - Handling numbered icons specially (number-1 -> #1)
 * - Removing common prefixes ('WB plattegrond-')
 * - Replacing dashes/underscores with spaces
 * - Capitalizing words
 *
 * @param string $filename The icon filename
 * @return string Human-readable display name
 */
function art_routes_get_icon_display_name($filename) {
    $icon_name = pathinfo($filename, PATHINFO_FILENAME);

    // Handle numbered icons specially
    if (preg_match('/^number-(\d+)$/', $icon_name, $matches)) {
        return '#' . $matches[1];
    }

    // Remove common prefixes and clean up (support both old and new naming)
    $display_name = str_replace(['WB-plattegrond-', 'WB plattegrond-'], ['', ''], $icon_name);
    $display_name = str_replace(['-', '_'], ' ', $display_name);
    return ucwords(trim($display_name));
}

/**
 * Get the default info point icon filename
 *
 * @return string Default info point icon filename
 */
function art_routes_get_default_info_icon() {
    return 'info.svg';
}
