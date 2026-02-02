<?php
/**
 * Terminology System for WP Art Routes Plugin
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
function wp_art_routes_get_default_terminology() {
    return [
        'route' => [
            'singular' => __('Route', 'wp-art-routes'),
            'plural'   => __('Routes', 'wp-art-routes'),
            'slug'     => 'route',
        ],
        'location' => [
            'singular' => __('Location', 'wp-art-routes'),
            'plural'   => __('Locations', 'wp-art-routes'),
            'slug'     => 'location',
        ],
        'info_point' => [
            'singular' => __('Info Point', 'wp-art-routes'),
            'plural'   => __('Info Points', 'wp-art-routes'),
            'slug'     => 'info-point',
        ],
        'creator' => [
            'singular' => __('Artist', 'wp-art-routes'),
            'plural'   => __('Artists', 'wp-art-routes'),
        ],
    ];
}

/**
 * Get global terminology settings from database
 *
 * Retrieves the 'wp_art_routes_terminology' option and merges it
 * with the hardcoded defaults to ensure all keys are present.
 *
 * @return array Global terminology settings merged with defaults
 */
function wp_art_routes_get_global_terminology() {
    $defaults = wp_art_routes_get_default_terminology();
    $saved = get_option('wp_art_routes_terminology', []);

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
function wp_art_routes_get_edition_terminology($edition_id) {
    $global = wp_art_routes_get_global_terminology();

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
function wp_art_routes_label($type, $plural = false, $edition_id = null) {
    // Get the appropriate terminology based on whether edition is provided
    if ($edition_id) {
        $terminology = wp_art_routes_get_edition_terminology($edition_id);
    } else {
        $terminology = wp_art_routes_get_global_terminology();
    }

    // Get the label key (singular or plural)
    $key = $plural ? 'plural' : 'singular';

    // Return the label if it exists
    if (isset($terminology[$type][$key])) {
        return $terminology[$type][$key];
    }

    // Fallback to defaults if type doesn't exist
    $defaults = wp_art_routes_get_default_terminology();
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
function wp_art_routes_slug($type) {
    $terminology = wp_art_routes_get_global_terminology();

    // Return the slug if it exists
    if (isset($terminology[$type]['slug'])) {
        return $terminology[$type]['slug'];
    }

    // Fallback to defaults
    $defaults = wp_art_routes_get_default_terminology();
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
function wp_art_routes_detect_edition_context() {
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
