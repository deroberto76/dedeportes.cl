<?php
/**
 * Dedeportes Modern functions and definitions
 *
 * @package Dedeportes_Modern
 */

if (!defined('DEDEPORTES_VERSION')) {
	define('DEDEPORTES_VERSION', '1.24.2');
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
}
add_action('widgets_init', 'dedeportes_widgets_init');

// Register Custom Widgets
require_once get_template_directory() . '/inc/class-dedeportes-scoreboard-widget.php';
require_once get_template_directory() . '/inc/class-dedeportes-tennis-scoreboard-widget.php';

function dedeportes_register_custom_widgets()
{
	register_widget('Dedeportes_Scoreboard_Widget');
	register_widget('Dedeportes_Tennis_Scoreboard_Widget');
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
		<?php _e('Esta entrada no aparecerá en el Home, pero sí en categorías.', 'dedeportes-modern'); ?></p>
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
