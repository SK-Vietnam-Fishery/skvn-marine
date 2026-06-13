<?php
/**
 * Title: SKVN Homepage Test Layout
 * Slug: skvn-marine/homepage-test
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"align":"full","className":"skvn-pattern-test-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-pattern-test-page">
	<!-- wp:group {"className":"skvn-pattern-hero","layout":{"type":"constrained"}} -->
	<div class="wp-block-group skvn-pattern-hero">
		<!-- wp:group {"className":"skvn-pattern-hero__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-pattern-hero__grid">
			<!-- wp:group {"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"className":"skvn-pattern-hero__eyebrow"} -->
				<p class="skvn-pattern-hero__eyebrow"><?php echo esc_html__( 'Reliable cold-chain seafood supply', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":1} -->
				<h1><?php echo esc_html__( 'Premium seafood for export buyers', 'skvn-marine' ); ?></h1>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Blue-first B2B layout test with mint support accents, placeholder content, and reusable sections for visual validation.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-skvn-primary"} -->
					<div class="wp-block-button is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo esc_html__( 'Request Quote', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->

			<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"skvn-pattern-hero__media is-style-skvn-rounded-media"} -->
			<figure class="wp-block-image size-large skvn-pattern-hero__media is-style-skvn-rounded-media"><img src="https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="<?php echo esc_attr__( 'Seafood packed for export', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:pattern {"slug":"skvn-marine/trust-strip"} /-->

	<!-- wp:group {"className":"skvn-home-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group skvn-home-section">
		<!-- wp:heading {"className":"skvn-home-section__heading"} -->
		<h2 class="skvn-home-section__heading"><?php echo esc_html__( 'Why choose us', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"className":"skvn-home-section__lead"} -->
		<p class="skvn-home-section__lead"><?php echo esc_html__( 'A reusable decision section for testing trust, quality, sourcing, and B2B buyer support cards.', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"skvn-feature-grid","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-feature-grid">
			<?php
			$skvn_marine_features = array(
				array( 'QC', __( 'Stable quality control', 'skvn-marine' ), __( 'Batch checks for sizing, temperature, packing condition, and documents.', 'skvn-marine' ) ),
				array( 'OR', __( 'Clear origin records', 'skvn-marine' ), __( 'Buyer-facing traceability details for export documentation.', 'skvn-marine' ) ),
				array( 'CC', __( 'Cold-chain discipline', 'skvn-marine' ), __( 'Frozen handling from storage to container loading.', 'skvn-marine' ) ),
				array( 'B2B', __( 'Packing support', 'skvn-marine' ), __( 'Carton labels, private label needs, and recurring buyer planning.', 'skvn-marine' ) ),
			);
			foreach ( $skvn_marine_features as $skvn_marine_feature ) :
				?>
				<!-- wp:group {"className":"skvn-feature-card","layout":{"type":"default"}} -->
				<div class="wp-block-group skvn-feature-card">
					<!-- wp:paragraph {"className":"skvn-feature-card__icon"} -->
					<p class="skvn-feature-card__icon"><?php echo esc_html( $skvn_marine_feature[0] ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3><?php echo esc_html( $skvn_marine_feature[1] ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( $skvn_marine_feature[2] ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"skvn-home-section skvn-factory-band","layout":{"type":"constrained"}} -->
	<div class="wp-block-group skvn-home-section skvn-factory-band">
		<!-- wp:group {"className":"skvn-factory-band__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-factory-band__grid">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":"is-style-skvn-rounded-media"} -->
			<figure class="wp-block-image size-large is-style-skvn-rounded-media"><img src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&amp;fit=crop&amp;w=1200&amp;q=80" alt="<?php echo esc_attr__( 'Cold storage and factory placeholder', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:group {"layout":{"type":"default"}} -->
			<div class="wp-block-group">
				<!-- wp:heading {"className":"skvn-home-section__heading"} -->
				<h2 class="skvn-home-section__heading"><?php echo esc_html__( 'Factory standards', 'skvn-marine' ); ?></h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Placeholder section for HACCP mindset, temperature logs, export documents, and packing control. Mint supports clean process and QA signals.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"skvn-home-section","layout":{"type":"constrained"}} -->
	<div class="wp-block-group skvn-home-section">
		<!-- wp:heading {"className":"skvn-home-section__heading"} -->
		<h2 class="skvn-home-section__heading"><?php echo esc_html__( 'Working process', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->
		<!-- wp:group {"className":"skvn-process-grid","layout":{"type":"default"}} -->
		<div class="wp-block-group skvn-process-grid">
			<?php
			$skvn_marine_steps = array( 'Inquiry', 'Specification', 'Quote', 'Production', 'Delivery' );
			foreach ( $skvn_marine_steps as $skvn_marine_index => $skvn_marine_step ) :
				?>
				<!-- wp:group {"className":"skvn-process-card","layout":{"type":"default"}} -->
				<div class="wp-block-group skvn-process-card">
					<!-- wp:paragraph {"className":"skvn-process-card__step"} -->
					<p class="skvn-process-card__step"><?php echo esc_html( str_pad( (string) ( $skvn_marine_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:heading {"level":3} -->
					<h3><?php echo esc_html( $skvn_marine_step ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html__( 'Short placeholder copy for validating process card spacing and responsive behavior.', 'skvn-marine' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->

	<!-- wp:pattern {"slug":"skvn-marine/newsletter-band"} /-->
	<!-- wp:pattern {"slug":"skvn-marine/site-footer"} /-->
</div>
<!-- /wp:group -->
