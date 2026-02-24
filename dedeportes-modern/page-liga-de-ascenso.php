<?php
/**
 * Template Name: Plantilla Liga Ascenso
 * Description: Page template for Liga de Ascenso layout. Matches slug "liga-de-ascenso".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
}

// Custom Query for 'liga-de-ascenso' category
$args = array(
    'category_name' => 'liga-de-ascenso',
    'posts_per_page' => 8,
    'paged' => $paged
);

$ascenso_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container u-pt-2">

        <!-- Page/Category Title Header -->
        <header class="page-header u-mb-2">
            <h1 class="page-title">Liga de Ascenso</h1>
            <div class="taxonomy-description">Toda la información del campeonato de Primera B.</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($ascenso_query->have_posts()): ?>

                    <div class="posts-list">
                        <?php while ($ascenso_query->have_posts()):
                            $ascenso_query->the_post(); ?>

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
                        $wp_query = $ascenso_query;

                        $next_link = get_next_posts_link('Ver más noticias del Ascenso', $ascenso_query->max_num_pages);

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

            <!-- SIDEBAR COLUMN (Liga Ascenso Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-ascenso')): ?>
                    <?php dynamic_sidebar('sidebar-ascenso'); ?>
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
                                        <td>La Serena</td>
                                        <td>5</td>
                                        <td>13</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Rangers</td>
                                        <td>5</td>
                                        <td>12</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Magallanes</td>
                                        <td>5</td>
                                        <td>10</td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td>S. Wanderers</td>
                                        <td>5</td>
                                        <td>9</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>Antofagasta</td>
                                        <td>5</td>
                                        <td>8</td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>Temuco</td>
                                        <td>5</td>
                                        <td>8</td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Santa Cruz</td>
                                        <td>5</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>S. Morning</td>
                                        <td>5</td>
                                        <td>7</td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>Limache</td>
                                        <td>5</td>
                                        <td>6</td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>Barnechea</td>
                                        <td>5</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>San Luis</td>
                                        <td>4</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>U. de Concepción</td>
                                        <td>4</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>13</td>
                                        <td>Recoleta</td>
                                        <td>5</td>
                                        <td>3</td>
                                    </tr>
                                    <tr>
                                        <td>14</td>
                                        <td>Curicó Unido</td>
                                        <td>4</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>15</td>
                                        <td>San Felipe</td>
                                        <td>4</td>
                                        <td>1</td>
                                    </tr>
                                    <tr>
                                        <td>16</td>
                                        <td>San Marcos</td>
                                        <td>5</td>
                                        <td>0</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="widget-note">
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
                                    <span class="match-time">Sab 16:00</span>
                                    <span class="match-versus">Rangers vs Temuco</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Dom 12:00</span>
                                    <span class="match-versus">Wanderers vs La Serena</span>
                                </li>
                                <li class="match-item">
                                    <span class="match-time">Lun 18:00</span>
                                    <span class="match-versus">Magallanes vs Santa Cruz</span>
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
