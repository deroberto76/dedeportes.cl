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

        <div class="layout-grid">

            <!-- MAIN CONTENT COLUMN -->
            <div class="layout-main">
                <?php
                // 1. Noticia Principal (La más reciente)
                $main_query = new WP_Query(array(
                    'posts_per_page' => 1,
                    'ignore_sticky_posts' => 1
                ));

                if ($main_query->have_posts()):
                    while ($main_query->have_posts()):
                        $main_query->the_post();

                        // Obtener Categoría Primaria (Yoast SEO) o la primera disponible
                        $category_display = '';
                        if (class_exists('WPSEO_Primary_Term')) {
                            $wpseo_primary_term = new WPSEO_Primary_Term('category', get_the_ID());
                            $primary_term_id = $wpseo_primary_term->get_primary_term();
                            $term = get_term($primary_term_id);
                            if (!is_wp_error($term) && $term) {
                                $category_display = $term->name;
                            }
                        }

                        if (empty($category_display)) {
                            $categories = get_the_category();
                            if (!empty($categories)) {
                                $category_display = $categories[0]->name;
                            }
                        }
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item main-featured-post'); ?>>
                            <div class="post-content">
                                <?php if ($category_display): ?>
                                    <span class="badge mb-2"><?php echo esc_html($category_display); ?></span>
                                <?php endif; ?>
                                <h2 class="post-title" style="font-size: 2rem;">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="post-excerpt">
                                    <?php echo wp_trim_words(get_the_excerpt(), 35); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile;
                    wp_reset_postdata();
                endif;

                // --- SECCIÓN: ÚLTIMOS PARTIDOS (Desde Base de Datos) ---
                $host = 'localhost';
                $dbname = 'pjdmenag_futbol';
                $user = 'pjdmenag_futbol';
                $pass = 'n[[cY^7gvog~';

                try {
                    $pdo_matches = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                    $pdo_matches->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Buscamos los últimos partidos. 
                    // Nota: Si la tabla tiene duplicados (una fila por equipo), 
                    // intentamos filtrar por una supuesta columna 'condicion' o similar.
                    // Si no estamos seguros, seleccionamos y filtramos en PHP para evitar errores de SQL.
                    $sql_list = "SELECT * FROM partidos ORDER BY fecha DESC LIMIT 40";
                    $stmt_list = $pdo_matches->query($sql_list);
                    $raw_matches = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

                    $matches = [];
                    $seen_matches = [];

                    foreach ($raw_matches as $m) {
                        // Crear una clave única para el partido independientemente del orden
                        $teams = [$m['equipo'], $m['rival']];
                        sort($teams);
                        $match_key = $m['fecha'] . '_' . $teams[0] . '_' . $teams[1];

                        if (!isset($seen_matches[$match_key])) {
                            // Si no hay columna condicion, asumimos que el primer registro que encontramos 
                            // (o el que tiene equipo=local en una estructura de 2 filas) es el que queremos.
                            // Pero como el usuario quiere Local vs Visitante, y si la tabla tiene 'condicion', la usamos.
                            if (isset($m['condicion']) && $m['condicion'] !== 'Local') {
                                // Si es visitante, invertimos para mostrar Local vs Visitante
                                $matches[] = [
                                    'fecha' => $m['fecha'],
                                    'torneo' => $m['torneo'],
                                    'local' => $m['rival'],
                                    'visitante' => $m['equipo'],
                                    'goles_local' => $m['goles_rival'],
                                    'goles_visitante' => $m['goles_equipo']
                                ];
                            } else {
                                $matches[] = [
                                    'fecha' => $m['fecha'],
                                    'torneo' => $m['torneo'],
                                    'local' => $m['equipo'],
                                    'visitante' => $m['rival'],
                                    'goles_local' => $m['goles_equipo'],
                                    'goles_visitante' => $m['goles_rival']
                                ];
                            }
                            $seen_matches[$match_key] = true;
                        }
                        if (count($matches) >= 20)
                            break;
                    }
                } catch (PDOException $e) {
                    $matches_error = $e->getMessage();
                }

                if (!empty($matches)): ?>
                    <section class="latest-matches-section u-mb-4">
                        <h2 class="section-category-title">Todos los Partidos</h2>
                        <div class="sidebar-widget" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
                            <div class="widget-content">
                                <table class="match-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: var(--surface); border-bottom: 1px solid var(--border);">
                                            <th
                                                style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">
                                                Fecha</th>
                                            <th style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;"
                                                class="hide-mobile">Torneo</th>
                                            <th
                                                style="padding: 1rem; text-align: right; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">
                                                Local</th>
                                            <th
                                                style="padding: 1rem; text-align: center; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">
                                                Resultado</th>
                                            <th
                                                style="padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">
                                                Visitante</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($matches as $match):
                                            $date_formatted = date_i18n('j \d\e F', strtotime($match['fecha']));
                                            ?>
                                            <tr style="border-bottom: 1px solid var(--border);">
                                                <td style="padding: 1rem; font-size: 0.95rem; font-weight: 600;">
                                                    <?php echo $date_formatted; ?></td>
                                                <td style="padding: 1rem; font-size: 0.9rem; color: var(--text-muted);"
                                                    class="hide-mobile"><?php echo esc_html($match['torneo']); ?></td>
                                                <td style="padding: 1rem; text-align: right; font-weight: 600;">
                                                    <div
                                                        style="display: inline-flex; align-items: center; gap: 0.5rem; justify-content: flex-end;">
                                                        <span><?php echo esc_html($match['local']); ?></span>
                                                        <img src="<?php echo dedeportes_get_team_shield($match['local']); ?>"
                                                            class="team-shield" alt="" onerror="this.style.display='none'">
                                                    </div>
                                                </td>
                                                <td
                                                    style="padding: 1rem; text-align: center; font-weight: 800; font-size: 1.1rem; white-space: nowrap;">
                                                    <?php echo $match['goles_local']; ?> -
                                                    <?php echo $match['goles_visitante']; ?>
                                                </td>
                                                <td style="padding: 1rem; text-align: left; font-weight: 600;">
                                                    <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                                        <img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>"
                                                            class="team-shield" alt="" onerror="this.style.display='none'">
                                                        <span><?php echo esc_html($match['visitante']); ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <?php
                // 2. Secciones por Categoría
                $categories_to_show = array(
                    'Fútbol nacional' => 'futbol-nacional',
                    'Fútbol internacional' => 'futbol-internacional',
                    'Selecciones' => 'selecciones'
                );

                foreach ($categories_to_show as $title => $slug):
                    $cat_query = new WP_Query(array(
                        'category_name' => $slug,
                        'posts_per_page' => 4,
                        'post__not_in' => array(get_the_ID()) // Evitar repetir la principal si cae en la misma categoría
                    ));

                    if ($cat_query->have_posts()): ?>
                        <section class="category-section section-<?php echo esc_attr($slug); ?>">
                            <h2 class="section-category-title"><?php echo esc_html($title); ?></h2>
                            <div class="posts-list">
                                <?php while ($cat_query->have_posts()):
                                    $cat_query->the_post(); ?>
                                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item'); ?>>
                                        <div class="post-content">
                                            <h3 class="post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h3>
                                            <div class="post-excerpt">
                                                <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        </section>
                        <?php
                    endif;
                    wp_reset_postdata();
                endforeach;
                ?>
            </div>

            <!-- SIDEBAR COLUMN -->
            <aside class="layout-sidebar">

                <?php if (is_active_sidebar('sidebar-home')): ?>
                    <?php dynamic_sidebar('sidebar-home'); ?>
                <?php endif; ?>


                <!-- Widget: Fútbol -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Fútbol</h3>
                    <div class="widget-content">
                        <?php
                        $query_futbol = new WP_Query(array('category_name' => 'futbol', 'posts_per_page' => 4));
                        if ($query_futbol->have_posts()):
                            while ($query_futbol->have_posts()):
                                $query_futbol->the_post();
                                ?>
                                <div class="mini-post">
                                    <a href="<?php the_permalink(); ?>" class="mini-post-link">
                                        <span class="mini-post-title"><?php the_title(); ?></span>
                                        <span class="mini-post-date"><?php echo get_the_date('d M'); ?></span>
                                    </a>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        else:
                            echo '<p class="text-muted">Sin noticias recientes.</p>';
                        endif;
                        ?>
                    </div>
                </div>



            </aside>

        </div> <!-- .layout-grid -->
    </div>
</main><!-- #main -->

<?php
get_footer();
