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
 * Query live products for the Product Collection block.
 *
 * @param array $attributes Block attributes.
 * @return array
 */
function skvn_marine_blocks_query_collection_products( $attributes ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$limit      = isset( $attributes['itemsToShow'] ) ? absint( $attributes['itemsToShow'] ) : 3;
	$limit      = min( 10, max( 1, $limit ) );
	$order_mode = isset( $attributes['orderMode'] ) ? sanitize_key( $attributes['orderMode'] ) : 'newest';
	$tax_query  = skvn_marine_blocks_build_collection_tax_query(
		$attributes,
		array(
			'category' => 'product_cat',
			'tag'      => 'product_tag',
		)
	);
	$args       = array(
		'limit'  => $limit,
		'status' => 'publish',
		'return' => 'objects',
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query;
	}

	if ( 'featured' === $order_mode ) {
		$args['featured'] = true;
		$args['orderby']  = 'date';
		$args['order']    = 'DESC';
	} elseif ( 'manual' === $order_mode ) {
		$args['orderby'] = array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		);
	} elseif ( 'shuffle-balanced' === $order_mode ) {
		return skvn_marine_blocks_query_collection_products_shuffle( $attributes, $limit, $tax_query );
	} else {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	}

	return wc_get_products( $args );
}

/**
 * Query products with a host-friendly balanced shuffle pool.
 *
 * @param array $attributes Block attributes.
 * @param int   $limit      Final item limit.
 * @param array $tax_query  Prepared taxonomy query.
 * @return WC_Product[]
 */
function skvn_marine_blocks_query_collection_products_shuffle( $attributes, $limit, $tax_query ) {
	$base_args = array(
		'status' => 'publish',
		'return' => 'ids',
		'limit'  => 30,
	);

	if ( ! empty( $tax_query ) ) {
		$base_args['tax_query'] = $tax_query;
	}

	$total = count(
		wc_get_products(
			array_merge(
				$base_args,
				array(
					'limit' => 31,
				)
			)
		)
	);

	if ( $total <= 30 ) {
		$ids = wc_get_products( $base_args );
	} else {
		$ids = array_merge(
			skvn_marine_blocks_query_collection_product_ids( $base_args, 15, 'title', 'ASC' ),
			skvn_marine_blocks_query_collection_product_ids( $base_args, 15, 'title', 'DESC' ),
			skvn_marine_blocks_query_collection_product_ids( $base_args, 15, 'date', 'DESC' ),
			skvn_marine_blocks_query_collection_product_ids( $base_args, 15, 'date', 'ASC' )
		);
	}

	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );
	shuffle( $ids );
	$ids = array_slice( $ids, 0, $limit );

	if ( empty( $ids ) ) {
		return array();
	}

	return wc_get_products(
		array(
			'include' => $ids,
			'orderby' => 'include',
			'limit'   => count( $ids ),
			'status'  => 'publish',
			'return'  => 'objects',
		)
	);
}

/**
 * Query a small product ID slice for balanced shuffle.
 *
 * @param array  $base_args Base query args.
 * @param int    $limit     Item limit.
 * @param string $orderby   Orderby field.
 * @param string $order     Order direction.
 * @return int[]
 */
function skvn_marine_blocks_query_collection_product_ids( $base_args, $limit, $orderby, $order ) {
	return wc_get_products(
		array_merge(
			$base_args,
			array(
				'limit'   => $limit,
				'orderby' => $orderby,
				'order'   => $order,
			)
		)
	);
}
