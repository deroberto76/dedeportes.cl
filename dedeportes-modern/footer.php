<footer id="colophon" class="site-footer">
    <div class="container">
        <div class="footer-widgets">
            <!-- In a real theme we would register widget areas here -->
            <div class="footer-branding">
                <h2 class="site-logo"><a href="<?php echo esc_url(home_url('/')); ?>">De<span>Deportes</span></a>
                </h2>
                <p>Tu fuente diaria de noticias deportivas. Cobertura premium de fútbol, tenis, y más.</p>
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
            printf(esc_html__('Theme: %1$s by %2$s.', 'dedeportes-modern'), 'Dedeportes Modern', '<a href="#">Antigravity</a>');
            ?>
        </div><!-- .site-info -->
    </div>
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>