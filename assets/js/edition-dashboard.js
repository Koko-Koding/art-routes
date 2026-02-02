/**
 * Edition Dashboard JavaScript
 */
(function($) {
    'use strict';

    // Dashboard state
    const state = {
        editionId: null,
        edition: null,
        routes: [],
        locations: [],
        infoPoints: [],
        availableIcons: [],
        map: null,
        mapLayers: {
            routes: null,
            locations: null,
            infoPoints: null
        }
    };

    // Cache DOM elements
    const $editionSelect = $('#edition-select');
    const $dashboardContent = $('#dashboard-content');
    const $noEditionMessage = $('#no-edition-message');
    const $viewFrontendLink = $('#view-frontend-link');

    /**
     * Initialize the dashboard
     */
    function init() {
        $editionSelect.on('change', onEditionChange);
        $('.section-header').on('click', toggleSection);

        if ($editionSelect.val()) {
            loadEdition($editionSelect.val());
        }
    }

    /**
     * Handle edition dropdown change
     */
    function onEditionChange() {
        const editionId = $editionSelect.val();
        if (editionId) {
            const url = new URL(window.location);
            url.searchParams.set('edition_id', editionId);
            window.history.pushState({}, '', url);
            loadEdition(editionId);
        } else {
            $dashboardContent.hide();
            $noEditionMessage.show();
            $viewFrontendLink.hide();
            state.editionId = null;
        }
    }

    /**
     * Load edition data via AJAX
     */
    function loadEdition(editionId) {
        state.editionId = editionId;
        $dashboardContent.show();
        $noEditionMessage.hide();
        showTableLoading('routes');
        showTableLoading('locations');
        showTableLoading('info-points');

        $.ajax({
            url: wpArtRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wp_art_routes_dashboard_get_items',
                nonce: wpArtRoutesDashboard.nonce,
                edition_id: editionId
            },
            success: function(response) {
                if (response.success) {
                    state.edition = response.data.edition;
                    state.routes = response.data.routes;
                    state.locations = response.data.locations;
                    state.infoPoints = response.data.info_points;
                    state.availableIcons = response.data.available_icons;

                    $viewFrontendLink.attr('href', state.edition.permalink).show();
                    renderTables();
                    initMap();
                } else {
                    alert(response.data.message || wpArtRoutesDashboard.strings.error);
                }
            },
            error: function() {
                alert(wpArtRoutesDashboard.strings.error);
            }
        });
    }

    function showTableLoading(type) {
        const $tbody = $(`#${type}-table-body`);
        const colspan = type === 'routes' ? 4 : (type === 'locations' ? 8 : 7);
        $tbody.html(`<tr class="loading-row"><td colspan="${colspan}">${wpArtRoutesDashboard.strings.loading}</td></tr>`);
    }

    function toggleSection() {
        const $section = $(this).closest('.collapsible');
        const $content = $section.find('.section-content');
        const $icon = $section.find('.toggle-icon');

        if ($section.hasClass('collapsed')) {
            $section.removeClass('collapsed');
            $content.slideDown(200);
            $icon.text('▼');
        } else {
            $section.addClass('collapsed');
            $content.slideUp(200);
            $icon.text('▶');
        }
        saveCollapseState();
    }

    function saveCollapseState() {
        if (!state.editionId) return;
        const collapseState = {
            routes: $('#routes-section').hasClass('collapsed'),
            locations: $('#locations-section').hasClass('collapsed'),
            infoPoints: $('#info-points-section').hasClass('collapsed')
        };
        localStorage.setItem(`dashboard-collapse-${state.editionId}`, JSON.stringify(collapseState));
    }

    function restoreCollapseState() {
        if (!state.editionId) return;
        const saved = localStorage.getItem(`dashboard-collapse-${state.editionId}`);
        if (!saved) return;
        try {
            const collapseState = JSON.parse(saved);
            ['routes', 'locations', 'infoPoints'].forEach(function(type) {
                const sectionId = type === 'infoPoints' ? 'info-points-section' : `${type}-section`;
                const $section = $(`#${sectionId}`);
                if (collapseState[type]) {
                    $section.addClass('collapsed');
                    $section.find('.section-content').hide();
                    $section.find('.toggle-icon').text('▶');
                } else {
                    $section.removeClass('collapsed');
                    $section.find('.section-content').show();
                    $section.find('.toggle-icon').text('▼');
                }
            });
        } catch (e) {}
    }

    // Placeholder functions (will be implemented in subsequent tasks)
    function renderTables() {
        // Task 7
        console.log('renderTables: TODO');
    }

    function initMap() {
        // Task 10
        console.log('initMap: TODO');
    }

    $(document).ready(init);
    window.wpArtRoutesDashboardState = state;

})(jQuery);
