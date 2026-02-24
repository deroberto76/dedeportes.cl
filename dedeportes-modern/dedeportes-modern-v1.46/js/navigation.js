/**
 * Navigation.js
 * Handles the menu toggling for the overlay menu.
 */
document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.getElementById('menu-toggle');
    const mainNavigation = document.getElementById('site-navigation');
    const body = document.body;

    console.log('Menu script loaded. Toggle:', menuToggle, 'Nav:', mainNavigation);

    if (!menuToggle || !mainNavigation) {
        console.error('Menu elements not found');
        return;
    }

    menuToggle.addEventListener('click', function () {
        // Toggle ARIA
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', !isExpanded);

        // Toggle Classes
        mainNavigation.classList.toggle('is-open');
        menuToggle.classList.toggle('is-active');
        body.classList.toggle('menu-open'); // To prevent scrolling if needed
    });

    // Close menu when clicking outside (on the backdrop)
    // or when pressing Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mainNavigation.classList.contains('is-open')) {
            menuToggle.click();
        }
    });
});
