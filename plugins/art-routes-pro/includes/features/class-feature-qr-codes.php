<?php
/**
 * QR Codes feature.
 *
 * Generates QR codes for edition locations/info points.
 * Admin page lets organizers view and download QR codes for printing.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Art_Routes_Pro_Feature_QR_Codes {

    public function init() {
        require_once ART_ROUTES_PRO_DIR . 'lib/class-qr-generator.php';

        add_action( 'admin_menu', array( $this, 'add_menu_page' ), 25 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_art_routes_pro_download_qr_zip', array( $this, 'ajax_download_zip' ) );
    }

    public function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=edition',
            __( 'QR Codes', 'art-routes-pro' ),
            __( 'QR Codes', 'art-routes-pro' ),
            'edit_posts',
            'art-routes-pro-qr-codes',
            array( $this, 'render_page' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( 'edition_page_art-routes-pro-qr-codes' !== $hook ) {
            return;
        }
        wp_enqueue_style(
            'art-routes-pro-qr',
            ART_ROUTES_PRO_URL . 'assets/css/qr-codes.css',
            array(),
            ART_ROUTES_PRO_VERSION
        );
    }

    public function render_page() {
        $editions = get_posts( array(
            'post_type'   => 'edition',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby'     => 'title',
            'order'       => 'ASC',
        ) );

        $selected_edition = isset( $_GET['edition_id'] ) ? absint( $_GET['edition_id'] ) : 0;
        if ( ! $selected_edition && ! empty( $editions ) ) {
            $selected_edition = $editions[0]->ID;
        }

        $locations    = array();
        $info_points  = array();
        $edition_label_location = __( 'Locations', 'art-routes-pro' );
        $edition_label_info     = __( 'Info Points', 'art-routes-pro' );

        if ( $selected_edition ) {
            if ( function_exists( 'art_routes_label' ) ) {
                $edition_label_location = art_routes_label( 'location', true, $selected_edition );
                $edition_label_info     = art_routes_label( 'info_point', true, $selected_edition );
            }

            $locations = get_posts( array(
                'post_type'   => 'artwork',
                'post_status' => array( 'publish', 'draft' ),
                'numberposts' => -1,
                'meta_key'    => '_edition_id',
                'meta_value'  => $selected_edition,
                'orderby'     => 'meta_value_num',
                'meta_key'    => '_artwork_number',
                'order'       => 'ASC',
            ) );

            $info_points = get_posts( array(
                'post_type'   => 'information_point',
                'post_status' => array( 'publish', 'draft' ),
                'numberposts' => -1,
                'meta_key'    => '_edition_id',
                'meta_value'  => $selected_edition,
                'orderby'     => 'title',
                'order'       => 'ASC',
            ) );
        }

        ?>
        <div class="wrap art-routes-pro-qr-wrap">
            <h1><?php esc_html_e( 'QR Codes', 'art-routes-pro' ); ?></h1>

            <?php if ( empty( $editions ) ) : ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e( 'No editions found. Create an edition first to generate QR codes.', 'art-routes-pro' ); ?></p>
                </div>
                <?php return; ?>
            <?php endif; ?>

            <form method="get" class="art-routes-pro-qr-filter">
                <input type="hidden" name="post_type" value="edition" />
                <input type="hidden" name="page" value="art-routes-pro-qr-codes" />
                <label for="edition_id"><?php esc_html_e( 'Edition:', 'art-routes-pro' ); ?></label>
                <select name="edition_id" id="edition_id" onchange="this.form.submit()">
                    <?php foreach ( $editions as $edition ) : ?>
                        <option value="<?php echo esc_attr( $edition->ID ); ?>"
                            <?php selected( $selected_edition, $edition->ID ); ?>>
                            <?php echo esc_html( $edition->post_title ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php if ( empty( $locations ) && empty( $info_points ) ) : ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e( 'This edition has no locations or info points yet.', 'art-routes-pro' ); ?></p>
                </div>
                <?php return; ?>
            <?php endif; ?>

            <div class="art-routes-pro-qr-actions">
                <button type="button" class="button button-primary" id="art-routes-pro-download-all">
                    <?php esc_html_e( 'Download All QR Codes (ZIP)', 'art-routes-pro' ); ?>
                </button>
                <span class="spinner" id="art-routes-pro-zip-spinner"></span>
            </div>

            <?php if ( ! empty( $locations ) ) : ?>
                <h2><?php echo esc_html( $edition_label_location ); ?></h2>
                <div class="art-routes-pro-qr-grid">
                    <?php foreach ( $locations as $location ) :
                        $this->render_qr_card( $location, 'location' );
                    endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $info_points ) ) : ?>
                <h2><?php echo esc_html( $edition_label_info ); ?></h2>
                <div class="art-routes-pro-qr-grid">
                    <?php foreach ( $info_points as $point ) :
                        $this->render_qr_card( $point, 'info_point' );
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <script>
        (function($) {
            // Individual SVG download.
            $(document).on('click', '.art-routes-pro-qr-download', function(e) {
                e.preventDefault();
                var $card = $(this).closest('.art-routes-pro-qr-card');
                var svg = $card.find('.art-routes-pro-qr-svg svg')[0].outerHTML;
                var filename = $(this).data('filename') + '.svg';
                var blob = new Blob([svg], {type: 'image/svg+xml'});
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            });

            // Download all as ZIP.
            $('#art-routes-pro-download-all').on('click', function() {
                var $btn = $(this).prop('disabled', true);
                var $spinner = $('#art-routes-pro-zip-spinner').addClass('is-active');
                window.location.href = ajaxurl + '?action=art_routes_pro_download_qr_zip&edition_id=<?php echo esc_js( $selected_edition ); ?>&_wpnonce=<?php echo esc_js( wp_create_nonce( 'art_routes_pro_qr_zip' ) ); ?>';
                setTimeout(function() {
                    $btn.prop('disabled', false);
                    $spinner.removeClass('is-active');
                }, 3000);
            });
        })(jQuery);
        </script>
        <?php
    }

    private function render_qr_card( $post, $type ) {
        $number = '';
        if ( 'location' === $type ) {
            $number = get_post_meta( $post->ID, '_artwork_number', true );
        }

        $url = get_permalink( $post->ID );
        $svg = Art_Routes_QR_Generator::svg( $url, 4, 2 );

        if ( ! $svg ) {
            return;
        }

        $label    = $number ? $number . '. ' . $post->post_title : $post->post_title;
        $filename = sanitize_file_name( ( $number ? $number . '-' : '' ) . $post->post_title );
        $status   = $post->post_status;

        ?>
        <div class="art-routes-pro-qr-card <?php echo 'draft' === $status ? 'art-routes-pro-qr-card--draft' : ''; ?>">
            <div class="art-routes-pro-qr-svg">
                <?php echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG generated by our QR class. ?>
            </div>
            <div class="art-routes-pro-qr-label">
                <?php echo esc_html( $label ); ?>
                <?php if ( 'draft' === $status ) : ?>
                    <span class="art-routes-pro-badge-draft"><?php esc_html_e( 'Draft', 'art-routes-pro' ); ?></span>
                <?php endif; ?>
            </div>
            <div class="art-routes-pro-qr-url">
                <code><?php echo esc_html( $url ); ?></code>
            </div>
            <button type="button" class="button art-routes-pro-qr-download"
                    data-filename="<?php echo esc_attr( $filename ); ?>">
                <?php esc_html_e( 'Download SVG', 'art-routes-pro' ); ?>
            </button>
        </div>
        <?php
    }

    // =========================================================================
    // ZIP download
    // =========================================================================

    public function ajax_download_zip() {
        check_ajax_referer( 'art_routes_pro_qr_zip', '_wpnonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
            wp_die( esc_html__( 'Permission denied.', 'art-routes-pro' ) );
        }

        $edition_id = absint( $_GET['edition_id'] ?? 0 );
        if ( ! $edition_id ) {
            wp_die( esc_html__( 'No edition selected.', 'art-routes-pro' ) );
        }

        if ( ! class_exists( 'ZipArchive' ) ) {
            wp_die( esc_html__( 'ZipArchive PHP extension is required for ZIP downloads.', 'art-routes-pro' ) );
        }

        $edition_title = get_the_title( $edition_id );

        $locations = get_posts( array(
            'post_type'   => 'artwork',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_key'    => '_edition_id',
            'meta_value'  => $edition_id,
        ) );

        $info_points = get_posts( array(
            'post_type'   => 'information_point',
            'post_status' => 'publish',
            'numberposts' => -1,
            'meta_key'    => '_edition_id',
            'meta_value'  => $edition_id,
        ) );

        $tmp_file = wp_tempnam( 'qr-codes' );
        $zip      = new ZipArchive();
        $zip->open( $tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE );

        foreach ( $locations as $location ) {
            $number   = get_post_meta( $location->ID, '_artwork_number', true );
            $url      = get_permalink( $location->ID );
            $svg      = Art_Routes_QR_Generator::svg( $url, 10, 4 );
            $filename = sanitize_file_name( ( $number ? $number . '-' : '' ) . $location->post_title ) . '.svg';
            if ( $svg ) {
                $zip->addFromString( 'locations/' . $filename, $svg );
            }
        }

        foreach ( $info_points as $point ) {
            $url      = get_permalink( $point->ID );
            $svg      = Art_Routes_QR_Generator::svg( $url, 10, 4 );
            $filename = sanitize_file_name( $point->post_title ) . '.svg';
            if ( $svg ) {
                $zip->addFromString( 'info-points/' . $filename, $svg );
            }
        }

        $zip->close();

        $zip_filename = sanitize_file_name( $edition_title . '-qr-codes' ) . '.zip';

        header( 'Content-Type: application/zip' );
        header( 'Content-Disposition: attachment; filename="' . $zip_filename . '"' );
        header( 'Content-Length: ' . filesize( $tmp_file ) );
        readfile( $tmp_file );
        unlink( $tmp_file );
        exit;
    }
}
