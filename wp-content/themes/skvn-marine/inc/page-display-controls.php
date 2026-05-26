<?php
/**
 * Page-level display controls for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_PAGE_DISPLAY_META = array(
	'_skvn_hide_header',
	'_skvn_hide_footer',
	'_skvn_hide_title',
	'_skvn_full_width_canvas',
);

add_action( 'init', 'skvn_marine_register_page_display_meta' );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_enqueue_page_display_controls' );
add_filter( 'body_class', 'skvn_marine_page_display_body_classes' );

/**
 * Register page display meta for editor sidebar controls.
 *
 * @return void
 */
function skvn_marine_register_page_display_meta() {
	foreach ( SKVN_MARINE_PAGE_DISPLAY_META as $meta_key ) {
		register_post_meta(
			'page',
			$meta_key,
			array(
				'type'              => 'boolean',
				'single'            => true,
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => 'skvn_marine_can_edit_page_display_meta',
			)
		);
	}
}

/**
 * Check edit permission for page display meta.
 *
 * @param bool   $allowed Whether the user can edit the meta value.
 * @param string $meta_key Meta key.
 * @param int    $post_id Post ID.
 * @return bool
 */
function skvn_marine_can_edit_page_display_meta( $allowed, $meta_key, $post_id ) {
	unset( $allowed, $meta_key );

	return current_user_can( 'edit_post', $post_id );
}

/**
 * Enqueue the block editor sidebar controls.
 *
 * @return void
 */
function skvn_marine_enqueue_page_display_controls() {
	$screen = get_current_screen();

	if ( ! $screen || 'page' !== $screen->post_type ) {
		return;
	}

	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$script_path = $theme_dir . '/assets/js/page-display-controls.js';
	$style_path  = $theme_dir . '/assets/css/page-display-controls-editor.css';

	if ( ! file_exists( $script_path ) ) {
		return;
	}

	wp_enqueue_script(
		'skvn-marine-page-display-controls',
		$theme_uri . '/assets/js/page-display-controls.js',
		array(
			'wp-components',
			'wp-compose',
			'wp-data',
			'wp-edit-post',
			'wp-element',
			'wp-i18n',
			'wp-plugins',
		),
		filemtime( $script_path ),
		true
	);

	if ( file_exists( $style_path ) ) {
		wp_enqueue_style(
			'skvn-marine-page-display-controls',
			$theme_uri . '/assets/css/page-display-controls-editor.css',
			array( 'wp-components' ),
			filemtime( $style_path )
		);
	}
}

/**
 * Add frontend body classes based on page display meta.
 *
 * @param array<int,string> $classes Body classes.
 * @return array<int,string>
 */
function skvn_marine_page_display_body_classes( $classes ) {
	if ( ! is_page() ) {
		return $classes;
	}

	$post_id = get_queried_object_id();

	if ( ! $post_id ) {
		return $classes;
	}

	$class_map = array(
		'_skvn_hide_header'       => 'skvn-hide-header',
		'_skvn_hide_footer'       => 'skvn-hide-footer',
		'_skvn_hide_title'        => 'skvn-hide-title',
		'_skvn_full_width_canvas' => 'skvn-full-width-canvas',
	);

	foreach ( $class_map as $meta_key => $class_name ) {
		if ( (bool) get_post_meta( $post_id, $meta_key, true ) ) {
			$classes[] = $class_name;
		}
	}

	return $classes;
}
