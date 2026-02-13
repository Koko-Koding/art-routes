/**
 * Icon Preview for meta boxes
 *
 * Shared icon preview handler for artwork, info point, and route icon selectors.
 * Each meta box pushes config to wpArtRoutesIconConfigs before this script loads.
 *
 * Config format: { selectId: string, previewId: string, iconUrls: object }
 *
 * @package WP Art Routes
 */

jQuery(document).ready(function($) {
    if (typeof wpArtRoutesIconConfigs === 'undefined') {
        return;
    }

    wpArtRoutesIconConfigs.forEach(function(config) {
        var $select = $('#' + config.selectId);
        var $preview = $('#' + config.previewId);
        var iconUrls = config.iconUrls;

        if (!$select.length || !$preview.length) {
            return;
        }

        $select.on('change', function() {
            var selectedIcon = $(this).val();

            if (selectedIcon && iconUrls[selectedIcon]) {
                $preview.show();
                $preview.find('img').attr('src', iconUrls[selectedIcon]).attr('alt', selectedIcon);
            } else {
                $preview.hide();
            }
        });
    });
});
