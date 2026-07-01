<?php
/**
 * Blog heading auto-numbering — frontend + editor render adapter.
 *
 * Loaded only when the post_heading_numbers feature is enabled via
 * skvn_core_controls. Emits CSS-counter rules that number h2–h5 inside the blog
 * post body. Config (depth/style) comes from skvn_heading_number.
 *
 * Selectors are written explicitly (no :is()) and all values are whitelisted
 * literals (depth/style/format) → CSS carries no user input.
 *
 * Known design debt: style "decimal" with skipped heading levels (h2→h4) renders
 * "1.0.1" — inherent CSS-counter limit, accepted for now; proper fix = JS tree
 * numbering when this becomes a standalone plugin. See decision doc.
 *
 * @package skvn-marine-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'skvn_marine_blocks_heading_number_frontend', 20 );
add_action( 'enqueue_block_editor_assets', 'skvn_marine_blocks_heading_number_editor' );

/**
 * Build the heading-numbering CSS for a given scope selector.
 *
 * @param string                        $selector Scope selector (no trailing space).
 * @param array{depth:string,style:string} $cfg    Sanitized config.
 * @return string
 */
function skvn_marine_blocks_heading_number_css( string $selector, array $cfg ): string {
	$levels    = array( 'h2', 'h3', 'h4', 'h5' );
	$mixed_fmt = array( 'upper-roman', 'decimal', 'lower-alpha', 'lower-roman' );
	$depth_map = array(
		'h3' => 1,
		'h4' => 2,
		'h5' => 3,
	);
	$depth_idx = $depth_map[ $cfg['depth'] ] ?? 1;
	$is_mixed  = 'mixed' === $cfg['style'];

	// Reset + increment (always all four levels; each resets deeper levels).
	$css  = "{$selector}{counter-reset:h2 h3 h4 h5}";
	$css .= "{$selector} h2{counter-increment:h2;counter-reset:h3 h4 h5}";
	$css .= "{$selector} h3{counter-increment:h3;counter-reset:h4 h5}";
	$css .= "{$selector} h4{counter-increment:h4;counter-reset:h5}";
	$css .= "{$selector} h5{counter-increment:h5}";

	// ::before numbers, up to the chosen depth.
	for ( $i = 0; $i <= $depth_idx; $i++ ) {
		$level = $levels[ $i ];

		if ( $is_mixed ) {
			$content = 'counter(' . $level . ', ' . $mixed_fmt[ $i ] . ') ". "';
		} else {
			$parts = array();
			for ( $j = 0; $j <= $i; $j++ ) {
				$parts[] = 'counter(' . $levels[ $j ] . ')';
			}
			// Top level → "1. "; deeper → "1.1 " (inner separator is a dot).
			$tail    = ( 1 === count( $parts ) ) ? ' ". "' : ' " "';
			$content = implode( ' "." ', $parts ) . $tail;
		}

		$css .= "{$selector} {$level}::before{content:{$content}}";
	}

	// Per-heading opt-out via .no-number (explicit selectors, no :is()).
	foreach ( $levels as $level ) {
		$css .= "{$selector} {$level}.no-number{counter-increment:none}";
		$css .= "{$selector} {$level}.no-number::before{content:none}";
	}

	return $css;
}

/**
 * Print numbering CSS on single blog posts (frontend).
 *
 * @return void
 */
function skvn_marine_blocks_heading_number_frontend(): void {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	$css = skvn_marine_blocks_heading_number_css(
		'.single-post .entry-content',
		skvn_marine_blocks_get_heading_number()
	);

	// CSS is built from whitelisted literals only — safe to print.
	echo "\n<style id=\"skvn-heading-number\">" . $css . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Enqueue numbering CSS in the block editor — only when editing a post.
 *
 * @return void
 */
function skvn_marine_blocks_heading_number_editor(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}

	$css = skvn_marine_blocks_heading_number_css(
		'.editor-styles-wrapper',
		skvn_marine_blocks_get_heading_number()
	);

	wp_register_style( 'skvn-heading-number-editor', false );
	wp_enqueue_style( 'skvn-heading-number-editor' );
	wp_add_inline_style( 'skvn-heading-number-editor', $css );
}
