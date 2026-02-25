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

    <!-- SEO & Open Graph -->
    <?php
    $og_title = is_front_page() ? get_bloginfo('name') : get_the_title();
    $og_url = is_front_page() ? home_url('/') : get_permalink();
    $og_type = is_single() ? 'article' : 'website';
    $og_image = get_template_directory_uri() . '/screenshot.png'; // Ultimate fallback
    $custom_social_image = get_theme_mod('dedeportes_social_image');

    if (has_post_thumbnail()) {
        $og_image = get_the_post_thumbnail_url(null, 'large');
    } elseif ($custom_social_image) {
        $og_image = $custom_social_image;
    }
    $og_description = get_bloginfo('description');
    if (is_single() && has_excerpt()) {
        $og_description = get_the_excerpt();
    } elseif (is_single()) {
        $og_description = wp_trim_words(get_the_content(), 20);
    }
    ?>
    <meta property="og:title" content="<?php echo esc_attr($og_title); ?>" />
    <meta property="og:url" content="<?php echo esc_attr($og_url); ?>" />
    <meta property="og:type" content="<?php echo esc_attr($og_type); ?>" />
    <meta property="og:image" content="<?php echo esc_attr($og_image); ?>" />
    <meta property="og:description" content="<?php echo esc_attr($og_description); ?>" />
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo esc_attr($og_title); ?>" />
    <meta name="twitter:description" content="<?php echo esc_attr($og_description); ?>" />
    <meta name="twitter:image" content="<?php echo esc_attr($og_image); ?>" />
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
            </div><!-- .container -->


        </header><!-- #masthead -->