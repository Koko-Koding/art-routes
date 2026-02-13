#!/usr/bin/env php
<?php
/**
 * Migration Script: Rename wp-art-routes to art-routes
 *
 * This script migrates the database entries from the old wp-art-routes naming
 * to the new art-routes naming. It is NOT shipped with the plugin — it's a
 * one-time migration tool for the 2 production sites.
 *
 * Usage: wp eval-file bin/migrate-rename.php
 *
 * Run this AFTER:
 * 1. Deploying the renamed plugin (art-routes/art-routes.php)
 * 2. Deactivating the old plugin (if it still shows in the list)
 *
 * This script is idempotent — safe to run multiple times.
 */

if ( ! defined( 'ABSPATH' ) ) {
    echo "Error: This script must be run via WP-CLI: wp eval-file bin/migrate-rename.php\n";
    exit( 1 );
}

global $wpdb;

echo "=== Art Routes Rename Migration ===\n\n";

// 1. Rename wp_options entries
echo "1. Migrating options...\n";
$options = [
    'version',
    'default_route_id',
    'enable_location_tracking',
    'terminology',
    'default_location_icon',
];

foreach ( $options as $opt ) {
    $old_key = "wp_art_routes_{$opt}";
    $new_key = "art_routes_{$opt}";
    $val     = get_option( $old_key );
    if ( $val !== false ) {
        update_option( $new_key, $val );
        delete_option( $old_key );
        echo "   Migrated: {$old_key} -> {$new_key}\n";
    } else {
        echo "   Skipped (not found): {$old_key}\n";
    }
}

// Also migrate option groups used by settings API
$option_groups = [
    'wp_art_routes_options'             => 'art_routes_options',
    'wp_art_routes_terminology_options' => 'art_routes_terminology_options',
];
foreach ( $option_groups as $old_key => $new_key ) {
    $val = get_option( $old_key );
    if ( $val !== false ) {
        update_option( $new_key, $val );
        delete_option( $old_key );
        echo "   Migrated: {$old_key} -> {$new_key}\n";
    }
}

// 2. Rename user meta
echo "\n2. Migrating user meta...\n";
$count = $wpdb->query(
    "UPDATE {$wpdb->usermeta}
     SET meta_key = 'art_routes_visited_artworks'
     WHERE meta_key = 'wp_art_routes_visited_artworks'"
);
echo "   Updated {$count} user meta rows\n";

// 3. Update block names in post content
echo "\n3. Updating block names in post content...\n";

$blocks = [
    'wp-art-routes/edition-map' => 'art-routes/edition-map',
    'wp-art-routes/routes-map'  => 'art-routes/routes-map',
];

foreach ( $blocks as $old_name => $new_name ) {
    $count = $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->posts}
             SET post_content = REPLACE(post_content, %s, %s)
             WHERE post_content LIKE %s",
            $old_name,
            $new_name,
            '%' . $wpdb->esc_like( $old_name ) . '%'
        )
    );
    echo "   Updated {$count} posts: {$old_name} -> {$new_name}\n";
}

// 4. Rename upload directory
echo "\n4. Renaming upload directory...\n";
$upload = wp_upload_dir();
$old_dir = $upload['basedir'] . '/wp-art-routes-icons/';
$new_dir = $upload['basedir'] . '/art-routes-icons/';

if ( is_dir( $old_dir ) && ! is_dir( $new_dir ) ) {
    if ( rename( $old_dir, $new_dir ) ) {
        echo "   Renamed: {$old_dir} -> {$new_dir}\n";
    } else {
        echo "   ERROR: Failed to rename {$old_dir}\n";
    }
} elseif ( is_dir( $new_dir ) ) {
    echo "   Skipped: {$new_dir} already exists\n";
} else {
    echo "   Skipped: {$old_dir} does not exist\n";
}

// 5. Update active_plugins
echo "\n5. Updating active plugins...\n";
$plugins = get_option( 'active_plugins', [] );
$old_plugin = 'wp-art-routes/wp-art-routes.php';
$new_plugin = 'art-routes/art-routes.php';
$key = array_search( $old_plugin, $plugins, true );

if ( $key !== false ) {
    $plugins[ $key ] = $new_plugin;
    update_option( 'active_plugins', $plugins );
    echo "   Updated: {$old_plugin} -> {$new_plugin}\n";
} elseif ( in_array( $new_plugin, $plugins, true ) ) {
    echo "   Skipped: {$new_plugin} already active\n";
} else {
    echo "   WARNING: Neither old nor new plugin found in active_plugins\n";
    echo "   You may need to activate the plugin manually.\n";
}

// 6. Flush rewrite rules
echo "\n6. Flushing rewrite rules...\n";
flush_rewrite_rules();
echo "   Done\n";

echo "\n=== Migration Complete ===\n";
echo "Please verify the site is working correctly.\n";
