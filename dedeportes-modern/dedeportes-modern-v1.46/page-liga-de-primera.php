<?php
/**
 * Template Name: Plantilla Liga Primera
 * Description: Page template for Liga de Primera layout. Matches slug "liga-de-primera".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
}

// Custom Query for 'liga-de-primera' category
$args = array(
    'category_name' => 'liga-de-primera',
    'posts_per_page' => 8,
    'paged' => $paged
);

$liga_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Page/Category Title Header -->
        <header class="page-header" style="margin-bottom: 2rem;">
            <h1 class="page-title">Liga de Primera División</h1>
            <div class="taxonomy-description">Todas las noticias del campeonato nacional.</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($liga_query->have_posts()): ?>

                    <div class="posts-grid">
                        <?php while ($liga_query->have_posts()):
                            $liga_query->the_post(); ?>

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
                        $temp_query = $wp_query;
                        $wp_query = $liga_query;

                        $next_link = get_next_posts_link('Ver más noticias de Primera', $liga_query->max_num_pages);

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

            <!-- SIDEBAR COLUMN (Liga Primera Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-liga')): ?>
                    <?php dynamic_sidebar('sidebar-liga'); ?>
                <?php else: ?>
                    <!-- Default/Fallback Content -->

                    <!-- Widget: Tabla de Posiciones (16 equipos) -->
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
                                        <td>U. de Chile</td>
                                        <td>5</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Iquique</td>
                                        <td>5</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>O'Higgins</td>
                                        <td>5</td>
                                        <td>9</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>Palestino</td>
                                        <td>5</td>
                                        <td>8</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Everton</td>
                                        <td>5</td>
                                        <td>8</td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Cobreloa</td>
                                        <td>5</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Colo-Colo</td>
                                        <td>5</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>U. Española</td>
                                        <td>5</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>Coquimbo</td>
                                        <td>5</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>Ñublense</td>
                                        <td>5</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>Huachipato</td>
                                        <td>4</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>U. Católica</td>
                                        <td>4</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>13</td>
                                        <td>Audax</td>
                                        <td>5</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>14</td>
                                        <td>Cobresal</td>
                                        <td>4</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>15</td>
                                        <td>La Calera</td>
                                        <td>4</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>16</td>
                                        <td>Copiapó</td>
                                        <td>5</td>
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
                                    <span class="match-time">Sab 12:00</span>
                                    <span class="match-versus">Everton vs Palestino</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Dom 18:00</span>
                                    <span class="match-versus">Colo-Colo vs U. de Chile</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Lun 20:30</span>
                                    <span class="match-versus">Audax vs Coquimbo</span>
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
