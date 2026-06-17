<?php
/**
 * Title: SKVN Trang Đang Xây Dựng
 * Slug: skvn-marine/page-under-construction
 * Categories: skvn-marine
 * Description: Trang placeholder với hero slider cho các mục đang hoàn thiện. Thay ảnh, copy và CTA trước khi publish.
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
				<img alt="<?php echo esc_attr__( 'Hải sản đóng gói xuất khẩu', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":1} -->
				<h1 class="wp-block-heading"><?php echo esc_html__( 'Trang này đang được xây dựng', 'skvn-marine' ); ?></h1>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Chúng tôi đang hoàn thiện nội dung để mang đến thông tin đầy đủ và chính xác nhất cho đối tác xuất khẩu.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/"><?php echo esc_html__( 'Về trang chủ', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:skvn-marine/slide -->

			<!-- wp:skvn-marine/slide {"overlayOpacity":45,"backgroundImageUrl":"https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1920&q=80","backgroundImageAlt":"Cold storage and processing plant"} -->
			<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide skvn-slide--has-background">
				<img alt="<?php echo esc_attr__( 'Kho lạnh và nhà máy chế biến', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading"><?php echo esc_html__( 'Sắp ra mắt', 'skvn-marine' ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Thông tin sản phẩm, quy trình và tài liệu xuất khẩu sẽ được cập nhật tại đây trong thời gian tới.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/products/"><?php echo esc_html__( 'Xem sản phẩm', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:skvn-marine/slide -->

			<!-- wp:skvn-marine/slide {"overlayOpacity":45,"backgroundImageUrl":"https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1920&q=80","backgroundImageAlt":"Fishing vessel at sea"} -->
			<div class="wp-block-skvn-marine-slide skvn-slide swiper-slide skvn-slide--has-background">
				<img alt="<?php echo esc_attr__( 'Tàu đánh bắt trên biển', 'skvn-marine' ); ?>" class="skvn-slide__background-image" src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&amp;fit=crop&amp;w=1920&amp;q=80"/>
				<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:0.45"></span>
				<!-- wp:heading {"level":2} -->
				<h2 class="wp-block-heading"><?php echo esc_html__( 'Cần hỗ trợ ngay?', 'skvn-marine' ); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php echo esc_html__( 'Trong thời gian chờ đợi, bạn có thể liên hệ đội ngũ bán hàng hoặc gửi yêu cầu báo giá.', 'skvn-marine' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-skvn-primary"} -->
					<div class="wp-block-button is-style-skvn-primary"><a class="wp-block-button__link wp-element-button" href="/request-a-quote/"><?php echo esc_html__( 'Yêu cầu báo giá', 'skvn-marine' ); ?></a></div>
					<!-- /wp:button -->

					<!-- wp:button -->
					<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/"><?php echo esc_html__( 'Liên hệ', 'skvn-marine' ); ?></a></div>
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
		<p class="skvn-section__eyebrow"><?php echo esc_html__( 'Thông báo', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"className":"skvn-section__heading"} -->
		<h2 class="wp-block-heading skvn-section__heading"><?php echo esc_html__( 'Nội dung đang được cập nhật', 'skvn-marine' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"skvn-section__lead"} -->
		<p class="skvn-section__lead"><?php echo esc_html__( 'Đây là trang placeholder. Khi nội dung sẵn sàng, thay slider, copy và liên kết bằng nội dung chính thức hoặc chuyển sang pattern trang đầy đủ.', 'skvn-marine' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->