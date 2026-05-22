<?php
/**
 * Title: SKVN Woo Category Strip
 * Slug: skvn-marine/woo-category-strip
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"className":"skvn-product-category-strip","layout":{"type":"constrained"}} -->
<div class="wp-block-group skvn-product-category-strip">
	<!-- wp:heading {"level":2,"className":"skvn-product-category-strip__heading"} -->
	<h2 class="skvn-product-category-strip__heading"><?php echo esc_html__( 'Seafood categories', 'skvn-marine' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"skvn-product-category-strip__lead"} -->
	<p class="skvn-product-category-strip__lead"><?php echo esc_html__( 'Use WooCommerce native categories so product taxonomy remains owned by WooCommerce.', 'skvn-marine' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:woocommerce/product-categories {"hasCount":false,"hasImage":true,"isDropdown":false,"className":"skvn-product-category-strip__list"} /-->
</div>
<!-- /wp:group -->
