<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    <?php if (!has_site_icon() && file_exists(get_template_directory() . '/favicon.png')): ?>
        <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/favicon.png'); ?>" sizes="32x32" />
        <link rel="icon" href="<?php echo esc_url(get_template_directory_uri() . '/favicon.png'); ?>" sizes="192x192" />
        <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri() . '/favicon.png'); ?>" />
    <?php endif; ?>
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

                <button id="menu-toggle" class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="screen-reader-text"><?php esc_html_e('Menu', 'dedeportes-modern'); ?></span>
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>

                <nav id="site-navigation" class="main-navigation">
                    <div class="nav-overlay-content">
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
                    </div>
                </nav><!-- #site-navigation -->


        </header><!-- #masthead -->