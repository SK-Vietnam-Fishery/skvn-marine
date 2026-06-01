<?php
/**
 * Page-level display controls for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_PAGE_DISPLAY_BOOLEAN_META = array(
	'_skvn_hide_header',
	'_skvn_hide_footer',
	'_skvn_hide_title',
	'_skvn_full_width_canvas',
);
const SKVN_MARINE_PAGE_DISPLAY_PRESET_META = '_skvn_page_display_preset';
const SKVN_MARINE_PAGE_DISPLAY_PRESETS     = array(
	'default',
	'skvn_landing_canvas',
	'custom',
);

add_action( 'init', 'skvn_marine_register_page_display_meta' );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_enqueue_page_display_controls' );
add_filter( 'body_class', 'skvn_marine_page_display_body_classes' );
add_filter( 'generate_sidebar_layout', 'skvn_marine_page_display_sidebar_layout' );

/**
 * Register page display meta for editor sidebar controls.
 *
 * @return void
 */
function skvn_marine_register_page_display_meta() {
	foreach ( SKVN_MARINE_PAGE_DISPLAY_BOOLEAN_META as $meta_key ) {
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

	register_post_meta(
		'page',
		SKVN_MARINE_PAGE_DISPLAY_PRESET_META,
		array(
			'type'              => 'string',
			'single'            => true,
			'default'           => 'default',
			'show_in_rest'      => true,
			'sanitize_callback' => 'skvn_marine_sanitize_page_display_preset',
			'auth_callback'     => 'skvn_marine_can_edit_page_display_meta',
		)
	);
}

/**
 * Sanitize the page display preset.
 *
 * @param mixed $value Raw preset value.
 * @return string
 */
function skvn_marine_sanitize_page_display_preset( $value ) {
	$value = is_string( $value ) ? sanitize_key( $value ) : 'default';

	if ( ! in_array( $value, SKVN_MARINE_PAGE_DISPLAY_PRESETS, true ) ) {
		return 'default';
	}

	return $value;
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
		if ( skvn_marine_page_uses_display_feature( $post_id, $meta_key ) ) {
			$classes[] = $class_name;
		}
	}

	return $classes;
}

/**
 * Force the GeneratePress sidebar layout when the SKVN canvas owns page width.
 *
 * @param string $layout GeneratePress sidebar layout.
 * @return string
 */
function skvn_marine_page_display_sidebar_layout( $layout ) {
	if ( ! is_page() ) {
		return $layout;
	}

	$post_id = get_queried_object_id();

	if ( $post_id && skvn_marine_page_uses_display_feature( $post_id, '_skvn_full_width_canvas' ) ) {
		return 'no-sidebar';
	}

	return $layout;
}

/**
 * Check whether a display feature is enabled directly or by preset.
 *
 * @param int    $post_id Post ID.
 * @param string $meta_key Display feature meta key.
 * @return bool
 */
function skvn_marine_page_uses_display_feature( $post_id, $meta_key ) {
	if ( (bool) get_post_meta( $post_id, $meta_key, true ) ) {
		return true;
	}

	$preset   = get_post_meta( $post_id, SKVN_MARINE_PAGE_DISPLAY_PRESET_META, true );
	$features = skvn_marine_get_page_display_preset_features( $preset );

	return ! empty( $features[ $meta_key ] );
}

/**
 * Get display features enabled by a preset.
 *
 * @param string $preset Page display preset.
 * @return array<string,bool>
 */
function skvn_marine_get_page_display_preset_features( $preset ) {
	if ( 'skvn_landing_canvas' !== $preset ) {
		return array();
	}

	return array(
		'_skvn_hide_title'        => true,
		'_skvn_full_width_canvas' => true,
	);
}
