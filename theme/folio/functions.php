<?php
/**
 * Folio theme setup. Content types and settings come from the Folio Core plugin; this theme only shows them.
 *
 * @package folio
 */

defined( 'ABSPATH' ) || exit;

require get_template_directory() . '/inc/fallback.php';
require get_template_directory() . '/inc/template-tags.php';

add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 240, 'flex-height' => true, 'flex-width' => true ) );
		add_theme_support( 'editor-styles' );
		add_editor_style( array( 'assets/css/fonts.css', 'assets/css/editor.css' ) );
		add_post_type_support( 'page', 'excerpt' );

		register_nav_menus(
			array(
				'primary' => __( 'Header menu', 'folio' ),
				'footer'  => __( 'Footer menu', 'folio' ),
			)
		);

		add_image_size( 'folio-card', 1200, 900, true );
		add_image_size( 'folio-wide', 1920, 1200, false );
		add_image_size( 'folio-avatar', 160, 160, true );
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$dir = get_template_directory();
		wp_enqueue_style( 'folio-fonts', get_theme_file_uri( 'assets/css/fonts.css' ), array(), (string) filemtime( $dir . '/assets/css/fonts.css' ) );
		wp_enqueue_style( 'folio', get_theme_file_uri( 'assets/css/site.css' ), array( 'folio-fonts' ), (string) filemtime( $dir . '/assets/css/site.css' ) );
		wp_enqueue_script( 'folio', get_theme_file_uri( 'assets/js/site.js' ), array(), (string) filemtime( $dir . '/assets/js/site.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
);

// Colours from Portfolio Settings, the light/dark choice before the page paints (no flash), and the font preloads.
add_action(
	'wp_head',
	function () {
		$accent  = folio_opt( 'accent', '#6c4cf5' );
		$accent2 = folio_opt( 'accent2', '#ff6a3d' );
		$mode    = folio_opt( 'mode', 'system' );
		foreach ( array( 'space-grotesk-latin-wght-normal.woff2', 'inter-latin-wght-normal.woff2' ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( get_theme_file_uri( 'assets/fonts/' . $font ) ) );
		}
		printf( "<style id=\"folio-colors\">:root{--accent:%s;--accent-2:%s}</style>\n", esc_attr( $accent ? $accent : '#6c4cf5' ), esc_attr( $accent2 ? $accent2 : '#ff6a3d' ) );
		// Saved choice > site default > device setting.
		printf(
			"<script>(function(){var d=document.documentElement,m=%s,s=null;try{s=localStorage.getItem('folio-theme')}catch(e){}var t=s||(m==='system'?(matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light'):m);d.setAttribute('data-theme',t);d.classList.add('js')})();</script>\n",
			wp_json_encode( in_array( $mode, array( 'light', 'dark', 'system' ), true ) ? $mode : 'system' )
		);
		if ( ! has_site_icon() ) {
			printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( get_theme_file_uri( 'assets/images/favicon.svg' ) ) );
		}
	},
	1
);

add_action(
	'init',
	function () {
		// Emoji scripts are not needed and cost a request on every page.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
	}
);

// Keep the WordPress version out of the page source.
remove_action( 'wp_head', 'wp_generator' );

// "Read more" style excerpts.
add_filter( 'excerpt_length', fn() => 26 );
add_filter( 'excerpt_more', fn() => '...' );

// Archive titles without the "Archives:" / "Category:" prefix.
add_filter( 'get_the_archive_title_prefix', '__return_empty_string' );

// Body classes for the header style on pages with a dark hero.
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'is-home';
		}
		return $classes;
	}
);
