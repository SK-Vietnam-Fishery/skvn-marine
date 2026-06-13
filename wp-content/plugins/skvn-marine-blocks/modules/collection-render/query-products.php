<?php
/**
 * Product collection query adapter.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query live products for later product collection phases.
 *
 * @param array $attributes Block attributes.
 * @return array
 */
function skvn_marine_blocks_query_collection_products( $attributes ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$limit = isset( $attributes['itemsToShow'] ) ? absint( $attributes['itemsToShow'] ) : 3;

	return wc_get_products(
		array(
			'limit'  => min( 10, max( 1, $limit ) ),
			'status' => 'publish',
			'return' => 'objects',
		)
	);
}
