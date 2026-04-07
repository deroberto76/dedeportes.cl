<?php
/**
 * Template Name: Fútbol Internacional
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
                <header class="page-header" style="margin-bottom: 2rem;">
                    <h1 class="page-title" style="font-size: 2rem; font-weight: 700;">Fútbol Internacional</h1>
                </header>
                <?php
                // --- SECCIÓN: ÚLTIMOS PARTIDOS (Desde Base de Datos) ---
                $host = 'localhost';
                $dbname = 'pjdmenag_futbol';
                $user = 'pjdmenag_futbol';
                $pass = 'n[[cY^7gvog~';

                try {
                    $pdo_matches = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
                    // Buscamos partidos de torneos internacionales.
                    $sql_list = "SELECT * FROM partidos WHERE torneo IN ('Copa Libertadores', 'Copa Sudamericana') ORDER BY id DESC LIMIT 300";
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
                            'pais_local' => $is_away ? (isset($m['pais_rival']) ? $m['pais_rival'] : '') : (isset($m['pais_equipo']) ? $m['pais_equipo'] : ''),
                            'pais_visitante' => $is_away ? (isset($m['pais_equipo']) ? $m['pais_equipo'] : '') : (isset($m['pais_rival']) ? $m['pais_rival'] : ''),
                            'goles_local' => $is_away ? $m['goles_rival'] : $m['goles_equipo'],
                            'goles_visitante' => $is_away ? $m['goles_equipo'] : $m['goles_rival'],
                            'is_local_row' => $is_local,
                            'hora' => isset($m['hora']) ? trim($m['hora']) : '',
                            'estado' => isset($m['estado']) ? strtolower(trim($m['estado'])) : ''
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

                    // Separar Partidos de Hoy (por jugar) de los Resultados Recientes (finalizados)
                    $matches_today = [];
                    $matches_completed = [];

                    // Usar current_time para obtener la fecha de hoy según la zona horaria ajustada en WordPress
                    $today_str = current_time('Y-m-d');

                    $today_grouped = [];
                    $completed_grouped = [];

                    foreach ($matches as $match) {
                        $match_date_str = substr($match['fecha'], 0, 10);
                        $torneo = empty($match['torneo']) ? 'Otros' : $match['torneo'];

                        // Formatear la fecha para Todos los partidos
                        $fecha_db = str_replace('/', '-', $match['fecha']);
                        $timestamp = strtotime($fecha_db);
                        $date_formatted = $timestamp ? date_i18n('j \d\e F', $timestamp) : $match['fecha'];

                        // Si el partido es hoy, lo guardamos agrupado por torneo
                        if ($match_date_str === $today_str) {
                            if (!isset($today_grouped[$torneo])) {
                                $today_grouped[$torneo] = [];
                            }
                            $today_grouped[$torneo][] = $match;
                        } elseif ($match['estado'] === 'finalizado') {
                            if (!isset($completed_grouped[$torneo])) {
                                $completed_grouped[$torneo] = [];
                            }
                            if (!isset($completed_grouped[$torneo][$date_formatted])) {
                                $completed_grouped[$torneo][$date_formatted] = [];
                            }
                            if (count($completed_grouped[$torneo][$date_formatted]) < 15) {
                                $completed_grouped[$torneo][$date_formatted][] = $match;
                            }
                        }
                    }

                    // Ordenar Partidos de hoy por hora dentro de su grupo
                    foreach ($today_grouped as $t => &$t_matches) {
                        usort($t_matches, function ($a, $b) {
                            $hora_a = !empty($a['hora']) ? $a['hora'] : '23:59:59';
                            $hora_b = !empty($b['hora']) ? $b['hora'] : '23:59:59';
                            return strcmp($hora_a, $hora_b);
                        });
                    }

                    $country_codes = [
                        'chile' => 'CHI',
                        'argentina' => 'ARG',
                        'brasil' => 'BRA',
                        'colombia' => 'COL',
                        'ecuador' => 'ECU',
                        'bolivia' => 'BOL',
                        'perú' => 'PER',
                        'peru' => 'PER',
                        'paraguay' => 'PAR',
                        'uruguay' => 'URU',
                        'venezuela' => 'VEN'
                    ];

                } catch (PDOException $e) {
                }
                ?>

                <!-- Partidos de Hoy -->
                <?php if (!empty($today_grouped)): ?>
                    <section class="intl-matches-section">
                        <h2 class="section-category-title">Partidos de Hoy</h2>
                        <?php foreach ($today_grouped as $torneo => $torneo_matches): ?>
                            <h3 class="intl-torneo-title"><?php echo esc_html($torneo); ?></h3>
                            <div class="intl-list-container">
                                <?php foreach ($torneo_matches as $match):
                                    $time_formatted = !empty($match['hora']) ? date('H:i', strtotime($match['hora'])) : '';
                                    $p_l = strtolower(trim($match['pais_local']));
                                    $p_code_l = (!empty($p_l)) ? (isset($country_codes[$p_l]) ? $country_codes[$p_l] : substr(strtoupper($p_l), 0, 3)) : '';
                                    $p_v = strtolower(trim($match['pais_visitante']));
                                    $p_code_v = (!empty($p_v)) ? (isset($country_codes[$p_v]) ? $country_codes[$p_v] : substr(strtoupper($p_v), 0, 3)) : '';
                                    ?>
                                    <div class="intl-match-box">
                                        <div class="intl-team-row">
                                            <span><?php echo esc_html($match['local']); ?><?php if ($p_code_l): ?> <span
                                                        class="intl-country">(<?php echo esc_html($p_code_l); ?>)</span><?php endif; ?></span>
                                            <span class="intl-time"><?php echo esc_html($time_formatted); ?></span>
                                        </div>
                                        <div class="intl-team-row" style="margin-top: 2px;">
                                            <span><?php echo esc_html($match['visitante']); ?><?php if ($p_code_v): ?> <span
                                                        class="intl-country">(<?php echo esc_html($p_code_v); ?>)</span><?php endif; ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>

                <!-- Todos los Partidos -->
                <?php if (!empty($completed_grouped)): ?>
                    <section class="intl-matches-section">
                        <h2 class="section-category-title">Todos los partidos</h2>
                        <?php foreach ($completed_grouped as $torneo => $dates): ?>
                            <h3 class="intl-torneo-title"><?php echo esc_html($torneo); ?></h3>
                            <?php foreach ($dates as $date => $date_matches): ?>
                                <div class="intl-list-container" style="margin-bottom: 2rem;">
                                    <div class="intl-date-header"><?php echo esc_html($date); ?></div>
                                    <?php foreach ($date_matches as $match):
                                        $p_l = strtolower(trim($match['pais_local']));
                                        $p_code_l = (!empty($p_l)) ? (isset($country_codes[$p_l]) ? $country_codes[$p_l] : substr(strtoupper($p_l), 0, 3)) : '';
                                        $p_v = strtolower(trim($match['pais_visitante']));
                                        $p_code_v = (!empty($p_v)) ? (isset($country_codes[$p_v]) ? $country_codes[$p_v] : substr(strtoupper($p_v), 0, 3)) : '';
                                        ?>
                                        <div class="intl-match-box-completed">
                                            <div class="intl-team-row">
                                                <span><?php echo esc_html($match['local']); ?><?php if ($p_code_l): ?> <span
                                                            class="intl-country">(<?php echo esc_html($p_code_l); ?>)</span><?php endif; ?></span>
                                                <span class="intl-score"><?php echo esc_html($match['goles_local']); ?></span>
                                            </div>
                                            <div class="intl-team-row" style="margin-top: 2px;">
                                                <span><?php echo esc_html($match['visitante']); ?><?php if ($p_code_v): ?> <span
                                                            class="intl-country">(<?php echo esc_html($p_code_v); ?>)</span><?php endif; ?></span>
                                                <span class="intl-score"><?php echo esc_html($match['goles_visitante']); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </section>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR COLUMN -->
            <aside class="layout-sidebar">

                <!-- Mejores Rendimientos Internacionales -->
                <?php
                $standings = [];
                try {
                    $pdo_perf = new PDO("mysql:host=localhost;dbname=pjdmenag_futbol;charset=utf8", "pjdmenag_futbol", "n[[cY^7gvog~");
                    $pdo_perf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $sql_perf = "SELECT 
                                equipo AS Equipo,
                                COUNT(*) AS PJ,
                                SUM(CASE WHEN goles_equipo > goles_rival THEN 3 WHEN goles_equipo = goles_rival THEN 1 ELSE 0 END) AS Pts,
                                ROUND((SUM(CASE WHEN goles_equipo > goles_rival THEN 3 WHEN goles_equipo = goles_rival THEN 1 ELSE 0 END) / (COUNT(*) * 3)) * 100, 1) AS Rendimiento
                            FROM partidos
                            WHERE estado = 'finalizado' AND torneo IN ('Copa Libertadores', 'Copa Sudamericana')
                            GROUP BY equipo
                            ORDER BY Rendimiento DESC, Pts DESC
                            LIMIT 10";
                    $stmt_perf = $pdo_perf->query($sql_perf);
                    $standings = $stmt_perf->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                }
                ?>
                <div class="sidebar-widget">
                    <h3 class="widget-title">Mejores Rendimientos</h3>
                    <div class="widget-content">
                        <?php if (!empty($standings)): ?>
                            <table class="ranking-table" style="width: 100%; font-size: 0.9rem;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Equipo</th>
                                        <th style="text-align: center;">PJ</th>
                                        <th style="text-align: right;">% Rend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($standings as $row): ?>
                                        <tr>
                                            <td style="font-weight: 600;"><?php echo esc_html($row['Equipo']); ?></td>
                                            <td style="text-align: center; opacity: 0.7;"><?php echo $row['PJ']; ?></td>
                                            <td style="text-align: right; font-weight: 700; color: var(--primary);">
                                                <?php echo $row['Rendimiento']; ?>%
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <p style="font-size: 0.7rem; margin-top: 1rem; opacity: 0.6; text-align: center;">
                                * Calculado sobre total de partidos jugados.
                            </p>
                        <?php else: ?>
                            <p class="text-muted">Sin datos.</p>
                        <?php endif; ?>
                    </div>
                </div>


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
