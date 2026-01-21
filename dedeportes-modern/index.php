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

        <?php if (have_posts()): ?>

            <?php
            // Separate the first post for the Hero Section
            $post_index = 0;
            ?>

            <div class="posts-wrapper">

                <?php while (have_posts()):
                    the_post();
                    $post_index++; ?>

                    <?php if ($post_index === 1): ?>
                        <!-- HERO SECTION -->
                        <article id="post-<?php the_ID(); ?>" <?php post_class('hero-section'); ?>>
                            <div class="hero-background">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('full');
                                } else {
                                    // Fallback placeholder pattern
                                    echo '<div style="width:100%;height:100%;background: linear-gradient(45deg, var(--surface), var(--background));"></div>';
                                }
                                ?>
                            </div>
                            <div class="hero-content">
                                <span class="hero-cat">
                                    <?php the_category(', '); ?>
                                </span>
                                <h2 class="hero-title"><a href="<?php the_permalink(); ?>" rel="bookmark">
                                        <?php the_title(); ?>
                                    </a></h2>
                                <div class="hero-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                                <a href="<?php the_permalink(); ?>" class="btn">Leer más</a>
                            </div>
                        </article>

                        <div class="posts-grid"> <!-- Start Grid -->

                        <?php else: ?>
                            <!-- REGULAR POST CARD -->
                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            the_post_thumbnail('medium_large');
                                        } else {
                                            echo '<div style="width:100%;height:100%;background: var(--surface-hover);"></div>';
                                        }
                                        ?>
                                    </a>
                                </div>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <?php echo get_the_date(); ?> &bull;
                                        <?php the_category(', '); ?>
                                    </div>
                                    <h3 class="post-title"><a href="<?php the_permalink(); ?>">
                                            <?php the_title(); ?>
                                        </a></h3>
                                    <p>
                                        <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                    </p>
                                </div>
                            </article>

                        <?php endif; ?>

                    <?php endwhile; ?>

                </div> <!-- End Grid -->
            </div>

            <?php
            // Pagination
            the_posts_navigation(array(
                'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'dedeportes-modern') . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'dedeportes-modern') . '</span> <span class="nav-title">%title</span>',
            ));

        else:

            // If no content, include the "No posts found" template.
            echo '<div class="container"><p>No se encontraron noticias.</p></div>';

        endif;
        ?>

    </div>
</main><!-- #main -->

<?php
get_footer();
