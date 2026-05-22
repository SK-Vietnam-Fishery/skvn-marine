<?php
/**
 * Title: SKVN Woo Product Grid
 * Slug: skvn-marine/woo-product-grid
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"className":"skvn-product-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group skvn-product-section">
	<!-- wp:group {"className":"skvn-product-section__header","layout":{"type":"default"}} -->
	<div class="wp-block-group skvn-product-section__header">
		<!-- wp:paragraph {"className":"skvn-pattern-hero__eyebrow"} -->
		<p class="skvn-pattern-hero__eyebrow"><?php echo esc_html__( 'Export-ready seafood', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"className":"skvn-home-section__heading"} -->
		<h2 class="skvn-home-section__heading"><?php echo esc_html__( 'Featured products', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"skvn-home-section__lead"} -->
		<p class="skvn-home-section__lead"><?php echo esc_html__( 'WooCommerce-native product section for testing B2B product cards, mobile-visible CTAs, and SKVN visual styling.', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:woocommerce/product-collection {"query":{"perPage":4,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","search":"","exclude":[],"inherit":false,"taxQuery":[],"isProductCollectionBlock":true},"className":"skvn-product-grid"} -->
	<div class="wp-block-woocommerce-product-collection skvn-product-grid">
		<!-- wp:woocommerce/product-template {"className":"skvn-product-grid__template"} -->
			<!-- wp:group {"className":"skvn-product-card","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-product-card">
				<!-- wp:woocommerce/product-image {"showSaleBadge":false,"imageSizing":"thumbnail"} /-->
				<!-- wp:post-title {"textAlign":"left","level":3,"isLink":true,"className":"skvn-product-card__title"} /-->
				<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"className":"skvn-product-card__price"} /-->
				<!-- wp:woocommerce/product-button {"textAlign":"left","className":"skvn-product-card__cta"} /-->
			</div>
			<!-- /wp:group -->
		<!-- /wp:woocommerce/product-template -->
	</div>
	<!-- /wp:woocommerce/product-collection -->
</div>
<!-- /wp:group -->
