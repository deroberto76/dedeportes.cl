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
                // --- SECCIÓN: ÚLTIMOS PARTIDOS (Desde Base de Datos) ---
                $host = 'localhost';
                $dbname = 'pjdmenag_futbol';
                $user = 'pjdmenag_futbol';
                $pass = 'n[[cY^7gvog~';

                try {
                    $pdo_matches = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                    // Buscamos los últimos partidos.
                    // Intentamos ordenar cronológicamente asumiendo formato DD/MM/YYYY o YYYY-MM-DD.
                    // Probamos primero con una detección de formato en SQL si es posible, o simplemente STR_TO_DATE.
                    $sql_list = "SELECT * FROM partidos 
                                 ORDER BY 
                                    CASE 
                                        WHEN fecha LIKE '__/__/____' THEN STR_TO_DATE(fecha, '%d/%m/%Y')
                                        ELSE fecha 
                                    END DESC 
                                 LIMIT 100";

                    $stmt_list = $pdo_matches->query($sql_list);
                    $raw_matches = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

                    $matches = [];
                    $seen_matches = [];

                    foreach ($raw_matches as $m) {
                        // Crear una clave única para el partido basada en la fecha y los equipos involucrados
                        $teams = [$m['equipo'], $m['rival']];
                        sort($teams);
                        $match_key = $m['fecha'] . '_' . $teams[0] . '_' . $teams[1];

                        if (!isset($seen_matches[$match_key])) {
                            // Intentamos identificar quién es el local. 
                            // Si existe la columna 'condicion' y es 'Visitante', invertimos.
                            // Si no existe, tomamos la fila tal cual.
                            $is_away = (isset($m['condicion']) && (strtolower($m['condicion']) === 'visitante' || strtolower($m['condicion']) === 'v'));

                            if ($is_away) {
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
                                            // Normalizar fecha para strtotime (reemplazar / por - si es necesario)
                                            $fecha_db = str_replace('/', '-', $match['fecha']);
                                            $timestamp = strtotime($fecha_db);
                                            // Fallback si strtotime falla: usar el valor crudo o intentar otro parseo
                                            $date_formatted = $timestamp ? date_i18n('j \d\e F', $timestamp) : $match['fecha'];
                                            ?>
                                            <tr style="border-bottom: 1px solid var(--border);">
                                                <td style="padding: 1rem; font-size: 0.95rem; font-weight: 600;">
                                                    <?php echo $date_formatted; ?>
                                                </td>
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
                // Secciones de categorías eliminadas por petición del usuario
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
