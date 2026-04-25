<?php
/**
 * The template for displaying all pages
 *
 * @package Dedeportes_Modern
 */

get_header();
?>

<main id="primary" class="site-main">

    <div class="container" style="padding-top: 2rem;">
        <?php
        while (have_posts()):
            the_post();
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

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
            </article><!-- #post-<?php the_ID(); ?> -->

            <?php
        endwhile; // End of the loop.
        ?>
    </div>

</main><!-- #main -->

<?php
get_footer();
