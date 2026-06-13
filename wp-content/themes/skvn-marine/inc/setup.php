<?php
/**
 * Theme setup for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'skvn_marine_setup' );
add_action( 'init', 'skvn_marine_register_pattern_categories' );

/**
 * Register theme supports and menus.
 *
 * @return void
 */
function skvn_marine_setup() {
	load_child_theme_textdomain( 'skvn-marine', get_stylesheet_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'editor-styles' );

	add_editor_style( 'style.css' );

	register_nav_menus(
		array(
			'primary' => esc_html__( 'Primary Menu', 'skvn-marine' ),
			'footer'  => esc_html__( 'Footer Menu', 'skvn-marine' ),
		)
	);
}

/**
 * Register the theme pattern category used by SKVN page compositions.
 *
 * @return void
 */
function skvn_marine_register_pattern_categories() {
	register_block_pattern_category(
		'skvn-marine',
		array(
			'label' => esc_html__( 'SKVN Marine', 'skvn-marine' ),
		)
	);
}
