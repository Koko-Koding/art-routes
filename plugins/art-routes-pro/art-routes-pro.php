<?php
/**
 * Plugin Name: Art Routes Pro
 * Plugin URI: https://example.com/art-routes-pro
 * Description: Premium add-on for Art Routes — adds QR codes, visitor analytics, PDF exports, and more.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Drikus Roor
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: art-routes-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'ART_ROUTES_PRO_VERSION', '1.0.0' );
define( 'ART_ROUTES_PRO_FILE', __FILE__ );
define( 'ART_ROUTES_PRO_DIR', plugin_dir_path( __FILE__ ) );
define( 'ART_ROUTES_PRO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if the free Art Routes plugin is active.
 */
function art_routes_pro_check_requirements() {
    if ( ! defined( 'WP_ART_ROUTES_VERSION' ) ) {
        add_action( 'admin_notices', 'art_routes_pro_missing_free_notice' );
        return false;
    }
    return true;
}

function art_routes_pro_missing_free_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <strong>Art Routes Pro</strong> requires the free
            <a href="https://wordpress.org/plugins/art-routes/">Art Routes</a>
            plugin to be installed and activated.
        </p>
    </div>
    <?php
}

/**
 * Initialize the plugin after all plugins are loaded.
 */
function art_routes_pro_init() {
    if ( ! art_routes_pro_check_requirements() ) {
        return;
    }

    require_once ART_ROUTES_PRO_DIR . 'includes/class-license.php';
    require_once ART_ROUTES_PRO_DIR . 'includes/class-updater.php';
    require_once ART_ROUTES_PRO_DIR . 'includes/class-pro-features.php';

    Art_Routes_Pro_License::instance();
    Art_Routes_Pro_Updater::instance();
    Art_Routes_Pro_Features::instance();
}
add_action( 'plugins_loaded', 'art_routes_pro_init' );

/**
 * Helper to check if Pro is licensed and active.
 */
function art_routes_pro_is_licensed() {
    if ( ! class_exists( 'Art_Routes_Pro_License' ) ) {
        return false;
    }
    return Art_Routes_Pro_License::instance()->is_valid();
}
