<?php
/**
 * Template Name: Plantilla Equipo
 * Description: Muestra las estadísticas detalladas y partidos finalizados de un equipo en diseño de ancho completo.
 *
 * @package Dedeportes_Modern
 */

get_header();

$team_name = get_the_title();
$team_name_db = str_replace(['’', '‘', '”', '“'], ["'", "'", '"', '"'], $team_name);

// Configuración de la Base de Datos
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

$matches = [];
$global_stats = [
    'PJ' => 0,
    'PG' => 0,
    'PE' => 0,
    'PP' => 0,
    'GF' => 0,
    'GC' => 0,
    'Pts' => 0
];
$tournament_stats = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Búsqueda del equipo base de forma flexible
    $search_base = str_replace(['’', '‘', '”', '“', '´', "'", '-', '.'], ' ', $team_name);
    $parts = explode(' ', $search_base);
    $keyword = '';
    $common = ['universidad', 'deportes', 'union', 'club', 'social', 'de', 'la', 'el'];
    foreach ($parts as $p) {
        $p = trim($p);
        if (strlen($p) > 2 && !in_array(strtolower($p), $common)) {
            $keyword = $p;
            break;
        }
    }
    if (!$keyword)
        $keyword = $team_name;
    $like_term = '%' . $keyword . '%';

    // Obtener todos los partidos del equipo que NO estén reportados como 'por jugar'
    $stmt = $pdo->prepare("SELECT * FROM partidos WHERE (LOWER(equipo) LIKE LOWER(?) OR LOWER(rival) LIKE LOWER(?)) AND estado != 'por jugar' ORDER BY id DESC LIMIT 500");
    $stmt->execute([$like_term, $like_term]);
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $seen_matches = [];
    foreach ($raw_data as $m) {
        $fecha_raw = trim($m['fecha']);

        $teams_key = [$m['equipo'], $m['rival']];
        sort($teams_key);
        $match_key = $fecha_raw . '_' . $teams_key[0] . '_' . $teams_key[1];

        // Evitamos contar dos veces si es que la base de datos guardó la ida y la vuelta junta en el mismo CSV con equipos invertidos
        if (isset($seen_matches[$match_key]))
            continue;
        $seen_matches[$match_key] = true;

        $cond = isset($m['condicion']) ? strtolower(trim($m['condicion'])) : '';
        $is_away = in_array($cond, ['visitante', 'v', 'visita', 'visiting']);
        $is_local = in_array($cond, ['local', 'l', 'casa', 'home']);

        $local_team = $is_away ? $m['rival'] : $m['equipo'];
        $visitor_team = $is_away ? $m['equipo'] : $m['rival'];
        $local_goals = $is_away ? (int) $m['goles_rival'] : (int) $m['goles_equipo'];
        $visitor_goals = $is_away ? (int) $m['goles_equipo'] : (int) $m['goles_rival'];

        // Determinar qué goles hizo nuestro equipo actual
        $team_goals = $is_away ? $visitor_goals : $local_goals;
        $opponent_goals = $is_away ? $local_goals : $visitor_goals;

        $pts = 0;
        $r = '';
        if ($team_goals > $opponent_goals) {
            $pts = 3;
            $r = 'G';
        } elseif ($team_goals === $opponent_goals) {
            $pts = 1;
            $r = 'E';
        } else {
            $pts = 0;
            $r = 'P';
        }

        $torneo = $m['torneo'];
        if (!isset($tournament_stats[$torneo])) {
            $tournament_stats[$torneo] = ['PJ' => 0, 'G' => 0, 'E' => 0, 'P' => 0, 'GF' => 0, 'GC' => 0, 'Pts' => 0];
        }

        // Agregar a Estadísticas Globales
        $global_stats['PJ']++;
        $global_stats['GF'] += $team_goals;
        $global_stats['GC'] += $opponent_goals;
        $global_stats['Pts'] += $pts;
        if ($r === 'G')
            $global_stats['PG']++;
        if ($r === 'E')
            $global_stats['PE']++;
        if ($r === 'P')
            $global_stats['PP']++;

        // Agregar a Estadísticas de Torneo
        $tournament_stats[$torneo]['PJ']++;
        $tournament_stats[$torneo]['GF'] += $team_goals;
        $tournament_stats[$torneo]['GC'] += $opponent_goals;
        $tournament_stats[$torneo]['Pts'] += $pts;
        if ($r === 'G')
            $tournament_stats[$torneo]['G']++;
        if ($r === 'E')
            $tournament_stats[$torneo]['E']++;
        if ($r === 'P')
            $tournament_stats[$torneo]['P']++;

        $timestamp = strtotime(str_replace('/', '-', $fecha_raw));
        $matches[] = [
            'fecha' => $fecha_raw,
            'timestamp' => $timestamp,
            'torneo' => $torneo,
            'local' => $local_team,
            'visitante' => $visitor_team,
            'goles_local' => $local_goals,
            'goles_visitante' => $visitor_goals,
            'estado' => 'Final'
        ];
    }

    // Ordenar partidos del más reciente al más antiguo
    usort($matches, function ($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

$team_rendimiento = $global_stats['PJ'] > 0 ? round(($global_stats['Pts'] / ($global_stats['PJ'] * 3)) * 100, 1) : 0;
?>

<main id="primary" class="site-main team-page bg-light" style="background-color: #f8fafc; padding-bottom: 4rem;">
    <div class="container" style="padding-top: 3rem; max-width: 1100px; margin: 0 auto; width: 100%;">

        <!-- ZONA 1: Cabecera del Equipo -->
        <header class="team-profile-header"
            style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 2.5rem;">
            <?php if (function_exists('dedeportes_get_team_shield')): ?>
                <img src="<?php echo esc_url(dedeportes_get_team_shield($team_name)); ?>"
                    alt="<?php echo esc_attr($team_name); ?>" style="width: 80px; height: 80px; object-fit: contain;">
            <?php endif; ?>
            <div>
                <h1
                    style="font-size: 2.25rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1.2; padding-bottom: 0.25rem;">
                    <?php echo esc_html($team_name); ?></h1>
                <?php if (function_exists('dedeportes_get_team_abbreviation')): ?>
                    <span
                        style="font-size: 1rem; color: #64748b; font-weight: 600; text-transform: uppercase;"><?php echo esc_html(dedeportes_get_team_abbreviation($team_name)); ?></span>
                <?php endif; ?>
            </div>
        </header>

        <!-- ZONA 2: Tarjetas de Resumen Global -->
        <div class="team-summary-cards"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <div class="stat-card"
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem;">Puntos</div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #3b82f6;"><?php echo $global_stats['Pts']; ?>
                </div>
            </div>
            <div class="stat-card"
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem;">Goles a Favor
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #22c55e;"><?php echo $global_stats['GF']; ?>
                </div>
            </div>
            <div class="stat-card"
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem;">Goles en Contra
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #ef4444;"><?php echo $global_stats['GC']; ?>
                </div>
            </div>
            <div class="stat-card"
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; text-align: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <div style="font-size: 0.9rem; color: #64748b; font-weight: 600; margin-bottom: 0.5rem;">Rendimiento
                </div>
                <div style="font-size: 2.5rem; font-weight: 800; color: #a855f7;"><?php echo $team_rendimiento; ?>%
                </div>
            </div>
        </div>

        <!-- ZONA 3: Estadísticas por Torneo -->
        <section class="team-tournaments-section" style="margin-bottom: 4rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">Estadísticas por
                Torneo</h2>
            <div
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <table style="width: 100%; border-collapse: collapse; min-width: 600px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th
                                style="padding: 1rem 1.5rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Torneo</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                PJ</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                G</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                E</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                P</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                GF</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                GC</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 800; color: #0f172a;">
                                Pts</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Rend.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tournament_stats as $torneo => $stats):
                            $rend_t = $stats['PJ'] > 0 ? round(($stats['Pts'] / ($stats['PJ'] * 3)) * 100, 1) : 0;
                            ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1.25rem 1.5rem; font-weight: 600; color: #0f172a;">
                                    <?php echo esc_html($torneo); ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['PJ']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['G']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['E']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['P']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['GF']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center;"><?php echo $stats['GC']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center; font-weight: 800; color: #0f172a;">
                                    <?php echo $stats['Pts']; ?></td>
                                <td style="padding: 1.25rem 1rem; text-align: center; font-weight: 600; color: #475569;">
                                    <?php echo $rend_t; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tournament_stats)): ?>
                            <tr>
                                <td colspan="9" style="padding: 2rem; text-align: center; color: #64748b;">No hay datos de
                                    torneos jugados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- ZONA 4: Todos los Partidos -->
        <section class="team-all-matches">
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">Todos los Partidos
            </h2>
            <div
                style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <th
                                style="padding: 1rem 1.5rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Fecha</th>
                            <th
                                style="padding: 1rem; text-align: left; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Torneo</th>
                            <th
                                style="padding: 1rem; text-align: right; width: 30%; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Local</th>
                            <th
                                style="padding: 1rem; text-align: center; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Resultado</th>
                            <th
                                style="padding: 1rem; text-align: left; width: 30%; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Visitante</th>
                            <th
                                style="padding: 1rem 1.5rem; text-align: right; font-size: 0.85rem; font-weight: 600; color: #64748b;">
                                Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $match): ?>
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td
                                    style="padding: 1.25rem 1.5rem; color: #475569; font-size: 0.95rem; white-space: nowrap;">
                                    <?php echo esc_html($match['fecha']); ?></td>
                                <td style="padding: 1.25rem 1rem; color: #475569; font-size: 0.95rem;">
                                    <?php echo esc_html($match['torneo']); ?></td>

                                <!-- Local -->
                                <td style="padding: 1.25rem 1rem; text-align: right;">
                                    <div
                                        style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem;">
                                        <span
                                            style="font-weight: <?php echo ($match['local'] === $team_name) ? '800' : '600'; ?>; color: #0f172a; font-size: 0.95rem;">
                                            <?php echo esc_html($match['local']); ?>
                                        </span>
                                        <?php if (function_exists('dedeportes_get_team_shield')): ?>
                                            <img src="<?php echo esc_url(dedeportes_get_team_shield($match['local'])); ?>"
                                                style="width: 24px; height: 24px; object-fit: contain;" alt=""
                                                onerror="this.style.display='none'">
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Resultado -->
                                <td
                                    style="padding: 1.25rem 1rem; text-align: center; font-weight: 800; font-size: 1.1rem; color: #0f172a; white-space: nowrap;">
                                    <?php echo esc_html($match['goles_local'] . ' - ' . $match['goles_visitante']); ?>
                                </td>

                                <!-- Visitante -->
                                <td style="padding: 1.25rem 1rem; text-align: left;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <?php if (function_exists('dedeportes_get_team_shield')): ?>
                                            <img src="<?php echo esc_url(dedeportes_get_team_shield($match['visitante'])); ?>"
                                                style="width: 24px; height: 24px; object-fit: contain;" alt=""
                                                onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <span
                                            style="font-weight: <?php echo ($match['visitante'] === $team_name) ? '800' : '600'; ?>; color: #0f172a; font-size: 0.95rem;">
                                            <?php echo esc_html($match['visitante']); ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Estado -->
                                <td style="padding: 1.25rem 1.5rem; text-align: right;">
                                    <span
                                        style="display: inline-block; padding: 0.25rem 0.5rem; background: #e2e8f0; color: #475569; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">
                                        <?php echo esc_html($match['estado']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($matches)): ?>
                            <tr>
                                <td colspan="6" style="padding: 3rem; text-align: center; color: #64748b;">No hay partidos
                                    finalizados registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</main>

<?php get_footer(); ?>