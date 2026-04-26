<?php
/**
 * Template Name: Estadísticas Generales
 * Description: Plantilla para mostrar los Top 5 en diversas estadísticas generales de todos los torneos.
 *
 * @package Dedeportes_Modern
 */

get_header();

// CONFIGURACIÓN DE BASE DE DATOS (Extraída de archivos locales)
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

/**
 * Función centralizada para obtener estadísticas procesadas
 */
function get_tournament_stats($pdo, $torneo_filter = null)
{
    $where_clause = "WHERE estado != 'por jugar'";
    if ($torneo_filter) {
        $where_clause .= " AND torneo LIKE " . $pdo->quote('%' . $torneo_filter . '%');
    }

    $sql = "SELECT 
                equipo AS Equipo,
                COUNT(*) AS PJ,
                SUM(CASE WHEN goles_equipo > goles_rival THEN 1 ELSE 0 END) AS PG,
                SUM(CASE WHEN goles_equipo = goles_rival THEN 1 ELSE 0 END) AS PE,
                SUM(CASE WHEN goles_equipo < goles_rival THEN 1 ELSE 0 END) AS PP,
                SUM(goles_equipo) AS GF,
                SUM(goles_rival) AS GC,
                SUM(goles_equipo) - SUM(goles_rival) AS Dif,
                SUM(CASE 
                    WHEN goles_equipo > goles_rival THEN 3 
                    WHEN goles_equipo = goles_rival THEN 1 
                    ELSE 0 
                END) AS Pts
            FROM partidos
            $where_clause
            GROUP BY equipo";

    $stmt = $pdo->query($sql);
    $standings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consideramos solo equipos que hayan jugado al menos un partido
    $standings = array_filter($standings, fn($t) => $t['PJ'] > 0);

    if (empty($standings))
        return null;

    // Calcular Rendimiento (%)
    foreach ($standings as &$t) {
        $max_pts_possible = $t['PJ'] * 3;
        $t['Rend'] = ($max_pts_possible > 0) ? round(($t['Pts'] / $max_pts_possible) * 100, 1) : 0;
    }
    unset($t);

    // 1. Más Puntos
    $top_pts = $standings;
    usort($top_pts, fn($a, $b) => $b['Pts'] <=> $a['Pts'] ?: $b['Dif'] <=> $a['Dif']);

    // 2. Más Partidos Jugados
    $top_pj = $standings;
    usort($top_pj, fn($a, $b) => $b['PJ'] <=> $a['PJ'] ?: $b['Pts'] <=> $a['Pts']);

    // 3. Más Partidos Ganados
    $top_pg = $standings;
    usort($top_pg, fn($a, $b) => $b['PG'] <=> $a['PG'] ?: $b['Pts'] <=> $a['Pts']);

    // 4. Más Goles a Favor
    $top_gf = $standings;
    usort($top_gf, fn($a, $b) => $b['GF'] <=> $a['GF'] ?: $b['Dif'] <=> $a['Dif']);

    // 5. Menos Goles en Contra (30% PJ req)
    $max_pj = !empty($top_pj) ? $top_pj[0]['PJ'] : 0;
    $min_pj_req = max(1, floor($max_pj * 0.3));
    $top_gc_filtered = array_filter($standings, fn($t) => $t['PJ'] >= $min_pj_req);
    $top_gc = $top_gc_filtered ?: $standings;
    usort($top_gc, fn($a, $b) => $a['GC'] <=> $b['GC'] ?: $b['PJ'] <=> $a['PJ']);

    // 6. Mejor Diferencia de Goles
    $top_dif = $standings;
    usort($top_dif, fn($a, $b) => $b['Dif'] <=> $a['Dif'] ?: $b['Pts'] <=> $a['Pts']);

    // 7. Mejor Rendimiento
    $top_rend = $standings;
    usort($top_rend, fn($a, $b) => $b['Rend'] <=> $a['Rend'] ?: $b['Pts'] <=> $a['Pts']);

    return [
        'full' => $standings,
        'pts' => array_slice($top_pts, 0, 5),
        'pj' => array_slice($top_pj, 0, 5),
        'pg' => array_slice($top_pg, 0, 5),
        'gf' => array_slice($top_gf, 0, 5),
        'gc' => array_slice($top_gc, 0, 5),
        'dif' => array_slice($top_dif, 0, 5),
        'rend' => array_slice($top_rend, 0, 5)
    ];
}

/**
 * Función auxiliar para renderizar una tarjeta de Top 5
 */
function render_top5_card($title, $data, $metric_key, $metric_label)
{
    if (empty($data))
        return;
    echo '<div class="sidebar-widget top5-card" style="padding: 0; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-md); display: flex; flex-direction: column; background: var(--card-bg); border-radius: var(--radius-md);">';
    echo '<h3 class="widget-title" style="margin: 0; padding: 1.25rem; background: var(--surface); border-bottom: 2px solid var(--primary); text-align: center; width: 100%; box-sizing: border-box; font-size: 1.15rem;">' . esc_html($title) . '</h3>';
    echo '<div class="widget-content" style="padding: 0.75rem 1.25rem; flex: 1;">';
    echo '<ul style="list-style: none; padding: 0; margin: 0;">';

    $pos = 1;
    foreach ($data as $team) {
        $bold = ($pos === 1) ? 'font-weight: 800; color: var(--primary);' : 'font-weight: 600;';
        $metric_val = $team[$metric_key];

        if ($metric_key === 'Dif' && $metric_val > 0)
            $metric_val = '+' . $metric_val;
        if ($metric_key === 'Rend')
            $metric_val = $metric_val . '%';

        echo '<li style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">';
        echo '<div style="display: flex; align-items: center; gap: 0.75rem;">';
        echo '<span style="font-size: 1rem; font-weight: 800; color: var(--text-muted); width: 20px; text-align: center;">' . $pos . '</span>';

        if (function_exists('dedeportes_get_team_shield')) {
            echo '<img src="' . esc_url(dedeportes_get_team_shield($team['Equipo'])) . '" style="width: 28px; height: 28px; object-fit: contain;" alt="" onerror="this.style.display=\'none\'">';
        }

        echo '<span style="' . $bold . ' font-size: 0.95rem;">' . esc_html($team['Equipo']) . '</span>';
        echo '</div>';

        echo '<div style="text-align: right;">';
        echo '<span style="font-size: 1.1rem; font-weight: 800; color: var(--text-main);">' . $metric_val . '</span>';
        echo '<span style="font-size: 0.7rem; color: var(--text-muted); margin-left: 0.2rem; text-transform: uppercase;">' . esc_html($metric_label) . '</span>';
        echo '</div>';
        echo '</li>';

        $pos++;
    }

    echo '</ul>';
    echo '</div></div>';
}

function is_chilean_team($team)
{
    $chilean_patterns = [
        'Colo',
        'U. de Chile',
        'Católica',
        'Iquique',
        'O\'Higgins',
        'Palestino',
        'Everton',
        'Cobreloa',
        'Española',
        'Coquimbo',
        'Ñublense',
        'Huachipato',
        'Audax',
        'Cobresal',
        'La Calera',
        'Copiapó',
        'Concepción',
        'La Serena',
        'Limache',
        'Wanderers',
        'Rangers',
        'Temuco',
        'San Luis',
        'Curicó',
        'Magallanes'
    ];
    foreach ($chilean_patterns as $p) {
        if (stripos($team, $p) !== false)
            return true;
    }
    return false;
}

function render_tournament_section($title, $stats, $is_general = false)
{
    if (!$stats)
        return;
    echo '<section class="tournament-stats-section" style="margin-top: 4rem;">';
    echo '<h2 style="text-align: center; margin-bottom: 2.5rem; font-size: 1.8rem; color: var(--primary); font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">' . esc_html($title) . '</h2>';
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">';

    render_top5_card('Equipo con Más Puntos', $stats['pts'], 'Pts', 'Pts');
    if ($is_general) {
        render_top5_card('Más Partidos Jugados', $stats['pj'], 'PJ', 'PJ');
    }
    render_top5_card('Más Partidos Ganados', $stats['pg'], 'PG', 'G');

    if ($is_general) {
        // En sección general: solo chilenos, diseño estándar
        $chilean_rend = array_filter($stats['full'], 'is_chilean_team');
        usort($chilean_rend, fn($a, $b) => $b['Rend'] <=> $a['Rend'] ?: $b['Pts'] <=> $a['Pts']);
        render_top5_card('Equipos con Mejor Rendimiento', array_slice($chilean_rend, 0, 5), 'Rend', '%');
    } else {
        render_top5_card('Equipos con Mejor Rendimiento', $stats['rend'], 'Rend', '%');
    }

    render_top5_card('Más Goles a Favor', $stats['gf'], 'GF', 'Goles');
    render_top5_card('Menos Goles en Contra', $stats['gc'], 'GC', 'Goles');
    render_top5_card('Mejor Diferencia de Goles', $stats['dif'], 'Dif', 'Dif');

    echo '</div></section>';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $general_stats = get_tournament_stats($pdo);
    $primera_stats = get_tournament_stats($pdo, 'Liga de Primera');
    $copa_liga_stats = get_tournament_stats($pdo, 'Copa de Liga');
    $libertadores_stats = get_tournament_stats($pdo, 'Copa Libertadores');
    $sudamericana_stats = get_tournament_stats($pdo, 'Copa Sudamericana');

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem; padding-bottom: 5rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 4rem; text-align: center;">
            <h1 class="page-title" style="font-size: 3rem; margin-bottom: 0.5rem; font-weight: 800;">Estadísticas del
                Fútbol</h1>
            <p class="text-muted" style="font-size: 1.1rem;">Análisis y rankings de equipos por desempeño y torneos.</p>
        </header>

        <div class="layout-main" style="max-width: 1240px; margin: 0 auto; width: 100%;">

            <?php if (isset($db_error)): ?>
                <div class="alert alert-danger"
                    style="padding: 2rem; background: #fee2e2; border-radius: 8px; color: #991b1b; border: 1px solid #fecaca;">
                    <strong>Error de conexión:</strong> No se pudo conectar a la base de datos de estadísticas.
                    <?php if (current_user_can('manage_options'))
                        echo '<br><small>Detalle: ' . esc_html($db_error) . '</small>'; ?>
                </div>
            <?php elseif (!$general_stats): ?>
                <div class="alert alert-info"
                    style="padding: 2rem; background: #e0f2fe; border-radius: 8px; color: #075985;">
                    Aún no hay datos de partidos registrados.
                </div>
            <?php else: ?>

                <!-- SECCIÓN GENERAL -->
                <?php render_tournament_section('Estadísticas Generales', $general_stats, true); ?>

                <!-- SECCIÓN LIGA PRIMERA -->
                <?php render_tournament_section('Liga de Primera División', $primera_stats); ?>

                <!-- SECCIÓN COPA DE LIGA -->
                <?php render_tournament_section('Copa de la Liga', $copa_liga_stats); ?>

                <!-- SECCIÓN LIBERTADORES -->
                <?php render_tournament_section('Copa Libertadores', $libertadores_stats); ?>

                <!-- SECCIÓN SUDAMERICANA -->
                <?php render_tournament_section('Copa Sudamericana', $sudamericana_stats); ?>

            <?php endif; ?>

        </div>
    </div>
</main>

<style>
    .top5-card ul li:last-child {
        border-bottom: none !important;
    }
</style>

<?php get_footer(); ?>