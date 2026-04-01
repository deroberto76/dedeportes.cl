<?php
/**
 * Template Name: Estadísticas Fútbol
 * Description: Plantilla para mostrar la tabla de posiciones automatizada desde la base de datos.
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

    // Consulta para calcular la tabla de posiciones
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
            WHERE torneo = 'Liga de Primera' AND estado != 'por jugar'
            GROUP BY equipo
            ORDER BY Pts DESC, Dif DESC, GF DESC";

    $stmt = $pdo->query($sql);
    $standings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 3rem; text-align: center;">
            <h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 0.5rem;">Estadísticas en Tiempo Real</h1>
            <p class="text-muted">Tabla de posiciones actualizada automáticamente desde la base de datos.</p>
        </header>

        <div class="layout-grid" style="grid-template-columns: 1fr;">

            <div class="layout-main" style="max-width: 900px; margin: 0 auto; width: 100%;">

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
                        Aún no hay datos de partidos registrados para este torneo.
                    </div>
                <?php else: ?>

                    <div class="sidebar-widget" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
                        <div class="widget-content">
                            <table class="ranking-table" style="width: 100%; margin-bottom: 0;">
                                <thead>
                                    <tr>
                                        <th style="padding: 1rem; text-align: center;">Pos</th>
                                        <th style="padding: 1rem;">Equipo</th>
                                        <th style="padding: 1rem; text-align: center;">PJ</th>
                                        <th style="padding: 1rem; text-align: center;">G</th>
                                        <th style="padding: 1rem; text-align: center;">E</th>
                                        <th style="padding: 1rem; text-align: center;">P</th>
                                        <th style="padding: 1rem; text-align: center;" class="hide-mobile">GF</th>
                                        <th style="padding: 1rem; text-align: center;" class="hide-mobile">GC</th>
                                        <th style="padding: 1rem; text-align: center;">Dif</th>
                                        <th style="padding: 1rem; text-align: center; background: var(--surface);">Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pos = 1;
                                    foreach ($standings as $row):
                                        $highlight = ($pos <= 4) ? 'style="border-left: 4px solid var(--primary);"' : '';
                                        ?>
                                        <tr <?php echo $highlight; ?>>
                                            <td style="text-align: center; font-weight: 700;">
                                                <?php echo $pos++; ?>
                                            </td>
                                            <td style="font-weight: 600;">
                                                <?php echo esc_html($row['Equipo']); ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $row['PJ']; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $row['PG']; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $row['PE']; ?>
                                            </td>
                                            <td style="text-align: center;">
                                                <?php echo $row['PP']; ?>
                                            </td>
                                            <td style="text-align: center;" class="hide-mobile">
                                                <?php echo $row['GF']; ?>
                                            </td>
                                            <td style="text-align: center;" class="hide-mobile">
                                                <?php echo $row['GC']; ?>
                                            </td>
                                            <td
                                                style="text-align: center; font-weight: 600; color: <?php echo ($row['Dif'] >= 0) ? '#16a34a' : '#dc2626'; ?>;">
                                                <?php echo ($row['Dif'] > 0) ? '+' . $row['Dif'] : $row['Dif']; ?>
                                            </td>
                                            <td style="text-align: center; font-weight: 800; background: var(--surface);">
                                                <?php echo $row['Pts']; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <p style="margin-top: 2rem; font-size: 0.85rem; color: var(--text-muted); text-align: center;">
                        * Los criterios de desempate son: Puntos > Diferencia de Goles > Goles a Favor.
                    </p>

                <?php endif; ?>

            </div>

        </div> <!-- .layout-grid -->
    </div>
</main>

<style>
    @media (max-width: 600px) {
        .hide-mobile {
            display: none;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.85rem;
        }
    }
</style>

<?php
get_footer();
