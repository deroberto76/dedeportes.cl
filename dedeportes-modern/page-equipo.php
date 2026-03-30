<?php
/**
 * Template Name: Plantilla Equipo
 * Description: Muestra todos los partidos de un equipo específico y carga su sidebar correspondiente.
 */

get_header();

$team_name = get_the_title();
$team_slug = sanitize_title($team_name);

// Configuración de la Base de Datos
$host = 'localhost';
$dbname = 'pjdmenag_futbol';
$user = 'pjdmenag_futbol';
$pass = 'n[[cY^7gvog~';

$matches = [];
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    // Buscamos partidos donde el equipo sea local o rival
    $stmt = $pdo->prepare("SELECT * FROM partidos WHERE equipo = ? OR rival = ? ORDER BY id DESC LIMIT 100");
    $stmt->execute([$team_name, $team_name]);
    $raw_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $seen_matches = [];
    foreach ($raw_data as $m) {
        $fecha_raw = trim($m['fecha']);
        $timestamp = 0;

        $try_formats = ['d/m/Y', 'd-m-Y', 'Y-m-d', 'j/n/Y', 'j-n-Y'];
        foreach ($try_formats as $fmt) {
            $dt = DateTime::createFromFormat($fmt, $fecha_raw);
            if ($dt !== false) {
                if ((int) $dt->format('Y') < 100) {
                    $dt->setDate(2000 + (int) $dt->format('Y'), (int) $dt->format('m'), (int) $dt->format('d'));
                }
                $timestamp = $dt->getTimestamp();
                break;
            }
        }
        if (!$timestamp) {
            $fecha_norm = str_replace('/', '-', $fecha_raw);
            $timestamp = strtotime($fecha_norm);
        }

        $teams_key = [$m['equipo'], $m['rival']];
        sort($teams_key);
        $match_key = $fecha_raw . '_' . $teams_key[0] . '_' . $teams_key[1];

        $cond = isset($m['condicion']) ? strtolower(trim($m['condicion'])) : '';
        $is_away = in_array($cond, ['visitante', 'v', 'visita', 'visiting']);
        $is_local = in_array($cond, ['local', 'l', 'casa', 'home']);

        $m_processed = [
            'id' => (int) $m['id'],
            'timestamp' => (int) $timestamp,
            'fecha' => $fecha_raw,
            'torneo' => $m['torneo'],
            'local' => $is_away ? $m['rival'] : $m['equipo'],
            'visitante' => $is_away ? $m['equipo'] : $m['rival'],
            'goles_local' => $is_away ? $m['goles_rival'] : $m['goles_equipo'],
            'goles_visitante' => $is_away ? $m['goles_equipo'] : $m['goles_rival'],
            'is_local_row' => $is_local
        ];

        if (!isset($seen_matches[$match_key])) {
            $matches[] = $m_processed;
            $seen_matches[$match_key] = count($matches) - 1;
        } else {
            $idx = $seen_matches[$match_key];
            if ($is_local && !$matches[$idx]['is_local_row']) {
                $matches[$idx] = $m_processed;
            }
        }
    }

    usort($matches, function ($a, $b) {
        if ($a['timestamp'] !== $b['timestamp'])
            return ($b['timestamp'] - $a['timestamp']);
        return ($b['id'] - $a['id']);
    });
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<main id="primary" class="site-main team-page">
    <div class="container" style="padding-top: 2rem;">
        <div class="layout-grid">

            <div class="layout-main">
                <header class="team-header u-mb-4">
                    <h1 class="page-title">Partidos de
                        <?php echo esc_html($team_name); ?>
                    </h1>
                </header>

                <section class="team-matches">
                    <h2 class="section-category-title">Historial Reciente</h2>
                    <div class="sidebar-widget" style="padding: 0; overflow: hidden; border: 1px solid var(--border);">
                        <div class="widget-content">
                            <?php if (!empty($matches)): ?>
                                <div class="match-cards-list">
                                    <?php foreach ($matches as $match): ?>
                                        <div class="match-card">
                                            <div class="match-card-header">
                                                <span class="match-date">
                                                    <?php echo esc_html($match['fecha']); ?>
                                                </span>
                                                <span class="match-tournament">
                                                    <?php echo esc_html($match['torneo']); ?>
                                                </span>
                                            </div>
                                            <div class="match-card-teams">
                                                <div class="match-card-team local">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['local']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span class="team-name-full">
                                                            <?php echo esc_html($match['local']); ?>
                                                        </span>
                                                        <span class="team-name-short">
                                                            <?php echo esc_html(dedeportes_get_team_abbreviation($match['local'])); ?>
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="match-card-team visitor">
                                                    <img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>"
                                                        class="team-shield" alt="" onerror="this.style.display='none'">
                                                    <span class="team-name">
                                                        <span class="team-name-full">
                                                            <?php echo esc_html($match['visitante']); ?>
                                                        </span>
                                                        <span class="team-name-short">
                                                            <?php echo esc_html(dedeportes_get_team_abbreviation($match['visitante'])); ?>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="match-card-result">
                                                <div class="score-row">
                                                    <span class="score-local">
                                                        <?php echo esc_html($match['goles_local'] ?? '-'); ?>
                                                    </span>
                                                    <span class="score-divider">-</span>
                                                    <span class="score-visitor">
                                                        <?php echo esc_html($match['goles_visitante'] ?? '-'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p style="padding: 2rem; text-align: center; opacity: 0.7;">
                                    No se encontraron registros de partidos recientes para el equipo <strong>
                                        <?php echo esc_html($team_name); ?>
                                    </strong>.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="layout-sidebar">
                <?php
                $sidebar_id = 'sidebar-' . $team_slug;
                if (is_active_sidebar($sidebar_id)) {
                    dynamic_sidebar($sidebar_id);
                } else {
                    // Sidebar principal por defecto si no hay nada en el del equipo
                    if (is_active_sidebar('sidebar-1')) {
                        dynamic_sidebar('sidebar-1');
                    }
                }
                ?>
            </aside>

        </div>
    </div>
</main>

<?php get_footer(); ?>