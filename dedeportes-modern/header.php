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
                    <button id="search-toggle" class="btn-search" aria-label="Buscar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Search Overlay -->
            <div id="search-overlay" class="search-overlay">
                <div class="search-container">
                    <form role="search" method="get" class="search-form"
                        action="<?php echo esc_url(home_url('/')); ?>">
                        <label>
                            <span
                                class="screen-reader-text"><?php echo _x('Search for:', 'label', 'dedeportes-modern'); ?></span>
                            <input type="search" class="search-field"
                                placeholder="<?php echo esc_attr_x('Buscar noticias...', 'placeholder', 'dedeportes-modern'); ?>"
                                value="<?php echo get_search_query(); ?>" name="s" />
                        </label>
                        <button type="submit" class="search-submit" aria-label="Buscar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </form>
                    <button id="search-close" class="search-close" aria-label="Cerrar">&times;</button>
                </div>
            </div>
        </header><!-- #masthead -->