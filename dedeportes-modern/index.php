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

            <!-- MAIN CONTENT COLUMN (Últimas Entradas) -->
            <div class="layout-main">
                <h2 class="section-title">Últimas Noticias</h2>

                <?php
                // Custom Query for Latest 8 Posts
                $args_latest = array(
                    'posts_per_page' => 8,
                    'ignore_sticky_posts' => 1
                );
                $query_latest = new WP_Query($args_latest);
                ?>

                <?php if ($query_latest->have_posts()): ?>

                    <div class="posts-grid">
                        <?php while ($query_latest->have_posts()):
                            $query_latest->the_post(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

                                <?php if (has_post_thumbnail()): ?>
                                    <div class="post-thumbnail">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php the_post_thumbnail('medium_large'); ?>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="post-visual"></div> <!-- Color strip -->
                                <?php endif; ?>

                                <div class="post-content">
                                    <div class="post-meta">
                                        <?php echo get_the_date(); ?>
                                    </div>
                                    <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <div class="post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                    </div>
                                    <div class="post-footer">
                                        <a href="<?php the_permalink(); ?>" class="btn-link">Leer más &rarr;</a>
                                    </div>
                                </div>
                            </article>

                        <?php endwhile; ?>
                    </div>

                    <!-- Botón Ver Más -->
                    <div class="load-more-container">
                        <a href="<?php echo esc_url(home_url('/page/2/')); ?>" class="btn btn-large btn-block">
                            Ver más noticias
                        </a>
                    </div>

                    <?php wp_reset_postdata(); ?>

                <?php else: ?>
                    <p>No se encontraron noticias.</p>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR COLUMN -->
            <aside class="layout-sidebar">

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

                <!-- Widget: Mercado -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Mercado</h3>
                    <div class="widget-content">
                        <?php
                        $query_mercado = new WP_Query(array('category_name' => 'mercado', 'posts_per_page' => 4));
                        if ($query_mercado->have_posts()):
                            while ($query_mercado->have_posts()):
                                $query_mercado->the_post();
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
