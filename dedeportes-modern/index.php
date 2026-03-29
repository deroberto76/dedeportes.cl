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
                    // Buscamos una cantidad mayor de registros.
                    $sql_list = "SELECT * FROM partidos ORDER BY id DESC LIMIT 300";
                    $stmt_list = $pdo_matches->query($sql_list);
                    $raw_data = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

                    $matches = [];
                    $seen_matches = [];

                    foreach ($raw_data as $m) {
                        $fecha_raw = trim($m['fecha']);
                        $timestamp = 0;

                        // Intentamos formatos comunes
                        $try_formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'j/n/Y', 'j-n-Y'];
                        foreach ($try_formats as $fmt) {
                            $dt = DateTime::createFromFormat($fmt, $fecha_raw);
                            if ($dt !== false) {
                                // Corregir años de 2 dígitos
                                if ((int) $dt->format('Y') < 100) {
                                    $dt->setDate(2000 + (int) $dt->format('Y'), (int) $dt->format('m'), (int) $dt->format('d'));
                                }
                                $timestamp = $dt->getTimestamp();
                                break;
                            }
                        }

                        // Fallback a strtotime
                        if (!$timestamp) {
                            $fecha_norm = str_replace('/', '-', $fecha_raw);
                            $timestamp = strtotime($fecha_norm);
                        }

                        // Clave única para evitar duplicados
                        $teams = [$m['equipo'], $m['rival']];
                        sort($teams);
                        $match_key = $fecha_raw . '_' . $teams[0] . '_' . $teams[1];

                        // Procesar la fila actual
                        $cond = isset($m['condicion']) ? strtolower(trim($m['condicion'])) : '';
                        $is_away = in_array($cond, ['visitante', 'v', 'visita', 'visiting']);
                        $is_local = in_array($cond, ['local', 'l', 'casa', 'home']);

                        $m_processed = [
                            'timestamp' => (int) $timestamp,
                            'id' => (int) $m['id'],
                            'fecha' => $fecha_raw,
                            'torneo' => $m['torneo'],
                            'local' => $is_away ? $m['rival'] : $m['equipo'],
                            'visitante' => $is_away ? $m['equipo'] : $m['rival'],
                            'goles_local' => $is_away ? $m['goles_rival'] : $m['goles_equipo'],
                            'goles_visitante' => $is_away ? $m['goles_equipo'] : $m['goles_rival'],
                            'is_local_row' => $is_local
                        ];

                        if (!isset($seen_matches[$match_key])) {
                            $matches[] = $m_processed;
                            $seen_matches[$match_key] = count($matches) - 1; // Guardamos el índice
                        } else {
                            // Si ya vimos este partido, y esta fila es explícitamente "Local", la preferimos
                            $idx = $seen_matches[$match_key];
                            if ($is_local && !$matches[$idx]['is_local_row']) {
                                $matches[$idx] = $m_processed;
                            }
                        }
                    }

                    // Ordenar: 1º por timestamp DESC, 2º por ID DESC
                    usort($matches, function ($a, $b) {
                        if ($a['timestamp'] !== $b['timestamp']) {
                            return ($b['timestamp'] - $a['timestamp']);
                        }
                        return ($b['id'] - $a['id']);
                    });

                    // Limitar a los 20 más recientes
                    $matches = array_slice($matches, 0, 20);
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
