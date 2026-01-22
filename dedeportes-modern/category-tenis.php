<?php
/**
 * The template for displaying Category: Tenis
 *
 * @package Dedeportes_Modern
 */

get_header();
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Category Title Header -->
        <header class="page-header" style="margin-bottom: 2rem;">
            <h1 class="page-title">Noticias de Tenis</h1>
            <div class="taxonomy-description">Cobertura exclusiva del tenis nacional e internacional.</div>
        </header>

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">

                <?php if (have_posts()): ?>

                    <div class="posts-grid">
                        <?php while (have_posts()):
                            the_post(); ?>

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
                        // Custom text requested by user
                        $next_link = get_next_posts_link('Ver más noticias de tenis');
                        if ($next_link) {
                            echo str_replace('<a', '<a class="btn btn-large btn-block"', $next_link);
                        }
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
