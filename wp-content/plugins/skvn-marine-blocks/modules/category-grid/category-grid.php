<?php
/**
 * Product Category Grid bootstrap.
 *
 * Category data (thumbnail/description/name/archive/count) stays owned by
 * WooCommerce. The block stores only term IDs, local order, local destination
 * and display/design attributes.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SKVN_MARINE_BLOCKS_CATEGORY_GRID_TAXONOMY' ) ) {
	define( 'SKVN_MARINE_BLOCKS_CATEGORY_GRID_TAXONOMY', 'product_cat' );
}

if ( ! defined( 'SKVN_MARINE_BLOCKS_CATEGORY_GRID_MAX_TERMS' ) ) {
	define( 'SKVN_MARINE_BLOCKS_CATEGORY_GRID_MAX_TERMS', 60 );
}

require_once __DIR__ . '/resolver.php';
require_once __DIR__ . '/render.php';
require_once __DIR__ . '/rest.php';
