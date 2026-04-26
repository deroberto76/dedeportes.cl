<?php
/**
 * Template Name: Estadísticas Generales
 * Description: Plantilla para mostrar los Top 5 en diversas estadísticas generales de todos los torneos.
 *
 * @package Dedeportes_Modern
 */

get_header();

// CONFIGURACIÓN DE BASE DE DATOS
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

    $standings = array_filter($standings, fn($t) => $t['PJ'] > 0);

    if (empty($standings))
        return null;

    foreach ($standings as &$t) {
        $max_pj = $t['PJ'];
        $t['Rend'] = ($max_pj > 0) ? round(($t['Pts'] / ($max_pj * 3)) * 100, 1) : 0;
    }
    unset($t);

    $top_pts = $standings;
    usort($top_pts, fn($a, $b) => $b['Pts'] <=> $a['Pts'] ?: $b['Dif'] <=> $a['Dif']);

    $top_pj = $standings;
    usort($top_pj, fn($a, $b) => $b['PJ'] <=> $a['PJ'] ?: $b['Pts'] <=> $a['Pts']);

    $top_pg = $standings;
    usort($top_pg, fn($a, $b) => $b['PG'] <=> $a['PG'] ?: $b['Pts'] <=> $a['Pts']);

    $top_gf = $standings;
    usort($top_gf, fn($a, $b) => $b['GF'] <=> $a['GF'] ?: $b['Dif'] <=> $a['Dif']);

    $max_pj_global = !empty($top_pj) ? $top_pj[0]['PJ'] : 0;
    $min_pj_req = max(1, floor($max_pj_global * 0.3));
    $top_gc_filtered = array_filter($standings, fn($t) => $t['PJ'] >= $min_pj_req);
    $top_gc = $top_gc_filtered ?: $standings;
    usort($top_gc, fn($a, $b) => $a['GC'] <=> $b['GC'] ?: $b['PJ'] <=> $a['PJ']);

    $top_dif = $standings;
    usort($top_dif, fn($a, $b) => $b['Dif'] <=> $a['Dif'] ?: $b['Pts'] <=> $a['Pts']);

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

function render_top5_card($title, $data, $metric_key, $metric_label)
{
    if (empty($data))
        return;
    ?>
    <div class="stats-premium-card">
        <div class="card-header-premium">
            <h4><?php echo esc_html($title); ?></h4>
        </div>
        <div class="card-body-premium">
            <ul class="stats-list-premium">
                <?php
                $pos = 1;
                foreach ($data as $team):
                    $metric_val = $team[$metric_key];
                    if ($metric_key === 'Dif' && $metric_val > 0)
                        $metric_val = '+' . $metric_val;
                    if ($metric_key === 'Rend')
                        $metric_val = $metric_val . '%';
                    ?>
                    <li class="stat-item-premium pos-<?php echo $pos; ?>">
                        <span class="pos-badge"><?php echo $pos; ?></span>
                        <div class="team-info">
                            <?php if (function_exists('dedeportes_get_team_shield')): ?>
                                <img src="<?php echo esc_url(dedeportes_get_team_shield($team['Equipo'])); ?>" class="team-shield"
                                    alt="" onerror="this.style.display='none'">
                            <?php endif; ?>
                            <span class="team-name"><?php echo esc_html($team['Equipo']); ?></span>
                        </div>
                        <div class="metric-info">
                            <span class="metric-value"><?php echo $metric_val; ?></span>
                            <span class="metric-label"><?php echo esc_html($metric_label); ?></span>
                        </div>
                    </li>
                    <?php $pos++; endforeach; ?>
            </ul>
        </div>
    </div>
    <?php
}

function is_chilean_team($team)
{
    $chilean_patterns = ['Colo', 'U. de Chile', 'Católica', 'Iquique', 'O\'Higgins', 'Palestino', 'Everton', 'Cobreloa', 'Española', 'Coquimbo', 'Coquimbo Unido', 'Ñublense', 'Huachipato', 'Audax', 'Cobresal', 'La Calera', 'Copiapó', 'Concepción', 'La Serena', 'Limache', 'Wanderers', 'Rangers', 'Temuco', 'San Luis', 'Curicó', 'Magallanes'];
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
    ?>
    <section class="tournament-section-premium">
        <h2 class="section-title-premium"><?php echo esc_html($title); ?></h2>
        <div class="stats-grid-premium">
            <?php
            render_top5_card('Equipo con Más Puntos', $stats['pts'], 'Pts', 'Pts');
            if ($is_general)
                render_top5_card('Más Partidos Jugados', $stats['pj'], 'PJ', 'PJ');
            render_top5_card('Más Partidos Ganados', $stats['pg'], 'PG', 'Ganados');

            if ($is_general) {
                $chilean_rend = array_filter($stats['full'], 'is_chilean_team');
                usort($chilean_rend, fn($a, $b) => $b['Rend'] <=> $a['Rend'] ?: $b['Pts'] <=> $a['Pts']);
                render_top5_card('Mejor Rendimiento (Chile)', array_slice($chilean_rend, 0, 5), 'Rend', '%');
            } else {
                render_top5_card('Más Rendimiento', $stats['rend'], 'Rend', '%');
            }

            render_top5_card('Goles a Favor', $stats['gf'], 'GF', 'Goles');
            render_top5_card('Goles en Contra', $stats['gc'], 'GC', 'Goles');
            render_top5_card('Dif de Goles', $stats['dif'], 'Dif', 'Goles');
            ?>
        </div>
    </section>
    <?php
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

<style>
    :root {
        --premium-bg: #fdfdfd;
        --premium-accent: #2563eb;
        --premium-card: #ffffff;
        --premium-text: #0f172a;
        --premium-muted: #64748b;
        --premium-border: #f1f5f9;
        --premium-gradient: linear-gradient(135deg, #2563eb, #1e4ed8);
    }

    .stats-main-container {
        padding: 4rem 1rem;
        background-color: var(--premium-bg);
        min-height: 100vh;
    }

    .stats-header-premium {
        text-align: center;
        max-width: 800px;
        margin: 0 auto 5rem;
    }

    .stats-header-premium h1 {
        font-family: 'Outfit', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        color: var(--premium-text);
        margin-bottom: 1rem;
        letter-spacing: -0.02em;
    }

    .stats-header-premium p {
        font-size: 1.25rem;
        color: var(--premium-muted);
        font-weight: 400;
    }

    .tournament-section-premium {
        margin-bottom: 6rem;
    }

    .section-title-premium {
        font-family: 'Outfit', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        text-align: center;
        color: var(--premium-text);
        margin-bottom: 3rem;
        position: relative;
        padding-bottom: 1rem;
    }

    .section-title-premium::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: var(--premium-gradient);
        border-radius: 2px;
    }

    .stats-grid-premium {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 2rem;
        max-width: 1300px;
        margin: 0 auto;
    }

    .stats-premium-card {
        background: var(--premium-card);
        border-radius: 12px;
        border: 1px solid var(--premium-border);
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stats-premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    }

    .card-header-premium {
        padding: 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--premium-border);
    }

    .card-header-premium h4 {
        margin: 0;
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--premium-text);
        text-align: center;
    }

    .card-body-premium {
        padding: 1rem 1.5rem;
    }

    .stats-list-premium {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .stat-item-premium {
        display: flex;
        align-items: center;
        padding: 0.85rem 0;
        border-bottom: 1px solid var(--premium-border);
    }

    .stat-item-premium:last-child {
        border-bottom: none;
    }

    .pos-badge {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        border-radius: 6px;
        background: #f1f5f9;
        color: var(--premium-muted);
        margin-right: 1rem;
    }

    .pos-1 .pos-badge {
        background: #fef3c7;
        color: #92400e;
    }

    .team-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex: 1;
    }

    .team-shield {
        width: 32px;
        height: 32px;
        object-fit: contain;
    }

    .team-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--premium-text);
    }

    .pos-1 .team-name {
        font-weight: 800;
        color: var(--premium-accent);
    }

    .metric-info {
        text-align: right;
    }

    .metric-value {
        display: block;
        font-size: 1.125rem;
        font-weight: 800;
        color: var(--premium-text);
        line-height: 1;
    }

    .metric-label {
        font-size: 0.7rem;
        color: var(--premium-muted);
        text-transform: uppercase;
        letter-spacing: 0.025em;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .stats-header-premium h1 {
            font-size: 2.5rem;
        }

        .stats-grid-premium {
            grid-template-columns: 1fr;
        }
    }
</style>

<main id="primary" class="site-main stats-main-container">
    <div class="container-fluid">

        <header class="stats-header-premium">
            <h1 class="page-title">Estadísticas del Fútbol</h1>
            <p>Análisis integral y rankings de rendimiento por torneo.</p>
        </header>

        <div class="layout-main-premium">

            <?php if (isset($db_error)): ?>
                <div class="alert-premium-error">
                    <strong>Error de conexión:</strong> No se pudo conectar a la base de datos de estadísticas.
                    <?php if (current_user_can('manage_options'))
                        echo '<br><small>Detalle: ' . esc_html($db_error) . '</small>'; ?>
                </div>
            <?php elseif (!$general_stats): ?>
                <div class="alert-premium-info">Aún no hay datos de partidos registrados.</div>
            <?php else: ?>

                <?php render_tournament_section('Estadísticas Generales', $general_stats, true); ?>
                <?php render_tournament_section('Liga de Primera División', $primera_stats); ?>
                <?php render_tournament_section('Copa de la Liga', $copa_liga_stats); ?>
                <?php render_tournament_section('Copa Libertadores', $libertadores_stats); ?>
                <?php render_tournament_section('Copa Sudamericana', $sudamericana_stats); ?>

            <?php endif; ?>

        </div>
    </div>
</main>

<?php get_footer(); ?>

<?php get_footer(); ?>