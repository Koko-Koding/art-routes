<?php
/**
 * Import/Export Admin Page for WP Art Routes Plugin
 *
 * Provides CSV import and CSV/GPX export functionality for editions.
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Import/Export submenu page under Editions
 */
function wp_art_routes_add_import_export_page()
{
    add_submenu_page(
        'edit.php?post_type=edition',
        __('Import/Export', 'wp-art-routes'),
        __('Import/Export', 'wp-art-routes'),
        'manage_options',
        'wp-art-routes-import-export',
        'wp_art_routes_render_import_export_page',
        100 // Position: after Dashboard, before Settings
    );
}
add_action('admin_menu', 'wp_art_routes_add_import_export_page');

/**
 * Render the Import/Export admin page
 */
function wp_art_routes_render_import_export_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'wp-art-routes'));
    }

    // Handle CSV import if form was submitted
    $import_result = null;
    if (isset($_POST['wp_art_routes_import_csv']) && isset($_POST['_wpnonce'])) {
        $import_result = wp_art_routes_handle_csv_import();
    }

    // Handle GPX import if form was submitted
    if (isset($_POST['wp_art_routes_import_gpx']) && isset($_POST['_wpnonce_gpx'])) {
        $import_result = wp_art_routes_handle_gpx_import();
    }

    // Determine current tab
    $current_tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'import';

    // Define available tabs
    $tabs = [
        'import' => __('Import', 'wp-art-routes'),
        'export' => __('Export', 'wp-art-routes'),
    ];

    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <?php if ($import_result) : ?>
            <?php if (is_wp_error($import_result)) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo esc_html($import_result->get_error_message()); ?></p>
                </div>
            <?php elseif (is_array($import_result)) : ?>
                <?php
                $dashboard_url = admin_url('edit.php?post_type=edition&page=wp-art-routes-dashboard&edition_id=' . $import_result['edition_id']);
                $edition_title = get_the_title($import_result['edition_id']);
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php echo esc_html($import_result['message']); ?>
                        <br><br>
                        <a href="<?php echo esc_url($dashboard_url); ?>" class="button button-secondary">
                            <?php printf(
                                /* translators: %s: edition title */
                                esc_html__('Go to %s Dashboard', 'wp-art-routes'),
                                esc_html($edition_title)
                            ); ?>
                            →
                        </a>
                    </p>
                </div>
            <?php else : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html($import_result); ?></p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <nav class="nav-tab-wrapper">
            <?php foreach ($tabs as $tab_slug => $tab_label) : ?>
                <a href="<?php echo esc_url(add_query_arg(['tab' => $tab_slug], admin_url('edit.php?post_type=edition&page=wp-art-routes-import-export'))); ?>"
                   class="nav-tab <?php echo $current_tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
                    <?php echo esc_html($tab_label); ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="tab-content" style="margin-top: 20px;">
            <?php
            switch ($current_tab) {
                case 'export':
                    wp_art_routes_render_export_tab();
                    break;
                case 'import':
                default:
                    wp_art_routes_render_import_tab();
                    break;
            }
            ?>
        </div>
    </div>
    <?php
}

/**
 * Render the Import tab
 */
function wp_art_routes_render_import_tab()
{
    // Get all editions for dropdown
    $editions = wp_art_routes_get_editions();
    $location_label = wp_art_routes_label('location', true);
    $info_point_label = wp_art_routes_label('info_point', true);

    ?>
    <style>
        .new-edition-name-row { display: none; }
        .new-edition-name-row.visible { display: table-row; }
    </style>

    <div class="card" style="max-width: 800px; margin-bottom: 20px;">
        <h2><?php printf(
            /* translators: %1$s: locations label (e.g., "Locations"), %2$s: info points label (e.g., "Info Points") */
            esc_html__('Import %1$s & %2$s from CSV', 'wp-art-routes'),
            esc_html($location_label),
            esc_html($info_point_label)
        ); ?></h2>

        <p class="description">
            <?php esc_html_e('Upload a CSV file to import locations and information points. Items will be created as drafts for review before publishing.', 'wp-art-routes'); ?>
        </p>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('wp_art_routes_import_csv', '_wpnonce'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="import_edition_id"><?php esc_html_e('Edition', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="import_edition_id" id="import_edition_id" required>
                            <option value=""><?php esc_html_e('Select an edition', 'wp-art-routes'); ?></option>
                            <?php foreach ($editions as $edition) : ?>
                                <option value="<?php echo esc_attr($edition->ID); ?>">
                                    <?php echo esc_html($edition->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ <?php esc_html_e('Create new edition', 'wp-art-routes'); ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('All imported items will be assigned to this edition.', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="new-edition-name-row" id="csv-new-edition-row">
                    <th scope="row">
                        <label for="csv_new_edition_name"><?php esc_html_e('New Edition Name', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="csv_new_edition_name" id="csv_new_edition_name" class="regular-text" />
                        <p class="description">
                            <?php esc_html_e('Enter a name for the new edition (e.g., "Kunstroute 2026").', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="import_csv_file"><?php esc_html_e('CSV File', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="file" name="import_csv_file" id="import_csv_file" accept=".csv" required />
                        <p class="description">
                            <?php esc_html_e('Select a CSV file following the template format.', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="wp_art_routes_import_csv" class="button button-primary"
                       value="<?php esc_attr_e('Import CSV', 'wp-art-routes'); ?>" />
            </p>
        </form>

        <hr style="margin: 30px 0;" />

        <h3><?php esc_html_e('CSV Template', 'wp-art-routes'); ?></h3>
        <p class="description">
            <?php esc_html_e('Download the template CSV file to see the required format for importing.', 'wp-art-routes'); ?>
        </p>

        <p>
            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin-ajax.php?action=wp_art_routes_download_csv_template'), 'wp_art_routes_csv_template')); ?>"
               class="button">
                <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                <?php esc_html_e('Download Template CSV', 'wp-art-routes'); ?>
            </a>
        </p>

        <h4><?php esc_html_e('CSV Format', 'wp-art-routes'); ?></h4>
        <p class="description">
            <?php esc_html_e('The CSV file should have the following columns:', 'wp-art-routes'); ?>
        </p>
        <table class="widefat striped" style="max-width: 700px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Column', 'wp-art-routes'); ?></th>
                    <th><?php esc_html_e('Required', 'wp-art-routes'); ?></th>
                    <th><?php esc_html_e('Description', 'wp-art-routes'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>Type</code></td>
                    <td><?php esc_html_e('Yes', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('"location" or "info_point"', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Name</code></td>
                    <td><?php esc_html_e('Yes', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('The title of the item', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Description</code></td>
                    <td><?php esc_html_e('No', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('Full description/content', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Latitude</code></td>
                    <td><?php esc_html_e('Yes', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('GPS latitude (e.g., 52.0907)', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Longitude</code></td>
                    <td><?php esc_html_e('Yes', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('GPS longitude (e.g., 5.1214)', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Number</code></td>
                    <td><?php esc_html_e('No', 'wp-art-routes'); ?></td>
                    <td><?php printf(
                        /* translators: %s: location label (e.g., "Locations") */
                        esc_html__('%s only: Display number (e.g., A1, 1)', 'wp-art-routes'),
                        esc_html($location_label)
                    ); ?></td>
                </tr>
                <tr>
                    <td><code>Icon</code></td>
                    <td><?php esc_html_e('No', 'wp-art-routes'); ?></td>
                    <td><?php esc_html_e('Icon filename (e.g., icon.svg)', 'wp-art-routes'); ?></td>
                </tr>
                <tr>
                    <td><code>Creator</code></td>
                    <td><?php esc_html_e('No', 'wp-art-routes'); ?></td>
                    <td><?php printf(
                        /* translators: %s: location label (e.g., "Locations") */
                        esc_html__('%s only: Creator/artist name', 'wp-art-routes'),
                        esc_html($location_label)
                    ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="card" style="max-width: 800px; margin-bottom: 20px;">
        <h2><?php esc_html_e('Import from GPX', 'wp-art-routes'); ?></h2>

        <p class="description">
            <?php esc_html_e('Upload a GPX file to import route paths and/or waypoints. Routes will be created as drafts for review.', 'wp-art-routes'); ?>
        </p>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('wp_art_routes_import_gpx', '_wpnonce_gpx'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="gpx_import_edition_id"><?php esc_html_e('Edition', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <select name="gpx_import_edition_id" id="gpx_import_edition_id" required>
                            <option value=""><?php esc_html_e('Select an edition', 'wp-art-routes'); ?></option>
                            <?php foreach ($editions as $edition) : ?>
                                <option value="<?php echo esc_attr($edition->ID); ?>">
                                    <?php echo esc_html($edition->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="new">+ <?php esc_html_e('Create new edition', 'wp-art-routes'); ?></option>
                        </select>
                        <p class="description">
                            <?php esc_html_e('All imported items will be assigned to this edition.', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
                <tr class="new-edition-name-row" id="gpx-new-edition-row">
                    <th scope="row">
                        <label for="gpx_new_edition_name"><?php esc_html_e('New Edition Name', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="text" name="gpx_new_edition_name" id="gpx_new_edition_name" class="regular-text" />
                        <p class="description">
                            <?php esc_html_e('Enter a name for the new edition (e.g., "Kunstroute 2026").', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="import_gpx_file"><?php esc_html_e('GPX File', 'wp-art-routes'); ?> <span class="required">*</span></label>
                    </th>
                    <td>
                        <input type="file" name="import_gpx_file" id="import_gpx_file" accept=".gpx" required />
                        <p class="description">
                            <?php esc_html_e('Select a GPX file containing tracks and/or waypoints.', 'wp-art-routes'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="gpx_import_mode"><?php esc_html_e('Import Mode', 'wp-art-routes'); ?></label>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="gpx_import_mode" value="route_only" checked />
                                <?php esc_html_e('Route path only', 'wp-art-routes'); ?>
                                <p class="description" style="margin-left: 24px; margin-top: 4px;">
                                    <?php esc_html_e('Import tracks as route paths. Waypoints are ignored.', 'wp-art-routes'); ?>
                                </p>
                            </label>
                            <br />
                            <label>
                                <input type="radio" name="gpx_import_mode" value="route_and_waypoints" />
                                <?php printf(
                                    /* translators: %s: locations label */
                                    esc_html__('Route path + waypoints as %s', 'wp-art-routes'),
                                    esc_html($location_label)
                                ); ?>
                                <p class="description" style="margin-left: 24px; margin-top: 4px;">
                                    <?php printf(
                                        /* translators: %s: locations label */
                                        esc_html__('Import tracks as route paths and waypoints as %s.', 'wp-art-routes'),
                                        esc_html($location_label)
                                    ); ?>
                                </p>
                            </label>
                            <br />
                            <label>
                                <input type="radio" name="gpx_import_mode" value="waypoints_only" />
                                <?php printf(
                                    /* translators: %s: locations label */
                                    esc_html__('Waypoints as %s only', 'wp-art-routes'),
                                    esc_html($location_label)
                                ); ?>
                                <p class="description" style="margin-left: 24px; margin-top: 4px;">
                                    <?php esc_html_e('Ignore track data, only import waypoints.', 'wp-art-routes'); ?>
                                </p>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="wp_art_routes_import_gpx" class="button button-primary"
                       value="<?php esc_attr_e('Import GPX', 'wp-art-routes'); ?>" />
            </p>
        </form>
    </div>

    <script type="text/javascript">
    (function() {
        // Toggle new edition name field for CSV import
        var csvSelect = document.getElementById('import_edition_id');
        var csvNewRow = document.getElementById('csv-new-edition-row');
        var csvNameInput = document.getElementById('csv_new_edition_name');

        if (csvSelect && csvNewRow) {
            csvSelect.addEventListener('change', function() {
                if (this.value === 'new') {
                    csvNewRow.classList.add('visible');
                    csvNameInput.required = true;
                } else {
                    csvNewRow.classList.remove('visible');
                    csvNameInput.required = false;
                    csvNameInput.value = '';
                }
            });
        }

        // Toggle new edition name field for GPX import
        var gpxSelect = document.getElementById('gpx_import_edition_id');
        var gpxNewRow = document.getElementById('gpx-new-edition-row');
        var gpxNameInput = document.getElementById('gpx_new_edition_name');

        if (gpxSelect && gpxNewRow) {
            gpxSelect.addEventListener('change', function() {
                if (this.value === 'new') {
                    gpxNewRow.classList.add('visible');
                    gpxNameInput.required = true;
                } else {
                    gpxNewRow.classList.remove('visible');
                    gpxNameInput.required = false;
                    gpxNameInput.value = '';
                }
            });
        }
    })();
    </script>
    <?php
}

/**
 * Render the Export tab
 */
function wp_art_routes_render_export_tab()
{
    // Get all editions for dropdown
    $editions = wp_art_routes_get_editions();

    ?>
    <div class="card" style="max-width: 800px;">
        <h2><?php esc_html_e('Export Edition Data', 'wp-art-routes'); ?></h2>

        <p class="description">
            <?php esc_html_e('Export all locations and information points from an edition to CSV or GPX format.', 'wp-art-routes'); ?>
        </p>

        <?php if (!empty($editions)) : ?>
            <form method="post" id="export-form">
                <?php wp_nonce_field('wp_art_routes_export_edition', '_wpnonce'); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="export_edition_id"><?php esc_html_e('Edition', 'wp-art-routes'); ?></label>
                        </th>
                        <td>
                            <select name="export_edition_id" id="export_edition_id" required>
                                <option value=""><?php esc_html_e('Select an edition', 'wp-art-routes'); ?></option>
                                <?php foreach ($editions as $edition) : ?>
                                    <option value="<?php echo esc_attr($edition->ID); ?>">
                                        <?php echo esc_html($edition->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="export_format"><?php esc_html_e('Format', 'wp-art-routes'); ?></label>
                        </th>
                        <td>
                            <select name="export_format" id="export_format" required>
                                <option value="csv"><?php esc_html_e('CSV (Spreadsheet)', 'wp-art-routes'); ?></option>
                                <option value="gpx"><?php esc_html_e('GPX (GPS Exchange Format)', 'wp-art-routes'); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e('CSV is best for spreadsheet editing. GPX is compatible with GPS devices and mapping apps.', 'wp-art-routes'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" id="export-button" class="button button-primary">
                        <span class="dashicons dashicons-download" style="vertical-align: middle;"></span>
                        <?php esc_html_e('Export', 'wp-art-routes'); ?>
                    </button>
                </p>
            </form>

            <script type="text/javascript">
                document.getElementById('export-button').addEventListener('click', function() {
                    var editionId = document.getElementById('export_edition_id').value;
                    var format = document.getElementById('export_format').value;
                    var nonce = document.querySelector('input[name="_wpnonce"]').value;

                    if (!editionId) {
                        alert('<?php echo esc_js(__('Please select an edition.', 'wp-art-routes')); ?>');
                        return;
                    }

                    var url = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>' +
                              '?action=wp_art_routes_export_edition' +
                              '&edition_id=' + encodeURIComponent(editionId) +
                              '&format=' + encodeURIComponent(format) +
                              '&_wpnonce=' + encodeURIComponent(nonce);

                    window.location.href = url;
                });
            </script>
        <?php else : ?>
            <p class="description" style="color: #d63638;">
                <?php esc_html_e('No editions found. Please create an edition first.', 'wp-art-routes'); ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Handle CSV import
 *
 * @return string|WP_Error Success message or error
 */
function wp_art_routes_handle_csv_import()
{
    // Verify nonce
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'wp_art_routes_import_csv')) {
        return new WP_Error('invalid_nonce', __('Security check failed. Please try again.', 'wp-art-routes'));
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        return new WP_Error('insufficient_permissions', __('You do not have permission to import data.', 'wp-art-routes'));
    }

    // Check edition ID or create new edition
    $edition_id_input = isset($_POST['import_edition_id']) ? sanitize_text_field(wp_unslash($_POST['import_edition_id'])) : '';
    $edition_id = 0;
    $created_new_edition = false;

    if ($edition_id_input === 'new') {
        // Create new edition
        $new_edition_name = isset($_POST['csv_new_edition_name']) ? sanitize_text_field(wp_unslash($_POST['csv_new_edition_name'])) : '';
        if (empty($new_edition_name)) {
            return new WP_Error('missing_edition_name', __('Please enter a name for the new edition.', 'wp-art-routes'));
        }

        $edition_id = wp_insert_post([
            'post_title'  => $new_edition_name,
            'post_type'   => 'edition',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ]);

        if (is_wp_error($edition_id)) {
            return new WP_Error('edition_creation_failed', __('Failed to create edition.', 'wp-art-routes'));
        }
        $created_new_edition = true;
    } else {
        $edition_id = absint($edition_id_input);
    }

    if (!$edition_id) {
        return new WP_Error('missing_edition', __('Please select an edition.', 'wp-art-routes'));
    }

    // Verify edition exists
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        return new WP_Error('invalid_edition', __('Selected edition does not exist.', 'wp-art-routes'));
    }

    // Check file upload
    if (!isset($_FILES['import_csv_file']) || $_FILES['import_csv_file']['error'] !== UPLOAD_ERR_OK) {
        $error_message = __('File upload failed.', 'wp-art-routes');
        if (isset($_FILES['import_csv_file']['error'])) {
            switch ($_FILES['import_csv_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = __('The file is too large.', 'wp-art-routes');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = __('No file was uploaded.', 'wp-art-routes');
                    break;
            }
        }
        return new WP_Error('upload_error', $error_message);
    }

    // Verify file type
    $file_info = wp_check_filetype($_FILES['import_csv_file']['name']);
    if ($file_info['ext'] !== 'csv') {
        return new WP_Error('invalid_file_type', __('Please upload a CSV file.', 'wp-art-routes'));
    }

    // Read CSV file using WP_Filesystem
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }

    $csv_content = $wp_filesystem->get_contents($_FILES['import_csv_file']['tmp_name']);
    if (!$csv_content) {
        return new WP_Error('file_read_error', __('Could not read the uploaded file.', 'wp-art-routes'));
    }

    // Parse CSV content into rows
    $csv_lines = explode("\n", $csv_content);
    $csv_lines = array_filter($csv_lines, function ($line) {
        return trim($line) !== '';
    });

    if (empty($csv_lines)) {
        return new WP_Error('empty_file', __('The CSV file is empty.', 'wp-art-routes'));
    }

    // Get header row
    $header = str_getcsv(array_shift($csv_lines));
    if (!$header) {
        return new WP_Error('empty_file', __('The CSV file is empty.', 'wp-art-routes'));
    }

    // Normalize header names (trim and lowercase)
    $header = array_map(function ($col) {
        return strtolower(trim($col));
    }, $header);

    // Check required columns
    $required_columns = ['type', 'name', 'latitude', 'longitude'];
    $missing_columns = array_diff($required_columns, $header);
    if (!empty($missing_columns)) {
        return new WP_Error(
            'missing_columns',
            sprintf(
                /* translators: %s: comma-separated list of missing column names */
                __('Missing required columns: %s', 'wp-art-routes'),
                implode(', ', $missing_columns)
            )
        );
    }

    // Find column indices
    $col_indices = [];
    $columns = ['type', 'name', 'description', 'latitude', 'longitude', 'number', 'icon', 'creator'];
    foreach ($columns as $col) {
        $index = array_search($col, $header);
        $col_indices[$col] = $index !== false ? $index : -1;
    }

    // Process rows
    $locations_created = 0;
    $locations_skipped = 0;
    $info_points_created = 0;
    $info_points_skipped = 0;
    $errors = [];
    $row_number = 1;

    foreach ($csv_lines as $csv_line) {
        $row_number++;
        $row = str_getcsv($csv_line);

        // Skip empty rows
        if (empty(array_filter($row))) {
            continue;
        }

        // Get values from row
        $type = isset($row[$col_indices['type']]) ? sanitize_text_field(strtolower(trim($row[$col_indices['type']]))) : '';
        $name = isset($row[$col_indices['name']]) ? sanitize_text_field(trim($row[$col_indices['name']])) : '';
        $description = $col_indices['description'] >= 0 && isset($row[$col_indices['description']])
            ? wp_kses_post(trim($row[$col_indices['description']])) : '';
        $latitude = isset($row[$col_indices['latitude']]) ? trim($row[$col_indices['latitude']]) : '';
        $longitude = isset($row[$col_indices['longitude']]) ? trim($row[$col_indices['longitude']]) : '';
        $number = $col_indices['number'] >= 0 && isset($row[$col_indices['number']])
            ? sanitize_text_field(trim($row[$col_indices['number']])) : '';
        $icon = $col_indices['icon'] >= 0 && isset($row[$col_indices['icon']])
            ? sanitize_file_name(trim($row[$col_indices['icon']])) : '';
        $creator = $col_indices['creator'] >= 0 && isset($row[$col_indices['creator']])
            ? sanitize_text_field(trim($row[$col_indices['creator']])) : '';

        // Validate type
        if (!in_array($type, ['location', 'info_point'], true)) {
            $errors[] = sprintf(
                /* translators: %1$d: row number, %2$s: invalid type value */
                __('Row %1$d: Invalid type "%2$s". Must be "location" or "info_point".', 'wp-art-routes'),
                $row_number,
                $type
            );
            continue;
        }

        // Validate name
        if (empty($name)) {
            $errors[] = sprintf(
                /* translators: %d: row number */
                __('Row %d: Name is required.', 'wp-art-routes'),
                $row_number
            );
            continue;
        }

        // Validate coordinates
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            $errors[] = sprintf(
                /* translators: %d: row number */
                __('Row %d: Invalid latitude or longitude.', 'wp-art-routes'),
                $row_number
            );
            continue;
        }

        $latitude = floatval($latitude);
        $longitude = floatval($longitude);

        // Validate latitude range
        if ($latitude < -90 || $latitude > 90) {
            $errors[] = sprintf(
                /* translators: %d: row number */
                __('Row %d: Latitude must be between -90 and 90.', 'wp-art-routes'),
                $row_number
            );
            continue;
        }

        // Validate longitude range
        if ($longitude < -180 || $longitude > 180) {
            $errors[] = sprintf(
                /* translators: %d: row number */
                __('Row %d: Longitude must be between -180 and 180.', 'wp-art-routes'),
                $row_number
            );
            continue;
        }

        // Determine post type
        $post_type = ($type === 'location') ? 'artwork' : 'information_point';

        // Check for duplicates
        if ($type === 'location') {
            // Check for duplicate location by coordinates
            $existing_by_coords = wp_art_routes_find_duplicate_location($latitude, $longitude, $edition_id);
            if ($existing_by_coords) {
                $locations_skipped++;
                continue;
            }
            // Check for duplicate location by name
            $existing_by_name = wp_art_routes_find_duplicate_location_by_name($name, $edition_id);
            if ($existing_by_name) {
                $locations_skipped++;
                continue;
            }
        } else {
            // Check for duplicate info point by coordinates
            $existing_by_coords = wp_art_routes_find_duplicate_info_point($latitude, $longitude, $edition_id);
            if ($existing_by_coords) {
                $info_points_skipped++;
                continue;
            }
            // Check for duplicate info point by name
            $existing_by_name = wp_art_routes_find_duplicate_info_point_by_name($name, $edition_id);
            if ($existing_by_name) {
                $info_points_skipped++;
                continue;
            }
        }

        // Create post as draft
        $post_data = [
            'post_title'   => $name,
            'post_content' => $description,
            'post_type'    => $post_type,
            'post_status'  => 'draft',
            'post_author'  => get_current_user_id(),
        ];

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            $errors[] = sprintf(
                /* translators: %1$d: row number, %2$s: error message */
                __('Row %1$d: Failed to create post - %2$s', 'wp-art-routes'),
                $row_number,
                $post_id->get_error_message()
            );
            continue;
        }

        // Save meta fields
        update_post_meta($post_id, '_artwork_latitude', $latitude);
        update_post_meta($post_id, '_artwork_longitude', $longitude);
        update_post_meta($post_id, '_edition_id', $edition_id);

        // Type-specific meta
        if ($type === 'location') {
            if (!empty($number)) {
                update_post_meta($post_id, '_artwork_number', $number);
            }
            if (!empty($icon)) {
                update_post_meta($post_id, '_artwork_icon', $icon);
            }
            // Store creator name as post excerpt for reference (actual artist linking is done manually)
            if (!empty($creator)) {
                wp_update_post([
                    'ID'           => $post_id,
                    'post_excerpt' => $creator,
                ]);
            }
            $locations_created++;
        } else {
            if (!empty($icon)) {
                update_post_meta($post_id, '_info_point_icon', $icon);
            }
            $info_points_created++;
        }
    }

    // Build result message
    $location_label = wp_art_routes_label('location', $locations_created !== 1);
    $info_point_label = wp_art_routes_label('info_point', $info_points_created !== 1);

    $message = sprintf(
        /* translators: %1$d: number of locations, %2$s: location label, %3$d: number of info points, %4$s: info point label */
        __('Import complete: %1$d %2$s and %3$d %4$s created as drafts.', 'wp-art-routes'),
        $locations_created,
        $location_label,
        $info_points_created,
        $info_point_label
    );

    // Add information about skipped duplicates
    $skipped_parts = [];
    if ($locations_skipped > 0) {
        $skipped_location_label = wp_art_routes_label('location', $locations_skipped !== 1);
        $skipped_parts[] = sprintf(
            /* translators: %1$d: number of locations, %2$s: location label */
            __('%1$d %2$s', 'wp-art-routes'),
            $locations_skipped,
            $skipped_location_label
        );
    }
    if ($info_points_skipped > 0) {
        $skipped_info_point_label = wp_art_routes_label('info_point', $info_points_skipped !== 1);
        $skipped_parts[] = sprintf(
            /* translators: %1$d: number of info points, %2$s: info point label */
            __('%1$d %2$s', 'wp-art-routes'),
            $info_points_skipped,
            $skipped_info_point_label
        );
    }

    if (!empty($skipped_parts)) {
        $message .= ' ' . sprintf(
            /* translators: %s: skipped items summary */
            __('%s skipped (duplicates).', 'wp-art-routes'),
            implode(' ' . __('and', 'wp-art-routes') . ' ', $skipped_parts)
        );
    }

    if (!empty($errors)) {
        $message .= ' ' . sprintf(
            /* translators: %d: number of errors */
            _n('%d row had errors.', '%d rows had errors.', count($errors), 'wp-art-routes'),
            count($errors)
        );
    }

    // Return result array with message and edition data
    return [
        'message'    => $message,
        'edition_id' => $edition_id,
        'new_edition' => $created_new_edition,
    ];
}

/**
 * Check if a location already exists near the given coordinates in the given edition
 *
 * @param float $lat Latitude to check
 * @param float $lon Longitude to check
 * @param int   $edition_id Edition ID to check within
 * @param float $tolerance Distance tolerance in degrees (default ~2 meters)
 * @return int|false Existing post ID if found, false otherwise
 */
function wp_art_routes_find_duplicate_location($lat, $lon, $edition_id, $tolerance = 0.00002)
{
    $existing_locations = get_posts([
        'post_type' => 'artwork',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
    ]);

    foreach ($existing_locations as $location_id) {
        $existing_lat = get_post_meta($location_id, '_artwork_latitude', true);
        $existing_lon = get_post_meta($location_id, '_artwork_longitude', true);

        if (is_numeric($existing_lat) && is_numeric($existing_lon)) {
            $lat_diff = abs((float)$existing_lat - $lat);
            $lon_diff = abs((float)$existing_lon - $lon);

            // Check if within tolerance (approximately 2 meters)
            if ($lat_diff <= $tolerance && $lon_diff <= $tolerance) {
                return $location_id;
            }
        }
    }

    return false;
}

/**
 * Check if a route already exists with the given name in the given edition
 *
 * @param string $name Route name to check
 * @param int    $edition_id Edition ID to check within
 * @return int|false Existing post ID if found, false otherwise
 */
function wp_art_routes_find_duplicate_route($name, $edition_id)
{
    $existing_routes = get_posts([
        'post_type' => 'art_route',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 1,
        'title' => $name,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
    ]);

    if (!empty($existing_routes)) {
        return $existing_routes[0]->ID;
    }

    return false;
}

/**
 * Check if a location already exists with the given name in the given edition
 *
 * @param string $name Location name to check
 * @param int    $edition_id Edition ID to check within
 * @return int|false Existing post ID if found, false otherwise
 */
function wp_art_routes_find_duplicate_location_by_name($name, $edition_id)
{
    $existing_locations = get_posts([
        'post_type' => 'artwork',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 1,
        'title' => $name,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
    ]);

    if (!empty($existing_locations)) {
        return $existing_locations[0]->ID;
    }

    return false;
}

/**
 * Check if an info point already exists near the given coordinates in the given edition
 *
 * @param float $lat Latitude to check
 * @param float $lon Longitude to check
 * @param int   $edition_id Edition ID to check within
 * @param float $tolerance Distance tolerance in degrees (default ~2 meters)
 * @return int|false Existing post ID if found, false otherwise
 */
function wp_art_routes_find_duplicate_info_point($lat, $lon, $edition_id, $tolerance = 0.00002)
{
    $existing_info_points = get_posts([
        'post_type' => 'information_point',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
    ]);

    foreach ($existing_info_points as $info_point_id) {
        $existing_lat = get_post_meta($info_point_id, '_artwork_latitude', true);
        $existing_lon = get_post_meta($info_point_id, '_artwork_longitude', true);

        if (is_numeric($existing_lat) && is_numeric($existing_lon)) {
            $lat_diff = abs((float)$existing_lat - $lat);
            $lon_diff = abs((float)$existing_lon - $lon);

            // Check if within tolerance (approximately 2 meters)
            if ($lat_diff <= $tolerance && $lon_diff <= $tolerance) {
                return $info_point_id;
            }
        }
    }

    return false;
}

/**
 * Check if an info point already exists with the given name in the given edition
 *
 * @param string $name Info point name to check
 * @param int    $edition_id Edition ID to check within
 * @return int|false Existing post ID if found, false otherwise
 */
function wp_art_routes_find_duplicate_info_point_by_name($name, $edition_id)
{
    $existing_info_points = get_posts([
        'post_type' => 'information_point',
        'post_status' => ['publish', 'draft', 'pending', 'private'],
        'posts_per_page' => 1,
        'title' => $name,
        'meta_key' => '_edition_id',
        'meta_value' => $edition_id,
    ]);

    if (!empty($existing_info_points)) {
        return $existing_info_points[0]->ID;
    }

    return false;
}

/**
 * Handle GPX import
 *
 * @return string|WP_Error Success message or error
 */
function wp_art_routes_handle_gpx_import()
{
    // Verify nonce
    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce_gpx'])), 'wp_art_routes_import_gpx')) {
        return new WP_Error('invalid_nonce', __('Security check failed. Please try again.', 'wp-art-routes'));
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        return new WP_Error('insufficient_permissions', __('You do not have permission to import data.', 'wp-art-routes'));
    }

    // Check edition ID or create new edition
    $edition_id_input = isset($_POST['gpx_import_edition_id']) ? sanitize_text_field(wp_unslash($_POST['gpx_import_edition_id'])) : '';
    $edition_id = 0;
    $created_new_edition = false;

    if ($edition_id_input === 'new') {
        // Create new edition
        $new_edition_name = isset($_POST['gpx_new_edition_name']) ? sanitize_text_field(wp_unslash($_POST['gpx_new_edition_name'])) : '';
        if (empty($new_edition_name)) {
            return new WP_Error('missing_edition_name', __('Please enter a name for the new edition.', 'wp-art-routes'));
        }

        $edition_id = wp_insert_post([
            'post_title'  => $new_edition_name,
            'post_type'   => 'edition',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ]);

        if (is_wp_error($edition_id)) {
            return new WP_Error('edition_creation_failed', __('Failed to create edition.', 'wp-art-routes'));
        }
        $created_new_edition = true;
    } else {
        $edition_id = absint($edition_id_input);
    }

    if (!$edition_id) {
        return new WP_Error('missing_edition', __('Please select an edition.', 'wp-art-routes'));
    }

    // Verify edition exists
    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        return new WP_Error('invalid_edition', __('Selected edition does not exist.', 'wp-art-routes'));
    }

    // Get import mode
    $import_mode = isset($_POST['gpx_import_mode']) ? sanitize_key(wp_unslash($_POST['gpx_import_mode'])) : 'route_only';
    if (!in_array($import_mode, ['route_only', 'route_and_waypoints', 'waypoints_only'], true)) {
        $import_mode = 'route_only';
    }

    // Check file upload
    if (!isset($_FILES['import_gpx_file']) || $_FILES['import_gpx_file']['error'] !== UPLOAD_ERR_OK) {
        $error_message = __('File upload failed.', 'wp-art-routes');
        if (isset($_FILES['import_gpx_file']['error'])) {
            switch ($_FILES['import_gpx_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = __('The file is too large.', 'wp-art-routes');
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = __('No file was uploaded.', 'wp-art-routes');
                    break;
            }
        }
        return new WP_Error('upload_error', $error_message);
    }

    // Verify file type - check extension directly since GPX is not in WordPress's default allowed types
    $file_extension = strtolower(pathinfo($_FILES['import_gpx_file']['name'], PATHINFO_EXTENSION));
    if ($file_extension !== 'gpx') {
        return new WP_Error('invalid_file_type', __('Please upload a GPX file.', 'wp-art-routes'));
    }

    // Read GPX file using WP_Filesystem
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once ABSPATH . '/wp-admin/includes/file.php';
        WP_Filesystem();
    }
    $gpx_content = $wp_filesystem->get_contents($_FILES['import_gpx_file']['tmp_name']);
    if (!$gpx_content) {
        return new WP_Error('file_read_error', __('Could not read the uploaded file.', 'wp-art-routes'));
    }

    // Parse GPX
    libxml_use_internal_errors(true);
    $gpx = simplexml_load_string($gpx_content);
    if (!$gpx) {
        $xml_errors = libxml_get_errors();
        libxml_clear_errors();
        return new WP_Error('xml_parse_error', __('Invalid GPX file. Could not parse XML.', 'wp-art-routes'));
    }

    // Register namespaces
    $gpx->registerXPathNamespace('gpx', 'http://www.topografix.com/GPX/1/1');
    $gpx->registerXPathNamespace('gpx10', 'http://www.topografix.com/GPX/1/0');

    $routes_created = 0;
    $routes_skipped = 0;
    $locations_created = 0;
    $locations_skipped = 0;
    $errors = [];

    // Import tracks as routes (if not waypoints_only mode)
    if ($import_mode !== 'waypoints_only') {
        // Try GPX 1.1 namespace first, then 1.0, then no namespace
        $tracks = $gpx->xpath('//gpx:trk');
        if (empty($tracks)) {
            $tracks = $gpx->xpath('//gpx10:trk');
        }
        if (empty($tracks)) {
            $tracks = $gpx->trk;
        }

        foreach ($tracks as $track) {
            // Get track name
            $track_name = isset($track->name) ? sanitize_text_field((string) $track->name) : '';
            if (empty($track_name)) {
                $track_name = sprintf(
                    /* translators: %d: route number */
                    __('Imported Route %d', 'wp-art-routes'),
                    $routes_created + $routes_skipped + 1
                );
            }

            // Check for duplicate route by name
            $existing_route_id = wp_art_routes_find_duplicate_route($track_name, $edition_id);
            if ($existing_route_id) {
                $routes_skipped++;
                continue;
            }

            // Get track description
            $track_desc = isset($track->desc) ? wp_kses_post((string) $track->desc) : '';

            // Collect all track points from all segments
            $route_path = [];
            foreach ($track->trkseg as $segment) {
                foreach ($segment->trkpt as $point) {
                    $lat = (float) $point['lat'];
                    $lon = (float) $point['lon'];

                    if (is_numeric($lat) && is_numeric($lon) &&
                        $lat >= -90 && $lat <= 90 &&
                        $lon >= -180 && $lon <= 180) {
                        // Use object format {lat, lng} for compatibility with route editor
                        $route_path[] = ['lat' => $lat, 'lng' => $lon];
                    }
                }
            }

            if (empty($route_path)) {
                $errors[] = sprintf(
                    /* translators: %s: track name */
                    __('Track "%s" has no valid points and was skipped.', 'wp-art-routes'),
                    $track_name
                );
                continue;
            }

            // Create route post
            $post_data = [
                'post_title'   => $track_name,
                'post_content' => $track_desc,
                'post_type'    => 'art_route',
                'post_status'  => 'draft',
                'post_author'  => get_current_user_id(),
            ];

            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                $errors[] = sprintf(
                    /* translators: %1$s: track name, %2$s: error message */
                    __('Failed to create route "%1$s": %2$s', 'wp-art-routes'),
                    $track_name,
                    $post_id->get_error_message()
                );
                continue;
            }

            // Save route path as JSON with pretty print for readability
            update_post_meta($post_id, '_route_path', wp_json_encode($route_path, JSON_PRETTY_PRINT));
            update_post_meta($post_id, '_edition_id', $edition_id);

            $routes_created++;
        }

        // Also check for <rte> elements (routes without segments)
        $rte_routes = $gpx->xpath('//gpx:rte');
        if (empty($rte_routes)) {
            $rte_routes = $gpx->xpath('//gpx10:rte');
        }
        if (empty($rte_routes)) {
            $rte_routes = $gpx->rte;
        }

        foreach ($rte_routes as $rte) {
            $rte_name = isset($rte->name) ? sanitize_text_field((string) $rte->name) : '';
            if (empty($rte_name)) {
                $rte_name = sprintf(
                    /* translators: %d: route number */
                    __('Imported Route %d', 'wp-art-routes'),
                    $routes_created + $routes_skipped + 1
                );
            }

            // Check for duplicate route by name
            $existing_route_id = wp_art_routes_find_duplicate_route($rte_name, $edition_id);
            if ($existing_route_id) {
                $routes_skipped++;
                continue;
            }

            $rte_desc = isset($rte->desc) ? wp_kses_post((string) $rte->desc) : '';

            $route_path = [];
            foreach ($rte->rtept as $point) {
                $lat = (float) $point['lat'];
                $lon = (float) $point['lon'];

                if (is_numeric($lat) && is_numeric($lon) &&
                    $lat >= -90 && $lat <= 90 &&
                    $lon >= -180 && $lon <= 180) {
                    // Use object format {lat, lng} for compatibility with route editor
                    $route_path[] = ['lat' => $lat, 'lng' => $lon];
                }
            }

            if (empty($route_path)) {
                continue;
            }

            $post_data = [
                'post_title'   => $rte_name,
                'post_content' => $rte_desc,
                'post_type'    => 'art_route',
                'post_status'  => 'draft',
                'post_author'  => get_current_user_id(),
            ];

            $post_id = wp_insert_post($post_data);

            if (!is_wp_error($post_id)) {
                // Save route path as JSON with pretty print for readability
                update_post_meta($post_id, '_route_path', wp_json_encode($route_path, JSON_PRETTY_PRINT));
                update_post_meta($post_id, '_edition_id', $edition_id);
                $routes_created++;
            }
        }
    }

    // Import waypoints as locations (if not route_only mode)
    if ($import_mode !== 'route_only') {
        $waypoints = $gpx->xpath('//gpx:wpt');
        if (empty($waypoints)) {
            $waypoints = $gpx->xpath('//gpx10:wpt');
        }
        if (empty($waypoints)) {
            $waypoints = $gpx->wpt;
        }

        foreach ($waypoints as $wpt) {
            $lat = (float) $wpt['lat'];
            $lon = (float) $wpt['lon'];

            // Validate coordinates
            if (!is_numeric($lat) || !is_numeric($lon) ||
                $lat < -90 || $lat > 90 ||
                $lon < -180 || $lon > 180) {
                continue;
            }

            // Get waypoint name
            $wpt_name = isset($wpt->name) ? sanitize_text_field((string) $wpt->name) : '';
            if (empty($wpt_name)) {
                $wpt_name = sprintf(
                    /* translators: %d: location number */
                    __('Imported Location %d', 'wp-art-routes'),
                    $locations_created + $locations_skipped + 1
                );
            }

            // Check for duplicate location by coordinates (within ~2 meters)
            $existing_location_id = wp_art_routes_find_duplicate_location($lat, $lon, $edition_id);
            if ($existing_location_id) {
                $locations_skipped++;
                continue;
            }

            // Also check for duplicate by name
            $existing_by_name = wp_art_routes_find_duplicate_location_by_name($wpt_name, $edition_id);
            if ($existing_by_name) {
                $locations_skipped++;
                continue;
            }

            // Get waypoint description (try desc, then cmt)
            $wpt_desc = '';
            if (isset($wpt->desc) && !empty((string) $wpt->desc)) {
                $wpt_desc = wp_kses_post((string) $wpt->desc);
            } elseif (isset($wpt->cmt) && !empty((string) $wpt->cmt)) {
                $wpt_desc = wp_kses_post((string) $wpt->cmt);
            }

            // Create location post
            $post_data = [
                'post_title'   => $wpt_name,
                'post_content' => $wpt_desc,
                'post_type'    => 'artwork',
                'post_status'  => 'draft',
                'post_author'  => get_current_user_id(),
            ];

            $post_id = wp_insert_post($post_data);

            if (is_wp_error($post_id)) {
                $errors[] = sprintf(
                    /* translators: %1$s: waypoint name, %2$s: error message */
                    __('Failed to create location "%1$s": %2$s', 'wp-art-routes'),
                    $wpt_name,
                    $post_id->get_error_message()
                );
                continue;
            }

            // Save meta fields
            update_post_meta($post_id, '_artwork_latitude', $lat);
            update_post_meta($post_id, '_artwork_longitude', $lon);
            update_post_meta($post_id, '_edition_id', $edition_id);

            // Try to get number from waypoint type or sym
            if (isset($wpt->type)) {
                $type = sanitize_text_field((string) $wpt->type);
                // Extract number if type contains one
                if (preg_match('/(\d+)/', $type, $matches)) {
                    update_post_meta($post_id, '_artwork_number', $matches[1]);
                }
            }

            $locations_created++;
        }
    }

    // Build result message
    $route_label = wp_art_routes_label('route', $routes_created !== 1);
    $location_label = wp_art_routes_label('location', $locations_created !== 1);

    $message_parts = [];

    if ($import_mode !== 'waypoints_only') {
        $message_parts[] = sprintf(
            /* translators: %1$d: number of routes, %2$s: route label */
            __('%1$d %2$s', 'wp-art-routes'),
            $routes_created,
            $route_label
        );
    }

    if ($import_mode !== 'route_only') {
        $message_parts[] = sprintf(
            /* translators: %1$d: number of locations, %2$s: location label */
            __('%1$d %2$s', 'wp-art-routes'),
            $locations_created,
            $location_label
        );
    }

    $message = sprintf(
        /* translators: %s: items created summary */
        __('GPX import complete: %s created as drafts.', 'wp-art-routes'),
        implode(' ' . __('and', 'wp-art-routes') . ' ', $message_parts)
    );

    // Add information about skipped duplicates
    $skipped_parts = [];
    if ($routes_skipped > 0) {
        $skipped_route_label = wp_art_routes_label('route', $routes_skipped !== 1);
        $skipped_parts[] = sprintf(
            /* translators: %1$d: number of routes, %2$s: route label */
            __('%1$d %2$s', 'wp-art-routes'),
            $routes_skipped,
            $skipped_route_label
        );
    }
    if ($locations_skipped > 0) {
        $skipped_location_label = wp_art_routes_label('location', $locations_skipped !== 1);
        $skipped_parts[] = sprintf(
            /* translators: %1$d: number of locations, %2$s: location label */
            __('%1$d %2$s', 'wp-art-routes'),
            $locations_skipped,
            $skipped_location_label
        );
    }

    if (!empty($skipped_parts)) {
        $message .= ' ' . sprintf(
            /* translators: %s: skipped items summary */
            __('%s skipped (duplicates).', 'wp-art-routes'),
            implode(' ' . __('and', 'wp-art-routes') . ' ', $skipped_parts)
        );
    }

    if (!empty($errors)) {
        $message .= ' ' . sprintf(
            /* translators: %d: number of errors */
            _n('%d item had errors.', '%d items had errors.', count($errors), 'wp-art-routes'),
            count($errors)
        );
    }

    // Return result array with message and edition data
    return [
        'message'    => $message,
        'edition_id' => $edition_id,
        'new_edition' => $created_new_edition,
    ];
}

/**
 * Convert an array to a CSV line string
 *
 * This is a helper function to avoid using fputcsv with file handles.
 *
 * @param array $fields Array of field values
 * @return string CSV-formatted line
 */
function wp_art_routes_array_to_csv_line($fields)
{
    $escaped_fields = [];
    foreach ($fields as $field) {
        $field = (string) $field;
        // Escape double quotes by doubling them
        $field = str_replace('"', '""', $field);
        // Wrap in quotes if contains comma, quote, or newline
        if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false || strpos($field, "\r") !== false) {
            $field = '"' . $field . '"';
        }
        $escaped_fields[] = $field;
    }
    return implode(',', $escaped_fields);
}

/**
 * AJAX handler for downloading CSV template
 */
function wp_art_routes_download_csv_template()
{
    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wp_art_routes_csv_template')) {
        wp_die(esc_html__('Security check failed.', 'wp-art-routes'));
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this resource.', 'wp-art-routes'));
    }

    // Build CSV content
    $csv_rows = [];
    $csv_rows[] = ['Type', 'Name', 'Description', 'Latitude', 'Longitude', 'Number', 'Icon', 'Creator'];
    $csv_rows[] = ['location', 'Example Artwork', 'Description of the artwork', '52.0907', '5.1214', 'A1', 'icon.svg', 'Artist Name'];
    $csv_rows[] = ['info_point', 'Parking Area', 'Free parking available', '52.0910', '5.1220', '', '', ''];
    $csv_rows[] = ['location', 'Another Artwork', 'Another description', '52.0915', '5.1230', 'A2', '', 'Another Artist'];
    $csv_rows[] = ['info_point', 'Information Booth', 'Get maps and information here', '52.0920', '5.1240', '', 'info.svg', ''];

    // Convert rows to CSV string
    $csv_content = chr(0xEF) . chr(0xBB) . chr(0xBF); // BOM for Excel UTF-8 compatibility
    foreach ($csv_rows as $row) {
        $csv_content .= wp_art_routes_array_to_csv_line($row) . "\n";
    }

    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="wp-art-routes-import-template.csv"');
    header('Content-Length: ' . strlen($csv_content));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV output for download
    echo $csv_content;
    exit;
}
add_action('wp_ajax_wp_art_routes_download_csv_template', 'wp_art_routes_download_csv_template');

/**
 * AJAX handler for exporting edition data
 */
function wp_art_routes_export_edition()
{
    // Verify nonce
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'wp_art_routes_export_edition')) {
        wp_die(esc_html__('Security check failed.', 'wp-art-routes'));
    }

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to access this resource.', 'wp-art-routes'));
    }

    // Get parameters
    $edition_id = isset($_GET['edition_id']) ? absint(wp_unslash($_GET['edition_id'])) : 0;
    $format = isset($_GET['format']) ? sanitize_key(wp_unslash($_GET['format'])) : 'csv';

    // Validate edition
    if (!$edition_id) {
        wp_die(esc_html__('Please select an edition.', 'wp-art-routes'));
    }

    $edition = get_post($edition_id);
    if (!$edition || $edition->post_type !== 'edition') {
        wp_die(esc_html__('Selected edition does not exist.', 'wp-art-routes'));
    }

    // Export based on format
    if ($format === 'gpx') {
        wp_art_routes_export_edition_gpx($edition);
    } else {
        wp_art_routes_export_edition_csv($edition);
    }

    exit;
}
add_action('wp_ajax_wp_art_routes_export_edition', 'wp_art_routes_export_edition');

/**
 * Export edition data as CSV
 *
 * @param WP_Post $edition The edition post object
 */
function wp_art_routes_export_edition_csv($edition)
{
    // Get edition data
    $artworks = wp_art_routes_get_edition_artworks($edition->ID);
    $info_points = wp_art_routes_get_edition_information_points($edition->ID);

    // Generate filename
    $filename = sanitize_file_name($edition->post_title) . '-export.csv';

    // Build CSV content
    $csv_rows = [];
    $csv_rows[] = ['Type', 'Name', 'Description', 'Latitude', 'Longitude', 'Number', 'Icon', 'Creator', 'ID', 'Permalink'];

    // Add locations
    foreach ($artworks as $artwork) {
        // Get artist names
        $creator_names = [];
        if (!empty($artwork['artists'])) {
            foreach ($artwork['artists'] as $artist) {
                $creator_names[] = $artist['title'];
            }
        }

        // Get icon filename from URL
        $icon = '';
        if (!empty($artwork['icon_url'])) {
            $icon = basename($artwork['icon_url']);
        }

        $csv_rows[] = [
            'location',
            $artwork['title'],
            wp_strip_all_tags($artwork['description']),
            $artwork['latitude'],
            $artwork['longitude'],
            $artwork['number'],
            $icon,
            implode(', ', $creator_names),
            $artwork['id'],
            $artwork['permalink'],
        ];
    }

    // Add info points
    foreach ($info_points as $info_point) {
        // Get icon filename from URL
        $icon = '';
        if (!empty($info_point['icon_url'])) {
            $icon = basename($info_point['icon_url']);
        }

        $csv_rows[] = [
            'info_point',
            $info_point['title'],
            wp_strip_all_tags($info_point['excerpt']),
            $info_point['latitude'],
            $info_point['longitude'],
            '',
            $icon,
            '',
            $info_point['id'],
            $info_point['permalink'],
        ];
    }

    // Convert rows to CSV string
    $csv_content = chr(0xEF) . chr(0xBB) . chr(0xBF); // BOM for Excel UTF-8 compatibility
    foreach ($csv_rows as $row) {
        $csv_content .= wp_art_routes_array_to_csv_line($row) . "\n";
    }

    // Set headers
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($csv_content));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSV output for download
    echo $csv_content;
}

/**
 * Export edition data as GPX
 *
 * @param WP_Post $edition The edition post object
 */
function wp_art_routes_export_edition_gpx($edition)
{
    // Get edition data
    $artworks = wp_art_routes_get_edition_artworks($edition->ID);
    $info_points = wp_art_routes_get_edition_information_points($edition->ID);
    $routes = wp_art_routes_get_edition_routes($edition->ID);

    // Generate filename
    $filename = sanitize_file_name($edition->post_title) . '-export.gpx';

    // Sanitize function (reuse from ajax-handlers.php if available)
    $sanitize = function ($content) {
        if (empty($content)) {
            return '';
        }
        $content = strip_shortcodes($content);
        $content = wp_strip_all_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = trim($content);
        $content = preg_replace('/\s+/', ' ', $content);
        return htmlspecialchars($content, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    };

    // Build GPX content
    $edition_title = $sanitize($edition->post_title);
    $edition_description = $sanitize($edition->post_content);
    $creation_time = gmdate('Y-m-d\TH:i:s\Z');

    $gpx = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' . "\n";
    $gpx .= '<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="WP Art Routes Plugin"' . "\n";
    $gpx .= '    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    $gpx .= '    xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">' . "\n";
    $gpx .= '  <metadata>' . "\n";
    $gpx .= '    <name>' . $edition_title . '</name>' . "\n";
    $gpx .= '    <desc>' . $edition_description . '</desc>' . "\n";
    $gpx .= '    <time>' . $creation_time . '</time>' . "\n";
    $gpx .= '  </metadata>' . "\n";

    // Add locations as waypoints
    $location_label = wp_art_routes_label('location', false);
    foreach ($artworks as $index => $artwork) {
        $artwork_name = $sanitize($artwork['title']);
        $artwork_desc = $sanitize($artwork['description'] ?? $artwork['excerpt'] ?? '');
        $artwork_number = !empty($artwork['number']) ? $sanitize($artwork['number']) : ($index + 1);

        if (isset($artwork['latitude']) && isset($artwork['longitude']) &&
            is_numeric($artwork['latitude']) && is_numeric($artwork['longitude'])) {
            $gpx .= '  <wpt lat="' . esc_attr($artwork['latitude']) . '" lon="' . esc_attr($artwork['longitude']) . '">' . "\n";
            $gpx .= '    <name>' . $location_label . ' ' . $artwork_number . ': ' . $artwork_name . '</name>' . "\n";
            if ($artwork_desc) {
                $gpx .= '    <desc>' . $artwork_desc . '</desc>' . "\n";
            }
            $gpx .= '    <type>' . $location_label . '</type>' . "\n";
            $gpx .= '  </wpt>' . "\n";
        }
    }

    // Add information points as waypoints
    $info_point_label = wp_art_routes_label('info_point', false);
    foreach ($info_points as $info_point) {
        $info_name = $sanitize($info_point['title']);
        $info_desc = $sanitize($info_point['excerpt'] ?? '');

        if (isset($info_point['latitude']) && isset($info_point['longitude']) &&
            is_numeric($info_point['latitude']) && is_numeric($info_point['longitude'])) {
            $gpx .= '  <wpt lat="' . esc_attr($info_point['latitude']) . '" lon="' . esc_attr($info_point['longitude']) . '">' . "\n";
            $gpx .= '    <name>' . $info_point_label . ': ' . $info_name . '</name>' . "\n";
            if ($info_desc) {
                $gpx .= '    <desc>' . $info_desc . '</desc>' . "\n";
            }
            $gpx .= '    <type>' . $info_point_label . '</type>' . "\n";
            $gpx .= '  </wpt>' . "\n";
        }
    }

    // Add routes as tracks
    foreach ($routes as $route) {
        if (empty($route['route_path'])) {
            continue;
        }

        $route_title = $sanitize($route['title']);
        $gpx .= '  <trk>' . "\n";
        $gpx .= '    <name>' . $route_title . '</name>' . "\n";
        $gpx .= '    <trkseg>' . "\n";

        foreach ($route['route_path'] as $point) {
            // Handle both array and object format
            if (is_array($point) && isset($point[0]) && isset($point[1])) {
                $lat = $point[0];
                $lng = $point[1];
            } elseif (is_object($point) || is_array($point)) {
                $point = (array) $point;
                $lat = isset($point['lat']) ? $point['lat'] : '';
                $lng = isset($point['lng']) ? $point['lng'] : '';
            } else {
                continue;
            }

            if (is_numeric($lat) && is_numeric($lng)) {
                $gpx .= '      <trkpt lat="' . esc_attr($lat) . '" lon="' . esc_attr($lng) . '"></trkpt>' . "\n";
            }
        }

        $gpx .= '    </trkseg>' . "\n";
        $gpx .= '  </trk>' . "\n";
    }

    $gpx .= '</gpx>' . "\n";

    // Set headers and output
    header('Content-Type: application/gpx+xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($gpx));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $gpx is valid GPX XML content for file download
    echo $gpx;
}
