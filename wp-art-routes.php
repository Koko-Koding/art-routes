<?php

/**
 * Plugin Name: WP Art Routes
 * Plugin URI: https://github.com/Koko-Koding/wp-art-routes
 * Description: Interactive art route maps with OpenStreetMap integration for WordPress. Create custom routes with artworks and points of interest, track user progress, and display interactive maps with Leaflet.js.
 * Version: wenb-1.23.0
 * Author: Drikus Roor - Koko Koding
 * Author URI: https://github.com/drikusroor
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-art-routes
 * Domain Path: /languages
 * Requires at least: 5.6
 * Tested up to: 6.6
 * Requires PHP: 7.4
 * Network: false
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_ART_ROUTES_VERSION', 'wenb-1.23.0');
define('WP_ART_ROUTES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_ART_ROUTES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_ART_ROUTES_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Load plugin text domain for translations
 */
function wp_art_routes_load_textdomain()
{
    load_plugin_textdomain(
        'wp-art-routes',
        false,
        dirname(WP_ART_ROUTES_PLUGIN_BASENAME) . '/languages'
    );
}
add_action('plugins_loaded', 'wp_art_routes_load_textdomain');

// Load required files
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/post-types.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/template-functions.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/scripts.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/shortcodes.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/ajax-handlers.php';
require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/settings.php';

/**
 * Activation hook
 */
function wp_art_routes_activate()
{
    // Trigger our function that registers the custom post types
    require_once WP_ART_ROUTES_PLUGIN_DIR . 'includes/post-types.php';
    wp_art_routes_register_post_types();

    // Clear the permalinks after the post types have been registered
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wp_art_routes_activate');

/**
 * Deactivation hook
 */
function wp_art_routes_deactivate()
{
    // Unregister the post types so the rules are no longer in memory
    unregister_post_type('art_route');
    unregister_post_type('artwork');

    // Clear the permalinks
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wp_art_routes_deactivate');

/**
 * Uninstall hook - Clean up plugin data when deleted
 */
function wp_art_routes_uninstall()
{
    // Remove plugin options
    delete_option('wp_art_routes_default_route_id');
    delete_option('wp_art_routes_enable_location_tracking');
    
    // Remove user meta data
    delete_metadata('user', 0, 'wp_art_routes_visited_artworks', '', true);
    
    // Note: We don't delete custom post types and their data
    // as users may want to keep their routes and artworks
    // even after uninstalling the plugin
}
register_uninstall_hook(__FILE__, 'wp_art_routes_uninstall');
