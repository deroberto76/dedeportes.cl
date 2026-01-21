<?php
/**
 * Dedeportes Modern functions and definitions
 */

if ( ! function_exists( 'dedeportes_modern_setup' ) ) :
	function dedeportes_modern_setup() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Let WordPress manage the document title.
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails on posts and pages.
		add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 1200, 630, true ); // Full HD crop

		// Register Navigation Menus
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary Menu', 'dedeportes-modern' ),
            'footer'  => esc_html__( 'Footer Menu', 'dedeportes-modern' ),
		) );

		// Html5 support
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
            'style',
            'script'
		) );
	}
endif;
add_action( 'after_setup_theme', 'dedeportes_modern_setup' );

/**
 * Enqueue scripts and styles.
 */
function dedeportes_modern_scripts() {
	wp_enqueue_style( 'dedeportes-modern-style', get_stylesheet_uri(), array(), '1.0.0' );
    
    // Enqueue Google Fonts (Outfit & Inter)
    wp_enqueue_style( 'dedeportes-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;700;800&display=swap', array(), null );
}
add_action( 'wp_enqueue_scripts', 'dedeportes_modern_scripts' );

/**
 * Custom excerpt length
 */
function dedeportes_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'dedeportes_custom_excerpt_length', 999 );
?>
