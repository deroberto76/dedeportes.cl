<?php
/**
 * Template Name: Plantilla Copa Sudamericana
 * Description: Page template for Copa Sudamericana layout. Matches slug "copa-sudamericana".
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
            WHERE torneo = 'Copa Sudamericana' 
              AND TRIM(grupo) = :grupo 
              AND (estado != 'por jugar' OR estado IS NULL)
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

// Setup Custom Pagination for News
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if (get_query_var('page')) {
    $paged = get_query_var('page');
}

$args_news = array(
    'category_name' => 'copa-sudamericana',
    'posts_per_page' => 8,
    'paged' => $paged
);
$sudamericana_query = new WP_Query($args_news);
?>

<main id="primary" class="site-main">
    <div class="container" style="padding-top: 2rem;">

        <!-- Header de la Página -->
        <header class="page-header" style="margin-bottom: 3rem; text-align: center;">
            <h1 class="page-title" style="font-size: 2.5rem; margin-bottom: 0.5rem;">Copa CONMEBOL Sudamericana</h1>
            <p class="text-muted">Tablas de posiciones y noticias actualizadas.</p>
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
                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 2rem; margin-bottom: 3rem;">
                        <?php foreach ($grupos as $g): ?>
                            <?php if (!empty($standings_por_grupo[$g])): ?>
                                <div class="sidebar-widget"
                                    style="padding: 0; overflow: hidden; border: 1px solid var(--border); box-shadow: var(--shadow-sm); border-radius: 8px; background-color: var(--card-bg);">
                                    <h2
                                        style="padding: 1rem 1.5rem; background: var(--surface); margin: 0; font-size: 1.25rem; border-bottom: 1px solid var(--border); color: var(--primary);">
                                        Grupo
                                        <?php echo $g; ?>
                                    </h2>
                                    <div class="widget-content">
                                        <table class="ranking-table" style="width: 100%; margin-bottom: 0;">
                                            <thead>
                                                <tr>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="0">
                                                        Pos</th>
                                                    <th style="padding: 0.75rem;" class="sortable" data-col="1">Equipo</th>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="2">
                                                        PJ</th>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="3">
                                                        G</th>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="4">
                                                        E</th>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="5">
                                                        P</th>
                                                    <th style="padding: 0.75rem; text-align: center;" class="sortable" data-col="8">
                                                        Dif</th>
                                                    <th style="padding: 0.75rem; text-align: center; background: var(--surface);"
                                                        class="sortable" data-col="9">Pts</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $pos = 1;
                                                foreach ($standings_por_grupo[$g] as $row):
                                                    // Highlighting: 1 Blue, 2 Celeste
                                                    $highlight = 'style="border-left: 4px solid transparent;"';
                                                    if ($pos == 1)
                                                        $highlight = 'style="border-left: 4px solid #2563eb;"';
                                                    else if ($pos == 2)
                                                        $highlight = 'style="border-left: 4px solid #38bdf8;"';
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <div
                        style="margin-bottom: 4rem; font-size: 0.95rem; color: var(--text-main); text-align: left; background: var(--surface); padding: 1.5rem; border-radius: 8px; border-left: 4px solid var(--primary);">
                        <p style="margin-bottom: 0;">
                            <span
                                style="display:inline-block; width:12px; height:12px; background:#2563eb; margin-right:5px;"></span>
                            Clasificados a Octavos de Final
                            <span
                                style="display:inline-block; width:12px; height:12px; background:#38bdf8; margin-left:15px; margin-right:5px;"></span>
                            Play-offs Octavos de Final
                        </p>
                    </div>

                <?php endif; ?>

                <!-- SECCIÓN NOTICIAS -->
                <section class="news-section">
                    <h2 class="section-category-title"
                        style="border-left: 4px solid var(--primary); padding-left: 1rem; margin-bottom: 2rem;">Últimas
                        Noticias</h2>
                    <?php if ($sudamericana_query->have_posts()): ?>
                        <div class="posts-list"
                            style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                            <?php while ($sudamericana_query->have_posts()):
                                $sudamericana_query->the_post(); ?>
                                <article id="post-<?php the_ID(); ?>" <?php post_class('post-list-item'); ?> style="background:
                            var(--card-bg); border: 1px solid var(--border); border-radius: 8px; overflow: hidden;
                            display: flex; flex-direction: column;">
                                    <?php if (has_post_thumbnail()): ?>
                                        <div class="post-thumbnail" style="height: 180px; overflow: hidden;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('medium_large', ['style' => 'width:100%; height:100%; object-fit:cover;']); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="post-content" style="padding: 1.5rem; flex-grow: 1;">
                                        <h3 class="post-title" style="margin-top: 0; font-size: 1.1rem;">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_title(); ?>
                                            </a>
                                        </h3>
                                        <div class="post-excerpt" style="font-size: 0.9rem; opacity: 0.8;">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                                        </div>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>

                        <!-- Paginación -->
                        <div class="load-more-container" style="margin-top: 3rem; text-align: center;">
                            <?php
                            $temp_query = $wp_query;
                            $wp_query = $sudamericana_query;
                            $next_link = get_next_posts_link('Ver más noticias', $sudamericana_query->max_num_pages);
                            if ($next_link) {
                                echo str_replace('<a', '<a class="btn btn-large"', $next_link);
                            }
                            $wp_query = $temp_query;
                            wp_reset_postdata();
                            ?>
                        </div>
                    <?php else: ?>
                        <p>No se encontraron noticias en esta categoría.</p>
                    <?php endif; ?>
                </section>

            </div>

        </div> <!-- .layout-grid -->
    </div>
</main>

<style>
    @media (max-width: 600px) {
        .standings-container {
            grid-template-columns: 1fr !important;
        }

        .ranking-table th,
        .ranking-table td {
            padding: 0.5rem !important;
            font-size: 0.8rem;
        }
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background-color: rgba(0, 0, 0, 0.05);
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
                        if (pos == 1) color = '#2563eb';
                        else if (pos == 2) color = '#38bdf8';
                        row.style.borderLeft = '4px solid ' + color;
                    });
                });
            });
        });
    });
</script>

<?php
get_footer();
