<?php
/**
 * Core Control bootstrap for SKVN Marine Blocks.
 *
 * Registers the skvn_core_controls option, the wp-admin submenu page,
 * and injects the enabled flags into the Gutenberg editor as
 * window.skvnCoreControls so JS features can gate themselves at runtime.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/registry.php';
require_once __DIR__ . '/admin-page.php';

const SKVN_MARINE_BLOCKS_CORE_CONTROLS_OPTION = 'skvn_core_controls';

// Load feature PHP adapters that are enabled.
add_action( 'init', 'skvn_marine_blocks_load_core_control_features', 1 );

add_action( 'admin_menu', 'skvn_marine_blocks_core_control_menu' );
add_action( 'admin_init', 'skvn_marine_blocks_register_core_control_settings' );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_blocks_core_control_inject_config' );

/**
 * Register the Core Control submenu page under SKVN Marine.
 *
 * @return void
 */
function skvn_marine_blocks_core_control_menu() {
	add_submenu_page(
		'skvn-marine',
		esc_html__( 'SKVN Core Control', 'skvn-marine-blocks' ),
		esc_html__( 'Core Control', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine-core-control',
		'skvn_marine_blocks_render_core_control_page'
	);
}

/**
 * Register the skvn_core_controls option with sanitization.
 *
 * @return void
 */
function skvn_marine_blocks_register_core_control_settings() {
	register_setting(
		'skvn_core_controls_group',
		SKVN_MARINE_BLOCKS_CORE_CONTROLS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'skvn_marine_blocks_sanitize_core_controls',
			'default'           => skvn_marine_blocks_get_core_controls_defaults(),
		)
	);
}

/**
 * Sanitize the core controls option — accept only known boolean keys.
 *
 * @param mixed $value Raw submitted value.
 * @return array<string,bool>
 */
function skvn_marine_blocks_sanitize_core_controls( $value ): array {
	$registry = skvn_marine_blocks_core_control_registry();
	$defaults = skvn_marine_blocks_get_core_controls_defaults();
	$input    = is_array( $value ) ? $value : array();
	$result   = array();

	foreach ( $registry as $id => $feature ) {
		$result[ $id ] = isset( $input[ $id ] ) && '1' === (string) $input[ $id ];
	}

	return $result;
}

/**
 * Inject enabled feature flags into the Gutenberg editor as window.skvnCoreControls.
 *
 * @return void
 */
function skvn_marine_blocks_core_control_inject_config() {
	if ( ! wp_script_is( 'skvn-marine-blocks-editor', 'registered' ) ) {
		return;
	}

	$enabled = skvn_marine_blocks_get_core_controls();
	$json    = wp_json_encode( $enabled );

	wp_add_inline_script(
		'skvn-marine-blocks-editor',
		'window.skvnCoreControls = ' . $json . ';',
		'before'
	);
}

/**
 * Get the current core controls state, merged with defaults for unknown keys.
 *
 * @return array<string,bool>
 */
function skvn_marine_blocks_get_core_controls(): array {
	$defaults = skvn_marine_blocks_get_core_controls_defaults();
	$stored   = get_option( SKVN_MARINE_BLOCKS_CORE_CONTROLS_OPTION, array() );

	if ( ! is_array( $stored ) ) {
		return $defaults;
	}

	return array_merge( $defaults, array_intersect_key( $stored, $defaults ) );
}

/**
 * Load PHP feature adapters for enabled Core Control features.
 *
 * @return void
 */
function skvn_marine_blocks_load_core_control_features() {
	$enabled = skvn_marine_blocks_get_core_controls();

	if ( ! empty( $enabled['button_hover'] ) ) {
		require_once __DIR__ . '/features/button-hover.php';
	}
}
