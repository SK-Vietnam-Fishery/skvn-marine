<?php
/**
 * Title: SKVN Card Grid Test
 * Slug: skvn-marine/card-grid-test
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"className":"skvn-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group skvn-section">
	<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
	<p class="skvn-section__eyebrow"><?php echo esc_html__( 'Layout block test', 'skvn-marine' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:heading {"className":"skvn-section__heading"} -->
	<h2 class="wp-block-heading skvn-section__heading"><?php echo esc_html__( 'Product and service card grid', 'skvn-marine' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"className":"skvn-section__lead"} -->
	<p class="skvn-section__lead"><?php echo esc_html__( 'Use this pattern to verify columns, equal heights, image treatments, card emphasis, alignment, and responsive wrapping.', 'skvn-marine' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:skvn-marine/card-grid {"columns":3,"mobileColumns":1,"gap":"md","cardStyle":"elevated","inset":"none","contentAlign":"left","equalHeights":true} -->
	<div class="wp-block-skvn-marine-card-grid skvn-card-grid skvn-card-grid--3 skvn-card-grid--mobile-1 skvn-card-grid--card-style-elevated skvn-card-grid--inset-none skvn-card-grid--content-left skvn-card-grid--equal-heights">
		<!-- wp:skvn-marine/card {"variant":"default","featured":false,"imageTreatment":"full-bleed"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--image-full-bleed">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="https://placehold.co/720x540/ddfaf4/073b5a?text=Full+Bleed" alt="<?php echo esc_attr__( 'Full bleed card image treatment test', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Frozen seafood supply', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Short copy tests standard card spacing and CTA alignment.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html__( 'View details', 'skvn-marine' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:skvn-marine/card -->

		<!-- wp:skvn-marine/card {"variant":"featured","featured":true,"imageTreatment":"inset"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--featured skvn-card--image-inset">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="https://placehold.co/720x540/eaf7ff/0f5c8c?text=Inset+Featured" alt="<?php echo esc_attr__( 'Inset featured card image treatment test', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Featured export program', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Longer copy verifies equal-height behavior when one card contains more buyer-facing information than adjacent cards.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html__( 'Request quote', 'skvn-marine' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:skvn-marine/card -->

		<!-- wp:skvn-marine/card {"variant":"pricing","featured":false,"imageTreatment":"full-bleed"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--pricing skvn-card--image-full-bleed">
			<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
			<figure class="wp-block-image size-large"><img src="https://placehold.co/720x540/f0f9ff/0369a1?text=Pricing+Variant" alt="<?php echo esc_attr__( 'Pricing variant card image treatment test', 'skvn-marine' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Private label packing', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Pricing variant test with editable Gutenberg content.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
				<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="#"><?php echo esc_html__( 'Discuss packing', 'skvn-marine' ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:skvn-marine/card -->
	</div>
	<!-- /wp:skvn-marine/card-grid -->

	<!-- wp:heading {"level":2,"className":"skvn-section__heading"} -->
	<h2 class="wp-block-heading skvn-section__heading"><?php echo esc_html__( 'Process-step grid', 'skvn-marine' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:skvn-marine/card-grid {"columns":3,"mobileColumns":2,"gap":"sm","cardStyle":"bordered","inset":"none","contentAlign":"center","equalHeights":true} -->
	<div class="wp-block-skvn-marine-card-grid skvn-card-grid skvn-card-grid--3 skvn-card-grid--mobile-2 skvn-card-grid--gap-sm skvn-card-grid--card-style-bordered skvn-card-grid--inset-none skvn-card-grid--content-center skvn-card-grid--equal-heights">
		<!-- wp:skvn-marine/card {"variant":"process-step","stepNumber":"01","featured":false,"imageTreatment":"inset"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--process-step skvn-card--image-inset"><span aria-hidden="true" class="skvn-card__step-number">01</span>
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Tell us the requirement', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Test editable process content and decorative number placement.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:skvn-marine/card -->

		<!-- wp:skvn-marine/card {"variant":"process-step","stepNumber":"02","featured":false,"imageTreatment":"inset"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--process-step skvn-card--image-inset"><span aria-hidden="true" class="skvn-card__step-number">02</span>
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Confirm specification', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'A longer description checks row height and centered copy alignment.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:skvn-marine/card -->

		<!-- wp:skvn-marine/card {"variant":"process-step","stepNumber":"03","featured":false,"imageTreatment":"inset"} -->
		<div class="wp-block-skvn-marine-card skvn-card skvn-card--process-step skvn-card--image-inset"><span aria-hidden="true" class="skvn-card__step-number">03</span>
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading"><?php echo esc_html__( 'Prepare shipment', 'skvn-marine' ); ?></h3>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html__( 'Resize the editor and frontend to verify three desktop columns and two mobile columns.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:skvn-marine/card -->
	</div>
	<!-- /wp:skvn-marine/card-grid -->
</div>
<!-- /wp:group -->
