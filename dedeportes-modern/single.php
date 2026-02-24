<?php
/**
 * The template for displaying all single posts
 *
 * @package Dedeportes_Modern
 */

get_header();
?>

<main id="primary" class="site-main">

    <?php
    while (have_posts()):
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="container" style="padding-top: 1rem;">
                <header class="single-post-header">
                    <?php the_title('<h1 class="single-post-title">', '</h1>'); ?>
                </header>

                <?php if (has_post_thumbnail()): ?>
                    <div class="single-post-thumbnail">
                        <?php the_post_thumbnail('full'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php
                    the_content();

                    wp_link_pages(
                        array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'dedeportes-modern'),
                            'after' => '</div>',
                        )
                    );
                    ?>
                </div><!-- .entry-content -->

                <footer class="entry-footer"
                    style="max-width: 800px; margin: 4rem auto 0; border-top: 1px solid var(--border); padding-top: 2rem;">
                    <div class="post-meta"
                        style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.9rem; color: var(--text-muted);">
                        <span class="posted-on">
                            <?php echo get_the_date(); ?>
                        </span>
                        <span class="cat-links">
                            <?php the_category(', '); ?>
                        </span>
                    </div>
                    <?php
                    // Tags
                    $tags_list = get_the_tag_list('', esc_html__(', ', 'dedeportes-modern'));
                    if ($tags_list) {
                        printf('<span class="tags-links">' . esc_html__('Etiquetas: %1$s', 'dedeportes-modern') . '</span>', $tags_list);
                    }
                    ?>
                </footer><!-- .entry-footer -->
            </div>

        </article><!-- #post-<?php the_ID(); ?> -->



        <div class="container" style="margin-top: 4rem; padding-top: 4rem; border-top: 1px solid var(--border);">
            <h3 class="section-title" style="text-align: center; margin-bottom: 3rem; font-size: 2rem;">Últimas Noticias
            </h3>

            <?php
            $current_id = get_the_ID();
            $args_related = array(
                'posts_per_page' => 3,
                'post__not_in' => array($current_id),
                'ignore_sticky_posts' => 1
            );
            $query_related = new WP_Query($args_related);

            if ($query_related->have_posts()): ?>
                <div class="posts-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
                    <?php while ($query_related->have_posts()):
                        $query_related->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

                            <?php if (has_post_thumbnail()): ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large'); ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="post-visual"></div>
                            <?php endif; ?>

                            <div class="post-content">
                                <div class="post-meta">
                                    <?php echo get_the_date(); ?>
                                </div>
                                <h3 class="post-title" style="font-size: 1.25rem;"><a
                                        href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="post-footer">
                                    <a href="<?php the_permalink(); ?>" class="btn-link">Leer más &rarr;</a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <?php

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
