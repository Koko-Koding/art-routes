/**
 * Edition Map Gutenberg Block
 *
 * Displays an interactive map with routes, locations, and info points
 * from a selected Edition.
 *
 * @package WP Art Routes
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
        Placeholder,
    } = wp.components;
    const { Fragment, createElement } = wp.element;
    const ServerSideRender = wp.serverSideRender;

    // Get localized block data
    const blockData = window.wpArtRoutesBlockData || {};
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
     * Register the Edition Map block
     */
    registerBlockType('wp-art-routes/edition-map', {
        title: i18n.blockTitle || 'Edition Map',
        description:
            i18n.blockDescription ||
            'Display an interactive map for an Edition.',
        icon: blockIcon,
        category: 'wp-art-routes',
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

            // Block preview using ServerSideRender
            const blockPreview = createElement(
                'div',
                { className: 'wp-art-routes-edition-map-block-wrapper' },
                createElement(ServerSideRender, {
                    block: 'wp-art-routes/edition-map',
                    attributes: attributes,
                    EmptyResponsePlaceholder: function () {
                        return createElement(
                            Placeholder,
                            {
                                icon: blockIcon,
                                label: i18n.blockTitle || 'Edition Map',
                            },
                            createElement(
                                'p',
                                null,
                                i18n.noEditionSelected ||
                                    'Please select an Edition or the block will auto-detect from the page context.'
                            )
                        );
                    },
                })
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
