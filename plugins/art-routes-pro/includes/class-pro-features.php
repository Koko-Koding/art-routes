<?php
/**
 * Pro features registry.
 *
 * Registers and gates premium features behind a valid license.
 * Each feature is a self-contained class loaded from includes/features/.
 *
 * To add a new pro feature:
 * 1. Create includes/features/class-feature-{slug}.php
 * 2. Register it in the register_features() method below
 * 3. The feature class should have an init() method
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Art_Routes_Pro_Features {

    private static $instance = null;
    private $features = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->register_features();
        $this->load_features();

        // Let the free plugin check if Pro is available.
        add_filter( 'art_routes_pro_active', '__return_true' );
        add_filter( 'art_routes_pro_is_licensed', array( $this, 'filter_is_licensed' ) );
        add_filter( 'art_routes_pro_feature_enabled', array( $this, 'filter_feature_enabled' ), 10, 2 );

        // Show Pro status in admin.
        add_action( 'admin_notices', array( $this, 'maybe_show_unlicensed_notice' ) );
    }

    /**
     * Register available pro features.
     *
     * Each feature has:
     * - label: human-readable name
     * - description: what it does
     * - file: path relative to includes/features/
     * - class: class name
     * - requires_license: whether a valid license is needed (default true)
     */
    private function register_features() {
        $this->features = array(
            'qr-codes' => array(
                'label'       => __( 'QR Code Generator', 'art-routes-pro' ),
                'description' => __( 'Generate QR codes for each location for use on physical signage.', 'art-routes-pro' ),
                'file'        => 'class-feature-qr-codes.php',
                'class'       => 'Art_Routes_Pro_Feature_QR_Codes',
            ),

            // Future features:
            //
            // 'analytics' => array(
            //     'label'       => __( 'Visitor Analytics', 'art-routes-pro' ),
            //     'description' => __( 'Track location visits and route completions.', 'art-routes-pro' ),
            //     'file'        => 'class-feature-analytics.php',
            //     'class'       => 'Art_Routes_Pro_Feature_Analytics',
            // ),
            //
            // 'pdf-export' => array(
            //     'label'       => __( 'PDF Route Guides', 'art-routes-pro' ),
            //     'description' => __( 'Generate printable PDF route guides with maps and location details.', 'art-routes-pro' ),
            //     'file'        => 'class-feature-pdf-export.php',
            //     'class'       => 'Art_Routes_Pro_Feature_PDF_Export',
            // ),
        );

        /**
         * Allow third-party add-ons to register features.
         *
         * @param array $features Registered features.
         */
        $this->features = apply_filters( 'art_routes_pro_features', $this->features );
    }

    /**
     * Load and initialize features that meet their requirements.
     */
    private function load_features() {
        $is_licensed = art_routes_pro_is_licensed();

        foreach ( $this->features as $slug => $feature ) {
            $requires_license = $feature['requires_license'] ?? true;

            if ( $requires_license && ! $is_licensed ) {
                continue;
            }

            $file = ART_ROUTES_PRO_DIR . 'includes/features/' . $feature['file'];
            if ( file_exists( $file ) ) {
                require_once $file;
                if ( class_exists( $feature['class'] ) ) {
                    $instance = new $feature['class']();
                    if ( method_exists( $instance, 'init' ) ) {
                        $instance->init();
                    }
                }
            }
        }
    }

    /**
     * Get all registered features with their status.
     */
    public function get_features() {
        $is_licensed = art_routes_pro_is_licensed();
        $result      = array();

        foreach ( $this->features as $slug => $feature ) {
            $requires_license = $feature['requires_license'] ?? true;
            $result[ $slug ]  = array_merge( $feature, array(
                'active' => ! $requires_license || $is_licensed,
            ) );
        }

        return $result;
    }

    /**
     * Check if a specific feature is registered and active.
     */
    public function is_feature_active( $slug ) {
        if ( ! isset( $this->features[ $slug ] ) ) {
            return false;
        }

        $requires_license = $this->features[ $slug ]['requires_license'] ?? true;
        return ! $requires_license || art_routes_pro_is_licensed();
    }

    // ------------------------------------------------------------------
    // Filters
    // ------------------------------------------------------------------

    public function filter_is_licensed( $is_licensed ) {
        return art_routes_pro_is_licensed();
    }

    public function filter_feature_enabled( $enabled, $feature_slug ) {
        return $this->is_feature_active( $feature_slug );
    }

    // ------------------------------------------------------------------
    // Admin notices
    // ------------------------------------------------------------------

    public function maybe_show_unlicensed_notice() {
        if ( art_routes_pro_is_licensed() ) {
            return;
        }

        $screen = get_current_screen();
        if ( ! $screen || false === strpos( $screen->id, 'edition' ) ) {
            return;
        }

        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <?php
                printf(
                    /* translators: %s: license settings URL */
                    esc_html__( 'Art Routes Pro is installed but not licensed. %s to unlock Pro features.', 'art-routes-pro' ),
                    '<a href="' . esc_url( admin_url( 'edit.php?post_type=edition&page=art-routes-pro-license' ) ) . '">' .
                    esc_html__( 'Enter your license key', 'art-routes-pro' ) . '</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }
}
