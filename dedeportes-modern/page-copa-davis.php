<?php
/**
 * Template Name: Plantilla Copa Davis
 * Description: Page template for Copa Davis specific layout.
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
}

// Custom Query for 'copa-davis' category
$args = array(
    'category_name' => 'copa-davis',
    'posts_per_page' => 8,
    'paged' => $paged
);

$copa_davis_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Page/Category Title Header -->
        <header class="page-header" style="margin-bottom: 2rem;">
            <h1 class="page-title">Copa Davis</h1>
            <div class="taxonomy-description">Cobertura del equipo chileno de Copa Davis</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($copa_davis_query->have_posts()): ?>

                    <div class="posts-list">
                        <?php while ($copa_davis_query->have_posts()):
                            $copa_davis_query->the_post(); ?>

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
                        // Hack to make pagination work with custom query on top of static page
                        $temp_query = $wp_query;
                        $wp_query = $copa_davis_query;

                        $next_link = get_next_posts_link('Ver más noticias', $copa_davis_query->max_num_pages);

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

            <!-- SIDEBAR COLUMN (Copa Davis Specific) -->
            <aside class="layout-sidebar">
                <?php if (is_active_sidebar('sidebar-copa-davis')): ?>
                    <?php dynamic_sidebar('sidebar-copa-davis'); ?>
                <?php else: ?>
                    <!-- Default/Fallback Content if no widgets are added -->

                    <!-- Widget: Partidos del Día -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Partidos del Día</h3>
                        <div class="widget-content">
                            <style>
                                .davis-match-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-size: 0.9rem;
                                }

                                .davis-match-table th {
                                    background-color: rgba(255, 255, 255, 0.05);
                                    padding: 0.5rem;
                                    text-align: center;
                                    border: 1px solid var(--border);
                                }

                                .davis-match-table td {
                                    padding: 0.5rem;
                                    border: 1px solid var(--border);
                                    text-align: center;
                                }

                                .davis-player {
                                    text-align: left !important;
                                    font-weight: 600;
                                }

                                .set-score {
                                    width: 40px;
                                }
                            </style>
                            <table class="davis-match-table">
                                <thead>
                                    <tr>
                                        <th style="text-align:left;">Tenistas</th>
                                        <th class="set-score">S1</th>
                                        <th class="set-score">S2</th>
                                        <th class="set-score">S3</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Partido 1 -->
                                    <tr>
                                        <td class="davis-player">🇨🇱 N. Jarry</td>
                                        <td>6</td>
                                        <td>4</td>
                                        <td>6</td>
                                    </tr>
                                    <tr>
                                        <td class="davis-player">🇦🇷 S. Báez</td>
                                        <td>4</td>
                                        <td>6</td>
                                        <td>3</td>
                                    </tr>
                                    <!-- Separador opcional -->
                                    <tr>
                                        <td colspan="4" style="border:none; height:10px;"></td>
                                    </tr>

                                    <!-- Partido 2 -->
                                    <tr>
                                        <td class="davis-player">🇨🇱 A. Tabilo</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td class="davis-player">🇦🇷 F. Cerúndolo</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p style="text-align:center; margin-top:1rem; font-size:0.8rem; opacity:0.7;">
                                <em>Resultados en vivo</em>
                            </p>
                        </div>
                    </div>

                    <!-- Widget: Otras Categorías -->
                    <div class="sidebar-widget">
                        <h3 class="widget-title">Otras Categorías</h3>
                        <div class="widget-content">
                            <ul style="list-style: none; padding-left: 0;">
                                <li style="margin-bottom: 0.5rem;"><a href="/category/tenis">Tenis General</a></li>
                                <li style="margin-bottom: 0.5rem;"><a href="/category/futbol">Fútbol</a></li>
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
