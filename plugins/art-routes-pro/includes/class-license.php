<?php
/**
 * LemonSqueezy license management.
 *
 * Handles activation, deactivation, periodic validation,
 * and the admin UI for entering a license key.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Art_Routes_Pro_License {

    const OPTION_KEY        = 'art_routes_pro_license';
    const VALIDATION_CACHE  = 'art_routes_pro_license_valid';
    const API_BASE          = 'https://api.lemonsqueezy.com/v1/licenses';

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_art_routes_pro_activate', array( $this, 'ajax_activate' ) );
        add_action( 'wp_ajax_art_routes_pro_deactivate', array( $this, 'ajax_deactivate' ) );
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    /**
     * Check if the current license is valid (uses transient cache).
     */
    public function is_valid() {
        $cached = get_transient( self::VALIDATION_CACHE );
        if ( false !== $cached ) {
            return (bool) $cached;
        }

        $data = $this->get_license_data();
        if ( empty( $data['key'] ) || empty( $data['instance_id'] ) ) {
            return false;
        }

        $valid = $this->validate_remote( $data['key'], $data['instance_id'] );
        set_transient( self::VALIDATION_CACHE, $valid ? 1 : 0, DAY_IN_SECONDS );

        return $valid;
    }

    /**
     * Get stored license data.
     */
    public function get_license_data() {
        return wp_parse_args( get_option( self::OPTION_KEY, array() ), array(
            'key'         => '',
            'instance_id' => '',
            'status'      => '',
            'customer'    => '',
            'product'     => '',
        ) );
    }

    // ------------------------------------------------------------------
    // Admin UI
    // ------------------------------------------------------------------

    public function add_menu_page() {
        // Add as submenu under the free plugin's menu.
        add_submenu_page(
            'edit.php?post_type=edition',
            __( 'Pro License', 'art-routes-pro' ),
            __( 'Pro License', 'art-routes-pro' ),
            'manage_options',
            'art-routes-pro-license',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'edition_page_art-routes-pro-license' !== $hook ) {
            return;
        }
        wp_enqueue_style(
            'art-routes-pro-admin',
            ART_ROUTES_PRO_URL . 'assets/css/admin.css',
            array(),
            ART_ROUTES_PRO_VERSION
        );
    }

    public function render_page() {
        $data  = $this->get_license_data();
        $valid = $this->is_valid();
        ?>
        <div class="wrap art-routes-pro-license-wrap">
            <h1><?php esc_html_e( 'Art Routes Pro License', 'art-routes-pro' ); ?></h1>

            <div class="art-routes-pro-license-card">
                <?php if ( $valid ) : ?>
                    <div class="art-routes-pro-status art-routes-pro-status--active">
                        <?php esc_html_e( 'License Active', 'art-routes-pro' ); ?>
                    </div>
                    <table class="art-routes-pro-license-details">
                        <tr>
                            <th><?php esc_html_e( 'License Key', 'art-routes-pro' ); ?></th>
                            <td><code><?php echo esc_html( $this->mask_key( $data['key'] ) ); ?></code></td>
                        </tr>
                        <?php if ( ! empty( $data['customer'] ) ) : ?>
                        <tr>
                            <th><?php esc_html_e( 'Customer', 'art-routes-pro' ); ?></th>
                            <td><?php echo esc_html( $data['customer'] ); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ( ! empty( $data['product'] ) ) : ?>
                        <tr>
                            <th><?php esc_html_e( 'Product', 'art-routes-pro' ); ?></th>
                            <td><?php echo esc_html( $data['product'] ); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                    <p>
                        <button type="button" class="button" id="art-routes-pro-deactivate">
                            <?php esc_html_e( 'Deactivate License', 'art-routes-pro' ); ?>
                        </button>
                    </p>
                <?php else : ?>
                    <div class="art-routes-pro-status art-routes-pro-status--inactive">
                        <?php esc_html_e( 'No Active License', 'art-routes-pro' ); ?>
                    </div>
                    <p class="description">
                        <?php esc_html_e( 'Enter your license key to activate Pro features.', 'art-routes-pro' ); ?>
                    </p>
                    <p>
                        <input type="text" id="art-routes-pro-key" class="regular-text"
                               placeholder="<?php esc_attr_e( 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX', 'art-routes-pro' ); ?>" />
                    </p>
                    <p>
                        <button type="button" class="button button-primary" id="art-routes-pro-activate">
                            <?php esc_html_e( 'Activate License', 'art-routes-pro' ); ?>
                        </button>
                    </p>
                <?php endif; ?>

                <div id="art-routes-pro-message" style="display:none;"></div>
            </div>

            <p class="description">
                <?php
                printf(
                    /* translators: %s: purchase URL */
                    esc_html__( 'Don\'t have a license key? %s', 'art-routes-pro' ),
                    '<a href="https://studio-roor.lemonsqueezy.com" target="_blank">' .
                    esc_html__( 'Purchase one here', 'art-routes-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>

        <script>
        (function($) {
            var $msg = $('#art-routes-pro-message');

            function showMessage(text, type) {
                $msg.removeClass('notice-success notice-error')
                    .addClass('notice notice-' + type)
                    .html('<p>' + text + '</p>')
                    .show();
            }

            $('#art-routes-pro-activate').on('click', function() {
                var key = $('#art-routes-pro-key').val().trim();
                if (!key) return;

                var $btn = $(this).prop('disabled', true).text('<?php echo esc_js( __( 'Activating...', 'art-routes-pro' ) ); ?>');

                $.post(ajaxurl, {
                    action: 'art_routes_pro_activate',
                    license_key: key,
                    _wpnonce: '<?php echo esc_js( wp_create_nonce( 'art_routes_pro_license' ) ); ?>'
                }, function(res) {
                    if (res.success) {
                        showMessage(res.data.message, 'success');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        showMessage(res.data.message, 'error');
                        $btn.prop('disabled', false).text('<?php echo esc_js( __( 'Activate License', 'art-routes-pro' ) ); ?>');
                    }
                }).fail(function() {
                    showMessage('<?php echo esc_js( __( 'Request failed. Please try again.', 'art-routes-pro' ) ); ?>', 'error');
                    $btn.prop('disabled', false).text('<?php echo esc_js( __( 'Activate License', 'art-routes-pro' ) ); ?>');
                });
            });

            $('#art-routes-pro-deactivate').on('click', function() {
                if (!confirm('<?php echo esc_js( __( 'Deactivate this license?', 'art-routes-pro' ) ); ?>')) return;

                var $btn = $(this).prop('disabled', true).text('<?php echo esc_js( __( 'Deactivating...', 'art-routes-pro' ) ); ?>');

                $.post(ajaxurl, {
                    action: 'art_routes_pro_deactivate',
                    _wpnonce: '<?php echo esc_js( wp_create_nonce( 'art_routes_pro_license' ) ); ?>'
                }, function(res) {
                    if (res.success) {
                        showMessage(res.data.message, 'success');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        showMessage(res.data.message, 'error');
                        $btn.prop('disabled', false).text('<?php echo esc_js( __( 'Deactivate License', 'art-routes-pro' ) ); ?>');
                    }
                });
            });
        })(jQuery);
        </script>
        <?php
    }

    // ------------------------------------------------------------------
    // AJAX handlers
    // ------------------------------------------------------------------

    public function ajax_activate() {
        check_ajax_referer( 'art_routes_pro_license', '_wpnonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'art-routes-pro' ) ) );
        }

        $key = sanitize_text_field( wp_unslash( $_POST['license_key'] ?? '' ) );
        if ( empty( $key ) ) {
            wp_send_json_error( array( 'message' => __( 'Please enter a license key.', 'art-routes-pro' ) ) );
        }

        $result = $this->activate_remote( $key );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'License activated successfully!', 'art-routes-pro' ) ) );
    }

    public function ajax_deactivate() {
        check_ajax_referer( 'art_routes_pro_license', '_wpnonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'art-routes-pro' ) ) );
        }

        $result = $this->deactivate_remote();

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }

        wp_send_json_success( array( 'message' => __( 'License deactivated.', 'art-routes-pro' ) ) );
    }

    // ------------------------------------------------------------------
    // LemonSqueezy API
    // ------------------------------------------------------------------

    /**
     * Activate a license key for this site.
     */
    private function activate_remote( $key ) {
        $response = wp_remote_post( self::API_BASE . '/activate', array(
            'timeout' => 15,
            'body'    => array(
                'license_key'   => $key,
                'instance_name' => $this->get_instance_name(),
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', __( 'Could not reach the license server. Please try again.', 'art-routes-pro' ) );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( empty( $body['activated'] ) && empty( $body['valid'] ) ) {
            $error = $body['error'] ?? __( 'Activation failed. Please check your license key.', 'art-routes-pro' );
            return new WP_Error( 'activation_failed', $error );
        }

        // Store license data.
        $license_data = array(
            'key'         => $key,
            'instance_id' => $body['instance']['id'] ?? '',
            'status'      => $body['license_key']['status'] ?? 'active',
            'customer'    => $body['meta']['customer_name'] ?? '',
            'product'     => $body['meta']['variant_name'] ?? $body['meta']['product_name'] ?? '',
        );

        update_option( self::OPTION_KEY, $license_data );
        delete_transient( self::VALIDATION_CACHE );
        set_transient( self::VALIDATION_CACHE, 1, DAY_IN_SECONDS );

        return true;
    }

    /**
     * Deactivate the current license for this site.
     */
    private function deactivate_remote() {
        $data = $this->get_license_data();

        if ( empty( $data['key'] ) || empty( $data['instance_id'] ) ) {
            delete_option( self::OPTION_KEY );
            delete_transient( self::VALIDATION_CACHE );
            return true;
        }

        $response = wp_remote_post( self::API_BASE . '/deactivate', array(
            'timeout' => 15,
            'body'    => array(
                'license_key' => $data['key'],
                'instance_id' => $data['instance_id'],
            ),
        ) );

        // Clear local data regardless of API response.
        delete_option( self::OPTION_KEY );
        delete_transient( self::VALIDATION_CACHE );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'api_error', __( 'License deactivated locally, but could not reach the license server.', 'art-routes-pro' ) );
        }

        return true;
    }

    /**
     * Validate license key remotely.
     */
    private function validate_remote( $key, $instance_id ) {
        $response = wp_remote_post( self::API_BASE . '/validate', array(
            'timeout' => 15,
            'body'    => array(
                'license_key' => $key,
                'instance_id' => $instance_id,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            // If we can't reach the server, assume valid to avoid locking out users.
            return true;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $body['valid'] ) ) {
            // Update stored status.
            $data = $this->get_license_data();
            $data['status'] = $body['license_key']['status'] ?? $data['status'];
            update_option( self::OPTION_KEY, $data );
            return true;
        }

        return false;
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function get_instance_name() {
        return wp_parse_url( home_url(), PHP_URL_HOST );
    }

    private function mask_key( $key ) {
        if ( strlen( $key ) <= 8 ) {
            return str_repeat( '*', strlen( $key ) );
        }
        return substr( $key, 0, 4 ) . str_repeat( '*', strlen( $key ) - 8 ) . substr( $key, -4 );
    }
}
