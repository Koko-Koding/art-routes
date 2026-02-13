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
        settings: null,
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
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'art_routes_dashboard_get_items',
                nonce: artRoutesDashboard.nonce,
                edition_id: editionId
            },
            success: function(response) {
                if (response.success) {
                    state.edition = response.data.edition;
                    state.routes = response.data.routes;
                    state.locations = response.data.locations;
                    state.infoPoints = response.data.info_points;
                    state.availableIcons = response.data.available_icons;
                    state.settings = response.data.settings;

                    $viewFrontendLink.attr('href', state.edition.permalink).show();
                    renderTables();
                    renderSettings();
                    initMap();
                } else {
                    alert(response.data.message || artRoutesDashboard.strings.error);
                }
            },
            error: function() {
                alert(artRoutesDashboard.strings.error);
            }
        });
    }

    function showTableLoading(type) {
        const $tbody = $(`#${type}-table-body`);
        const colspan = type === 'routes' ? 4 : (type === 'locations' ? 8 : 7);
        $tbody.html(`<tr class="loading-row"><td colspan="${colspan}">${artRoutesDashboard.strings.loading}</td></tr>`);
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
            $countSpan.text(`(${total}) - ${published} ${artRoutesDashboard.strings.published}, ${drafts} drafts`);
        });
    }

    /**
     * Render routes table
     */
    function renderRoutesTable() {
        const $tbody = $('#routes-table-body');
        $tbody.empty();

        if (state.routes.length === 0) {
            $tbody.html(`<tr class="no-items"><td colspan="4">${artRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.routes.forEach(function(route) {
            const statusClass = route.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = route.status === 'publish' ? artRoutesDashboard.strings.published : 'Draft';
            const toggleButton = route.status === 'publish'
                ? `<button type="button" class="button button-small status-toggle" data-id="${route.id}" data-action="draft" title="${artRoutesDashboard.strings.setToDraft || 'Set to Draft'}">${artRoutesDashboard.strings.toDraft || '→ Draft'}</button>`
                : `<button type="button" class="button button-small button-primary status-toggle" data-id="${route.id}" data-action="publish" title="${artRoutesDashboard.strings.publish || 'Publish'}">${artRoutesDashboard.strings.toPublish || '→ Publish'}</button>`;
            const row = `
                <tr data-id="${route.id}" data-type="route" data-status="${route.status}">
                    <td><input type="checkbox" class="item-checkbox" value="${route.id}"></td>
                    <td class="editable-cell" data-field="title" data-value="${escapeHtml(route.title)}">${escapeHtml(route.title)}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    <td class="actions-cell">
                        ${toggleButton}
                        <a href="${route.edit_url}" class="button button-small" target="_blank">${artRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${route.id}" data-type="route">${artRoutesDashboard.strings.delete}</button>
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
            $tbody.html(`<tr class="no-items"><td colspan="8">${artRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.locations.forEach(function(location) {
            const statusClass = location.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = location.status === 'publish' ? artRoutesDashboard.strings.published : 'Draft';
            const iconDisplay = location.icon ? escapeHtml(location.icon) : '-';
            const toggleButton = location.status === 'publish'
                ? `<button type="button" class="button button-small status-toggle" data-id="${location.id}" data-action="draft" title="${artRoutesDashboard.strings.setToDraft || 'Set to Draft'}">${artRoutesDashboard.strings.toDraft || '→ Draft'}</button>`
                : `<button type="button" class="button button-small button-primary status-toggle" data-id="${location.id}" data-action="publish" title="${artRoutesDashboard.strings.publish || 'Publish'}">${artRoutesDashboard.strings.toPublish || '→ Publish'}</button>`;
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
                        ${toggleButton}
                        <a href="${location.edit_url}" class="button button-small" target="_blank">${artRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${location.id}" data-type="location">${artRoutesDashboard.strings.delete}</button>
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
            $tbody.html(`<tr class="no-items"><td colspan="7">${artRoutesDashboard.strings.noItems}</td></tr>`);
            return;
        }

        state.infoPoints.forEach(function(infoPoint) {
            const statusClass = infoPoint.status === 'publish' ? 'publish' : 'draft';
            const statusLabel = infoPoint.status === 'publish' ? artRoutesDashboard.strings.published : 'Draft';
            const iconDisplay = infoPoint.icon ? escapeHtml(infoPoint.icon) : '-';
            const toggleButton = infoPoint.status === 'publish'
                ? `<button type="button" class="button button-small status-toggle" data-id="${infoPoint.id}" data-action="draft" title="${artRoutesDashboard.strings.setToDraft || 'Set to Draft'}">${artRoutesDashboard.strings.toDraft || '→ Draft'}</button>`
                : `<button type="button" class="button button-small button-primary status-toggle" data-id="${infoPoint.id}" data-action="publish" title="${artRoutesDashboard.strings.publish || 'Publish'}">${artRoutesDashboard.strings.toPublish || '→ Publish'}</button>`;
            const row = `
                <tr data-id="${infoPoint.id}" data-type="info_point" data-status="${infoPoint.status}">
                    <td><input type="checkbox" class="item-checkbox" value="${infoPoint.id}"></td>
                    <td class="editable-cell" data-field="title" data-value="${escapeHtml(infoPoint.title)}">${escapeHtml(infoPoint.title)}</td>
                    <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                    <td class="editable-cell" data-field="latitude" data-value="${infoPoint.latitude || ''}">${infoPoint.latitude || '-'}</td>
                    <td class="editable-cell" data-field="longitude" data-value="${infoPoint.longitude || ''}">${infoPoint.longitude || '-'}</td>
                    <td class="icon-cell" data-id="${infoPoint.id}" data-icon="${escapeHtml(infoPoint.icon || '')}">${iconDisplay}</td>
                    <td class="actions-cell">
                        ${toggleButton}
                        <a href="${infoPoint.edit_url}" class="button button-small" target="_blank">${artRoutesDashboard.strings.edit}</a>
                        <button type="button" class="button button-small delete-item" data-id="${infoPoint.id}" data-type="info_point">${artRoutesDashboard.strings.delete}</button>
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
     * Bind table interaction events
     */
    function bindTableEvents() {
        // Unbind first to prevent duplicates
        $(document).off('.dashboard');

        // Editable cells - click to edit
        $(document).on('click.dashboard', '.editable-cell:not(.editing)', startEditing);

        // Status badge click - toggle status
        $(document).on('click.dashboard', '.status-badge', toggleStatus);

        // Delete button
        $(document).on('click.dashboard', '.delete-item', deleteItem);

        // Status toggle button
        $(document).on('click.dashboard', '.status-toggle', handleStatusToggle);

        // Checkbox header - select all in section
        $(document).on('change.dashboard', '.select-all-checkbox', selectAllInSection);

        // Selection buttons
        $(document).on('click.dashboard', '.select-all', function() {
            selectItems($(this).closest('.dashboard-section'), 'all');
        });
        $(document).on('click.dashboard', '.select-none', function() {
            selectItems($(this).closest('.dashboard-section'), 'none');
        });
        $(document).on('click.dashboard', '.select-drafts', function() {
            selectItems($(this).closest('.dashboard-section'), 'drafts');
        });

        // Bulk action apply
        $(document).on('click.dashboard', '.bulk-apply', applyBulkAction);

        // Icon cell click
        $(document).on('click.dashboard', '.icon-cell', showIconSelector);
    }

    /**
     * Start inline editing
     */
    function startEditing() {
        const $cell = $(this);
        if ($cell.hasClass('editing')) return;

        const currentValue = $cell.data('value') || '';
        const field = $cell.data('field');

        $cell.addClass('editing');
        $cell.html(`<input type="text" value="${escapeHtml(currentValue)}" data-original="${escapeHtml(currentValue)}" />`);

        const $input = $cell.find('input');
        $input.focus().select();

        // Save on blur or Enter
        $input.on('blur', function() {
            finishEditing($cell, $(this).val());
        });

        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).blur();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditing($cell, $(this).data('original'));
            }
        });
    }

    /**
     * Finish inline editing and save
     */
    function finishEditing($cell, newValue) {
        const originalValue = $cell.find('input').data('original');
        const field = $cell.data('field');
        const postId = $cell.closest('tr').data('id');

        // If value hasn't changed, just restore display
        if (newValue === originalValue) {
            $cell.removeClass('editing');
            $cell.html(newValue || '—');
            $cell.data('value', newValue);
            return;
        }

        // Show saving state
        $cell.addClass('saving');
        $cell.removeClass('editing');
        $cell.html(newValue || '—');
        $cell.data('value', newValue);

        // Save via AJAX
        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'art_routes_dashboard_update_item',
                nonce: artRoutesDashboard.nonce,
                post_id: postId,
                field: field,
                value: newValue
            },
            success: function(response) {
                $cell.removeClass('saving');
                if (response.success) {
                    $cell.addClass('saved');
                    setTimeout(() => $cell.removeClass('saved'), 500);

                    // Update local state
                    updateLocalState(postId, field, newValue);

                    // Update map if coordinates changed
                    if (field === 'latitude' || field === 'longitude') {
                        updateMap();
                    }
                } else {
                    $cell.addClass('error');
                    alert(response.data.message || artRoutesDashboard.strings.error);
                    // Restore original value
                    $cell.html(originalValue || '—');
                    $cell.data('value', originalValue);
                    setTimeout(() => $cell.removeClass('error'), 2000);
                }
            },
            error: function() {
                $cell.removeClass('saving');
                $cell.addClass('error');
                alert(artRoutesDashboard.strings.error);
                $cell.html(originalValue || '—');
                $cell.data('value', originalValue);
                setTimeout(() => $cell.removeClass('error'), 2000);
            }
        });
    }

    /**
     * Cancel editing
     */
    function cancelEditing($cell, originalValue) {
        $cell.removeClass('editing');
        $cell.html(originalValue || '—');
    }

    /**
     * Handle status toggle button click
     */
    function handleStatusToggle() {
        const $button = $(this);
        const postId = $button.data('id');
        const newStatus = $button.data('action'); // 'publish' or 'draft'
        const $row = $button.closest('tr');
        const $badge = $row.find('.status-badge');
        const currentStatus = $badge.hasClass('publish') ? 'publish' : 'draft';

        if (currentStatus === newStatus) return; // Already in target status

        setItemStatus(postId, newStatus, $row, $badge);
    }

    /**
     * Toggle item status (publish/draft) - triggered by clicking status badge
     */
    function toggleStatus() {
        const $badge = $(this);
        const postId = $badge.closest('tr').data('id');
        const $row = $badge.closest('tr');
        const currentStatus = $badge.hasClass('publish') ? 'publish' : 'draft';
        const newStatus = currentStatus === 'publish' ? 'draft' : 'publish';

        setItemStatus(postId, newStatus, $row, $badge);
    }

    /**
     * Set item status to a specific value
     */
    function setItemStatus(postId, newStatus, $row, $badge) {
        const currentStatus = $badge.hasClass('publish') ? 'publish' : 'draft';

        // Optimistic UI update
        $badge.removeClass(currentStatus).addClass(newStatus);
        $badge.text(newStatus === 'publish' ? artRoutesDashboard.strings.published : 'Draft');
        $row.find('.item-checkbox').data('status', newStatus);

        // Save via AJAX
        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'art_routes_dashboard_update_item',
                nonce: artRoutesDashboard.nonce,
                post_id: postId,
                field: 'status',
                value: newStatus
            },
            success: function(response) {
                if (response.success) {
                    // Update local state
                    updateLocalState(postId, 'status', newStatus);
                    updateSectionCounts();
                    updateMap();
                    // Re-render tables to update toggle buttons
                    renderTables();
                } else {
                    // Revert on error
                    $badge.removeClass(newStatus).addClass(currentStatus);
                    $badge.text(currentStatus === 'publish' ? artRoutesDashboard.strings.published : 'Draft');
                    alert(response.data.message || artRoutesDashboard.strings.error);
                }
            },
            error: function() {
                // Revert on error
                $badge.removeClass(newStatus).addClass(currentStatus);
                $badge.text(currentStatus === 'publish' ? artRoutesDashboard.strings.published : 'Draft');
                alert(artRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Update local state after edit
     */
    function updateLocalState(postId, field, value) {
        // Find and update in routes
        const route = state.routes.find(r => r.id === postId);
        if (route) {
            route[field] = value;
            return;
        }

        // Find and update in locations
        const location = state.locations.find(l => l.id === postId);
        if (location) {
            location[field] = value;
            return;
        }

        // Find and update in info points
        const infoPoint = state.infoPoints.find(i => i.id === postId);
        if (infoPoint) {
            infoPoint[field] = value;
        }
    }

    /**
     * Delete single item
     */
    function deleteItem(e) {
        e.preventDefault();
        const postId = $(this).data('id');

        if (!confirm(artRoutesDashboard.strings.confirmDeleteSingle)) {
            return;
        }

        const $row = $(this).closest('tr');
        $row.css('opacity', '0.5');

        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'art_routes_dashboard_bulk_action',
                nonce: artRoutesDashboard.nonce,
                bulk_action: 'delete',
                post_ids: [postId]
            },
            success: function(response) {
                if (response.success) {
                    $row.fadeOut(300, function() {
                        $(this).remove();
                        removeFromLocalState(postId);
                        updateSectionCounts();
                        updateMap();
                    });
                } else {
                    $row.css('opacity', '1');
                    alert(response.data.message || artRoutesDashboard.strings.error);
                }
            },
            error: function() {
                $row.css('opacity', '1');
                alert(artRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Remove item from local state
     */
    function removeFromLocalState(postId) {
        state.routes = state.routes.filter(r => r.id !== postId);
        state.locations = state.locations.filter(l => l.id !== postId);
        state.infoPoints = state.infoPoints.filter(i => i.id !== postId);
    }

    /**
     * Select all items in section via header checkbox
     */
    function selectAllInSection() {
        const $section = $(this).closest('.dashboard-section');
        const isChecked = $(this).is(':checked');
        $section.find('.item-checkbox').prop('checked', isChecked);
    }

    /**
     * Select items by criteria
     */
    function selectItems($section, criteria) {
        const $checkboxes = $section.find('.item-checkbox');

        switch (criteria) {
            case 'all':
                $checkboxes.prop('checked', true);
                $section.find('.select-all-checkbox').prop('checked', true);
                break;
            case 'none':
                $checkboxes.prop('checked', false);
                $section.find('.select-all-checkbox').prop('checked', false);
                break;
            case 'drafts':
                $checkboxes.each(function() {
                    const isDraft = $(this).data('status') === 'draft';
                    $(this).prop('checked', isDraft);
                });
                break;
        }
    }

    /**
     * Apply bulk action
     */
    function applyBulkAction() {
        const $section = $(this).closest('.dashboard-section');
        const action = $section.find('.bulk-action-select').val();
        const $checked = $section.find('.item-checkbox:checked');
        const postIds = $checked.map(function() { return $(this).val(); }).get();

        if (!action) {
            return;
        }

        if (postIds.length === 0) {
            alert(artRoutesDashboard.strings.noItemsSelected);
            return;
        }

        // Confirm delete
        if (action === 'delete') {
            if (!confirm(artRoutesDashboard.strings.confirmDelete)) {
                return;
            }
        }

        // Disable UI during request
        const $button = $(this);
        $button.prop('disabled', true).text(artRoutesDashboard.strings.saving);
        $checked.closest('tr').css('opacity', '0.5');

        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: {
                action: 'art_routes_dashboard_bulk_action',
                nonce: artRoutesDashboard.nonce,
                bulk_action: action,
                post_ids: postIds
            },
            success: function(response) {
                $button.prop('disabled', false).text($button.closest('.bulk-actions').find('.bulk-action-select option:first').text().replace('Bulk Actions', 'Apply'));

                if (response.success) {
                    // Reload edition data to refresh tables
                    loadEdition(state.editionId);
                } else {
                    $checked.closest('tr').css('opacity', '1');
                    alert(response.data.message || artRoutesDashboard.strings.error);
                }
            },
            error: function() {
                $button.prop('disabled', false);
                $checked.closest('tr').css('opacity', '1');
                alert(artRoutesDashboard.strings.error);
            }
        });

        // Reset select
        $section.find('.bulk-action-select').val('');
    }

    /**
     * Show icon selector dropdown
     */
    function showIconSelector() {
        const $cell = $(this);
        const currentIcon = $cell.data('icon') || '';
        const postId = $cell.data('id');
        const $row = $cell.closest('tr');
        const postType = $row.data('type');

        // Build dropdown HTML
        let optionsHtml = '<option value="">— No Icon —</option>';
        state.availableIcons.forEach(function(icon) {
            const selected = icon.filename === currentIcon ? 'selected' : '';
            optionsHtml += `<option value="${escapeHtml(icon.filename)}" ${selected}>${escapeHtml(icon.display_name)}</option>`;
        });

        // Replace cell with select
        const originalHtml = $cell.html();
        $cell.html(`<select class="icon-select">${optionsHtml}</select>`);

        const $select = $cell.find('select');
        $select.focus();

        $select.on('change', function() {
            const newIcon = $(this).val();
            saveIcon($cell, postId, newIcon, originalHtml);
        });

        $select.on('blur', function() {
            // Restore original if no change
            setTimeout(function() {
                if ($cell.find('select').length) {
                    $cell.html(originalHtml);
                }
            }, 200);
        });
    }

    /**
     * Save icon selection
     */
    function saveIcon($cell, postId, newIcon, originalHtml) {
        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'art_routes_dashboard_update_item',
                nonce: artRoutesDashboard.nonce,
                post_id: postId,
                field: 'icon',
                value: newIcon
            },
            success: function(response) {
                if (response && response.success) {
                    // Update cell with new icon display name (consistent with table rendering)
                    const iconDisplay = response.data.icon_display_name || response.data.icon || '—';
                    $cell.html(escapeHtml(iconDisplay));
                    $cell.data('icon', response.data.icon || '');
                    $cell.attr('data-icon', response.data.icon || '');

                    // Update local state
                    updateLocalState(postId, 'icon', response.data.icon || '');
                    updateLocalState(postId, 'icon_url', response.data.icon_url || '');
                } else {
                    $cell.html(originalHtml);
                    var message = (response && response.data && response.data.message)
                        ? response.data.message
                        : artRoutesDashboard.strings.error;
                    alert(message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Icon save error:', status, error);
                $cell.html(originalHtml);
                alert(artRoutesDashboard.strings.error);
            }
        });
    }

    /**
     * Initialize the map
     */
    function initMap() {
        // Destroy existing map if any
        if (state.map) {
            state.map.remove();
            state.map = null;
        }

        // Create map
        state.map = L.map('dashboard-map', {
            scrollWheelZoom: false
        });

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(state.map);

        // Render markers
        updateMap();
    }

    /**
     * Update map markers
     */
    function updateMap() {
        if (!state.map) return;

        // Clear existing layers
        if (state.mapLayers.routes) {
            state.map.removeLayer(state.mapLayers.routes);
        }
        if (state.mapLayers.locations) {
            state.map.removeLayer(state.mapLayers.locations);
        }
        if (state.mapLayers.infoPoints) {
            state.map.removeLayer(state.mapLayers.infoPoints);
        }

        // Create layer groups
        state.mapLayers.routes = L.layerGroup();
        state.mapLayers.locations = L.layerGroup();
        state.mapLayers.infoPoints = L.layerGroup();

        const bounds = L.latLngBounds();
        let hasPoints = false;

        // Add routes as polylines
        state.routes.forEach(function(route) {
            if (route.route_path && route.route_path.length > 0) {
                const latLngs = route.route_path.map(function(point) {
                    // Handle both array format [lat, lng] and object format {lat, lng}
                    if (Array.isArray(point)) {
                        return [point[0], point[1]];
                    } else {
                        return [point.lat, point.lng];
                    }
                });

                const opacity = route.status === 'publish' ? 1 : 0.4;
                const polyline = L.polyline(latLngs, {
                    color: '#3388ff',
                    weight: 4,
                    opacity: opacity
                });

                polyline.bindTooltip(route.title);
                polyline.addTo(state.mapLayers.routes);

                latLngs.forEach(function(ll) {
                    bounds.extend(ll);
                    hasPoints = true;
                });
            }
        });

        // Add locations as markers
        state.locations.forEach(function(location) {
            if (location.latitude && location.longitude) {
                const opacity = location.status === 'publish' ? 1 : 0.5;
                const marker = L.circleMarker([location.latitude, location.longitude], {
                    radius: 8,
                    fillColor: '#2ecc71',
                    color: '#27ae60',
                    weight: 2,
                    opacity: opacity,
                    fillOpacity: opacity * 0.8
                });

                const label = location.number ? `${location.number}: ${location.title}` : location.title;
                marker.bindTooltip(label);
                marker.addTo(state.mapLayers.locations);

                bounds.extend([location.latitude, location.longitude]);
                hasPoints = true;
            }
        });

        // Add info points as markers
        state.infoPoints.forEach(function(infoPoint) {
            if (infoPoint.latitude && infoPoint.longitude) {
                const opacity = infoPoint.status === 'publish' ? 1 : 0.5;
                const marker = L.circleMarker([infoPoint.latitude, infoPoint.longitude], {
                    radius: 6,
                    fillColor: '#e67e22',
                    color: '#d35400',
                    weight: 2,
                    opacity: opacity,
                    fillOpacity: opacity * 0.8
                });

                marker.bindTooltip(infoPoint.title);
                marker.addTo(state.mapLayers.infoPoints);

                bounds.extend([infoPoint.latitude, infoPoint.longitude]);
                hasPoints = true;
            }
        });

        // Add layers to map
        state.mapLayers.routes.addTo(state.map);
        state.mapLayers.locations.addTo(state.map);
        state.mapLayers.infoPoints.addTo(state.map);

        // Fit bounds
        if (hasPoints) {
            state.map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            // Default view (Netherlands)
            state.map.setView([52.1326, 5.2913], 7);
        }
    }

    /**
     * Render edition settings form
     */
    function renderSettings() {
        if (!state.settings) return;

        const settings = state.settings;
        const globalTerm = settings.global_terminology || {};
        const editionTerm = settings.terminology || {};

        // Populate dates
        $('#edition_start_date').val(settings.start_date || '');
        $('#edition_end_date').val(settings.end_date || '');

        // Populate icon dropdown
        const $iconSelect = $('#edition_default_icon');
        $iconSelect.empty();
        $iconSelect.append('<option value="">' + (artRoutesDashboard.strings.useGlobalDefault || 'Use global default') + '</option>');

        state.availableIcons.forEach(function(icon) {
            const selected = icon.filename === settings.default_location_icon ? 'selected' : '';
            $iconSelect.append(`<option value="${escapeHtml(icon.filename)}" ${selected}>${escapeHtml(icon.display_name)}</option>`);
        });

        // Update icon preview
        updateIconPreview();

        // Bind icon change event
        $iconSelect.off('change').on('change', updateIconPreview);

        // Populate terminology fields with placeholders from global
        const termTypes = ['route', 'location', 'info_point', 'creator'];
        const termFields = ['singular', 'plural'];

        termTypes.forEach(function(type) {
            termFields.forEach(function(field) {
                const $input = $(`#term_${type}_${field}`);
                const globalValue = globalTerm[type] ? globalTerm[type][field] || '' : '';
                const editionValue = editionTerm[type] ? editionTerm[type][field] || '' : '';

                $input.val(editionValue);
                $input.attr('placeholder', globalValue);
            });
        });

        // Bind form submit
        $('#edition-settings-form').off('submit').on('submit', saveSettings);
    }

    /**
     * Update icon preview
     */
    function updateIconPreview() {
        const selectedIcon = $('#edition_default_icon').val();
        const $preview = $('#edition_default_icon_preview');

        if (selectedIcon) {
            const iconData = state.availableIcons.find(i => i.filename === selectedIcon);
            if (iconData && iconData.url) {
                $preview.html(`<img src="${escapeHtml(iconData.url)}" alt="" style="width: 24px; height: 24px; vertical-align: middle;">`);
            } else {
                $preview.empty();
            }
        } else {
            $preview.empty();
        }
    }

    /**
     * Save edition settings
     */
    function saveSettings(e) {
        e.preventDefault();

        const $form = $('#edition-settings-form');
        const $submitBtn = $('#save-edition-settings');
        const $status = $('#settings-save-status');

        $submitBtn.prop('disabled', true);
        $status.text(artRoutesDashboard.strings.saving || 'Saving...');

        // Collect form data
        const formData = {
            action: 'art_routes_dashboard_save_settings',
            nonce: artRoutesDashboard.nonce,
            edition_id: state.editionId,
            start_date: $('#edition_start_date').val(),
            end_date: $('#edition_end_date').val(),
            default_location_icon: $('#edition_default_icon').val(),
            terminology: {
                route: {
                    singular: $('#term_route_singular').val(),
                    plural: $('#term_route_plural').val()
                },
                location: {
                    singular: $('#term_location_singular').val(),
                    plural: $('#term_location_plural').val()
                },
                info_point: {
                    singular: $('#term_info_point_singular').val(),
                    plural: $('#term_info_point_plural').val()
                },
                creator: {
                    singular: $('#term_creator_singular').val(),
                    plural: $('#term_creator_plural').val()
                }
            }
        };

        $.ajax({
            url: artRoutesDashboard.ajaxUrl,
            type: 'POST',
            data: formData,
            success: function(response) {
                $submitBtn.prop('disabled', false);
                if (response.success) {
                    $status.text(artRoutesDashboard.strings.saved || 'Saved!').css('color', 'green');
                    setTimeout(function() {
                        $status.text('').css('color', '');
                    }, 3000);

                    // Update local state
                    state.settings.start_date = formData.start_date;
                    state.settings.end_date = formData.end_date;
                    state.settings.default_location_icon = formData.default_location_icon;
                    state.settings.terminology = formData.terminology;
                } else {
                    $status.text(response.data.message || artRoutesDashboard.strings.error).css('color', 'red');
                }
            },
            error: function() {
                $submitBtn.prop('disabled', false);
                $status.text(artRoutesDashboard.strings.error).css('color', 'red');
            }
        });
    }

    $(document).ready(init);
    window.artRoutesDashboardState = state;

})(jQuery);
