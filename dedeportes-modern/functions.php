<?php
/**
 * Dedeportes Modern functions and definitions
 *
 * @package Dedeportes_Modern
 */

if (!defined('DEDEPORTES_VERSION')) {
	define('DEDEPORTES_VERSION', '1.1.0');
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
 * Enqueue scripts and styles.
 */
function dedeportes_scripts()
{
	// Enqueue Google Fonts (Outfit & Inter)
	wp_enqueue_style('dedeportes-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@700;800&display=swap', array(), null);

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
	}
}
add_action('pre_get_posts', 'dedeportes_home_query');
