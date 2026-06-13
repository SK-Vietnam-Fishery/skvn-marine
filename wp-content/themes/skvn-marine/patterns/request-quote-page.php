<?php
/**
 * Title: SKVN Request Quote Page
 * Slug: skvn-marine/request-quote-page
 * Categories: skvn-marine
 */
?>
<!-- wp:group {"align":"full","className":"skvn-translated-page skvn-quote-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-translated-page skvn-quote-page">
	<!-- wp:group {"align":"full","className":"skvn-translated-hero skvn-quote-hero","layout":{"type":"default"}} -->
	<div class="wp-block-group alignfull skvn-translated-hero skvn-quote-hero">
		<!-- wp:columns {"className":"skvn-translated-hero__grid","verticalAlignment":"center"} -->
		<div class="wp-block-columns are-vertically-aligned-center skvn-translated-hero__grid">
			<!-- wp:column {"verticalAlignment":"center","className":"skvn-translated-hero__content"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-translated-hero__content">
				<!-- wp:paragraph {"className":"skvn-translated-hero__eyebrow"} -->
				<p class="skvn-translated-hero__eyebrow"><?php echo esc_html__( 'Request quotation', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"className":"skvn-translated-hero__heading"} -->
				<h1 class="wp-block-heading skvn-translated-hero__heading"><?php echo esc_html__( 'Tell us what seafood products you need', 'skvn-marine' ); ?></h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"className":"skvn-translated-hero__lead"} -->
				<p class="skvn-translated-hero__lead"><?php echo esc_html__( 'Share the product, packing, and delivery details your team already has. Form handling remains deferred until the approved CF7 milestone.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"skvn-translated-hero__actions"} -->
				<div class="wp-block-buttons skvn-translated-hero__actions">
					<!-- wp:button {"className":"skvn-button skvn-button--primary is-style-skvn-primary"} -->
					<div class="wp-block-button skvn-button skvn-button--primary is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo esc_html__( 'Contact Sales', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"skvn-button skvn-button--secondary"} -->
					<div class="wp-block-button skvn-button skvn-button--secondary"><a class="wp-block-button__link wp-element-button" href="/products/"><?php echo esc_html__( 'Review Products', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"center","className":"skvn-translated-hero__media"} -->
			<div class="wp-block-column is-vertically-aligned-center skvn-translated-hero__media">
				<!-- wp:group {"className":"skvn-quote-context-card","layout":{"type":"default"}} -->
				<div class="wp-block-group skvn-quote-context-card">
					<!-- wp:heading {"level":2} -->
					<h2 class="wp-block-heading"><?php echo esc_html__( 'Quote context', 'skvn-marine' ); ?></h2>
					<!-- /wp:heading -->

					<!-- wp:list -->
					<ul>
						<li><?php echo esc_html__( 'Product interest and size specification', 'skvn-marine' ); ?></li>
						<li><?php echo esc_html__( 'Estimated volume and shipment market', 'skvn-marine' ); ?></li>
						<li><?php echo esc_html__( 'Packing, label, certificate, or schedule notes', 'skvn-marine' ); ?></li>
					</ul>
					<!-- /wp:list -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"align":"full","className":"skvn-section skvn-section--soft skvn-quote-surface","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignfull skvn-section skvn-section--soft skvn-quote-surface">
		<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
		<p class="skvn-section__eyebrow"><?php echo esc_html__( 'Before the form milestone', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading"><?php echo esc_html__( 'Use this page as the visual quote surface', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead"><?php echo esc_html__( 'When CF7 returns to scope, replace the placeholder card with the approved request quote form. The page URL and product_id query string stay the same.', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:columns {"className":"skvn-quote-surface__grid"} -->
		<div class="wp-block-columns skvn-quote-surface__grid">
			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html__( 'Product', 'skvn-marine' ); ?></h3><!-- /wp:heading --><!-- wp:paragraph --><p><?php echo esc_html__( 'Species, size, cut, quantity, and target specification.', 'skvn-marine' ); ?></p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html__( 'Packing', 'skvn-marine' ); ?></h3><!-- /wp:heading --><!-- wp:paragraph --><p><?php echo esc_html__( 'Carton size, private label need, cold-chain notes, and documentation.', 'skvn-marine' ); ?></p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column"><!-- wp:group {"className":"skvn-card skvn-quote-surface__card","layout":{"type":"default"}} --><div class="wp-block-group skvn-card skvn-quote-surface__card"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading"><?php echo esc_html__( 'Delivery', 'skvn-marine' ); ?></h3><!-- /wp:heading --><!-- wp:paragraph --><p><?php echo esc_html__( 'Destination market, preferred schedule, and importer requirements.', 'skvn-marine' ); ?></p><!-- /wp:paragraph --></div><!-- /wp:group --></div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
