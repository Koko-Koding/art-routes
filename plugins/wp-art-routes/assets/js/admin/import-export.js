/**
 * Import/Export Page Scripts
 *
 * Handles edition dropdown toggle for CSV/GPX import and export button.
 * Expects wpArtRoutesImportExport to be localized with: { ajaxUrl, i18n }
 *
 * @package WP Art Routes
 */

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

    // Export button handler
    var exportButton = document.getElementById('export-button');
    if (exportButton) {
        exportButton.addEventListener('click', function() {
            var editionId = document.getElementById('export_edition_id').value;
            var format = document.getElementById('export_format').value;
            var nonceField = document.querySelector('#export-form input[name="_wpnonce"]');
            var nonce = nonceField ? nonceField.value : '';

            if (!editionId) {
                alert(wpArtRoutesImportExport.i18n.selectEdition);
                return;
            }

            var url = wpArtRoutesImportExport.ajaxUrl +
                      '?action=wp_art_routes_export_edition' +
                      '&edition_id=' + encodeURIComponent(editionId) +
                      '&format=' + encodeURIComponent(format) +
                      '&_wpnonce=' + encodeURIComponent(nonce);

            window.location.href = url;
        });
    }
})();
