<?php
/**
 * Plugin Name: WP Art Routes
 * Plugin URI: https://example.com/wp-art-routes
 * Description: Interactive art route maps with OpenStreetMap integration for WordPress
 * Version: 1.2.2
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-art-routes
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WP_ART_ROUTES_VERSION', '1.2.2');
define('WP_ART_ROUTES_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_ART_ROUTES_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_ART_ROUTES_PLUGIN_BASENAME', plugin_basename(__FILE__));

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
function wp_art_routes_activate() {
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
function wp_art_routes_deactivate() {
    // Unregister the post types so the rules are no longer in memory
    unregister_post_type('art_route');
    unregister_post_type('artwork');
    
    // Clear the permalinks
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'wp_art_routes_deactivate');