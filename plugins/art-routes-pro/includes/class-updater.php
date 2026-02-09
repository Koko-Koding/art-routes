<?php
/**
 * Self-hosted plugin updater.
 *
 * Checks a remote JSON endpoint for new versions and serves updates
 * to licensed users. Works with GitHub releases or any static host.
 *
 * Remote JSON format (host at e.g. https://example.com/art-routes-pro/update.json):
 * {
 *   "version": "1.1.0",
 *   "download_url": "https://example.com/art-routes-pro/art-routes-pro-1.1.0.zip",
 *   "requires": "5.8",
 *   "requires_php": "7.4",
 *   "tested": "6.7",
 *   "changelog": "<ul><li>New feature X</li></ul>"
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Art_Routes_Pro_Updater {

    /**
     * URL to the remote JSON file that describes the latest version.
     * Change this to your actual update endpoint.
     */
    const UPDATE_URL = 'https://example.com/art-routes-pro/update.json';

    const CACHE_KEY     = 'art_routes_pro_update_data';
    const CACHE_TTL     = 12 * HOUR_IN_SECONDS;
    const PLUGIN_SLUG   = 'art-routes-pro/art-routes-pro.php';

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_update' ) );
        add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
        add_action( 'upgrader_process_complete', array( $this, 'clear_cache' ), 10, 0 );
    }

    /**
     * Inject update info into WordPress's update transient.
     */
    public function check_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $remote = $this->get_remote_data();
        if ( ! $remote ) {
            return $transient;
        }

        $current_version = $transient->checked[ self::PLUGIN_SLUG ] ?? ART_ROUTES_PRO_VERSION;

        if ( version_compare( $remote['version'], $current_version, '>' ) ) {
            $update = (object) array(
                'slug'        => 'art-routes-pro',
                'plugin'      => self::PLUGIN_SLUG,
                'new_version' => $remote['version'],
                'url'         => $remote['homepage'] ?? '',
                'package'     => $this->get_download_url( $remote ),
                'tested'      => $remote['tested'] ?? '',
                'requires'    => $remote['requires'] ?? '5.8',
                'requires_php' => $remote['requires_php'] ?? '7.4',
            );

            $transient->response[ self::PLUGIN_SLUG ] = $update;
        } else {
            // Tell WordPress we checked and there's no update.
            $transient->no_update[ self::PLUGIN_SLUG ] = (object) array(
                'slug'        => 'art-routes-pro',
                'plugin'      => self::PLUGIN_SLUG,
                'new_version' => $current_version,
            );
        }

        return $transient;
    }

    /**
     * Provide plugin info for the "View Details" modal.
     */
    public function plugin_info( $result, $action, $args ) {
        if ( 'plugin_information' !== $action || 'art-routes-pro' !== ( $args->slug ?? '' ) ) {
            return $result;
        }

        $remote = $this->get_remote_data();
        if ( ! $remote ) {
            return $result;
        }

        return (object) array(
            'name'          => 'Art Routes Pro',
            'slug'          => 'art-routes-pro',
            'version'       => $remote['version'],
            'author'        => '<a href="https://example.com">Drikus Roor</a>',
            'homepage'      => $remote['homepage'] ?? '',
            'requires'      => $remote['requires'] ?? '5.8',
            'requires_php'  => $remote['requires_php'] ?? '7.4',
            'tested'        => $remote['tested'] ?? '',
            'download_link' => $this->get_download_url( $remote ),
            'sections'      => array(
                'changelog' => $remote['changelog'] ?? '',
            ),
        );
    }

    /**
     * Clear cached update data after an upgrade.
     */
    public function clear_cache() {
        delete_transient( self::CACHE_KEY );
    }

    // ------------------------------------------------------------------
    // Private
    // ------------------------------------------------------------------

    /**
     * Fetch and cache remote update data.
     */
    private function get_remote_data() {
        $cached = get_transient( self::CACHE_KEY );
        if ( false !== $cached ) {
            return $cached;
        }

        $response = wp_remote_get( self::UPDATE_URL, array( 'timeout' => 10 ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            // Cache the failure briefly to avoid hammering.
            set_transient( self::CACHE_KEY, array(), 15 * MINUTE_IN_SECONDS );
            return null;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $data['version'] ) ) {
            return null;
        }

        set_transient( self::CACHE_KEY, $data, self::CACHE_TTL );
        return $data;
    }

    /**
     * Get download URL — only return it if the user has a valid license.
     */
    private function get_download_url( $remote ) {
        if ( ! art_routes_pro_is_licensed() ) {
            return '';
        }

        // Append license key to download URL for server-side verification.
        $url  = $remote['download_url'] ?? '';
        $data = Art_Routes_Pro_License::instance()->get_license_data();

        if ( ! empty( $url ) && ! empty( $data['key'] ) ) {
            $url = add_query_arg( 'license_key', $data['key'], $url );
        }

        return $url;
    }
}
