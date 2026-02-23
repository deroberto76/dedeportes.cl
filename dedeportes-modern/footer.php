<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-widgets">
            <!-- In a real theme we would register widget areas here -->
            <div class="footer-branding u-mb-2">
                <h2 class="site-logo"><a href="<?php echo esc_url(home_url('/')); ?>">dedeportes.cl</a></h2>
                <p>fútbol y tenis chileno, todos los días</p>
            </div>
        </div>

        <div class="site-info">
            <a href="<?php echo esc_url(__('https://wordpress.org/', 'dedeportes-modern')); ?>">
                <?php
                /* translators: %s: CMS Name. */
                printf(esc_html__('Proudly powered by %s', 'dedeportes-modern'), 'WordPress');
                ?>
            </a>
            <span class="sep"> | </span>
            <?php
            /* translators: 1: Theme name, 2: Theme author. */
            printf(esc_html__('Theme: %1$s v1.50.', 'dedeportes-modern'), 'Dedeportes Modern');
            ?>
        </div><!-- .site-info -->
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->



<?php wp_footer(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Inline Menu Script Running');
        const menuToggle = document.getElementById('menu-toggle');
        const mainNavigation = document.getElementById('site-navigation');
        const body = document.body;

        if (!menuToggle || !mainNavigation) {
            console.error('Menu elements missing:', { menuToggle, mainNavigation });
            return;
        }

        menuToggle.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent any default button behavior
            console.log('Menu Toggle Clicked');

            // Toggle ARIA
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);

            // Toggle Classes
            mainNavigation.classList.toggle('is-open');
            menuToggle.classList.toggle('is-active');
            body.classList.toggle('menu-open');
        });

        // Close on Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && mainNavigation.classList.contains('is-open')) {
                menuToggle.click();
            }
        });
    });
</script>

</body>

</html>