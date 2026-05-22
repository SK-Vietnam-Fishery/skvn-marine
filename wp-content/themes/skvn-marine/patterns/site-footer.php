<?php
/**
 * Title: SKVN Site Footer
 * Slug: skvn-marine/site-footer
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"className":"skvn-site-footer","layout":{"type":"default"}} -->
<div class="wp-block-group skvn-site-footer">
	<!-- wp:group {"className":"skvn-site-footer__grid","layout":{"type":"default"}} -->
	<div class="wp-block-group skvn-site-footer__grid">
		<!-- wp:group {"className":"skvn-footer-brand","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-footer-brand">
			<!-- wp:heading {"level":2} -->
			<h2><?php echo esc_html__( 'SKVN Marine', 'skvn-marine' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'B2B seafood supply, cold-chain handling, and export-ready product support from Viet Nam.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Products', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Company', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","orientation":"vertical"}} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"default"}} -->
		<div class="wp-block-group">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Contact', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Request quotes, availability, and export packing support.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph {"className":"skvn-site-footer__bottom"} -->
	<p class="skvn-site-footer__bottom"><?php echo esc_html__( '© SKVN Marine. All rights reserved.', 'skvn-marine' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
