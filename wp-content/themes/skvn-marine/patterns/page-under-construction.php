<?php
/**
 * Title: SKVN Under Construction Page
 * Slug: skvn-marine/page-under-construction
 * Categories: skvn-marine
 * Description: Placeholder page with a hero slider for sections still in progress. Replace images, copy, and CTAs before publishing.
 */
$skvn_marine_slider_config = wp_json_encode(
	array(
		'autoplay'            => true,
		'autoplayDelay'       => 7000,
		'loop'                => true,
		'showArrows'          => true,
		'arrowStyle'          => 'circle',
		'arrowPosition'       => 'side-center',
		'showPagination'      => true,
		'paginationStyle'   => 'dots',
		'paginationPosition' => 'bottom-center',
		'effect'              => 'fade',
		'slidesPerView'       => 1,
	)
);
?>
<!-- wp:group {"align":"full","className":"skvn-under-construction-page","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull skvn-under-construction-page">
	<!-- wp:skvn-marine/slider {"autoplay":true,"autoplayDelay":7000,"loop":true,"showArrows":true,"arrowStyle":"circle","arrowPosition":"side-center","showPagination":true,"paginationStyle":"dots","paginationPosition":"bottom-center","effect":"fade","slidesPerView":1,"preset":"hero","responsiveSlides":"uniform"} -->
	<div class="wp-block-skvn-marine-slider skvn-slider swiper skvn-slider--hero" data-skvn-slider="<?php echo esc_attr( $skvn_marine_slider_config ); ?>">
		<div class="skvn-slider__wrapper swiper-wrapper">
			<!-- wp:skvn-marine/slide {"overlayOpacity":45,"backgroundImageUrl":"https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&w=1920&q=80","backgroundImageAlt":"Seafood export packing"} -->
			<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide skvn-slide--has-background">
				<img alt="<?php echo esc_attr__( 'Seafood packed for export', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":1} -->
				<h1 class="wp-block-heading"><?php echo esc_html__( 'This page is under construction', 'skvn-marine' ); ?></h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'We are preparing complete and accurate information for export buyers and partners.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/"><?php echo esc_html__( 'Back to homepage', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:skvn-marine/slide -->

			<!-- wp:skvn-marine/slide {"overlayOpacity":45,"backgroundImageUrl":"https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1920&q=80","backgroundImageAlt":"Cold storage and processing plant"} -->
			<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide skvn-slide--has-background">
				<img alt="<?php echo esc_attr__( 'Cold storage and processing plant', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading"><?php echo esc_html__( 'Coming soon', 'skvn-marine' ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Product details, process information, and export documentation will be published here shortly.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/products/"><?php echo esc_html__( 'View products', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:skvn-marine/slide -->

			<!-- wp:skvn-marine/slide {"overlayOpacity":45,"backgroundImageUrl":"https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1920&q=80","backgroundImageAlt":"Fishing vessel at sea"} -->
			<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide skvn-slide--has-background">
				<img alt="<?php echo esc_attr__( 'Fishing vessel at sea', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading"><?php echo esc_html__( 'Need help now?', 'skvn-marine' ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'While this page is being built, contact our sales team or send a quote request.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-skvn-primary"} -->
					<div class="wp-block-button is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/request-a-quote/"><?php echo esc_html__( 'Request a quote', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->

					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo esc_html__( 'Contact us', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:skvn-marine/slide -->
		</div>
		<div class="skvn-slider__controls">
			<div class="skvn-slider__arrows">
				<button class="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" type="button"></button>
				<button class="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" type="button"></button>
			</div>
			<div class="skvn-slider__pagination swiper-pagination"></div>
		</div>
	</div>
	<!-- /wp:skvn-marine/slider -->

	<!-- wp:pattern {"slug":"skvn-marine/trust-strip"} /-->

	<!-- wp:group {"className":"skvn-section skvn-under-construction-page__note","layout":{"type":"constrained"}} -->
	<div class="wp-block-group skvn-section skvn-under-construction-page__note">
		<!-- wp:paragraph {"className":"skvn-section__eyebrow"} -->
		<p class="skvn-section__eyebrow"><?php echo esc_html__( 'Notice', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading"><?php echo esc_html__( 'Content is being updated', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead"><?php echo esc_html__( 'This is a placeholder page. When the final content is ready, replace the slider, copy, and links or switch to a full page pattern.', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->