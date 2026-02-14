<?php

/**
 * Plugin Name: Art Routes
 * Plugin URI: https://github.com/Koko-Koding/art-routes
 * Description: Interactive art route maps with OpenStreetMap integration for WordPress. Create custom routes with artworks and points of interest, track user progress, and display interactive maps with Leaflet.js.
 * Version: 2.2.3
 * Author: Drikus Roor - Koko Koding
 * Author URI: https://github.com/drikusroor
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: art-routes
 * Domain Path: /languages
 * Requires at least: 5.6
 * Tested up to: 6.9
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ART_ROUTES_VERSION', '2.2.3');
define('ART_ROUTES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ART_ROUTES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ART_ROUTES_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Note: load_plugin_textdomain() is not needed since WordPress 4.6
// WordPress automatically loads translations for plugins hosted on WordPress.org

/**
 * Flush rewrite rules if plugin version has changed
 *
 * This ensures permalinks work correctly after plugin updates
 * without requiring manual flush via Settings > Permalinks.
 */
function art_routes_maybe_flush_rewrites()
{
    $stored_version = get_option('art_routes_version');

    if ($stored_version !== ART_ROUTES_VERSION) {
        // Migrate CPT names from old generic names to artro_ prefix (one-time)
        art_routes_migrate_cpt_names();

        // Version changed, flush rewrite rules
        flush_rewrite_rules();
        update_option('art_routes_version', ART_ROUTES_VERSION);
    }
}
add_action('init', 'art_routes_maybe_flush_rewrites', 999);

/**
 * One-time migration: rename old CPT slugs in the database
 *
 * Converts post_type values from the old generic names to the new
 * artro_ prefixed names. Safe to run multiple times (idempotent).
 */
function art_routes_migrate_cpt_names()
{
    if (get_option('art_routes_cpt_migration_done')) {
        return;
    }

    global $wpdb;

    $migrations = [
        'art_route'         => 'artro_route',
        'artwork'           => 'artro_artwork',
        'information_point' => 'artro_info_point',
        'edition'           => 'artro_edition',
    ];

    foreach ($migrations as $old => $new) {
        $wpdb->update(
            $wpdb->posts,
            ['post_type' => $new],
            ['post_type' => $old]
        );
    }

    update_option('art_routes_cpt_migration_done', true);
}

// Load required files
require_once ART_ROUTES_PLUGIN_DIR . 'includes/terminology.php';  // Must load first (provides helper functions)
require_once ART_ROUTES_PLUGIN_DIR . 'includes/post-types.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/template-functions.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/scripts.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/shortcodes.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/class-svg-sanitizer.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/settings.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/editions.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/blocks.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/import-export.php';
require_once ART_ROUTES_PLUGIN_DIR . 'includes/edition-dashboard.php';

/**
 * Activation hook
 */
function art_routes_activate()
{
    // Trigger our function that registers the custom post types
    require_once ART_ROUTES_PLUGIN_DIR . 'includes/post-types.php';
    art_routes_register_post_types();

    // Register the Edition CPT
    require_once ART_ROUTES_PLUGIN_DIR . 'includes/editions.php';
    art_routes_register_edition_post_type();

    // Clear the permalinks after the post types have been registered
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'art_routes_activate');

/**
 * Deactivation hook
 */
function art_routes_deactivate()
{
    // Unregister the post types so the rules are no longer in memory
    unregister_post_type('artro_route');
    unregister_post_type('artro_artwork');
    unregister_post_type('artro_info_point');
    unregister_post_type('artro_edition');

    // Clear the permalinks
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'art_routes_deactivate');

/**
 * Uninstall hook - Clean up plugin data when deleted
 */
function art_routes_uninstall()
{
    // Remove plugin options
    delete_option('art_routes_default_route_id');
    delete_option('art_routes_enable_location_tracking');
    delete_option('art_routes_terminology');

    // Remove user meta data
    delete_metadata('user', 0, 'art_routes_visited_artworks', '', true);

    // Note: We don't delete custom post types and their data
    // as users may want to keep their routes and artworks
    // even after uninstalling the plugin
}
register_uninstall_hook(__FILE__, 'art_routes_uninstall');
