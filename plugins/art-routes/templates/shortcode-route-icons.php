<?php

/**
 * Template: Shortcode Route Icons
 * Displays all route icons as horizontal links
 *
 * @var array $routes Array of route data
 */
if (!defined('ABSPATH')) exit;

if (empty($routes)) {
    echo '<div class="art-route-icons-empty">' . esc_html__('No routes found.', 'art-routes') . '</div>';
    return;
}

// Get icons base URL
$icons_url = plugin_dir_url(dirname(__FILE__)) . 'assets/icons/';
?>
<div class="art-route-icons-list">
    <?php foreach ($routes as $route):
        $icon_file = get_post_meta($route['id'], '_route_icon', true);
        $icon_url = $icon_file ? $icons_url . $icon_file : '';
        $route_url = get_permalink($route['id']);
        $route_title = $route['title'];
    ?>
        <a class="art-route-icon-link" href="<?php echo esc_url($route_url); ?>" title="<?php echo esc_attr($route_title); ?>">
            <?php if ($icon_url): ?>
                <img class="art-route-icon-img" src="<?php echo esc_url($icon_url); ?>" alt="<?php echo esc_attr($route_title); ?>" loading="lazy" />
            <?php else: ?>
                <span class="art-route-icon-fallback" aria-label="<?php echo esc_attr($route_title); ?>">🎨</span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
<?php
wp_enqueue_style('art-routes-route-icons-css');
wp_enqueue_script('art-routes-route-icons-js');
?>
