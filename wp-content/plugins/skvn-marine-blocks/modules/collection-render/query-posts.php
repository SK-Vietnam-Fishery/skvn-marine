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
 * Query live posts for the Post Collection block.
 *
 * @param array $attributes Block attributes.
 * @return WP_Post[]
 */
function skvn_marine_blocks_query_collection_posts( $attributes ) {
	$limit      = isset( $attributes['itemsToShow'] ) ? absint( $attributes['itemsToShow'] ) : 3;
	$limit      = min( 10, max( 1, $limit ) );
	$order_mode = isset( $attributes['orderMode'] ) ? sanitize_key( $attributes['orderMode'] ) : 'newest';
	$tax_query  = skvn_marine_blocks_build_collection_tax_query(
		$attributes,
		array(
			'category' => 'category',
			'tag'      => 'post_tag',
		)
	);

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'posts_per_page'      => $limit,
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query;
	}

	if ( 'manual' === $order_mode ) {
		$args['orderby'] = array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		);
	} elseif ( 'featured' === $order_mode ) {
		$sticky_posts = get_option( 'sticky_posts' );
		if ( ! empty( $sticky_posts ) ) {
			$args['post__in'] = array_map( 'absint', $sticky_posts );
			$args['orderby']  = 'post__in';
		} else {
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
		}
	} elseif ( 'shuffle-balanced' === $order_mode ) {
		return skvn_marine_blocks_query_collection_posts_shuffle( $attributes, $limit, $tax_query );
	} else {
		$args['orderby'] = 'date';
		$args['order']   = 'DESC';
	}

	$query = new WP_Query( $args );

	return $query->posts;
}

/**
 * Build a taxonomy query from collection attributes.
 *
 * @param array $attributes Block attributes.
 * @param array $taxonomies Supported taxonomy map.
 * @return array
 */
function skvn_marine_blocks_build_collection_tax_query( $attributes, $taxonomies ) {
	$relation = isset( $attributes['relation'] ) && 'AND' === strtoupper( (string) $attributes['relation'] ) ? 'AND' : 'OR';
	$queries  = array();

	$category_slugs = skvn_marine_blocks_sanitize_slug_list( isset( $attributes['categories'] ) ? $attributes['categories'] : array() );
	$tag_slugs      = skvn_marine_blocks_sanitize_slug_list( isset( $attributes['tags'] ) ? $attributes['tags'] : array() );

	if ( ! empty( $category_slugs ) && ! empty( $taxonomies['category'] ) ) {
		$queries[] = array(
			'taxonomy' => $taxonomies['category'],
			'field'    => 'slug',
			'terms'    => $category_slugs,
			'operator' => 'AND' === $relation ? 'AND' : 'IN',
		);
	}

	if ( ! empty( $tag_slugs ) && ! empty( $taxonomies['tag'] ) ) {
		$queries[] = array(
			'taxonomy' => $taxonomies['tag'],
			'field'    => 'slug',
			'terms'    => $tag_slugs,
			'operator' => 'AND' === $relation ? 'AND' : 'IN',
		);
	}

	if ( count( $queries ) > 1 ) {
		$queries['relation'] = $relation;
	}

	return $queries;
}

/**
 * Sanitize a list of term slugs.
 *
 * @param mixed $values Raw values.
 * @return string[]
 */
function skvn_marine_blocks_sanitize_slug_list( $values ) {
	if ( ! is_array( $values ) ) {
		return array();
	}

	return array_values(
		array_filter(
			array_map(
				static function ( $value ) {
					return sanitize_title( (string) $value );
				},
				$values
			)
		)
	);
}

/**
 * Query posts with a host-friendly balanced shuffle pool.
 *
 * @param array $attributes Block attributes.
 * @param int   $limit      Final item limit.
 * @param array $tax_query  Prepared taxonomy query.
 * @return WP_Post[]
 */
function skvn_marine_blocks_query_collection_posts_shuffle( $attributes, $limit, $tax_query ) {
	$base_args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'posts_per_page'         => 30,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	if ( ! empty( $tax_query ) ) {
		$base_args['tax_query'] = $tax_query;
	}

	$count_query_args                 = $base_args;
	$count_query_args['no_found_rows'] = false;
	$count_query                       = new WP_Query( $count_query_args );
	$total                            = (int) $count_query->found_posts;

	if ( $total <= 30 ) {
		$ids = $count_query->posts;
	} else {
		$ids = array_merge(
			skvn_marine_blocks_query_collection_post_ids( $base_args, 15, 'title', 'ASC' ),
			skvn_marine_blocks_query_collection_post_ids( $base_args, 15, 'title', 'DESC' ),
			skvn_marine_blocks_query_collection_post_ids( $base_args, 15, 'date', 'DESC' ),
			skvn_marine_blocks_query_collection_post_ids( $base_args, 15, 'date', 'ASC' )
		);
	}

	$ids = array_values( array_unique( array_map( 'absint', $ids ) ) );
	shuffle( $ids );
	$ids = array_slice( $ids, 0, $limit );

	if ( empty( $ids ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'post__in'       => $ids,
			'orderby'        => 'post__in',
			'posts_per_page' => count( $ids ),
		)
	);
}

/**
 * Query a small post ID slice for balanced shuffle.
 *
 * @param array  $base_args Base query args.
 * @param int    $limit     Item limit.
 * @param string $orderby   Orderby field.
 * @param string $order     Order direction.
 * @return int[]
 */
function skvn_marine_blocks_query_collection_post_ids( $base_args, $limit, $orderby, $order ) {
	$args                   = $base_args;
	$args['posts_per_page'] = $limit;
	$args['orderby']        = $orderby;
	$args['order']          = $order;
	$query                  = new WP_Query( $args );

	return $query->posts;
}
