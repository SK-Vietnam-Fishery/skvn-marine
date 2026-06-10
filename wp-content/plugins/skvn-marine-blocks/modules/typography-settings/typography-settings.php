<?php


/**
 * Register the typography settings admin page.
 *
 * @return void
 */
function skvn_marine_blocks_typoraphy_settings_menu() {
	add_menu_page(
		esc_html__( 'SKVN Marine', 'skvn-marine-blocks' ),
		esc_html__( 'SKVN Marine', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine',
		'skvn_marine_blocks_render_typography_settings_page',
		'dashicons-admin-site-alt3'
	);

	add_submenu_page(
		'skvn-marine',
		esc_html__( 'SKVN Typography Settings', 'skvn-marine-blocks' ),
		esc_html__( 'Typography', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine',
		'skvn_marine_blocks_render_typography_settings_page'
	);
}