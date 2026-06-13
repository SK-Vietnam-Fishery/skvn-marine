<?php
/**
 * Post collection query adapter.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query live posts for the Post Collection prototype.
 *
 * @param array $attributes Block attributes.
 * @return WP_Post[]
 */
function skvn_marine_blocks_query_collection_posts( $attributes ) {
	$limit      = isset( $attributes['itemsToShow'] ) ? absint( $attributes['itemsToShow'] ) : 3;
	$limit      = min( 10, max( 1, $limit ) );
	$order_mode = isset( $attributes['orderMode'] ) ? sanitize_key( $attributes['orderMode'] ) : 'newest';

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'posts_per_page'      => $limit,
	);

	if ( 'manual' === $order_mode ) {
		$args['orderby'] = array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		);
	} else {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	}

	$query = new WP_Query( $args );

	return $query->posts;
}
