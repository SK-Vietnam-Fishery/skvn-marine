<?php
/**
 * Title: B2B Seafood Feature Showcase
 * Slug: skvn-marine/b2b-seafood-feature-showcase
 * Categories: skvn-marine
 * Description: Editorial seafood introduction paired with an interactive SKVN Feature Showcase.
 */
?>
<!-- wp:group {"align":"full","className":"skvn-b2b-showcase-pattern","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-b2b-showcase-pattern">
	<!-- wp:columns {"verticalAlignment":"stretch","className":"skvn-b2b-showcase-pattern__grid"} -->
	<div class="wp-block-columns are-vertically-aligned-stretch skvn-b2b-showcase-pattern__grid">
		<!-- wp:column {"verticalAlignment":"stretch","width":"30%","className":"skvn-b2b-editorial"} -->
		<div class="wp-block-column is-vertically-aligned-stretch skvn-b2b-editorial" style="flex-basis:30%">
			<!-- wp:paragraph {"className":"skvn-b2b-editorial__eyebrow"} -->
			<p class="skvn-b2b-editorial__eyebrow"><?php echo esc_html__( 'Premium Ocean Catch', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":2,"className":"skvn-b2b-editorial__heading"} -->
			<h2 class="wp-block-heading skvn-b2b-editorial__heading"><?php echo esc_html__( 'Global', 'skvn-marine' ); ?><br><strong class="skvn-b2b-editorial__heading-accent"><?php echo esc_html__( 'Seafood', 'skvn-marine' ); ?></strong><br><?php echo esc_html__( 'Exporter', 'skvn-marine' ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"skvn-b2b-editorial__copy"} -->
			<p class="skvn-b2b-editorial__copy"><?php echo esc_html__( 'Premium wild-caught seafood prepared for demanding buyers in the United States, Japan, and the European Union.', 'skvn-marine' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:group {"className":"skvn-b2b-editorial__meta","layout":{"type":"default"}} -->
			<div class="wp-block-group skvn-b2b-editorial__meta">
				<!-- wp:paragraph {"className":"skvn-b2b-editorial__meta-label"} -->
				<p class="skvn-b2b-editorial__meta-label"><?php echo esc_html__( 'Compliance Standards', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"skvn-b2b-editorial__meta-text"} -->
				<p class="skvn-b2b-editorial__meta-text"><?php echo esc_html__( 'HACCP - BRC Global - IFS', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"stretch","width":"70%","className":"skvn-b2b-showcase-pattern__panels"} -->
		<div class="wp-block-column is-vertically-aligned-stretch skvn-b2b-showcase-pattern__panels" style="flex-basis:70%">
			<!-- wp:skvn-marine/feature-showcase -->
			<section class="wp-block-skvn-marine-feature-showcase skvn-feature-showcase skvn-feature-showcase--horizontal skvn-feature-showcase--mobile-accordion"><div class="skvn-feature-showcase__items"><details class="skvn-feature-showcase__item"><summary class="skvn-feature-showcase__summary"><span class="skvn-feature-showcase__index">01</span><span class="skvn-feature-showcase__label">01 . OCEAN GROUPER</span></summary><div class="skvn-feature-showcase__body"><span aria-hidden="true" class="skvn-feature-showcase__shade"></span><div class="skvn-feature-showcase__content"><h3 class="skvn-feature-showcase__title">Ocean Grouper Fillet</h3><p class="skvn-feature-showcase__copy">Wild-caught grouper prepared for premium restaurant and export programs.</p></div></div></details><details class="skvn-feature-showcase__item"><summary class="skvn-feature-showcase__summary"><span class="skvn-feature-showcase__index">02</span><span class="skvn-feature-showcase__label">02 . IQF TUNNEL FREEZING</span></summary><div class="skvn-feature-showcase__body"><span aria-hidden="true" class="skvn-feature-showcase__shade"></span><div class="skvn-feature-showcase__content"><h3 class="skvn-feature-showcase__title">IQF Freezing Technology</h3><p class="skvn-feature-showcase__copy">Fast freezing preserves texture, freshness, and product consistency.</p></div></div></details><details class="skvn-feature-showcase__item"><summary class="skvn-feature-showcase__summary"><span class="skvn-feature-showcase__index">03</span><span class="skvn-feature-showcase__label">03 . BARRAMUNDI</span></summary><div class="skvn-feature-showcase__body"><span aria-hidden="true" class="skvn-feature-showcase__shade"></span><div class="skvn-feature-showcase__content"><h3 class="skvn-feature-showcase__title">Premium Barramundi</h3><p class="skvn-feature-showcase__copy">Sustainably prepared seafood for retail and foodservice buyers.</p></div></div></details><details class="skvn-feature-showcase__item" open><summary class="skvn-feature-showcase__summary"><span class="skvn-feature-showcase__index">04</span><span class="skvn-feature-showcase__label">04 . EXPORT PROCESSING PLANT</span></summary><div class="skvn-feature-showcase__body"><span aria-hidden="true" class="skvn-feature-showcase__shade"></span><div class="skvn-feature-showcase__content"><h3 class="skvn-feature-showcase__title">Global Standard Processing</h3><p class="skvn-feature-showcase__copy">Controlled processing environments support demanding export requirements.</p></div></div></details></div></section>
			<!-- /wp:skvn-marine/feature-showcase -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
