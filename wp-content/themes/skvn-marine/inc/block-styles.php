<?php
/**
 * Block style registrations for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'skvn_marine_register_block_styles' );

/**
 * Register visual block styles.
 *
 * @return void
 */
function skvn_marine_register_block_styles() {
	register_block_style(
		'core/button',
		array(
			'name'  => 'skvn-primary',
			'label' => esc_html__( 'SKVN Primary', 'skvn-marine' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'skvn-section',
			'label' => esc_html__( 'SKVN Section', 'skvn-marine' ),
		)
	);

	register_block_style(
		'core/group',
		array(
			'name'  => 'skvn-card',
			'label' => esc_html__( 'SKVN Card', 'skvn-marine' ),
		)
	);

	register_block_style(
		'core/image',
		array(
			'name'  => 'skvn-rounded-media',
			'label' => esc_html__( 'SKVN Rounded Media', 'skvn-marine' ),
		)
	);
}
