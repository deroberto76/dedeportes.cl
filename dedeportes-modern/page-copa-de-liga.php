<?php
/**
 * Template Name: Copa de Liga
 * Description: Plantilla para mostrar las tablas de posiciones automatizadas de los grupos de la Copa de Liga.
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
$grupos = ['A', 'B', 'C', 'D'];
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
            WHERE torneo LIKE '%Copa de Liga%' 
              AND TRIM(grupo) LIKE CONCAT('%', :grupo, '%') 
              AND estado != 'por jugar'
            GROUP BY equipo
            ORDER BY Pts DESC, Dif DESC, GF DESC";

    $stmt = $pdo->prepare($sql);

    foreach ($grupos as $g) {
        $stmt->execute(['grupo' => $g]);
        $standings_por_grupo[$g] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 3rem; text-align: center;">
            <h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 0.5rem;">Tabla de Posiciones Copa de Liga
            </h1>
            <p class="text-muted">Estadísticas actualizadas.</p>
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
                <?php else: ?>

                    <?php foreach ($grupos as $g): ?>
                        <div class="sidebar-widget"
                            style="padding: 0; overflow: hidden; border: 1px solid var(--border); margin-bottom: 2rem; box-shadow: var(--shadow-sm); border-radius: 8px; background-color: var(--card-bg);">
                            <h2
                                style="padding: 1rem 1.5rem; background: var(--surface); margin: 0; font-size: 1.25rem; border-bottom: 1px solid var(--border); color: var(--primary);">
                                Grupo <?php echo $g; ?>
                            </h2>
                            <div class="widget-content">
                                <table class="ranking-table" style="width: 100%; margin-bottom: 0;">
                                    <thead>
                                        <tr>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="0">Pos
                                                &#x21D5;</th>
                                            <th style="padding: 1rem;" class="sortable" data-col="1">Equipo &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="2">PJ
                                                &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="3">G
                                                &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="4">E
                                                &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="5">P
                                                &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="hide-mobile sortable"
                                                data-col="6">GF &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="hide-mobile sortable"
                                                data-col="7">GC &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center;" class="sortable" data-col="8">Dif
                                                &#x21D5;</th>
                                            <th style="padding: 1rem; text-align: center; background: var(--surface);"
                                                class="sortable" data-col="9">Pts &#x21D5;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($standings_por_grupo[$g])): ?>
                                            <tr>
                                                <td colspan="10"
                                                    style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                                    Aún no hay datos de partidos registrados para el Grupo <?php echo $g; ?>.
                                                </td>
                                            </tr>
                                        <?php else:
                                            $pos = 1;
                                            foreach ($standings_por_grupo[$g] as $row):
                                                // El primero va marcado con una barra azul
                                                $highlight = ($pos == 1) ? 'style="border-left: 4px solid #2563eb;"' : 'style="border-left: 4px solid transparent;"';
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
                                            <?php endforeach;
                                        endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div
                        style="margin-top: 2rem; font-size: 0.95rem; color: var(--text-main); text-align: left; background: var(--surface); padding: 1.5rem; border-radius: 8px; border-left: 4px solid var(--primary);">
                        <p style="font-weight: 600; margin-bottom: 0; text-align: center;">El primero de cada grupo avanza a
                            las semifinales. El campeón obtiene clasificación a la Copa Libertadores como Chile 3.</p>
                    </div>

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

<style>
    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }

    th.asc::after {
        content: '';
    }

    th.desc::after {
        content: '';
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
                    // Check if it's the empty row
                    if (tbody.querySelector('td[colspan]')) return;

                    const colIndex = parseInt(header.getAttribute('data-col'));
                    const isAsc = header.classList.contains('asc');

                    headers.forEach(h => { h.classList.remove('asc', 'desc'); });
                    if (isAsc) {
                        header.classList.add('desc');
                    } else {
                        header.classList.add('asc');
                    }
                    const direction = isAsc ? -1 : 1;

                    const rows = Array.from(tbody.querySelectorAll('tr'));

                    rows.sort((a, b) => {
                        let valA = a.cells[colIndex].textContent.trim();
                        let valB = b.cells[colIndex].textContent.trim();

                        valA = valA.replace(/\+/g, '');
                        valB = valB.replace(/\+/g, '');

                        const numA = parseFloat(valA);
                        const numB = parseFloat(valB);

                        if (!isNaN(numA) && !isNaN(numB)) {
                            return (numA - numB) * direction;
                        }
                        return valA.localeCompare(valB) * direction;
                    });

                    rows.forEach(row => tbody.appendChild(row));

                    const newRows = Array.from(tbody.querySelectorAll('tr'));
                    newRows.forEach((row, idx) => {
                        row.cells[0].textContent = idx + 1;
                        const pos = idx + 1;
                        let highlight = 'none';
                        if (pos === 1) highlight = '4px solid #2563eb';
                        else highlight = '4px solid transparent';
                        row.style.borderLeft = highlight;
                    });
                });
            });
        });
    });
</script>

<?php
get_footer();
