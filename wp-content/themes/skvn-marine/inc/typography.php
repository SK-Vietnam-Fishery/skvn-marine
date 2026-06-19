<?php
/**
 * Typography CSS injection for SKVN Marine.
 *
 * Reads the option saved by the plugin typography-settings module and injects
 * CSS custom properties for palette and heading scale via wp_add_inline_style.
 *
 * This file owns only CSS delivery. Admin UI and option write belong to:
 * wp-content/plugins/skvn-marine-blocks/modules/typography-settings/typography-settings.php
 *
 * Scope contract: docs/decisions/typography-scope-and-font-loading.md
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared option key — must match SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION in plugin.
 */
const SKVN_MARINE_TYPOGRAPHY_OPTION = 'skvn_typography';

add_action( 'wp_enqueue_scripts', 'skvn_marine_inject_typography_css', 20 );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_inject_typography_css_editor', 20 );

// ---------------------------------------------------------------------------
// Scope helper (shared with inc/customizer.php)
// ---------------------------------------------------------------------------

/**
 * CSS selector root for typography custom properties.
 *
 * @param string $context 'frontend' or 'editor'.
 * @return string
 */
function skvn_marine_typography_scope_selector( string $context ): string {
	return 'editor' === $context
		? '.editor-styles-wrapper'
		: 'body:not(.wp-admin)';
}

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
 * Get typography settings safe for CSS output.
 *
 * When skvn-marine-blocks is active, delegate read + sanitize to the plugin
 * owner of skvn_typography. When the plugin is inactive, return theme defaults
 * only and ignore any raw option value in the database (C1 fallback).
 *
 * @return array<string,mixed>
 */
function skvn_marine_get_typography() {
	if ( function_exists( 'skvn_marine_blocks_get_typography' ) ) {
		return skvn_marine_blocks_get_typography();
	}

	return skvn_marine_get_default_typography();
}

// ---------------------------------------------------------------------------
// CSS builder
// ---------------------------------------------------------------------------

/**
 * Build scoped CSS custom properties from saved settings.
 *
 * @param string $context 'frontend' or 'editor'.
 * @return string CSS to inject.
 */
function skvn_marine_build_typography_css( string $context = 'frontend' ): string {
	$t      = skvn_marine_get_typography();
	$p      = $t['palette'];
	$h      = $t['heading'];
	$scope  = skvn_marine_typography_scope_selector( $context );

	return sprintf(
		'%1$s {
	--skvn-color-primary: %2$s;
	--skvn-color-accent:  %3$s;
	--skvn-color-surface: %4$s;
	--skvn-color-text:    %5$s;
	--skvn-h1-size: %6$s; --skvn-h1-weight: %7$s;
	--skvn-h2-size: %8$s; --skvn-h2-weight: %9$s;
	--skvn-h3-size: %10$s; --skvn-h3-weight: %11$s;
	--skvn-h4-size: %12$s; --skvn-h4-weight: %13$s;
}',
		$scope,
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

	wp_add_inline_style( 'skvn-marine-style', skvn_marine_build_typography_css( 'frontend' ) );
}

// ---------------------------------------------------------------------------
// Inject — block editor canvas
// ---------------------------------------------------------------------------

/**
 * Inject typography CSS into the block editor canvas.
 *
 * Uses a virtual style handle so the injection does not depend on
 * the exact handle WordPress assigns to add_editor_style().
 *
 * @return void
 */
function skvn_marine_inject_typography_css_editor() {
	wp_register_style( 'skvn-marine-typography-editor', false, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
	wp_enqueue_style( 'skvn-marine-typography-editor' );
	wp_add_inline_style( 'skvn-marine-typography-editor', skvn_marine_build_typography_css( 'editor' ) );
}