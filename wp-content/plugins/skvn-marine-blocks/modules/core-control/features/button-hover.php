<?php
/**
 * Core Button Hover Colors — frontend render adapter for SKVN Marine Blocks.
 *
 * Loaded only when the button_hover feature is enabled via skvn_core_controls.
 * Hooks render_block_core/button to inject inline CSS custom properties on the
 * wrapper and enqueue shared hover pseudo-class rules.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/button', 'skvn_marine_blocks_render_button_hover', 10, 2 );

/**
 * Enqueue frontend hover rules once per request when a hover button renders.
 *
 * @return void
 */
function skvn_marine_blocks_enqueue_button_hover_frontend_style(): void {
	static $enqueued = false;

	if ( $enqueued ) {
		return;
	}

	$enqueued   = true;
	$plugin_dir = dirname( dirname( dirname( __DIR__ ) ) );
	$style_file = $plugin_dir . '/build/style-index.ts.css';

	if ( ! file_exists( $style_file ) ) {
		return;
	}

	wp_enqueue_style(
		'skvn-marine-core-button-hover',
		plugins_url( 'build/style-index.ts.css', $plugin_dir . '/skvn-marine-blocks.php' ),
		array(),
		(string) filemtime( $style_file )
	);
}

/**
 * Inject inline hover CSS variables for core/button blocks that have hover colors set.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including attrs.
 * @return string
 */
function skvn_marine_blocks_render_button_hover( string $block_content, array $block ): string {
	$attrs      = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
	$hover_text = isset( $attrs['skvnHoverTextColor'] ) ? sanitize_hex_color( $attrs['skvnHoverTextColor'] ) : '';

	$hover_bg_raw = isset( $attrs['skvnHoverBgColor'] ) ? (string) $attrs['skvnHoverBgColor'] : '';
	$hover_bg     = sanitize_hex_color( $hover_bg_raw );
	if ( ! $hover_bg && preg_match( '/^(?:linear|radial|conic)-gradient\(/i', $hover_bg_raw ) ) {
		$cleaned  = preg_replace( '/[^a-zA-Z0-9\s\-,#().%\/]+/', '', wp_strip_all_tags( $hover_bg_raw ) );
		$hover_bg = preg_match( '/^(?:linear|radial|conic)-gradient\(/i', $cleaned ) ? $cleaned : '';
	}

	if ( ! $hover_text && ! $hover_bg ) {
		return $block_content;
	}

	$css_vars = array();

	if ( $hover_text ) {
		$css_vars[] = '--skvn-btn-hover-text:' . $hover_text;
	}
	if ( $hover_bg ) {
		$css_vars[] = '--skvn-btn-hover-bg:' . $hover_bg;
	}

	$style_attr = implode( ';', $css_vars ) . ';';

	$block_content = preg_replace_callback(
		'/(<div\s+class="([^"]*wp-block-button[^"]*)")(\s+style="([^"]*)")?/i',
		static function ( array $matches ) use ( $style_attr ): string {
			$existing = isset( $matches[4] ) ? $matches[4] : '';
			$merged   = $existing;

			if ( $merged !== '' && ! str_ends_with( rtrim( $merged ), ';' ) ) {
				$merged .= ';';
			}

			$merged .= $style_attr;

			return $matches[1] . ' style="' . esc_attr( $merged ) . '"';
		},
		$block_content,
		1
	);

	skvn_marine_blocks_enqueue_button_hover_frontend_style();

	return $block_content;
}