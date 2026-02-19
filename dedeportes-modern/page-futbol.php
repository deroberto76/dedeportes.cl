<?php
/**
 * Template Name: Plantilla Futbol Custom
 * Description: Page template for Futbol specific layout. Matches slug "futbol".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
} // Handle static page pagination quirk

// Custom Query for 'futbol' category
$args = array(
    'category_name' => 'futbol',
    'posts_per_page' => 8,
    'paged' => $paged
);

$futbol_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container u-pt-2">

        <!-- Page/Category Title Header from Static Page -->
        <?php while (have_posts()):
            the_post(); ?>
            <header class="page-header u-mb-2">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <div class="taxonomy-description"><?php the_content(); ?></div>
            </header>
        <?php endwhile; ?>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($futbol_query->have_posts()): ?>

                    <div class="posts-grid">
                        <?php while ($futbol_query->have_posts()):
                            $futbol_query->the_post(); ?>

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
                        // Hack to make pagination work with custom query on top of static page
                        $temp_query = $wp_query;
                        $wp_query = $futbol_query;

                        $next_link = get_next_posts_link('Ver más noticias de fútbol', $futbol_query->max_num_pages);

                        if ($next_link) {
                            echo str_replace('<a', '<a class="btn btn-large btn-block"', $next_link);
                        }

                        // Reset Main Query
                        $wp_query = $temp_query;
                        wp_reset_postdata();
                        ?>
                    </div>

                <?php else: ?>
                    <p>No se encontraron noticias en esta categoría.</p>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR COLUMN (Futbol Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-futbol')): ?>
                    <?php dynamic_sidebar('sidebar-futbol'); ?>
                <?php else: ?>
                    <!-- Default/Fallback Content if no widgets are added -->

                    <!-- Widget: Partidos de la Fecha -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Partidos de la Fecha</h3>
                        <div class="widget-content">
                            <ul class="match-list">
                                <li class="match-item">
                                    <span class="match-time">Sábado 18:00</span>
                                    <span class="match-versus">Colo-Colo vs U. Católica</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Domingo 12:00</span>
                                    <span class="match-versus">U. de Chile vs Huachipato</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Domingo 17:30</span>
                                    <span class="match-versus">Cobreloa vs Iquique</span>
                                </li>
                            </ul>
                            <p class="widget-note">
                                <em>Agrega un widget "HTML Personalizado" en "Sidebar Fútbol" para editar esto.</em>
                            </p>
                        </div>
                    </div>

                    <!-- Widget: Otras Categorías -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Otras Categorías</h3>
                        <div class="widget-content">
                            <ul class="u-mb-2" style="list-style: none; padding-left: 0;">
                                <li style="margin-bottom: 0.5rem;"><a href="/category/tenis">Tenis</a></li>
                                <li style="margin-bottom: 0.5rem;"><a href="/category/mercado">Mercado de Pases</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>

        </div> <!-- .layout-grid -->
    </div>
</main><!-- #main -->

<?php
get_footer();
