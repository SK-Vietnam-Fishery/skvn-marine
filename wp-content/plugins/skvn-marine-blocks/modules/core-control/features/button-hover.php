<?php
/**
 * Core Button Hover Colors — frontend render adapter for SKVN Marine Blocks.
 *
 * Loaded only when the button_hover feature is enabled via skvn_core_controls.
 * Hooks render_block_core/button to inject a scoped inline <style> with the
 * configured hover CSS custom properties.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'render_block_core/button', 'skvn_marine_blocks_render_button_hover', 10, 2 );

/**
 * Inject scoped hover CSS for core/button blocks that have hover colors set.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data including attrs.
 * @return string
 */
function skvn_marine_blocks_render_button_hover( string $block_content, array $block ): string {
	$attrs      = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
	$hover_text = isset( $attrs['skvnHoverTextColor'] ) ? sanitize_hex_color( $attrs['skvnHoverTextColor'] ) : '';
	$hover_bg   = isset( $attrs['skvnHoverBgColor'] ) ? sanitize_hex_color( $attrs['skvnHoverBgColor'] ) : '';

	if ( ! $hover_text && ! $hover_bg ) {
		return $block_content;
	}

	$unique_id  = 'skvn-btn-' . substr( md5( $hover_text . $hover_bg . wp_rand() ), 0, 8 );
	$css_vars   = array();

	if ( $hover_text ) {
		$css_vars[] = '--skvn-btn-hover-text:' . $hover_text;
	}
	if ( $hover_bg ) {
		$css_vars[] = '--skvn-btn-hover-bg:' . $hover_bg;
	}

	$inline_style = sprintf(
		'<style>.%1$s .wp-block-button__link:hover,.%1$s .wp-block-button__link:focus-visible{color:var(--skvn-btn-hover-text,inherit);background-color:var(--skvn-btn-hover-bg,inherit);transition:color .2s ease,background-color .2s ease}@media(prefers-reduced-motion:reduce){.%1$s .wp-block-button__link{transition:none}}</style>',
		esc_attr( $unique_id )
	);

	// Add the unique class to the outer .wp-block-button wrapper.
	$block_content = preg_replace(
		'/class="([^"]*wp-block-button[^"]*)"/',
		'class="$1 ' . esc_attr( $unique_id ) . '"',
		$block_content,
		1
	);

	return $inline_style . $block_content;
}
