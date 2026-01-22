<?php
/**
 * Template Name: Plantilla Tenis Custom
 * Description: Page template for Tennis specific layout. Matches slug "tenis".
 *
 * @package Dedeportes_Modern
 */

get_header();

// Setup Custom Pagination
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
} // Handle static page pagination quirk

// Custom Query for 'tenis' category
$args = array(
    'category_name' => 'tenis',
    'posts_per_page' => 8,
    'paged' => $paged
);

$tenis_query = new WP_Query($args);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Page/Category Title Header -->
        <header class="page-header" style="margin-bottom: 2rem;">
            <h1 class="page-title">Noticias de Tenis</h1>
            <div class="taxonomy-description">Cobertura exclusiva del tenis nacional e internacional.</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if ($tenis_query->have_posts()): ?>

                    <div class="posts-grid">
                        <?php while ($tenis_query->have_posts()):
                            $tenis_query->the_post(); ?>

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
                                    <h3 class="post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
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
                        $wp_query = $tenis_query;

                        $next_link = get_next_posts_link('Ver más noticias de tenis', $tenis_query->max_num_pages);

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

            <!-- SIDEBAR COLUMN (Tenis Specific) -->
            <aside class="layout-sidebar">

                <!-- Widget: Ranking Tenistas Chilenos -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Ranking ATP Chile</h3>
                    <div class="widget-content">
                        <table class="ranking-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jugador</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>19</td>
                                    <td>N. Jarry</td>
                                </tr>
                                <tr>
                                    <td>22</td>
                                    <td>A. Tabilo</td>
                                </tr>
                                <tr>
                                    <td>98</td>
                                    <td>C. Garín</td>
                                </tr>
                                <tr>
                                    <td>154</td>
                                    <td>T. Barrios</td>
                                </tr>
                                <tr>
                                    <td>320</td>
                                    <td>M. Soto</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Widget: Próximos Partidos -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Próximos Partidos</h3>
                    <div class="widget-content">
                        <ul class="match-list">
                            <li class="match-item">
                                <span class="match-time">Hoy 15:00</span>
                                <span class="match-versus">Jarry vs Alcaraz</span>
                            </li>
                            <li class="match-item">
                                <span class="match-time">Mañana 11:00</span>
                                <span class="match-versus">Tabilo vs Ruud</span>
                            </li>
                            <li class="match-item">
                                <span class="match-time">Viernes 20:00</span>
                                <span class="match-versus">Garín vs Sinner</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Fallback to general sidebar widgets if needed -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Otras Categorías</h3>
                    <div class="widget-content">
                        <ul style="list-style: none; padding-left: 0;">
                            <li style="margin-bottom: 0.5rem;"><a href="/category/futbol">Fútbol</a></li>
                            <li style="margin-bottom: 0.5rem;"><a href="/category/mercado">Mercado de Pases</a></li>
                        </ul>
                    </div>
                </div>

            </aside>

        </div> <!-- .layout-grid -->
    </div>
</main><!-- #main -->

<?php
get_footer();
