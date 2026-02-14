<?php
/**
 * Gutenberg Blocks Registration for Art Routes Plugin
 *
 * Registers the Edition Map block with server-side rendering.
 *
 * @package Art_Routes
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Gutenberg blocks for the plugin
 */
function art_routes_register_blocks()
{
    // Only register if Gutenberg is available
    if (!function_exists('register_block_type')) {
        return;
    }

    // Register block editor script
    wp_register_script(
        'art-routes-edition-map-block',
        ART_ROUTES_PLUGIN_URL . 'assets/js/blocks/edition-map-block.js',
        ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data'],
        ART_ROUTES_VERSION,
        true
    );

    // Register block editor styles
    wp_register_style(
        'art-routes-edition-map-block-editor',
        ART_ROUTES_PLUGIN_URL . 'assets/css/blocks/edition-map-block-editor.css',
        ['wp-edit-blocks'],
        ART_ROUTES_VERSION
    );

    // Prepare editions data for the block editor
    $editions = art_routes_get_editions();
    $editions_options = [
        [
            'value' => 0,
            'label' => __('Auto-detect from context', 'art-routes'),
        ],
    ];

    foreach ($editions as $edition) {
        $editions_options[] = [
            'value' => $edition->ID,
            'label' => $edition->post_title,
        ];
    }

    // Localize block data for the editor
    wp_localize_script('art-routes-edition-map-block', 'artRoutesBlockData', [
        'editions' => $editions_options,
        'i18n' => [
            'blockTitle' => __('Edition Map', 'art-routes'),
            'blockDescription' => __('Display an interactive map for an Edition with routes, locations, and info points.', 'art-routes'),
            'editionLabel' => __('Edition', 'art-routes'),
            'editionHelp' => __('Select an edition or use auto-detect to determine from page context.', 'art-routes'),
            'displayOptionsTitle' => __('Display Options', 'art-routes'),
            'showRoutesLabel' => __('Show Routes', 'art-routes'),
            'showLocationsLabel' => __('Show Locations', 'art-routes'),
            'showInfoPointsLabel' => __('Show Info Points', 'art-routes'),
            'showLegendLabel' => __('Show Legend', 'art-routes'),
            'mapSettingsTitle' => __('Map Settings', 'art-routes'),
            'heightLabel' => __('Map Height', 'art-routes'),
            'heightHelp' => __('Enter a CSS value (e.g., 500px, 50vh)', 'art-routes'),
            'previewNote' => __('Note: The map preview may differ from the frontend display.', 'art-routes'),
            'noEditionSelected' => __('Please select an Edition or the block will auto-detect from the page context.', 'art-routes'),
            // Editor preview strings
            'autoDetect' => __('Auto-detect from page', 'art-routes'),
            'unknownEdition' => __('Unknown Edition', 'art-routes'),
            'routes' => __('Routes', 'art-routes'),
            'locations' => __('Locations', 'art-routes'),
            'infoPoints' => __('Info Points', 'art-routes'),
            'legend' => __('Legend', 'art-routes'),
            'nothingSelected' => __('Nothing selected', 'art-routes'),
            'editorPreview' => __('Interactive map preview', 'art-routes'),
            'showingLabel' => __('Showing', 'art-routes'),
            'previewHint' => __('The interactive map will appear on the published page', 'art-routes'),
        ],
    ]);

    // Register the Edition Map block
    register_block_type('art-routes/edition-map', [
        'editor_script' => 'art-routes-edition-map-block',
        'editor_style' => 'art-routes-edition-map-block-editor',
        'render_callback' => 'art_routes_render_edition_map_block',
        'attributes' => [
            'editionId' => [
                'type' => 'number',
                'default' => 0,
            ],
            'showRoutes' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showLocations' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showInfoPoints' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'showLegend' => [
                'type' => 'boolean',
                'default' => true,
            ],
            'height' => [
                'type' => 'string',
                'default' => '500px',
            ],
        ],
    ]);
}
add_action('init', 'art_routes_register_blocks');

/**
 * Server-side render callback for the Edition Map block
 *
 * Converts block attributes to shortcode attributes and renders the map.
 *
 * @param array $attributes Block attributes from the editor
 * @return string HTML output for the map
 */
function art_routes_render_edition_map_block($attributes)
{
    // Determine routes parameter based on showRoutes attribute
    $routes_param = $attributes['showRoutes'] ? 'all' : 'none';

    // Build shortcode attributes
    $shortcode_atts = [
        'edition_id' => isset($attributes['editionId']) ? intval($attributes['editionId']) : 0,
        'routes' => $routes_param,
        'show_locations' => $attributes['showLocations'] ? 'true' : 'false',
        'show_info_points' => $attributes['showInfoPoints'] ? 'true' : 'false',
        'show_legend' => $attributes['showLegend'] ? 'true' : 'false',
        'height' => isset($attributes['height']) ? sanitize_text_field($attributes['height']) : '500px',
    ];

    // Use the existing shortcode function for rendering
    return art_routes_edition_map_shortcode($shortcode_atts);
}

/**
 * Add block category for WP Art Routes blocks
 *
 * @param array $categories Existing block categories
 * @return array Modified block categories
 */
function art_routes_block_categories($categories)
{
    return array_merge(
        $categories,
        [
            [
                'slug' => 'art-routes',
                'title' => __('Art Routes', 'art-routes'),
                'icon' => 'location-alt',
            ],
        ]
    );
}
add_filter('block_categories_all', 'art_routes_block_categories', 10, 1);
