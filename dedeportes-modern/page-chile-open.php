<?php
/**
 * Template Name: Plantilla Chile Open
 * Description: Page template for Chile Open specific layout. Matches slug "chile-open".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
} // Handle static page pagination quirk

// Custom Query for 'chile-open' category
$args = array(
    'category_name' => 'chile-open',
    'posts_per_page' => 8,
    'paged' => $paged
);

$chile_open_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Page/Category Title Header from Static Page -->
        <?php while (have_posts()):
            the_post(); ?>
            <header class="page-header" style="margin-bottom: 2rem;">
                <h1 class="page-title"><?php the_title(); ?></h1>
                <div class="taxonomy-description"><?php the_content(); ?></div>
            </header>
        <?php endwhile; ?>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($chile_open_query->have_posts()): ?>

                    <div class="posts-list">
                        <?php while ($chile_open_query->have_posts()):
                            $chile_open_query->the_post(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item'); ?>>
                                <div class="post-content">
                                    <h3 class="post-title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h3>
                                    <div class="post-excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 25); ?>
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
                        $wp_query = $chile_open_query;

                        $next_link = get_next_posts_link('Ver más noticias', $chile_open_query->max_num_pages);

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

            <!-- SIDEBAR COLUMN (Chile Open Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-chile-open')): ?>
                    <?php dynamic_sidebar('sidebar-chile-open'); ?>
                <?php else: ?>
                    <!-- Default/Fallback Content if no widgets are added -->

                    <!-- Widget: Tabla de Posiciones (4 cols, 4 rows) -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Tabla de Posiciones</h3>
                        <div class="widget-content">
                            <table class="ranking-table">
                                <thead>
                                    <tr>
                                        <th>Pos</th>
                                        <th>Equipo</th>
                                        <th>PJ</th>
                                        <th>Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Equipo A</td>
                                        <td>5</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Equipo B</td>
                                        <td>5</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Equipo C</td>
                                        <td>5</td>
                                        <td>8</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Equipo D</td>
                                        <td>5</td>
                                        <td>6</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="text-align:center; margin-top:1rem; font-size:0.8rem; opacity:0.7;">
                                <em>Agrega un widget "HTML Personalizado" en "Sidebar Chile Open" para editar esto.</em>
                            </p>
                        </div>
                    </div>

                    <!-- Widget: Partidos de la Fecha -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Partidos de la Fecha</h3>
                        <div class="widget-content">
                            <ul class="match-list">
                                <li class="match-item">
                                    <span class="match-time">18/02 18:00</span>
                                    <span class="match-versus">Equipo 1 vs Equipo 2</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">18/02 21:00</span>
                                    <span class="match-versus">Equipo 3 vs Equipo 4</span>
                                </li>
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
