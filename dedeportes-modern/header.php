<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#primary">
            <?php esc_html_e('Skip to content', 'dedeportes-modern'); ?>
        </a>

        <header id="masthead" class="site-header">
            <div class="container">
                <div class="site-branding">
                    <h1 class="site-logo">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">De<span>Deportes</span></a>
                    </h1>
                </div><!-- .site-branding -->

                <nav id="site-navigation" class="main-navigation">
                    <?php
                    wp_nav_menu(
                        array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container' => false,
                            'fallback_cb' => false,
                        )
                    );
                    ?>
                </nav><!-- #site-navigation -->

                <div class="header-actions">
                    <!-- Placeholder for search or CTA -->
                    <a href="#search" class="btn-search" aria-label="Search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </header><!-- #masthead -->