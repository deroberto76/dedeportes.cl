<?php
/**
 * The template for displaying category pages
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
                <header class="page-header">
                    <h2 class="section-title">
                        <?php single_term_title(); ?>
                    </h2>
                    <?php
                    the_archive_description('<div class="archive-description">', '</div>');
                    ?>
                </header>

                <?php if (have_posts()): ?>

                    <div class="posts-grid">
                        <?php while (have_posts()):
                            the_post(); ?>

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
                                    <h3 class="post-title"><a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a></h3>
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

                    <!-- Paginación -->
                    <div class="load-more-container">
                        <?php
                        // Next Posts Link styled as button
                        $next_link = get_next_posts_link('Ver más noticias');
                        if ($next_link) {
                            echo str_replace('<a', '<a class="btn btn-large btn-block"', $next_link);
                        }
                        ?>
                    </div>

                <?php else: ?>
                    <p>No se encontraron noticias en esta categoría.</p>
                <?php endif; ?>
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
                                        <span class="mini-post-title">
                                            <?php the_title(); ?>
                                        </span>
                                        <span class="mini-post-date">
                                            <?php echo get_the_date('d M'); ?>
                                        </span>
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
                                        <span class="mini-post-title">
                                            <?php the_title(); ?>
                                        </span>
                                        <span class="mini-post-date">
                                            <?php echo get_the_date('d M'); ?>
                                        </span>
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
