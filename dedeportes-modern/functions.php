<?php
/**
 * Dedeportes Modern functions and definitions
 *
 * @package Dedeportes_Modern
 */

if (!defined('DEDEPORTES_VERSION')) {
	define('DEDEPORTES_VERSION', '2.24');
}

/**
 * Helper: Obtener URL del escudo del equipo
 */
function dedeportes_get_team_shield($team_name)
{
	if (empty($team_name))
		return '';

	// Normalizar nombre: minúsculas, sin acentos, espacios -> guiones
	$slug = mb_strtolower(trim($team_name), 'UTF-8');
	$slug = str_replace(
		['á', 'é', 'í', 'ó', 'ú', 'ñ', ' '],
		['a', 'e', 'i', 'o', 'u', 'n', '-'],
		$slug
	);
	// Eliminar caracteres no alfanuméricos excepto guión
	$slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

	// Las imágenes se asumen en la carpeta /img/ en la raíz del sitio
	return home_url('/img/' . $slug . '.png');
}

/**
 * Helper: Obtener abreviación del nombre del equipo para móviles
 */
function dedeportes_get_team_abbreviation($team_name)
{
	if (empty($team_name))
		return '';

	$abbreviations = [
		'Unión La Calera' => 'U. La Calera',
		'Deportes La Serena' => 'La Serena',
		'Universidad de Concepción' => 'U. de Concepción',
		'Universidad Católica' => 'U. Católica',
		'Coquimbo Unido' => 'Coquimbo',
		'Deportes Concepción' => 'D. Concepción',
		'Universidad de Chile' => 'U. de Chile',
		'Deportes Limache' => 'Limache',
		'Audax Italiano' => 'Audax',
	];

	return isset($abbreviations[$team_name]) ? $abbreviations[$team_name] : $team_name;
}

/**
 * Helper: Obtener el ID del sidebar para un equipo
 * Dado que los sidebars se registraron con nombres cortos (e.g. "Coquimbo").
 */
function dedeportes_get_team_sidebar_id($team_name)
{
	if (empty($team_name))
		return '';

	$sidebar_names = [
		'Universidad de Chile' => 'U. de Chile',
		'Deportes Iquique' => 'Iquique',
		'Unión Española' => 'U. Española',
		'Coquimbo Unido' => 'Coquimbo',
		'Audax Italiano' => 'Audax',
		'Unión La Calera' => 'La Calera',
		'Deportes Copiapó' => 'Copiapó',
		'Deportes Concepción' => 'D. Concepción',
		'Deportes La Serena' => 'La Serena',
		'Deportes Limache' => 'Limache',
		'Universidad de Concepción' => 'U. de Concepción'
	];

	$sidebar_name = isset($sidebar_names[$team_name]) ? $sidebar_names[$team_name] : $team_name;
	return 'sidebar-' . sanitize_title($sidebar_name);
}

/**
 * Basic Theme Setup
 */
function dedeportes_setup()
{
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));

	// Register Menus
	register_nav_menus(
		array(
			'primary' => esc_html__('Primary Menu', 'dedeportes-modern'),
			'footer' => esc_html__('Footer Menu', 'dedeportes-modern'),
		)
	);
}
add_action('after_setup_theme', 'dedeportes_setup');

/**
 * Register Widget Area (Sidebar Tenis)
 */
function dedeportes_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Tenis', 'dedeportes-modern'),
			'id' => 'sidebar-tenis',
			'description' => esc_html__('Agrega widgets aquí para la página de Tenis.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Liga Primera', 'dedeportes-modern'),
			'id' => 'sidebar-liga',
			'description' => esc_html__('Agrega widgets aquí para la página de Liga Primera.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Liga Ascenso', 'dedeportes-modern'),
			'id' => 'sidebar-ascenso',
			'description' => esc_html__('Agrega widgets aquí para la página de Liga de Ascenso.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Copa Chile', 'dedeportes-modern'),
			'id' => 'sidebar-copa-chile',
			'description' => esc_html__('Agrega widgets aquí para la página de Copa Chile.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Fútbol', 'dedeportes-modern'),
			'id' => 'sidebar-futbol',
			'description' => esc_html__('Agrega widgets aquí para la página de Fútbol.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Portada', 'dedeportes-modern'),
			'id' => 'sidebar-home',
			'description' => esc_html__('Agrega widgets aquí para la portada (index).', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Sudamericano Sub 20', 'dedeportes-modern'),
			'id' => 'sidebar-sudamericano-sub-20f',
			'description' => esc_html__('Agrega widgets aquí para la página de Sudamericano Sub 20 Femenino.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Copa Libertadores', 'dedeportes-modern'),
			'id' => 'sidebar-copa-libertadores',
			'description' => esc_html__('Agrega widgets aquí para la página de Copa Libertadores.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Copa Davis', 'dedeportes-modern'),
			'id' => 'sidebar-copa-davis',
			'description' => esc_html__('Agrega widgets aquí para la página de Copa Davis.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	register_sidebar(
		array(
			'name' => esc_html__('Sidebar Chile Open', 'dedeportes-modern'),
			'id' => 'sidebar-chile-open',
			'description' => esc_html__('Agrega widgets aquí para la página de Chile Open.', 'dedeportes-modern'),
			'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		)
	);

	// --- REGISTRO DE SIDEBARS POR EQUIPO (20 Equipos) ---
	$teams = [
		'Colo-Colo',
		'U. de Chile',
		'Universidad Católica',
		'Iquique',
		'O\'Higgins',
		'Palestino',
		'Everton',
		'Cobreloa',
		'U. Española',
		'Coquimbo',
		'Ñublense',
		'Huachipato',
		'Audax',
		'Cobresal',
		'La Calera',
		'Copiapó',
		'D. Concepción',
		'La Serena',
		'Limache',
		'U. de Concepción'
	];

	foreach ($teams as $team) {
		register_sidebar(
			array(
				'name' => esc_html__('Sidebar ' . $team, 'dedeportes-modern'),
				'id' => 'sidebar-' . sanitize_title($team),
				'description' => esc_html__('Widgets específicos para la página de ' . $team, 'dedeportes-modern'),
				'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h3 class="widget-title">',
				'after_title' => '</h3>',
			)
		);
	}
}
add_action('widgets_init', 'dedeportes_widgets_init');

// Register Custom Widgets
$scoreboard_widget = __DIR__ . '/inc/class-dedeportes-scoreboard-widget.php';
$tennis_widget = __DIR__ . '/inc/class-dedeportes-tennis-scoreboard-widget.php';
$performance_widget = __DIR__ . '/inc/class-dedeportes-performance-widget.php';

if (file_exists($scoreboard_widget)) {
	require_once $scoreboard_widget;
}
if (file_exists($tennis_widget)) {
	require_once $tennis_widget;
}
if (file_exists($performance_widget)) {
	require_once $performance_widget;
}

function dedeportes_register_custom_widgets()
{
	if (class_exists('Dedeportes_Scoreboard_Widget')) {
		register_widget('Dedeportes_Scoreboard_Widget');
	}
	if (class_exists('Dedeportes_Tennis_Scoreboard_Widget')) {
		register_widget('Dedeportes_Tennis_Scoreboard_Widget');
	}
	if (class_exists('Dedeportes_Performance_Widget')) {
		register_widget('Dedeportes_Performance_Widget');
	}
}
add_action('widgets_init', 'dedeportes_register_custom_widgets');

/**
 * Enqueue scripts and styles.
 */
function dedeportes_scripts()
{
	// Enqueue Google Fonts (Outfit & Inter)
	wp_enqueue_style('dedeportes-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800&display=swap', array(), null);

	// Main Stylesheet
	// Main Stylesheet
	wp_enqueue_style('dedeportes-style', get_stylesheet_uri(), array(), DEDEPORTES_VERSION);
}
add_action('wp_enqueue_scripts', 'dedeportes_scripts');

/**
 * Modify Main Query for Homepage
 * Show 8 posts per page.
 */
function dedeportes_home_query($query)
{
	if ($query->is_home() && $query->is_main_query() && !is_admin()) {
		$query->set('posts_per_page', 8);
		$query->set('ignore_sticky_posts', 1);

		// Exclude posts marked as "Hide on Home"
		$meta_query = $query->get('meta_query');
		if (!is_array($meta_query)) {
			$meta_query = array();
		}
		$meta_query[] = array(
			'key' => '_dedeportes_hide_home',
			'compare' => 'NOT EXISTS',
		);
		$query->set('meta_query', $meta_query);
	}
}
add_action('pre_get_posts', 'dedeportes_home_query');

/**
 * Meta Box: Hide on Homepage
 */
function dedeportes_add_meta_box()
{
	add_meta_box(
		'dedeportes_hide_home_meta',
		__('Opciones de Portada', 'dedeportes-modern'),
		'dedeportes_render_meta_box',
		'post',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'dedeportes_add_meta_box');

function dedeportes_render_meta_box($post)
{
	$value = get_post_meta($post->ID, '_dedeportes_hide_home', true);
	wp_nonce_field('dedeportes_save_meta_box', 'dedeportes_meta_box_nonce');
	?>
	<p>
		<label>
			<input type="checkbox" name="dedeportes_hide_home" value="yes" <?php checked($value, 'yes'); ?> />
			<strong><?php _e('Ocultar en Portada', 'dedeportes-modern'); ?></strong>
		</label>
	</p>
	<p class="description" style="font-size:0.9em;">
		<?php _e('Esta entrada no aparecerá en el Home, pero sí en categorías.', 'dedeportes-modern'); ?>
	</p>
	<?php
}

function dedeportes_save_meta_box($post_id)
{
	if (!isset($_POST['dedeportes_meta_box_nonce']))
		return;
	if (!wp_verify_nonce($_POST['dedeportes_meta_box_nonce'], 'dedeportes_save_meta_box'))
		return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;
	if (!current_user_can('edit_post', $post_id))
		return;

	if (isset($_POST['dedeportes_hide_home'])) {
		update_post_meta($post_id, '_dedeportes_hide_home', 'yes');
	} else {
		delete_post_meta($post_id, '_dedeportes_hide_home');
	}
}
add_action('save_post', 'dedeportes_save_meta_box');

/**
 * Customizer Layout & SEO Settings
 */
function dedeportes_customize_register($wp_customize)
{
	// Section: SEO Options
	$wp_customize->add_section('dedeportes_seo_options', array(
		'title' => __('Opciones SEO & Social', 'dedeportes-modern'),
		'priority' => 120,
	));

	// Setting: Social Image Fallback
	$wp_customize->add_setting('dedeportes_social_image', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));

	// Control: Image Upload
	$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'dedeportes_social_image', array(
		'label' => __('Imagen Social por Defecto', 'dedeportes-modern'),
		'description' => __('Sube una imagen (ej. pantallazo de la portada) para mostrar en redes sociales cuando el contenido no tenga imagen destacada.', 'dedeportes-modern'),
		'section' => 'dedeportes_seo_options',
		'settings' => 'dedeportes_social_image',
	)));
}
add_action('customize_register', 'dedeportes_customize_register');

/**
 * Shortcode: Top 10 Partidos con más goles
 * Uso: [top_partidos_goles]
 */
function dedeportes_top_partidos_goles_shortcode($atts)
{
	// Configuración base de datos (según script check_db.php)
	$host = 'localhost';
	$dbname = 'pjdmenag_futbol';
	$user = 'pjdmenag_futbol';
	$pass = 'n[[cY^7gvog~';

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		return "<!-- Error de conexión a la BD de fútbol: " . esc_html($e->getMessage()) . " -->";
	}

	$sql = "SELECT *, (goles_equipo + goles_rival) AS total_goles FROM partidos WHERE condicion = 'Local' AND estado = 'finalizado' AND equipo IN ('Colo Colo', 'Deportes Limache', 'Universidad Católica', 'Universidad de Chile', 'Ñublense', 'O''Higgins', 'Huachipato', 'Coquimbo Unido', 'Universidad de Concepción', 'Audax Italiano', 'Unión La Calera', 'Deportes La Serena', 'Everton', 'Palestino', 'Cobresal', 'Deportes Concepción') ORDER BY total_goles DESC LIMIT 10";

	try {
		$stmt = $pdo->query($sql);
		$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return "<!-- Error en la consulta SQL: " . esc_html($e->getMessage()) . " -->";
	}

	if (empty($matches)) {
		return "<p>No hay partidos para mostrar en esta sección.</p>";
	}

	ob_start();
	?>
	<style>
		.top-goles-wrapper {
			display: flex;
			flex-direction: column;
			gap: 1.2rem;
			margin: 2rem 0;
		}

		.top-goles-card {
			background-color: #ffffff;
			border-left: 5px solid #0056b3;
			/* Borde azul a la izquierda */
			border-radius: 4px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
			/* Sombra suave para contenedor blanco */
			padding: 1.2rem 1.5rem;
			font-family: 'Inter', sans-serif;
		}

		.top-goles-header {
			font-size: 0.75rem;
			color: #64748b;
			text-transform: uppercase;
			font-weight: 600;
			letter-spacing: 0.05em;
			margin-bottom: 0.8rem;
			display: flex;
			justify-content: space-between;
		}

		.top-goles-body {
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin-bottom: 1rem;
		}

		.top-goles-team {
			flex: 1;
			font-weight: 700;
			font-size: 1.1rem;
			color: #0f172a;
		}

		.top-goles-team.local {
			text-align: right;
			padding-right: 1rem;
		}

		.top-goles-team.visita {
			text-align: left;
			padding-left: 1rem;
		}

		.top-goles-score {
			background-color: #0f172a;
			/* Resultado resaltado en negro */
			color: #ffffff;
			font-size: 1.25rem;
			font-weight: 800;
			padding: 0.4rem 1rem;
			border-radius: 4px;
			min-width: 70px;
			text-align: center;
			letter-spacing: 0.1em;
		}

		.top-goles-footer {
			text-align: center;
		}

		.top-goles-tag {
			background-color: #e2e8f0;
			color: #334155;
			font-size: 0.75rem;
			font-weight: 700;
			padding: 0.4rem 0.8rem;
			border-radius: 20px;
			/* Etiqueta redondeada abajo */
			display: inline-block;
		}

		@media (max-width: 600px) {
			.top-goles-team {
				font-size: 0.95rem;
			}

			.top-goles-score {
				font-size: 1.1rem;
				padding: 0.3rem 0.8rem;
				min-width: 60px;
			}
		}
	</style>
	<div class="top-goles-wrapper">
		<?php foreach ($matches as $match):
			$fecha = !empty($match['fecha']) ? date('d/m/Y', strtotime($match['fecha'])) : '';
			$torneo = !empty($match['torneo']) ? $match['torneo'] : 'Torneo';
			$goles_local = isset($match['goles_equipo']) ? $match['goles_equipo'] : 0;
			$goles_visita = isset($match['goles_rival']) ? $match['goles_rival'] : 0;
			$total_goles = isset($match['total_goles']) ? $match['total_goles'] : ($goles_local + $goles_visita);
			?>
			<div class="top-goles-card">
				<div class="top-goles-header">
					<span class="top-goles-tournament"><?php echo esc_html($torneo); ?></span>
					<span class="top-goles-date"><?php echo esc_html($fecha); ?></span>
				</div>
				<div class="top-goles-body">
					<div class="top-goles-team local"><?php echo esc_html($match['equipo']); ?></div>
					<div class="top-goles-score"><?php echo esc_html($goles_local . ' - ' . $goles_visita); ?></div>
					<div class="top-goles-team visita"><?php echo esc_html($match['rival']); ?></div>
				</div>
				<div class="top-goles-footer">
					<span class="top-goles-tag"><?php echo esc_html($total_goles); ?> GOLES EN TOTAL</span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	$output = ob_get_clean();
	return $output;
}
add_shortcode('top_partidos_goles', 'dedeportes_top_partidos_goles_shortcode');

/**
 * Helper: Obtener código de país (Normalizado)
 * Excluye CHI por petición del usuario.
 */
function dedeportes_get_country_code($country_name)
{
	if (empty($country_name))
		return '';
	$country_name = mb_strtolower(trim($country_name), 'UTF-8');
	if (in_array($country_name, ['chile', 'chi']))
		return '';

	$codes = [
		'argentina' => 'ARG',
		'brasil' => 'BRA',
		'colombia' => 'COL',
		'ecuador' => 'ECU',
		'bolivia' => 'BOL',
		'peru' => 'PER',
		'perú' => 'PER',
		'paraguay' => 'PAR',
		'uruguay' => 'URU',
		'venezuela' => 'VEN'
	];

	if (isset($codes[$country_name]))
		return $codes[$country_name];

	// Fallback: primeros 3 caracteres en mayúscula
	return mb_strtoupper(mb_substr($country_name, 0, 3, 'UTF-8'), 'UTF-8');
}

/**
 * Helper: Renderizar Card de Partido (Horizontal Desktop / 3 Líneas Móvil)
 * @param array $match Datos del partido
 * @param bool $show_date Si es true muestra fecha, si es false muestra hora/estado
 */
function dedeportes_render_match_card($match, $show_date = false)
{
	// Normalizar fecha y hora
	$fecha_db = str_replace('/', '-', $match['fecha']);
	$timestamp = strtotime($fecha_db);
	$date_formatted = $timestamp ? date_i18n('j \d\e F', $timestamp) : $match['fecha'];
	$time_formatted = !empty($match['hora']) ? date('H:i', strtotime($match['hora'])) : $date_formatted;

	if ($match['estado'] === 'finalizado') {
		$time_formatted = 'Final';
	}

	$meta_text = $show_date ? $date_formatted : $time_formatted;

	// Goles / Estado (Show dash if not finished, per user request)
	if ($match['estado'] === 'finalizado') {
		$goles_l = ($match['goles_local'] !== '' && $match['goles_local'] !== null) ? $match['goles_local'] : '0';
		$goles_v = ($match['goles_visitante'] !== '' && $match['goles_visitante'] !== null) ? $match['goles_visitante'] : '0';
	} else {
		$goles_l = '-';
		$goles_v = '-';
	}

	// Países
	$pais_l = dedeportes_get_country_code(isset($match['pais_local']) ? $match['pais_local'] : '');
	$pais_v = dedeportes_get_country_code(isset($match['pais_visitante']) ? $match['pais_visitante'] : '');

	?>
	<div class="match-card">
		<!-- Layout Desktop (Original) -->
		<div class="match-card-desktop-only">
			<div class="match-card-meta">
				<div class="match-card-date"><?php echo esc_html($meta_text); ?></div>
				<div class="match-card-tournament"><?php echo esc_html($match['torneo']); ?></div>
			</div>
			<div class="match-card-teams">
				<div class="match-card-team local">
					<img src="<?php echo dedeportes_get_team_shield($match['local']); ?>" class="team-shield" alt=""
						onerror="this.style.display='none'">
					<span class="team-name">
						<span class="team-name-full"><?php echo esc_html($match['local']); ?></span>
						<span
							class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['local'])); ?></span>
						<?php if (!empty($pais_l)): ?>
							<span class="team-country"><?php echo esc_html($pais_l); ?></span>
						<?php endif; ?>
					</span>
				</div>
				<div class="match-card-team visitor">
					<img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>" class="team-shield" alt=""
						onerror="this.style.display='none'">
					<span class="team-name">
						<span class="team-name-full"><?php echo esc_html($match['visitante']); ?></span>
						<span
							class="team-name-short"><?php echo esc_html(dedeportes_get_team_abbreviation($match['visitante'])); ?></span>
						<?php if (!empty($pais_v)): ?>
							<span class="team-country"><?php echo esc_html($pais_v); ?></span>
						<?php endif; ?>
					</span>
				</div>
			</div>
			<div class="match-card-result">
				<div class="score-row"><?php echo esc_html($goles_l); ?></div>
				<div class="score-row"><?php echo esc_html($goles_v); ?></div>
			</div>
		</div>

		<!-- Layout Mobile (3 Líneas) -->
		<div class="match-card-mobile-only">
			<!-- Línea 1: Torneo y Hora/Fecha -->
			<div class="m-match-line m-match-header">
				<span class="m-match-tournament"><?php echo esc_html($match['torneo']); ?></span>
				<span class="m-match-meta"><?php echo esc_html($meta_text); ?></span>
			</div>
			<!-- Línea 2: Local + País + Goles -->
			<div class="m-match-line m-match-team-row">
				<div class="m-match-team">
					<img src="<?php echo dedeportes_get_team_shield($match['local']); ?>" class="team-shield" alt=""
						onerror="this.style.display='none'">
					<span
						class="m-team-name"><?php echo esc_html(dedeportes_get_team_abbreviation($match['local'])); ?></span>
					<?php if (!empty($pais_l)): ?>
						<span class="m-team-country"><?php echo esc_html($pais_l); ?></span>
					<?php endif; ?>
				</div>
				<div class="m-match-score"><?php echo esc_html($goles_l); ?></div>
			</div>
			<!-- Línea 3: Visita + País + Goles -->
			<div class="m-match-line m-match-team-row">
				<div class="m-match-team">
					<img src="<?php echo dedeportes_get_team_shield($match['visitante']); ?>" class="team-shield" alt=""
						onerror="this.style.display='none'">
					<span
						class="m-team-name"><?php echo esc_html(dedeportes_get_team_abbreviation($match['visitante'])); ?></span>
					<?php if (!empty($pais_v)): ?>
						<span class="m-team-country"><?php echo esc_html($pais_v); ?></span>
					<?php endif; ?>
				</div>
				<div class="m-match-score"><?php echo esc_html($goles_v); ?></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Shortcode: Mapa de Estadios con partidos HOY
 * Uso: [dedeportes_mapa_estadios]
 */
function dedeportes_mapa_estadios_shortcode()
{
	// Configuración base de datos
	$host = 'localhost';
	$dbname = 'pjdmenag_futbol';
	$user = 'pjdmenag_futbol';
	$pass = 'n[[cY^7gvog~';

	try {
		$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {
		return "<!-- Error de conexión a la BD: " . esc_html($e->getMessage()) . " -->";
	}

	// Consulta para obtener estadios con partidos HOY
	$today = current_time('Y-m-d'); // Formato YYYY-MM-DD detectado en debug
	$sql = "SELECT e.nombre, e.latitud, e.longitud, e.place_id, p.equipo, p.rival, p.hora 
            FROM estadios e 
            INNER JOIN partidos p ON e.id_estadio = p.id_estadio 
            WHERE p.fecha = :today";

	try {
		$stmt = $pdo->prepare($sql);
		$stmt->execute(['today' => $today]);
		$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		return "<!-- Error en la consulta: " . esc_html($e->getMessage()) . " -->";
	}

	// Si no hay partidos hoy, mostrar mensaje amigable
	if (empty($resultados)) {
		return '<div class="map-no-matches" style="padding: 2rem; background: #f8fafc; border-radius: 10px; text-align: center; border: 1px solid #e2e8f0; font-family: \'Inter\', sans-serif;">
                    <p style="margin:0; color: #64748b; font-weight: 500;">No hay partidos programados para hoy.</p>
                </div>';
	}

	ob_start();
	?>
	<div id="mapa-estadios"
		style="width: 100%; height: 500px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin: 2rem 0;">
	</div>

	<script>
		function initMap() {
			// Centrar el mapa en Chile inicialmente
			const centroChile = {
				lat: -33.45,
				lng: -70.66
			};
			const map = new google.maps.Map(document.getElementById("mapa-estadios"), {
				zoom: 6,
				center: centroChile,
				mapTypeControl: false,
				streetViewControl: false,
				fullscreenControl: true,
				styles: [{
					"featureType": "poi",
					"stylers": [{
						"visibility": "off"
					}]
				}]
			});

			const estadios = <?php echo json_encode($resultados); ?>;
			const bounds = new google.maps.LatLngBounds();
			const infowindow = new google.maps.InfoWindow();

			estadios.forEach(estadio => {
				const posicion = {
					lat: parseFloat(estadio.latitud),
					lng: parseFloat(estadio.longitud)
				};

				// Crear marcador
				const marker = new google.maps.Marker({
					position: posicion,
					map: map,
					title: estadio.nombre,
					animation: google.maps.Animation.DROP
				});

				// Contenido del globo de información
				const contentString = `
					<div style="padding: 10px; font-family: 'Inter', sans-serif; min-width: 200px;">
						<h3 style="margin: 0 0 8px 0; font-size: 16px; color: #0f172a; font-weight: 700;">${estadio.nombre}</h3>
						<p style="margin: 4px 0; font-size: 14px; color: #1e293b;">
							<strong>${estadio.equipo} vs ${estadio.rival}</strong>
						</p>
						<p style="margin: 4px 0 12px 0; font-size: 13px; color: #64748b;">
							Hora: ${estadio.hora}
						</p>
						<a href="https://www.google.com/maps/dir/?api=1&destination_place_id=${estadio.place_id}" 
						   target="_blank" 
						   style="display: block; text-align: center; padding: 8px 12px; background: #0056b3; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 13px; transition: background 0.3s;">
						   📍 Cómo llegar (GPS)
						</a>
					</div>
				`;

				marker.addListener("click", () => {
					infowindow.setContent(contentString);
					infowindow.open(map, marker);
				});

				bounds.extend(posicion);
			});

			// Ajustar el mapa para que se vean todos los estadios del día
			if (estadios.length > 1) {
				map.fitBounds(bounds);
			} else if (estadios.length === 1) {
				map.setCenter(bounds.getCenter());
				map.setZoom(15);
			}
		}
	</script>
	<!-- REEMPLAZA "TU_API_KEY" POR TU LLAVE DE GOOGLE MAPS ABAJO -->
	<script src="https://maps.googleapis.com/maps/api/js?key=TU_API_KEY&callback=initMap" async defer></script>
	<?php
	return ob_get_clean();
}
add_shortcode('dedeportes_mapa_estadios', 'dedeportes_mapa_estadios_shortcode');
