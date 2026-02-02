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

    /**
     * Render all tables
     */
    function renderTables() {
        renderRoutesTable();
        renderLocationsTable();
        renderInfoPointsTable();
        updateSectionCounts();
        restoreCollapseState();
        bindTableEvents();
    }

    /**
     * Update section counts in headers
     */
    function updateSectionCounts() {
        const sections = [
            { type: 'routes', data: state.routes },
            { type: 'locations', data: state.locations },
            { type: 'info-points', data: state.infoPoints }
        ];

        sections.forEach(function(section) {
            const total = section.data.length;
            const published = section.data.filter(function(item) { return item.status === 'publish'; }).length;
            const drafts = total - published;
            const $countSpan = $(`#${section.type}-section .section-counts`);
            $countSpan.text(`(${total}) - ${published} ${wpArtRoutesDashboard.strings.published}, ${drafts} drafts`);
        });
    }

    /**
     * Render routes table
     */
    function renderRoutesTable() {
        const $tbody = $('#routes-table-body');
        $tbody.empty();

        if (state.routes.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="4">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.routes.forEach(function(route) {
            const statusClass = route.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = route.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft';
            const row = `
                <tr data-id="${route.id}" data-type="route" data-status="${route.status}">
                    <td><input type="checkbox" class="item-checkbox" value="${route.id}"></td>
                    <td class="editable-cell" data-field="title" data-value="${escapeHtml(route.title)}">${escapeHtml(route.title)}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    <td class="actions-cell">
                        <a href="${route.edit_url}" class="button button-small" target="_blank">${wpArtRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${route.id}" data-type="route">${wpArtRoutesDashboard.strings.delete}</button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    /**
     * Render locations table
     */
    function renderLocationsTable() {
        const $tbody = $('#locations-table-body');
        $tbody.empty();

        if (state.locations.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="8">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.locations.forEach(function(location) {
            const statusClass = location.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = location.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft';
            const iconDisplay = location.icon ? escapeHtml(location.icon) : '-';
            const row = `
                <tr data-id="${location.id}" data-type="location" data-status="${location.status}">
                    <td><input type="checkbox" class="item-checkbox" value="${location.id}"></td>
                    <td class="editable-cell" data-field="number" data-value="${escapeHtml(location.number || '')}">${escapeHtml(location.number || '-')}</td>
                    <td class="editable-cell" data-field="title" data-value="${escapeHtml(location.title)}">${escapeHtml(location.title)}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    <td class="editable-cell" data-field="latitude" data-value="${location.latitude || ''}">${location.latitude || '-'}</td>
                    <td class="editable-cell" data-field="longitude" data-value="${location.longitude || ''}">${location.longitude || '-'}</td>
                    <td class="icon-cell" data-id="${location.id}" data-icon="${escapeHtml(location.icon || '')}">${iconDisplay}</td>
                    <td class="actions-cell">
                        <a href="${location.edit_url}" class="button button-small" target="_blank">${wpArtRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${location.id}" data-type="location">${wpArtRoutesDashboard.strings.delete}</button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    /**
     * Render info points table
     */
    function renderInfoPointsTable() {
        const $tbody = $('#info-points-table-body');
        $tbody.empty();

        if (state.infoPoints.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="7">${wpArtRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.infoPoints.forEach(function(infoPoint) {
            const statusClass = infoPoint.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = infoPoint.status === 'publish' ? wpArtRoutesDashboard.strings.published : 'Draft';
            const iconDisplay = infoPoint.icon ? escapeHtml(infoPoint.icon) : '-';
            const row = `
                <tr data-id="${infoPoint.id}" data-type="info_point" data-status="${infoPoint.status}">
                    <td><input type="checkbox" class="item-checkbox" value="${infoPoint.id}"></td>
                    <td class="editable-cell" data-field="title" data-value="${escapeHtml(infoPoint.title)}">${escapeHtml(infoPoint.title)}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    <td class="editable-cell" data-field="latitude" data-value="${infoPoint.latitude || ''}">${infoPoint.latitude || '-'}</td>
                    <td class="editable-cell" data-field="longitude" data-value="${infoPoint.longitude || ''}">${infoPoint.longitude || '-'}</td>
                    <td class="icon-cell" data-id="${infoPoint.id}" data-icon="${escapeHtml(infoPoint.icon || '')}">${iconDisplay}</td>
                    <td class="actions-cell">
                        <a href="${infoPoint.edit_url}" class="button button-small" target="_blank">${wpArtRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${infoPoint.id}" data-type="info_point">${wpArtRoutesDashboard.strings.delete}</button>
                    </td>
                </tr>
            `;
            $tbody.append(row);
        });
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        if (text === null || text === undefined) {
            return '';
        }
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    /**
     * Bind table events (placeholder for Task 8)
     */
    function bindTableEvents() {
        // Task 8: Will implement inline editing, delete handlers, checkbox handlers
    }

    // Placeholder functions (will be implemented in subsequent tasks)
    function initMap() {
        // Task 10
        console.log('initMap: TODO');
    }

    $(document).ready(init);
    window.wpArtRoutesDashboardState = state;

})(jQuery);
