<?php
/**
 * WooCommerce visual integration for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_enqueue_styles', 'skvn_marine_woocommerce_enqueue_styles' );

/**
 * Keep WooCommerce styles enabled for V1 visual overrides.
 *
 * @param array<string,array<string,string>> $styles WooCommerce styles.
 * @return array<string,array<string,string>>
 */
function skvn_marine_woocommerce_enqueue_styles( $styles ) {
	return $styles;
}
