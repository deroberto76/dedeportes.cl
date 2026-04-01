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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para calcular la tabla de posiciones global (todos los torneos)
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
            WHERE estado != 'por jugar'
            GROUP BY equipo";

    $stmt = $pdo->query($sql);
    $standings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consideramos solo equipos que hayan jugado al menos un partido
    $standings = array_filter($standings, fn($t) => $t['PJ'] > 0);

    // 1. Más Puntos
    $top_pts = $standings;
    usort($top_pts, fn($a, $b) => $b['Pts'] <=> $a['Pts'] ?: $b['Dif'] <=> $a['Dif']);
    $top_pts = array_slice($top_pts, 0, 5);

    // 2. Más Partidos Jugados
    $top_pj = $standings;
    usort($top_pj, fn($a, $b) => $b['PJ'] <=> $a['PJ'] ?: $b['Pts'] <=> $a['Pts']);
    $top_pj = array_slice($top_pj, 0, 5);

    // 3. Más Partidos Ganados
    $top_pg = $standings;
    usort($top_pg, fn($a, $b) => $b['PG'] <=> $a['PG'] ?: $b['Pts'] <=> $a['Pts']);
    $top_pg = array_slice($top_pg, 0, 5);

    // 4. Más Goles a Favor
    $top_gf = $standings;
    usort($top_gf, fn($a, $b) => $b['GF'] <=> $a['GF'] ?: $b['Dif'] <=> $a['Dif']);
    $top_gf = array_slice($top_gf, 0, 5);

    // 5. Menos Goles en Contra
    // Para ser justos, filtramos a los equipos que al menos jugaron el 30% del máximo de partidos.
    $max_pj = !empty($top_pj) ? $top_pj[0]['PJ'] : 0;
    $min_pj_req = max(1, floor($max_pj * 0.3));

    $top_gc_all = array_filter($standings, fn($t) => $t['PJ'] >= $min_pj_req);
    $top_gc = $top_gc_all;
    usort($top_gc, fn($a, $b) => $a['GC'] <=> $b['GC'] ?: $b['PJ'] <=> $a['PJ']);
    $top_gc = array_slice($top_gc, 0, 5);

    // 6. Mejor Diferencia de Goles
    $top_dif = $standings;
    usort($top_dif, fn($a, $b) => $b['Dif'] <=> $a['Dif'] ?: $b['Pts'] <=> $a['Pts']);
    $top_dif = array_slice($top_dif, 0, 5);

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

/**
 * Función auxiliar para renderizar una tarjeta de Top 5
 */
function render_top5_card($title, $data, $metric_key, $metric_label)
{
    echo '<div class="sidebar-widget top5-card" style="padding: 0; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-md); display: flex; flex-direction: column;">';
    echo '<h3 class="widget-title" style="margin: 0; padding: 1.5rem; background: var(--surface); border-bottom: 2px solid var(--primary); text-align: center; width: 100%; box-sizing: border-box;">' . esc_html($title) . '</h3>';
    echo '<div class="widget-content" style="padding: 1rem; flex: 1;">';
    echo '<ul style="list-style: none; padding: 0; margin: 0;">';

    $pos = 1;
    foreach ($data as $team) {
        $bold = ($pos === 1) ? 'font-weight: 800; color: var(--primary);' : 'font-weight: 600;';
        $metric_val = ($metric_key === 'Dif' && $team[$metric_key] > 0) ? '+' . $team[$metric_key] : $team[$metric_key];

        echo '<li style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border);">';
        echo '<div style="display: flex; align-items: center; gap: 1rem;">';
        echo '<span style="font-size: 1.2rem; font-weight: 800; color: var(--text-muted); width: 24px; text-align: center;">' . $pos . '</span>';

        if (function_exists('dedeportes_get_team_shield')) {
            echo '<img src="' . esc_url(dedeportes_get_team_shield($team['Equipo'])) . '" style="width: 32px; height: 32px; object-fit: contain;" alt="" onerror="this.style.display=\'none\'">';
        }

        echo '<span style="' . $bold . ' font-size: 1.05rem;">' . esc_html($team['Equipo']) . '</span>';
        echo '</div>';

        echo '<div style="text-align: right;">';
        echo '<span style="font-size: 1.25rem; font-weight: 800; color: var(--text-main);">' . $metric_val . '</span>';
        echo '<span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.25rem;">' . esc_html($metric_label) . '</span>';
        echo '</div>';
        echo '</li>';

        $pos++;
    }

    echo '</ul>';

    if ($metric_key === 'GC') {
        echo '<div style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem; opacity: 0.8;">* Aplica regla de mínimo de partidos jugados.</div>';
    }

    echo '</div></div>';
}
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 3rem; text-align: center;">
            <h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 0.5rem;">Estadísticas Generales</h1>
            <p class="text-muted">Top 5 equipos en distintas métricas agrupando todos los torneos registrados.</p>
        </header>

        <div class="layout-main" style="max-width: 1200px; margin: 0 auto; width: 100%;">

            <?php if (isset($db_error)): ?>
                <div class="alert alert-danger"
                    style="padding: 2rem; background: #fee2e2; border-radius: 8px; color: #991b1b; border: 1px solid #fecaca;">
                    <strong>Error de conexión:</strong> No se pudo conectar a la base de datos de estadísticas.
                    <?php if (current_user_can('manage_options'))
                        echo '<br><small>Detalle: ' . esc_html($db_error) . '</small>'; ?>
                </div>
            <?php elseif (empty($standings)): ?>
                <div class="alert alert-info"
                    style="padding: 2rem; background: #e0f2fe; border-radius: 8px; color: #075985;">
                    Aún no hay datos de partidos registrados.
                </div>
            <?php else: ?>

                <!-- Grid de Tarjetas Estadísticas -->
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem; align-items: stretch;">
                    <?php
                    render_top5_card('Equipo con Más Puntos', $top_pts, 'Pts', 'Pts');
                    render_top5_card('Más Partidos Jugados', $top_pj, 'PJ', 'PJ');
                    render_top5_card('Más Partidos Ganados', $top_pg, 'PG', 'G');
                    render_top5_card('Más Goles a Favor', $top_gf, 'GF', 'Goles');
                    render_top5_card('Menos Goles en Contra', $top_gc, 'GC', 'Goles');
                    render_top5_card('Mejor Diferencia de Goles', $top_dif, 'Dif', 'Dif');
                    ?>
                </div>

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