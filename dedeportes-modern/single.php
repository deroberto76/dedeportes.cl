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

            <div class="container" style="padding-top: 3rem;">
                <header class="single-post-header">
                    <div class="post-meta" style="justify-content: center; display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <span class="posted-on">
                            <?php echo get_the_date(); ?>
                        </span>
                        <span class="cat-links">
                            <?php the_category(', '); ?>
                        </span>
                    </div>
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

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()):
            echo '<div class="container" style="max-width: 800px; margin-top: 3rem;">';
            comments_template();
            echo '</div>';
        endif;

    endwhile; // End of the loop.
    ?>

</main><!-- #main -->

<?php
get_footer();
