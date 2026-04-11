<?php
/**
 * Template Name: Plantilla Copa Libertadores
 * Description: Page template for Copa Libertadores layout. Matches slug "copa-libertadores".
 *
 * @package Dedeportes_Modern
 */

get_header();

// CONFIGURACIÓN DE BASE DE DATOS
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

$db_error = null;
$grupos = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
$standings_por_grupo = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta para calcular la tabla de posiciones por grupo
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
            WHERE torneo LIKE '%Copa Libertadores%' 
              AND TRIM(grupo) LIKE CONCAT('%', :grupo, '%') 
              AND fecha LIKE :year
              AND (estado != 'por jugar' OR estado IS NULL)
            GROUP BY equipo
            ORDER BY Pts DESC, Dif DESC, GF DESC";

    $stmt = $pdo->prepare($sql);

    $current_year = date('Y') . '%';
    foreach ($grupos as $g) {
        $stmt->execute(['grupo' => $g, 'year' => $current_year]);
        $standings_por_grupo[$g] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 3rem; padding-bottom: 5rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 4rem; text-align: center;">
            <h1 class="page-title" style="font-size: 3rem; margin-bottom: 0.75rem; font-weight: 800; color: #0f172a;">
                Tabla de Posiciones Copa Libertadores</h1>
            <p class="text-muted" style="font-size: 1.15rem;">Estadísticas actualizadas.</p>
        </header>

        <div class="layout-grid" style="grid-template-columns: 1fr;">

            <div class="layout-main" style="max-width: 1000px; margin: 0 auto; width: 100%;">

                <?php if ($db_error): ?>
                    <div class="alert alert-danger"
                        style="padding: 2rem; background: #fee2e2; border-radius: 8px; color: #991b1b; border: 1px solid #fecaca; margin-bottom: 2rem;">
                        <strong>Error de conexión:</strong> No se pudo conectar a la base de datos de estadísticas.
                        <?php if (current_user_can('manage_options'))
                            echo '<br><small>Detalle: ' . esc_html($db_error) . '</small>'; ?>
                    </div>
                <?php else: ?>

                    <!-- Tablas por Grupo -->
                    <div class="standings-container"
                        style="display: flex; flex-direction: column; gap: 3rem; margin-bottom: 3rem;">
                        <?php foreach ($grupos as $g): ?>
                            <?php if (!empty($standings_por_grupo[$g])): ?>
                                <div class="sidebar-widget"
                                    style="padding: 0; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm); border-radius: 12px; background-color: var(--card-bg);">
                                    <h2
                                        style="padding: 1.25rem 2rem; background: var(--surface); margin: 0; font-size: 1.5rem; border-bottom: 1px solid var(--border); color: #2563eb; font-weight: 700;">
                                        Grupo <?php echo $g; ?>
                                    </h2>
                                    <div class="widget-content">
                                        <table class="ranking-table"
                                            style="width: 100%; margin-bottom: 0; border-collapse: collapse;">
                                            <thead>
                                                <tr style="border-bottom: 1px solid var(--border);">
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="0">POS &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: left; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="1">EQUIPO &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="2">PJ &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="3">G &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="4">E &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="5">P &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="6">GF &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="7">GC &#x21D5;</th>
                                                    <th style="padding: 1.25rem 0.75rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800;"
                                                        class="sortable" data-col="8">DIF &#x21D5;</th>
                                                    <th style="padding: 1.25rem 1.5rem; text-align: center; color: #f59e0b; font-size: 0.85rem; font-weight: 800; background: var(--surface);"
                                                        class="sortable" data-col="9">PTS &#x21D5;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pos = 1;
                                                foreach ($standings_por_grupo[$g] as $row):
                                                    // Highlighting: 1-2 Blue, 3 Red
                                                    $highlight_color = 'transparent';
                                                    if ($pos <= 2)
                                                        $highlight_color = '#2563eb';
                                                    else if ($pos == 3)
                                                        $highlight_color = '#ef4444';
                                                    ?>
                                                    <tr
                                                        style="border-bottom: 1px solid var(--border); border-left: 4px solid <?php echo $highlight_color; ?>;">
                                                        <td
                                                            style="padding: 1rem; text-align: center; font-weight: 700; color: #0f172a;">
                                                            <?php echo $pos++; ?>
                                                        </td>
                                                        <td style="padding: 1rem; font-weight: 600; color: #1e293b;">
                                                            <?php echo esc_html($row['Equipo']); ?>
                                                        </td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['PJ']; ?></td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['PG']; ?></td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['PE']; ?></td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['PP']; ?></td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['GF']; ?></td>
                                                        <td style="padding: 1rem; text-align: center;"><?php echo $row['GC']; ?></td>
                                                        <td
                                                            style="padding: 1rem; text-align: center; font-weight: 700; color: <?php echo ($row['Dif'] > 0) ? '#16a34a' : (($row['Dif'] < 0) ? '#dc2626' : '#64748b'); ?>;">
                                                            <?php echo ($row['Dif'] > 0) ? '+' . $row['Dif'] : $row['Dif']; ?>
                                                        </td>
                                                        <td
                                                            style="padding: 1rem 1.5rem; text-align: center; font-weight: 800; background: var(--surface); color: #0f172a;">
                                                            <?php echo $row['Pts']; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <div
                        style="margin-top: 2rem; font-size: 1rem; color: #334155; text-align: center; background: #f1f5f9; padding: 1.5rem; border-radius: 12px; border-left: 5px solid #2563eb;">
                        <p style="margin: 0; font-weight: 600;">
                            <span
                                style="display:inline-block; width:12px; height:12px; background:#2563eb; border-radius: 2px; margin-right:5px;"></span>
                            Clasificados a Octavos de Final
                            <span
                                style="display:inline-block; width:12px; height:12px; background:#ef4444; border-radius: 2px; margin-left:20px; margin-right:5px;"></span>
                            Play-offs Copa Sudamericana
                        </p>
                    </div>

                <?php endif; ?>

            </div>

        </div> <!-- .layout-grid -->
    </div>
</main>

<style>
    @media (max-width: 800px) {

        .ranking-table th,
        .ranking-table td {
            padding: 0.75rem 0.5rem !important;
            font-size: 0.8rem !important;
        }

        .page-title {
            font-size: 2rem !important;
        }
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tables = document.querySelectorAll('.ranking-table');
        tables.forEach(table => {
            const headers = table.querySelectorAll('th.sortable');
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const colIndex = parseInt(header.getAttribute('data-col'));
                    const isAsc = header.classList.contains('asc');
                    headers.forEach(h => { h.classList.remove('asc', 'desc'); });
                    if (isAsc) { header.classList.add('desc'); } else { header.classList.add('asc'); }
                    const direction = isAsc ? -1 : 1;
                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    rows.sort((a, b) => {
                        let valA = a.cells[colIndex].textContent.trim().replace(/\+/g, '');
                        let valB = b.cells[colIndex].textContent.trim().replace(/\+/g, '');
                        const numA = parseFloat(valA);
                        const numB = parseFloat(valB);
                        if (!isNaN(numA) && !isNaN(numB)) { return (numA - numB) * direction; }
                        return valA.localeCompare(valB) * direction;
                    });

                    rows.forEach(row => tbody.appendChild(row));

                    // Rearrange positions and highlighting
                    const newRows = Array.from(tbody.querySelectorAll('tr'));
                    newRows.forEach((row, idx) => {
                        row.cells[0].textContent = idx + 1;
                        const pos = idx + 1;
                        let color = 'transparent';
                        if (pos <= 2) color = '#2563eb';
                        else if (pos == 3) color = '#ef4444';
                        row.style.borderLeft = '4px solid ' + color;
                    });
                });
            });
        });
    });
</script>

<?php
get_footer();
