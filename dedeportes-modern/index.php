<?php
/**
 * The main template file
 *
 * @package Dedeportes_Modern
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">
                <?php
                // 1. Noticia Principal (La más reciente)
                $main_query = new WP_Query(array(
                    'posts_per_page' => 1,
                    'ignore_sticky_posts' => 1
                ));

                if ($main_query->have_posts()):
                    while ($main_query->have_posts()):
                        $main_query->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item main-featured-post'); ?>>
                            <div class="post-content">
                                <span class="badge mb-2"><?php the_category(', '); ?></span>
                                <h2 class="post-title" style="font-size: 2rem;">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 35); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;

                // 2. Secciones por Categoría
                $categories_to_show = array(
                    'Fútbol nacional' => 'futbol-nacional',
                    'Tenis' => 'tenis',
                    'Fútbol internacional' => 'futbol-internacional',
                    'Fútbol femenino' => 'futbol-femenino',
                    'Selecciones' => 'selecciones'
                );

                foreach ($categories_to_show as $title => $slug):
                    $cat_query = new WP_Query(array(
                        'category_name' => $slug,
                        'posts_per_page' => 4,
                        'post__not_in' => array(get_the_ID()) // Evitar repetir la principal si cae en la misma categoría
                    ));

                    if ($cat_query->have_posts()): ?>
                        <section class="category-section">
                            <h2 class="section-category-title"><?php echo esc_html($title); ?></h2>
                            <div class="posts-list">
                                <?php while ($cat_query->have_posts()):
                                    $cat_query->the_post(); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item'); ?>>
                                        <div class="post-content">
                                            <h3 class="post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <div class="post-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php
                    endif;
                    wp_reset_postdata();
                endforeach;
                ?>
            </div>

            <!-- SIDEBAR COLUMN -->
            <aside class="layout-sidebar">

                <?php if (is_active_sidebar('sidebar-home')): ?>
                    <?php dynamic_sidebar('sidebar-home'); ?>
                <?php endif; ?>

                <!-- Widget: Tenis -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Tenis</h3>
                    <div class="widget-content">
                        <?php
                        $query_tenis = new WP_Query(array('category_name' => 'tenis', 'posts_per_page' => 4));
                        if ($query_tenis->have_posts()):
                            while ($query_tenis->have_posts()):
                                $query_tenis->the_post();
                                ?>
                                <div class="mini-post">
                                    <a href="<?php the_permalink(); ?>" class="mini-post-link">
                                        <span class="mini-post-title"><?php the_title(); ?></span>
                                        <span class="mini-post-date"><?php echo get_the_date('d M'); ?></span>
                                    </a>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        else:
                            echo '<p class="text-muted">Sin noticias recientes.</p>';
                        endif;
                        ?>
                    </div>
                </div>

                <!-- Widget: Fútbol -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Fútbol</h3>
                    <div class="widget-content">
                        <?php
                        $query_futbol = new WP_Query(array('category_name' => 'futbol', 'posts_per_page' => 4));
                        if ($query_futbol->have_posts()):
                            while ($query_futbol->have_posts()):
                                $query_futbol->the_post();
                                ?>
                                <div class="mini-post">
                                    <a href="<?php the_permalink(); ?>" class="mini-post-link">
                                        <span class="mini-post-title"><?php the_title(); ?></span>
                                        <span class="mini-post-date"><?php echo get_the_date('d M'); ?></span>
                                    </a>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        else:
                            echo '<p class="text-muted">Sin noticias recientes.</p>';
                        endif;
                        ?>
                    </div>
                </div>



            </aside>

        </div> <!-- .layout-grid -->
    </div>
</main><!-- #main -->

<?php
get_footer();
