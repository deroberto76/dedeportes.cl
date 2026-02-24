<?php
/**
 * Template Name: Plantilla Sudamericano Sub 20 Femenino
 * Description: Page template for Sudamericano Sub 20 Femenino layout. Matches slug "sudamericano-sub-20-femenino".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
}

// Custom Query for 'sudamericano-sub-20f' category
$args = array(
    'category_name' => 'sudamericano-sub-20f',
    'posts_per_page' => 8,
    'paged' => $paged
);

$sudamericano_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Page/Category Title Header -->
        <header class="page-header" style="margin-bottom: 2rem;">
            <h1 class="page-title">Sudamericano Sub 20 Femenino</h1>
            <div class="taxonomy-description">Noticias del Sudamericano Sub 20 Femenino.</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($sudamericano_query->have_posts()): ?>

                    <div class="posts-list">
                        <?php while ($sudamericano_query->have_posts()):
                            $sudamericano_query->the_post(); ?>

                            <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item'); ?>>
                                <div class="post-content">
                                    <div class="post-meta">
                                        <?php echo get_the_date(); ?>
                                    </div>
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
                        $temp_query = $wp_query;
                        $wp_query = $sudamericano_query;

                        $next_link = get_next_posts_link('Ver más noticias', $sudamericano_query->max_num_pages);

                        if ($next_link) {
                            echo str_replace('<a', '<a class="btn btn-large btn-block"', $next_link);
                        }

                        $wp_query = $temp_query;
                        wp_reset_postdata();
                        ?>
                    </div>

                <?php else: ?>
                    <p>No se encontraron noticias en esta categoría.</p>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR COLUMN (Sudamericano Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-sudamericano-sub-20f')): ?>
                    <?php dynamic_sidebar('sidebar-sudamericano-sub-20f'); ?>
                <?php else: ?>
                    <!-- Default/Fallback Content -->

                    <!-- Widget: Tabla de Posiciones (5 filas) -->
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
                                        <td>Chile</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Paraguay</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Colombia</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Venezuela</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Uruguay</td>
                                        <td>0</td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="text-align:center; margin-top:1rem; font-size:0.8rem; opacity:0.7;">
                                <em>Widget editable desde Admin</em>
                            </p>
                        </div>
                    </div>

                    <!-- Widget: Partidos de la Fecha -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Próxima Fecha</h3>
                        <div class="widget-content">
                            <ul class="match-list">
                                <li class="match-item">
                                    <span class="match-time">Mié 4 Feb 18:00</span>
                                    <span class="match-versus">Paraguay vs Chile</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Por definir</span>
                                    <span class="match-versus">Colombia vs Venezuela</span>
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
