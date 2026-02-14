/**
 * Edition Map Gutenberg Block
 *
 * Displays an interactive map with routes, locations, and info points
 * from a selected Edition.
 *
 * @package Art_Routes
 */

(function (wp) {
    'use strict';

    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const {
        PanelBody,
        SelectControl,
        ToggleControl,
        TextControl,
    } = wp.components;
    const { Fragment, createElement } = wp.element;

    // Get localized block data
    const blockData = window.artRoutesBlockData || {};
    const i18n = blockData.i18n || {};
    const editions = blockData.editions || [];

    // Block icon - map marker
    const blockIcon = createElement(
        'svg',
        {
            width: 24,
            height: 24,
            viewBox: '0 0 24 24',
            xmlns: 'http://www.w3.org/2000/svg',
        },
        createElement('path', {
            d: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
            fill: 'currentColor',
        })
    );

    /**
     * Get edition label by ID
     */
    function getEditionLabel(editionId) {
        if (!editionId || editionId === 0) {
            return i18n.autoDetect || 'Auto-detect from page';
        }
        const edition = editions.find(function (e) {
            return e.value === editionId;
        });
        return edition ? edition.label : i18n.unknownEdition || 'Unknown Edition';
    }

    /**
     * Register the Edition Map block
     */
    registerBlockType('art-routes/edition-map', {
        title: i18n.blockTitle || 'Edition Map',
        description:
            i18n.blockDescription ||
            'Display an interactive map for an Edition.',
        icon: blockIcon,
        category: 'art-routes',
        keywords: [
            'map',
            'edition',
            'route',
            'art',
            'location',
            'leaflet',
            'openstreetmap',
        ],
        supports: {
            html: false,
            align: ['wide', 'full'],
        },
        attributes: {
            editionId: {
                type: 'number',
                default: 0,
            },
            showRoutes: {
                type: 'boolean',
                default: true,
            },
            showLocations: {
                type: 'boolean',
                default: true,
            },
            showInfoPoints: {
                type: 'boolean',
                default: true,
            },
            showLegend: {
                type: 'boolean',
                default: true,
            },
            height: {
                type: 'string',
                default: '500px',
            },
        },

        /**
         * Block edit function - renders the block in the editor
         */
        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                editionId,
                showRoutes,
                showLocations,
                showInfoPoints,
                showLegend,
                height,
            } = attributes;

            // Build options for the edition select control
            const editionOptions = editions.map(function (edition) {
                return {
                    value: String(edition.value),
                    label: edition.label,
                };
            });

            // Inspector controls (sidebar settings)
            const inspectorControls = createElement(
                InspectorControls,
                null,
                // Edition Selection Panel
                createElement(
                    PanelBody,
                    {
                        title: i18n.editionLabel || 'Edition',
                        initialOpen: true,
                    },
                    createElement(SelectControl, {
                        label: i18n.editionLabel || 'Edition',
                        value: String(editionId),
                        options: editionOptions,
                        onChange: function (value) {
                            setAttributes({ editionId: parseInt(value, 10) });
                        },
                        help: i18n.editionHelp || 'Select an edition to display.',
                    })
                ),
                // Display Options Panel
                createElement(
                    PanelBody,
                    {
                        title: i18n.displayOptionsTitle || 'Display Options',
                        initialOpen: true,
                    },
                    createElement(ToggleControl, {
                        label: i18n.showRoutesLabel || 'Show Routes',
                        checked: showRoutes,
                        onChange: function (value) {
                            setAttributes({ showRoutes: value });
                        },
                    }),
                    createElement(ToggleControl, {
                        label: i18n.showLocationsLabel || 'Show Locations',
                        checked: showLocations,
                        onChange: function (value) {
                            setAttributes({ showLocations: value });
                        },
                    }),
                    createElement(ToggleControl, {
                        label: i18n.showInfoPointsLabel || 'Show Info Points',
                        checked: showInfoPoints,
                        onChange: function (value) {
                            setAttributes({ showInfoPoints: value });
                        },
                    }),
                    createElement(ToggleControl, {
                        label: i18n.showLegendLabel || 'Show Legend',
                        checked: showLegend,
                        onChange: function (value) {
                            setAttributes({ showLegend: value });
                        },
                    })
                ),
                // Map Settings Panel
                createElement(
                    PanelBody,
                    {
                        title: i18n.mapSettingsTitle || 'Map Settings',
                        initialOpen: false,
                    },
                    createElement(TextControl, {
                        label: i18n.heightLabel || 'Map Height',
                        value: height,
                        onChange: function (value) {
                            setAttributes({ height: value });
                        },
                        help: i18n.heightHelp || 'Enter a CSS value (e.g., 500px, 50vh)',
                    })
                )
            );

            // Build list of visible content
            const visibleItems = [];
            if (showRoutes) visibleItems.push(i18n.routes || 'Routes');
            if (showLocations) visibleItems.push(i18n.locations || 'Locations');
            if (showInfoPoints) visibleItems.push(i18n.infoPoints || 'Info Points');
            if (showLegend) visibleItems.push(i18n.legend || 'Legend');

            const visibleText = visibleItems.length > 0
                ? visibleItems.join(', ')
                : i18n.nothingSelected || 'Nothing selected';

            // Custom editor preview
            const blockPreview = createElement(
                'div',
                {
                    className: 'art-routes-edition-map-preview',
                    style: {
                        minHeight: '200px',
                        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        borderRadius: '4px',
                        padding: '24px',
                        color: '#fff',
                        display: 'flex',
                        flexDirection: 'column',
                        position: 'relative',
                        overflow: 'hidden',
                    },
                },
                // Background map pattern
                createElement(
                    'div',
                    {
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            opacity: 0.1,
                            backgroundImage: 'url("data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")',
                        },
                    }
                ),
                // Header with icon
                createElement(
                    'div',
                    {
                        style: {
                            display: 'flex',
                            alignItems: 'center',
                            gap: '12px',
                            marginBottom: '20px',
                            position: 'relative',
                            zIndex: 1,
                        },
                    },
                    createElement(
                        'div',
                        {
                            style: {
                                width: '48px',
                                height: '48px',
                                background: 'rgba(255,255,255,0.2)',
                                borderRadius: '8px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                            },
                        },
                        createElement(
                            'svg',
                            {
                                width: 28,
                                height: 28,
                                viewBox: '0 0 24 24',
                                fill: '#fff',
                            },
                            createElement('path', {
                                d: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
                            })
                        )
                    ),
                    createElement(
                        'div',
                        null,
                        createElement(
                            'div',
                            {
                                style: {
                                    fontSize: '18px',
                                    fontWeight: '600',
                                    marginBottom: '2px',
                                },
                            },
                            i18n.blockTitle || 'Edition Map'
                        ),
                        createElement(
                            'div',
                            {
                                style: {
                                    fontSize: '13px',
                                    opacity: 0.9,
                                },
                            },
                            i18n.editorPreview || 'Interactive map preview'
                        )
                    )
                ),
                // Settings summary
                createElement(
                    'div',
                    {
                        style: {
                            background: 'rgba(255,255,255,0.15)',
                            borderRadius: '8px',
                            padding: '16px',
                            position: 'relative',
                            zIndex: 1,
                        },
                    },
                    // Edition row
                    createElement(
                        'div',
                        {
                            style: {
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                marginBottom: '12px',
                                paddingBottom: '12px',
                                borderBottom: '1px solid rgba(255,255,255,0.2)',
                            },
                        },
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '13px',
                                    opacity: 0.9,
                                },
                            },
                            i18n.editionLabel || 'Edition'
                        ),
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '14px',
                                    fontWeight: '500',
                                },
                            },
                            getEditionLabel(editionId)
                        )
                    ),
                    // Visible content row
                    createElement(
                        'div',
                        {
                            style: {
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                                marginBottom: '12px',
                                paddingBottom: '12px',
                                borderBottom: '1px solid rgba(255,255,255,0.2)',
                            },
                        },
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '13px',
                                    opacity: 0.9,
                                },
                            },
                            i18n.showingLabel || 'Showing'
                        ),
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    textAlign: 'right',
                                    maxWidth: '200px',
                                },
                            },
                            visibleText
                        )
                    ),
                    // Height row
                    createElement(
                        'div',
                        {
                            style: {
                                display: 'flex',
                                justifyContent: 'space-between',
                                alignItems: 'center',
                            },
                        },
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '13px',
                                    opacity: 0.9,
                                },
                            },
                            i18n.heightLabel || 'Height'
                        ),
                        createElement(
                            'span',
                            {
                                style: {
                                    fontSize: '14px',
                                    fontWeight: '500',
                                    fontFamily: 'monospace',
                                },
                            },
                            height || '500px'
                        )
                    )
                ),
                // Footer hint
                createElement(
                    'div',
                    {
                        style: {
                            marginTop: 'auto',
                            paddingTop: '16px',
                            fontSize: '12px',
                            opacity: 0.7,
                            textAlign: 'center',
                            position: 'relative',
                            zIndex: 1,
                        },
                    },
                    i18n.previewHint || 'The interactive map will appear on the published page'
                )
            );

            return createElement(
                Fragment,
                null,
                inspectorControls,
                blockPreview
            );
        },

        /**
         * Block save function - returns null for server-side rendered blocks
         */
        save: function () {
            // Server-side rendered block, no save output needed
            return null;
        },
    });
})(window.wp);
