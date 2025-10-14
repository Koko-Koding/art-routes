<?php

/**
 * AJAX Handlers for the Art Routes Plugin
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX handler for marking an artwork as visited
 */
function wp_art_routes_ajax_mark_artwork_visited()
{
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wp_art_routes_nonce')) {
        wp_send_json_error('Invalid nonce');
        die();
    }

    // Check user is logged in (optional)
    if (!is_user_logged_in()) {
        // For anonymous users, you might store in a cookie or session
        // This depends on your requirements
        wp_send_json_success([
            'status' => 'anonymous',
            'message' => 'Stored in session',
        ]);
        die();
    }

    // Get data
    $artwork_id = isset($_POST['artwork_id']) ? intval($_POST['artwork_id']) : 0;
    $user_id = get_current_user_id();

    if ($artwork_id <= 0) {
        wp_send_json_error('Invalid artwork ID');
        die();
    }

    // Get user's visited artworks
    $visited = get_user_meta($user_id, 'wp_art_routes_visited_artworks', true);

    if (!is_array($visited)) {
        $visited = [];
    }

    // Add current artwork if not already visited
    if (!in_array($artwork_id, $visited)) {
        $visited[] = $artwork_id;
        update_user_meta($user_id, 'wp_art_routes_visited_artworks', $visited);
    }

    wp_send_json_success([
        'status' => 'success',
        'message' => 'Artwork marked as visited',
    ]);
    die();
}
add_action('wp_ajax_wp_art_routes_mark_artwork_visited', 'wp_art_routes_ajax_mark_artwork_visited');
add_action('wp_ajax_nopriv_wp_art_routes_mark_artwork_visited', 'wp_art_routes_ajax_mark_artwork_visited');

/**
 * AJAX handler for searching posts for artist association
 */
function wp_art_routes_search_posts_for_artist()
{
    // Verify nonce
    if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'artist_search_nonce')) {
        wp_send_json_error('Invalid nonce');
        die();
    }

    $term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : '';

    if (empty($term)) {
        wp_send_json([]);
        die();
    }

    // Prepare query arguments
    $args = array(
        'post_status' => 'publish',
        'posts_per_page' => 10,
        's' => $term,
    );

    // If specific post type is requested
    if (!empty($post_type)) {
        $args['post_type'] = $post_type;
    } else {
        // Get all public post types except excluded ones
        $excluded_post_types = array(
            'revision',
            'attachment',
            'nav_menu_item',
            'custom_css',
            'customize_changeset',
            'oembed_cache',
            'user_request',
            'wp_block',
            'art_route',
            'artwork'
        );
        $post_types = get_post_types(array('public' => true), 'names');
        $filtered_post_types = array_diff($post_types, $excluded_post_types);

        $args['post_type'] = $filtered_post_types;
    }

    $search_query = new WP_Query($args);
    $results = array();

    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            $post_id = get_the_ID();
            $post_type_obj = get_post_type_object(get_post_type());
            $post_type_label = $post_type_obj ? $post_type_obj->labels->singular_name : get_post_type();

            $results[] = array(
                'id' => $post_id,
                'label' => get_the_title(),
                'post_type' => get_post_type(),
                'post_type_label' => $post_type_label
            );
        }
    }

    wp_reset_postdata();
    wp_send_json($results);
    die();
}
add_action('wp_ajax_search_posts_for_artist', 'wp_art_routes_search_posts_for_artist');

/**
 * AJAX handler to get artworks and info points for a specific route
 */
function wp_ajax_get_route_points()
{
    check_ajax_referer('get_route_points_nonce', 'nonce');

    if (!isset($_POST['route_id']) || !current_user_can('edit_post', intval($_POST['route_id']))) {
        wp_send_json_error(['message' => __('Invalid request or permissions.', 'wp-art-routes')]);
    }

    $route_id = intval($_POST['route_id']);
    $points = wp_art_routes_get_associated_points($route_id);

    wp_send_json_success($points);
}
add_action('wp_ajax_get_route_points', 'wp_ajax_get_route_points');

/**
 * AJAX handler to save route path and associated points (artworks/info points)
 */
function wp_ajax_save_route_points()
{
    check_ajax_referer('save_route_points_nonce', 'nonce');

    if (!isset($_POST['route_id']) || !current_user_can('edit_post', intval($_POST['route_id']))) {
        wp_send_json_error(['message' => __('Invalid request or permissions.', 'wp-art-routes')]);
    }

    $route_id = intval($_POST['route_id']);
    $results = [];

    // 1. Save Route Path
    if (isset($_POST['route_path'])) {
        $sanitized_path = sanitize_textarea_field($_POST['route_path']);
        update_post_meta($route_id, '_route_path', $sanitized_path);
        $results['path_saved'] = true;
    }

    // 2. Save Route Length (calculated client-side, passed here)
    if (isset($_POST['route_length'])) {
        update_post_meta($route_id, '_route_length', sanitize_text_field($_POST['route_length']));
        $results['length_saved'] = true;
    }

    // 3. Handle Point Updates (Moved Points)
    if (isset($_POST['updated_points']) && is_array($_POST['updated_points'])) {
        $results['updated'] = [];
        foreach ($_POST['updated_points'] as $point) {
            $point_id = isset($point['id']) ? intval($point['id']) : 0;
            $lat = isset($point['lat']) ? sanitize_text_field($point['lat']) : null;
            $lng = isset($point['lng']) ? sanitize_text_field($point['lng']) : null;
            $icon_url = isset($point['icon_url']) ? esc_url_raw($point['icon_url']) : null;

            if ($point_id > 0 && $lat !== null && $lng !== null && current_user_can('edit_post', $point_id)) {
                update_post_meta($point_id, '_artwork_latitude', $lat);
                update_post_meta($point_id, '_artwork_longitude', $lng);
                // Save icon_url for info points
                $post_type = get_post_type($point_id);
                if ($post_type === 'information_point') {
                    if ($icon_url) {
                        // For backward compatibility, still save icon_url if provided
                        update_post_meta($point_id, '_info_point_icon_url', $icon_url);
                    } else {
                        delete_post_meta($point_id, '_info_point_icon_url');
                    }
                }
                $results['updated'][] = $point_id;
            }
        }
    }

    // 4. Handle Point Removals (Delete from system - no longer just disassociate)
    if (isset($_POST['removed_points']) && is_array($_POST['removed_points'])) {
        $results['removed'] = [];
        foreach ($_POST['removed_points'] as $point_id_raw) {
            $point_id = intval($point_id_raw);
            if ($point_id > 0 && current_user_can('edit_post', $point_id)) {
                // Since points are now global, we just note them as removed from the editor
                // The actual posts remain in the system
                $results['removed'][] = $point_id;
            }
        }
    }

    // 5. Handle New Points (Create Draft Posts)
    if (isset($_POST['new_points']) && is_array($_POST['new_points'])) {
        $results['added'] = [];
        foreach ($_POST['new_points'] as $point) {
            $type = isset($point['type']) ? sanitize_text_field($point['type']) : null;
            $lat = isset($point['lat']) ? sanitize_text_field($point['lat']) : null;
            $lng = isset($point['lng']) ? sanitize_text_field($point['lng']) : null;
            $icon_url = isset($point['icon_url']) ? esc_url_raw($point['icon_url']) : null;

            if (($type === 'artwork' || $type === 'information_point') && $lat !== null && $lng !== null) {
                $post_type = ($type === 'artwork') ? 'artwork' : 'information_point';
                $post_title = ($type === 'artwork') ? sprintf(__('New Artwork near %s, %s', 'wp-art-routes'), $lat, $lng) : sprintf(__('New Info Point near %s, %s', 'wp-art-routes'), $lat, $lng);

                $new_post_id = wp_insert_post([
                    'post_title' => $post_title,
                    'post_type' => $post_type,
                    'post_status' => 'draft', // Create as draft initially
                    'post_author' => get_current_user_id(),
                ]);

                if ($new_post_id && !is_wp_error($new_post_id)) {
                    // Save location
                    update_post_meta($new_post_id, '_artwork_latitude', $lat);
                    update_post_meta($new_post_id, '_artwork_longitude', $lng);
                    // Save icon_url for info points
                    if ($type === 'information_point' && $icon_url) {
                        update_post_meta($new_post_id, '_info_point_icon_url', $icon_url);
                    }
                    // No longer associate any points with routes - both artworks and info points are global
                    $results['added'][] = [
                        'temp_id' => isset($point['temp_id']) ? $point['temp_id'] : null,
                        'new_id' => $new_post_id,
                        'type' => $type,
                        'edit_link' => get_edit_post_link($new_post_id, 'raw')
                    ];
                } else {
                    // Log error if needed
                }
            }
        }
    }

    wp_send_json_success($results);
}
add_action('wp_ajax_save_route_points', 'wp_ajax_save_route_points');

/**
 * Helper function to get associated artworks and info points for a route
 * Fetches both published and draft points.
 *
 * @param int $route_id The ID of the route post.
 * @return array An array containing 'artworks' and 'information_points'.
 */
function wp_art_routes_get_associated_points($route_id)
{
    $points = [
        'artworks' => [],
        'information_points' => [],
    ];

    // Get all artworks (no longer tied to specific routes)
    $artwork_query_args = [
        'post_type' => 'artwork',
        'posts_per_page' => -1,
        'post_status' => ['publish', 'draft'],
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    $artwork_posts = get_posts($artwork_query_args);
    foreach ($artwork_posts as $artwork_post) {
        $latitude = get_post_meta($artwork_post->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($artwork_post->ID, '_artwork_longitude', true);

        if (is_numeric($latitude) && is_numeric($longitude)) {
            // Get icon information - prefer icon field, then fall back to no icon
            $icon_filename = get_post_meta($artwork_post->ID, '_artwork_icon', true);
            $icon_url = '';

            if (!empty($icon_filename)) {
                // Build URL from filename
                $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
                $icon_url = $icons_url . $icon_filename;
            }

            $point_data = [
                'id' => $artwork_post->ID,
                'title' => $artwork_post->post_title,
                'lat' => floatval($latitude),
                'lng' => floatval($longitude),
                'edit_link' => get_edit_post_link($artwork_post->ID, 'raw'),
                'type' => 'artwork',
                'status' => $artwork_post->post_status,
                'icon_url' => $icon_url ? esc_url($icon_url) : '',
            ];

            $points['artworks'][] = $point_data;
        }
    }

    // Get all information points (no longer tied to specific routes)
    $info_point_query_args = [
        'post_type' => 'information_point',
        'posts_per_page' => -1,
        'post_status' => ['publish', 'draft'],
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    $info_point_posts = get_posts($info_point_query_args);
    foreach ($info_point_posts as $info_post) {
        $latitude = get_post_meta($info_post->ID, '_artwork_latitude', true);
        $longitude = get_post_meta($info_post->ID, '_artwork_longitude', true);

        // Get icon information - prefer new icon field, fallback to old icon_url, then default
        $icon_filename = get_post_meta($info_post->ID, '_info_point_icon', true);
        $icon_url = '';

        if (!empty($icon_filename)) {
            // Build URL from filename
            $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
            $icon_url = $icons_url . $icon_filename;
        } else {
            // Fallback to old icon_url field for backward compatibility
            $icon_url = get_post_meta($info_post->ID, '_info_point_icon_url', true);

            // If still no icon, use default
            if (empty($icon_url)) {
                $icons_url = plugin_dir_url(__FILE__) . '../assets/icons/';
                $icon_url = $icons_url . 'WB plattegrond-Informatie.svg';
            }
        }

        if (is_numeric($latitude) && is_numeric($longitude)) {
            $point_data = [
                'id' => $info_post->ID,
                'title' => $info_post->post_title,
                'lat' => floatval($latitude),
                'lng' => floatval($longitude),
                'edit_link' => get_edit_post_link($info_post->ID, 'raw'),
                'type' => 'information_point',
                'status' => $info_post->post_status,
                'icon_url' => $icon_url ? esc_url($icon_url) : '',
            ];

            $points['information_points'][] = $point_data;
        }
    }

    return $points;
}

/**
 * AJAX handler for exporting route to GPX format
 */
function wp_art_routes_ajax_export_gpx()
{
    // Verify nonce - for GET requests, the nonce is in the URL parameter
    $nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
    if (!wp_verify_nonce($nonce, 'wp_art_routes_export_gpx')) {
        wp_die(__('Security check failed', 'wp-art-routes'));
    }

    $route_id = isset($_GET['route_id']) ? intval($_GET['route_id']) : 0;

    if ($route_id <= 0) {
        wp_die(__('Invalid route ID', 'wp-art-routes'));
    }

    // Get route data
    $route_data = wp_art_routes_get_route_data($route_id);

    if (!$route_data) {
        wp_die(__('Route not found', 'wp-art-routes'));
    }

    // Generate GPX content
    $gpx_content = wp_art_routes_generate_gpx($route_data);

    // Set headers for file download
    $filename = sanitize_file_name($route_data['title']) . '.gpx';
    header('Content-Type: application/gpx+xml; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($gpx_content));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');

    // Output GPX content and exit
    echo $gpx_content;
    exit;
}
add_action('wp_ajax_wp_art_routes_export_gpx', 'wp_art_routes_ajax_export_gpx');
add_action('wp_ajax_nopriv_wp_art_routes_export_gpx', 'wp_art_routes_ajax_export_gpx');

/**
 * Sanitize content for GPX XML format
 * 
 * Removes WordPress shortcodes, HTML entities, and ensures valid XML
 *
 * @param string $content Content to sanitize
 * @return string Sanitized content safe for GPX XML
 */
function wp_art_routes_sanitize_for_gpx($content) {
    if (empty($content)) {
        return '';
    }
    
    // Remove WordPress shortcodes (video, audio, gallery, etc.)
    $content = strip_shortcodes($content);
    
    // Strip HTML tags
    $content = strip_tags($content);
    
    // Decode HTML entities first to get the actual characters
    $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    // Replace problematic characters with XML-safe alternatives
    $replacements = [
        '&nbsp;' => ' ',      // Non-breaking space
        '&mdash;' => '—',     // Em dash
        '&ndash;' => '–',     // En dash
        '&hellip;' => '…',    // Ellipsis
        '&rsquo;' => "'",     // Right single quotation mark
        '&lsquo;' => "'",     // Left single quotation mark
        '&rdquo;' => '"',     // Right double quotation mark
        '&ldquo;' => '"',     // Left double quotation mark
        '&amp;' => '&',       // Ampersand
    ];
    
    foreach ($replacements as $entity => $replacement) {
        $content = str_replace($entity, $replacement, $content);
    }
    
    // Remove any remaining HTML entities that weren't decoded
    $content = preg_replace('/&[a-zA-Z0-9#]+;/', '', $content);
    
    // Trim whitespace and normalize line breaks
    $content = trim($content);
    $content = preg_replace('/\s+/', ' ', $content);
    
    // Finally, escape special XML characters properly
    // This converts &, <, >, ", ' to their XML entity equivalents
    $content = htmlspecialchars($content, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    
    return $content;
}

/**
 * Generate GPX XML content for a route
 *
 * @param array $route_data Route data from wp_art_routes_get_route_data()
 * @return string GPX XML content
 */
function wp_art_routes_generate_gpx($route_data)
{
    $route_title = wp_art_routes_sanitize_for_gpx($route_data['title']);
    $route_description = wp_art_routes_sanitize_for_gpx($route_data['description']);
    $creation_time = gmdate('Y-m-d\TH:i:s\Z');

    // Start GPX XML
    $gpx = '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>' . "\n";
    $gpx .= '<gpx xmlns="http://www.topografix.com/GPX/1/1" version="1.1" creator="WP Art Routes Plugin"' . "\n";
    $gpx .= '    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
    $gpx .= '    xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">' . "\n";
    $gpx .= '  <metadata>' . "\n";
    $gpx .= '    <name>' . $route_title . '</name>' . "\n";
    $gpx .= '    <desc>' . $route_description . '</desc>' . "\n";
    $gpx .= '    <time>' . $creation_time . '</time>' . "\n";
    $gpx .= '  </metadata>' . "\n";

    // Add artworks as waypoints FIRST (per GPX 1.1 spec: metadata, waypoints, routes, tracks, extensions)
    if (!empty($route_data['artworks'])) {
        foreach ($route_data['artworks'] as $index => $artwork) {
            $artwork_name = wp_art_routes_sanitize_for_gpx($artwork['title']);
            $artwork_desc = wp_art_routes_sanitize_for_gpx($artwork['description'] ?? $artwork['excerpt'] ?? '');
            $artwork_number = !empty($artwork['number']) ? wp_art_routes_sanitize_for_gpx($artwork['number']) : ($index + 1);

            // Only add waypoint if coordinates are valid
            if (
                isset($artwork['latitude']) && isset($artwork['longitude']) &&
                is_numeric($artwork['latitude']) && is_numeric($artwork['longitude'])
            ) {
                $gpx .= '  <wpt lat="' . esc_attr($artwork['latitude']) . '" lon="' . esc_attr($artwork['longitude']) . '">' . "\n";
                $gpx .= '    <name>Artwork ' . $artwork_number . ': ' . $artwork_name . '</name>' . "\n";
                if ($artwork_desc) {
                    $gpx .= '    <desc>' . $artwork_desc . '</desc>' . "\n";
                }
                $gpx .= '    <type>Artwork</type>' . "\n";
                $gpx .= '  </wpt>' . "\n";
            }
        }
    }

    // Add information points as waypoints
    if (!empty($route_data['information_points'])) {
        foreach ($route_data['information_points'] as $index => $info_point) {
            $info_name = wp_art_routes_sanitize_for_gpx($info_point['title']);
            $info_desc = wp_art_routes_sanitize_for_gpx($info_point['description'] ?? $info_point['excerpt'] ?? '');

            // Only add waypoint if coordinates are valid
            if (
                isset($info_point['latitude']) && isset($info_point['longitude']) &&
                is_numeric($info_point['latitude']) && is_numeric($info_point['longitude'])
            ) {
                $gpx .= '  <wpt lat="' . esc_attr($info_point['latitude']) . '" lon="' . esc_attr($info_point['longitude']) . '">' . "\n";
                $gpx .= '    <name>Info: ' . $info_name . '</name>' . "\n";
                if ($info_desc) {
                    $gpx .= '    <desc>' . $info_desc . '</desc>' . "\n";
                }
                $gpx .= '    <type>Information Point</type>' . "\n";
                $gpx .= '  </wpt>' . "\n";
            }
        }
    }

    // Add route path as a track
    if (!empty($route_data['route_path'])) {
        $gpx .= '  <trk>' . "\n";
        $gpx .= '    <name>' . $route_title . ' - Route Path</name>' . "\n";
        $gpx .= '    <desc>Main route path</desc>' . "\n";
        $gpx .= '    <trkseg>' . "\n";

        foreach ($route_data['route_path'] as $point) {
            // Handle both old array format [lat, lng] and new object format {lat: x, lng: y}
            if (is_array($point) && isset($point[0]) && isset($point[1])) {
                // Old format: [lat, lng]
                $lat = $point[0];
                $lng = $point[1];
            } elseif (is_object($point) || is_array($point)) {
                // New format: {lat: x, lng: y} or associative array
                $point = (array) $point; // Convert object to array if needed
                $lat = isset($point['lat']) ? $point['lat'] : '';
                $lng = isset($point['lng']) ? $point['lng'] : '';
            } else {
                // Skip invalid points
                continue;
            }

            // Only add valid coordinates
            if (is_numeric($lat) && is_numeric($lng) && $lat != '' && $lng != '') {
                $gpx .= '      <trkpt lat="' . esc_attr($lat) . '" lon="' . esc_attr($lng) . '">' . "\n";
                $gpx .= '      </trkpt>' . "\n";
            }
        }

        $gpx .= '    </trkseg>' . "\n";
        $gpx .= '  </trk>' . "\n";
    }

    $gpx .= '</gpx>' . "\n";

    return $gpx;
}
