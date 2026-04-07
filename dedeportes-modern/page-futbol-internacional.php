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

                    foreach ($matches as $match) {
                        // Extraer solo Y-m-d de la DB por si viene con horas (ej. 2026-03-31 15:30:00)
                        $match_date_str = substr($match['fecha'], 0, 10);

                        // Si el partido es hoy, lo agregamos a 'Partidos de hoy' sin importar si ya terminó o no.
                        if ($match_date_str === $today_str) {
                            $matches_today[] = $match;
                        } elseif ($match['estado'] === 'finalizado') {
                            $matches_completed[] = $match;
                        }
                    }

                    usort($matches_today, function ($a, $b) {
                        $hora_a = !empty($a['hora']) ? $a['hora'] : '23:59:59';
                        $hora_b = !empty($b['hora']) ? $b['hora'] : '23:59:59';
                        return strcmp($hora_a, $hora_b);
                    });

                    // Limitar los finalizados a los 20 más recientes
                    $matches_completed = array_slice($matches_completed, 0, 20);
                } catch (PDOException $e) {
                    $matches_error = $e->getMessage();
                }
                ?>

                <?php if (!empty($matches_today)): ?>
                    <section class="latest-matches-section u-mb-4">
                        <h2 class="section-category-title">Partidos de hoy</h2>
                        <div class="sidebar-widget" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
                            <div class="widget-content">
                                <div class="match-cards-list">
                                    <?php
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
                                    foreach ($matches_today as $match):
                                        // Normalizar fecha para strtotime
                                        $fecha_db = str_replace('/', '-', $match['fecha']);
                                        $timestamp = strtotime($fecha_db);
                                        $date_formatted = $timestamp ? date_i18n('j \d\e F', $timestamp) : $match['fecha'];
                                        $time_formatted = !empty($match['hora']) ? date('H:i', strtotime($match['hora'])) : $date_formatted;
                                        ?>
                                        <div class="match-card">
                                            <div class="match-card-meta">
                                                <div class="match-card-date"><?php echo esc_html($time_formatted); ?></div>
                                                <div class="match-card-tournament"><?php echo esc_html($match['torneo']); ?>
                                                </div>
                                            </div>
                                            <div class="match-card-teams">
                                                <div class="match-card-team local">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['local']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span
                                                            class="team-name-full"><?php echo esc_html($match['local']); ?></span>
                                                        <span
                                                            class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['local'])); ?></span>
                                                        <?php
                                                        $p_l = strtolower(trim($match['pais_local']));
                                                        if (!empty($p_l)):
                                                            $p_code = isset($country_codes[$p_l]) ? $country_codes[$p_l] : substr(strtoupper($p_l), 0, 3);
                                                            ?>
                                                            <span class="team-country"
                                                                style="display:block; font-size:0.75rem; color:var(--text-muted); line-height:1; font-weight:700; margin-top:2px;"><?php echo esc_html($p_code); ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                                <div class="match-card-team visitor">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span
                                                            class="team-name-full"><?php echo esc_html($match['visitante']); ?></span>
                                                        <span
                                                            class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['visitante'])); ?></span>
                                                        <?php
                                                        $p_v = strtolower(trim($match['pais_visitante']));
                                                        if (!empty($p_v)):
                                                            $p_code_v = isset($country_codes[$p_v]) ? $country_codes[$p_v] : substr(strtoupper($p_v), 0, 3);
                                                            ?>
                                                            <span class="team-country"
                                                                style="display:block; font-size:0.75rem; color:var(--text-muted); line-height:1; font-weight:700; margin-top:2px;"><?php echo esc_html($p_code_v); ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="match-card-result">
                                                <div class="score-row">
                                                    <?php echo ($match['goles_local'] !== '' && $match['goles_local'] !== null) ? esc_html($match['goles_local']) : '-'; ?>
                                                </div>
                                                <div class="score-row">
                                                    <?php echo ($match['goles_visitante'] !== '' && $match['goles_visitante'] !== null) ? esc_html($match['goles_visitante']) : '-'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($matches_completed)): ?>
                    <section class="latest-matches-section u-mb-4">
                        <h2 class="section-category-title">Todos los Partidos</h2>
                        <div class="sidebar-widget" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
                            <div class="widget-content">
                                <div class="match-cards-list">
                                    <?php foreach ($matches_completed as $match):
                                        // Normalizar fecha para strtotime
                                        $fecha_db = str_replace('/', '-', $match['fecha']);
                                        $timestamp = strtotime($fecha_db);
                                        $date_formatted = $timestamp ? date_i18n('j \d\e F', $timestamp) : $match['fecha'];
                                        ?>
                                        <div class="match-card">
                                            <div class="match-card-meta">
                                                <div class="match-card-date"><?php echo $date_formatted; ?></div>
                                                <div class="match-card-tournament"><?php echo esc_html($match['torneo']); ?>
                                                </div>
                                            </div>
                                            <div class="match-card-teams">
                                                <div class="match-card-team local">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['local']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span
                                                            class="team-name-full"><?php echo esc_html($match['local']); ?></span>
                                                        <span
                                                            class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['local'])); ?></span>
                                                    </span>
                                                </div>
                                                <div class="match-card-team visitor">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span
                                                            class="team-name-full"><?php echo esc_html($match['visitante']); ?></span>
                                                        <span
                                                            class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['visitante'])); ?></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="match-card-result">
                                                <div class="score-row"><?php echo $match['goles_local']; ?></div>
                                                <div class="score-row"><?php echo $match['goles_visitante']; ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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
