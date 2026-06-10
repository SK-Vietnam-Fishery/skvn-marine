<?php
/**
 * Typography CSS injection for SKVN Marine.
 *
 * Reads the option saved by the plugin typography-settings module and injects
 * CSS custom properties for palette and heading scale via wp_add_inline_style.
 *
 * This file owns only CSS delivery. Admin UI and option write belong to:
 * wp-content/plugins/skvn-marine-blocks/modules/typography-settings/typography-settings.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared option key — must match SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION in plugin.
 */
const SKVN_MARINE_TYPOGRAPHY_OPTION = 'skvn_typography';

add_action( 'wp_enqueue_scripts',    'skvn_marine_inject_typography_css', 20 );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_inject_typography_css_editor', 20 );

// ---------------------------------------------------------------------------
// Defaults and getter
// ---------------------------------------------------------------------------

/**
 * Default typography values.
 *
 * Must stay in sync with skvn_marine_blocks_get_default_typography() in plugin.
 *
 * @return array<string,mixed>
 */
function skvn_marine_get_default_typography() {
	return array(
		'palette' => array(
			'primary' => '#0369a1',
			'accent'  => '#0d9488',
			'surface' => '#eaf7ff',
			'text'    => '#334155',
		),
		'heading' => array(
			'h1' => array( 'size' => '3rem',     'weight' => '800' ),
			'h2' => array( 'size' => '2.25rem',  'weight' => '700' ),
			'h3' => array( 'size' => '1.875rem', 'weight' => '600' ),
			'h4' => array( 'size' => '1.5rem',   'weight' => '600' ),
		),
	);
}

/**
 * Get saved typography settings deep-merged with defaults.
 *
 * @return array<string,mixed>
 */
function skvn_marine_get_typography() {
	$saved    = get_option( SKVN_MARINE_TYPOGRAPHY_OPTION, array() );
	$defaults = skvn_marine_get_default_typography();

	if ( ! is_array( $saved ) ) {
		return $defaults;
	}

	if ( isset( $saved['palette'] ) && is_array( $saved['palette'] ) ) {
		$defaults['palette'] = array_merge( $defaults['palette'], $saved['palette'] );
	}

	foreach ( array( 'h1', 'h2', 'h3', 'h4' ) as $level ) {
		if ( isset( $saved['heading'][ $level ] ) && is_array( $saved['heading'][ $level ] ) ) {
			$defaults['heading'][ $level ] = array_merge(
				$defaults['heading'][ $level ],
				$saved['heading'][ $level ]
			);
		}
	}

	return $defaults;
}

// ---------------------------------------------------------------------------
// CSS builder
// ---------------------------------------------------------------------------

/**
 * Build the :root CSS custom properties string from saved settings.
 *
 * @return string CSS to inject.
 */
function skvn_marine_build_typography_css() {
	$t = skvn_marine_get_typography();
	$p = $t['palette'];
	$h = $t['heading'];

	return sprintf(
		':root {
	--skvn-color-primary: %1$s;
	--skvn-color-accent:  %2$s;
	--skvn-color-surface: %3$s;
	--skvn-color-text:    %4$s;
	--skvn-h1-size: %5$s; --skvn-h1-weight: %6$s;
	--skvn-h2-size: %7$s; --skvn-h2-weight: %8$s;
	--skvn-h3-size: %9$s; --skvn-h3-weight: %10$s;
	--skvn-h4-size: %11$s; --skvn-h4-weight: %12$s;
}',
		esc_attr( $p['primary'] ),
		esc_attr( $p['accent'] ),
		esc_attr( $p['surface'] ),
		esc_attr( $p['text'] ),
		esc_attr( $h['h1']['size'] ),   esc_attr( $h['h1']['weight'] ),
		esc_attr( $h['h2']['size'] ),   esc_attr( $h['h2']['weight'] ),
		esc_attr( $h['h3']['size'] ),   esc_attr( $h['h3']['weight'] ),
		esc_attr( $h['h4']['size'] ),   esc_attr( $h['h4']['weight'] )
	);
}

// ---------------------------------------------------------------------------
// Inject — frontend
// ---------------------------------------------------------------------------

/**
 * Inject typography CSS on the frontend after skvn-marine-style.
 *
 * @return void
 */
function skvn_marine_inject_typography_css() {
	if ( ! wp_style_is( 'skvn-marine-style', 'enqueued' ) ) {
		return;
	}

	wp_add_inline_style( 'skvn-marine-style', skvn_marine_build_typography_css() );
}

// ---------------------------------------------------------------------------
// Inject — block editor
// ---------------------------------------------------------------------------

/**
 * Inject typography CSS into the block editor.
 *
 * Uses a virtual style handle so the injection does not depend on
 * the exact handle WordPress assigns to add_editor_style().
 *
 * @return void
 */
function skvn_marine_inject_typography_css_editor() {
	wp_register_style( 'skvn-marine-typography-editor', false, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
	wp_enqueue_style( 'skvn-marine-typography-editor' );
	wp_add_inline_style( 'skvn-marine-typography-editor', skvn_marine_build_typography_css() );
}
