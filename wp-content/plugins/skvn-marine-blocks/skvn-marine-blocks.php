<?php
/**
 * Plugin Name: SKVN Marine Blocks
 * Description: Custom Gutenberg blocks for SKVN Marine.
 * Version: 0.1.0
 * Text Domain: skvn-marine-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'skvn_marine_blocks_register_blocks' );

/**
 * Register SKVN Marine custom blocks.
 *
 * @return void
 */
function skvn_marine_blocks_register_blocks() {
	$editor_script = __DIR__ . '/build/index.ts.js';
	$editor_asset  = __DIR__ . '/build/index.ts.asset.php';
	$editor_deps   = array();
	$editor_ver    = file_exists( $editor_script ) ? filemtime( $editor_script ) : '0.1.0';

	if ( file_exists( $editor_asset ) ) {
		$asset       = require $editor_asset;
		$editor_deps = isset( $asset['dependencies'] ) ? $asset['dependencies'] : array();
		$editor_ver  = isset( $asset['version'] ) ? $asset['version'] : $editor_ver;
	}

	if ( file_exists( $editor_script ) ) {
		wp_register_script(
			'skvn-marine-blocks-editor',
			plugins_url( 'build/index.ts.js', __FILE__ ),
			$editor_deps,
			$editor_ver,
			true
		);
	}

	$view_script = __DIR__ . '/build/view.ts.js';
	$view_asset  = __DIR__ . '/build/view.ts.asset.php';
	$view_deps   = array();
	$view_ver    = file_exists( $view_script ) ? filemtime( $view_script ) : '0.1.0';

	if ( file_exists( $view_asset ) ) {
		$asset     = require $view_asset;
		$view_deps = isset( $asset['dependencies'] ) ? $asset['dependencies'] : array();
		$view_ver  = isset( $asset['version'] ) ? $asset['version'] : $view_ver;
	}

	if ( file_exists( $view_script ) ) {
		wp_register_script(
			'skvn-marine-slider-view',
			plugins_url( 'build/view.ts.js', __FILE__ ),
			$view_deps,
			$view_ver,
			true
		);
	}

	$blocks = array(
		'slider',
		'slide',
		'accordion',
	);

	foreach ( $blocks as $block ) {
		$block_path = __DIR__ . '/src/' . $block;

		if ( file_exists( $block_path . '/block.json' ) ) {
			$args = array();

			if ( wp_script_is( 'skvn-marine-blocks-editor', 'registered' ) ) {
				$args['editor_script'] = 'skvn-marine-blocks-editor';
			}

			if ( 'slider' === $block && wp_script_is( 'skvn-marine-slider-view', 'registered' ) ) {
				$args['view_script'] = 'skvn-marine-slider-view';
			}

			register_block_type( $block_path, $args );
		}
	}
}
