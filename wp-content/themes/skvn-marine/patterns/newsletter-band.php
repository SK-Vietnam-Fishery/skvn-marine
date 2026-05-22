<?php
/**
 * Title: SKVN Newsletter Signup Band
 * Slug: skvn-marine/newsletter-band
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"className":"skvn-newsletter-band","layout":{"type":"constrained"}} -->
<div class="wp-block-group skvn-newsletter-band">
	<!-- wp:columns {"className":"skvn-newsletter-band__inner","verticalAlignment":"center"} -->
	<div class="wp-block-columns are-vertically-aligned-center skvn-newsletter-band__inner">
		<!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%">
			<!-- wp:heading {"level":3} -->
			<h3><?php echo esc_html__( 'Get Fresh Seafood Updates', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Receive sourcing notes, seasonal catch updates, and export availability from SKVN Marine.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-skvn-primary"} -->
				<div class="wp-block-button is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo esc_html__( 'Request Updates', 'skvn-marine' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"33.33%","className":"skvn-newsletter-media skvn-newsletter-media--overhang"} -->
		<div class="wp-block-column is-vertically-aligned-center skvn-newsletter-media skvn-newsletter-media--overhang" style="flex-basis:33.33%">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-skvn-rounded-media"} -->
			<figure class="wp-block-image size-large is-style-skvn-rounded-media"><img src="https://images.unsplash.com/photo-1615141982883-c7ad0e69fd62?auto=format&amp;fit=crop&amp;w=900&amp;q=80" alt="<?php echo esc_attr__( 'Seafood packed on ice', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
