/**
 * Route Icons
 *
 * Adds center-tile class to the center icon in the route icons grid.
 * On mobile, reorders to show center tile first.
 *
 * @package Art_Routes
 */

(function() {
    var grid = document.querySelector('.art-route-icons-list');
    if (!grid) return;
    var items = grid.querySelectorAll('.art-route-icon-link');
    if (items.length === 0) return;
    var centerIdx = Math.floor(items.length / 2);
    items.forEach(function(el) {
        el.classList.remove('center-tile', 'center-tile-mobile-first');
    });
    if (items[centerIdx]) {
        items[centerIdx].classList.add('center-tile', 'center-tile-mobile-first');
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
