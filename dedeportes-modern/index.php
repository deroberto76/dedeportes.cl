<?php
/**
 * The main template file - Redesign v1.50
 *
 * @package Dedeportes_Modern
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container u-pt-2">

        <div class="layout-grid v-1-50">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php
                // 1. FEATURED POST (Latest post from any category)
                $featured_query = new WP_Query(array(
                    'posts_per_page' => 1,
                    'ignore_sticky_posts' => 1,
                    'meta_query' => array(
                        array(
                            'key' => '_dedeportes_hide_home',
                            'compare' => 'NOT EXISTS',
                        ),
                    ),
                ));

                if ($featured_query->have_posts()):
                    while ($featured_query->have_posts()):
                        $featured_query->the_post(); ?>
                        <article class="featured-story u-mb-4">
                            <div class="post-meta-top">
                                <span class="cat-label"><?php echo get_the_category()[0]->name; ?></span>
                                <span
                                    class="post-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('hace', 'dedeportes-modern'); ?></span>
                            </div>
                            <h1 class="featured-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h1>
                            <div class="featured-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 40); ?>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata();
                endif; ?>

                <hr class="section-divider">

                <?php
                // HELPER: Function to render a category section
                function dedeportes_render_cat_section($title, $slug, $color_class)
                {
                    $cat_query = new WP_Query(array(
                        'category_name' => $slug,
                        'posts_per_page' => 4,
                        'offset' => 0, // In case we want to avoid duplicates if it was featured
                    ));

                    if ($cat_query->have_posts()): ?>
                        <section class="cat-section u-mb-4 <?php echo $color_class; ?>">
                            <h2 class="cat-section-title"><?php echo esc_html($title); ?></h2>
                            <div class="cat-posts">
                                <?php while ($cat_query->have_posts()):
                                    $cat_query->the_post(); ?>
                                    <div class="cat-post-item">
                                        <span
                                            class="post-time-small"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ' . __('hace', 'dedeportes-modern'); ?></span>
                                        <h3 class="cat-post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h3>
                                        <div class="cat-post-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php endif;
                    wp_reset_postdata();
                }

                // 2. CATEGORY SECTIONS
                dedeportes_render_cat_section('FÚTBOL NACIONAL', 'futbol', 'cat-bar-futbol');
                dedeportes_render_cat_section('TENIS', 'tenis', 'cat-bar-tenis');
                dedeportes_render_cat_section('TORNEOS INTERNACIONALES', 'torneos-internacionales', 'cat-bar-internacional');
                dedeportes_render_cat_section('FÚTBOL FEMENINO', 'futbol-femenino', 'cat-bar-femenino');
                dedeportes_render_cat_section('SELECCIONES', 'selecciones', 'cat-bar-selecciones');
                ?>

            </div>

            <!-- SIDEBAR COLUMN -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-home')): ?>
                    <?php dynamic_sidebar('sidebar-home'); ?>
                <?php endif; ?>
            </aside>

        </div> <!-- .layout-grid -->
    </div>
</main>

<?php
get_footer();
