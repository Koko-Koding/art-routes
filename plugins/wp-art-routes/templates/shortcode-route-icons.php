<?php

/**
 * Template: Shortcode Route Icons
 * Displays all route icons as horizontal links
 *
 * @var array $routes Array of route data
 */
if (!defined('ABSPATH')) exit;

if (empty($routes)) {
    echo '<div class="art-route-icons-empty">' . esc_html__('No routes found.', 'wp-art-routes') . '</div>';
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
<style>
    .art-route-icons-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 24px;
        justify-items: center;
        align-items: center;
        margin: 24px auto;
        max-width: 900px;
        1 place-items: center;
    }

    .art-route-icon-link {
        display: block;
        width: 100%;
        max-width: 360px;
        aspect-ratio: 1 / 1;
        text-align: center;
        margin: 0 auto;
        background: none;
        border: none;
        border-radius: 0;
        box-shadow: none;
        padding: 0;
        transition: transform 0.18s cubic-bezier(.4, 1.3, .6, 1);
    }

    .art-route-icon-img {
        width: 100%;
        height: auto;
        max-width: 360px;
        max-height: 360px;
        object-fit: contain;
        display: block;
        margin: 0 auto;
        background: none;
        border: none;
        border-radius: 0;
        box-shadow: none;
    }

    .art-route-icon-fallback {
        font-size: 3em;
        color: #bbb;
        display: block;
        width: 100%;
        height: 360px;
        line-height: 360px;
        text-align: center;
        background: none;
        border: none;
        border-radius: 0;
        box-shadow: none;
    }

    .art-route-icons-list {
        justify-content: center;
    }

    /* Pop out the center tile on larger screens */
    @media (min-width: 768px) {
        .art-route-icons-list .art-route-icon-link.center-tile {
            transform: scale(1.10);
            z-index: 2;
        }
    }

    .art-route-icon-link:hover,
    .art-route-icon-link:focus {
        transform: scale(1.08);
        z-index: 2;
        outline: none;
    }

    @media (max-width: 767px) {
        .art-route-icons-list {
            grid-template-columns: 1fr;
            max-width: 360px;
            gap: 24px;
        }

        .art-route-icon-link {
            max-width: 480px;
        }

        .art-route-icon-img,
        .art-route-icon-fallback {
            max-width: 480px;
            max-height: 480px;
            height: auto;
            line-height: 480px;
        }
    }
</style>
<script>
    // Add center-tile class to the center icon
    (function() {
        var grid = document.querySelector('.art-route-icons-list');
        if (!grid) return;
        var items = grid.querySelectorAll('.art-route-icon-link');
        if (items.length === 0) return;
        var centerIdx = Math.floor(items.length / 2);
        items.forEach(function(el, i) {
            el.classList.remove('center-tile', 'center-tile-mobile-first');
        });
        if (items[centerIdx]) {
            items[centerIdx].classList.add('center-tile', 'center-tile-mobile-first');
            // For mobile, set order to -1 so it appears first
            items[centerIdx].style.order = '';
        }
        // Reset order for all
        items.forEach(function(el) {
            el.style.order = '';
        });
        // On mobile, move center tile visually to first
        function updateOrder() {
            if (window.innerWidth <= 600 && items[centerIdx]) {
                items[centerIdx].style.order = '-1';
            } else if (items[centerIdx]) {
                items[centerIdx].style.order = '';
            }
        }
        updateOrder();
        window.addEventListener('resize', updateOrder);
    })();
</script>