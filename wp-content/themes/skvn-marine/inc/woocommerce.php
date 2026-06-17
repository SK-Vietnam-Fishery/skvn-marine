<?php
/**
 * WooCommerce visual integration for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'woocommerce_enqueue_styles', 'skvn_marine_woocommerce_enqueue_styles' );
add_filter( 'woocommerce_loop_add_to_cart_link', 'skvn_marine_woocommerce_loop_quote_link', 10, 3 );
add_action( 'wp', 'skvn_marine_woocommerce_replace_single_product_cta' );
add_filter( 'woocommerce_product_tabs', 'skvn_marine_woocommerce_product_tabs', 98 );
add_filter( 'woocommerce_placeholder_img', 'skvn_marine_woocommerce_placeholder', 10, 3 );

/**
 * Keep WooCommerce styles enabled for V1 visual overrides.
 *
 * @param array<string,array<string,string>> $styles WooCommerce styles.
 * @return array<string,array<string,string>>
 */
function skvn_marine_woocommerce_enqueue_styles( $styles ) {
	return $styles;
}

/**
 * Build the milestone-approved request quote URL for a product.
 *
 * @param WC_Product|int $product Product object or product ID.
 * @return string
 */
function skvn_marine_get_product_quote_url( $product ) {
	$product_id = $product instanceof WC_Product ? $product->get_id() : absint( $product );

	if ( ! $product_id ) {
		return home_url( '/request-a-quote/' );
	}

	return add_query_arg(
		array(
			'product_id' => $product_id,
		),
		home_url( '/request-a-quote/' )
	);
}

/**
 * Replace catalog add-to-cart buttons with same-site quote CTAs.
 *
 * @param string     $html    Original add-to-cart markup.
 * @param WC_Product $product Product object.
 * @param array      $args    Button args.
 * @return string
 */
function skvn_marine_woocommerce_loop_quote_link( $html, $product, $args ) {
	if ( ! $product instanceof WC_Product ) {
		return $html;
	}

	$classes = array( 'button', 'skvn-button', 'skvn-button--primary', 'skvn-product-card__quote-link' );

	if ( isset( $args['class'] ) && is_string( $args['class'] ) ) {
		$classes = array_merge(
			$classes,
			array_map( 'sanitize_html_class', preg_split( '/\s+/', $args['class'] ) )
		);
	}

	return sprintf(
		'<a href="%1$s" class="%2$s" aria-label="%3$s">%4$s</a>',
		esc_url( skvn_marine_get_product_quote_url( $product ) ),
		esc_attr( implode( ' ', array_unique( array_filter( $classes ) ) ) ),
		esc_attr(
			sprintf(
				/* translators: %s: product name. */
				__( 'Request a quote for %s', 'skvn-marine' ),
				$product->get_name()
			)
		),
		esc_html__( 'Request a Quote', 'skvn-marine' )
	);
}

/**
 * Swap the single product purchase area for the visual quote CTA in V1.
 *
 * @return void
 */
function skvn_marine_woocommerce_replace_single_product_cta() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'skvn_marine_woocommerce_single_quote_cta', 30 );
}

/**
 * Render the single product quote CTA with trust signals.
 *
 * Full-width primary CTA + secondary contact link + 3 trust badges.
 *
 * @return void
 */
function skvn_marine_woocommerce_single_quote_cta() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$quote_url   = esc_url( skvn_marine_get_product_quote_url( $product ) );
	$contact_url = esc_url( home_url( '/lien-he/' ) );

	?>
	<div class="skvn-product-cta-zone">
		<a class="skvn-product-cta-primary" href="<?php echo $quote_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above ?>">
			<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
			<?php esc_html_e( 'Yêu cầu báo giá sản phẩm này', 'skvn-marine' ); ?>
		</a>
		<a class="skvn-product-cta-secondary" href="<?php echo $contact_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- already escaped above ?>">
			<svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
			<?php esc_html_e( 'Liên hệ tư vấn trực tiếp', 'skvn-marine' ); ?>
		</a>
		<p class="skvn-product-cta-note"><?php esc_html_e( 'Phản hồi báo giá trong vòng 24h làm việc', 'skvn-marine' ); ?></p>

		<div class="skvn-product-trust" role="list">
			<div class="skvn-product-trust__item" role="listitem">
				<span class="skvn-product-trust__icon" aria-hidden="true">🏅</span>
				<span class="skvn-product-trust__label"><?php esc_html_e( 'VSATTP Bộ Y Tế', 'skvn-marine' ); ?></span>
			</div>
			<div class="skvn-product-trust__item" role="listitem">
				<span class="skvn-product-trust__icon" aria-hidden="true">❄️</span>
				<span class="skvn-product-trust__label"><?php esc_html_e( 'Cold Chain Certified', 'skvn-marine' ); ?></span>
			</div>
			<div class="skvn-product-trust__item" role="listitem">
				<span class="skvn-product-trust__icon" aria-hidden="true">✅</span>
				<span class="skvn-product-trust__label"><?php esc_html_e( 'Bảo hành 24 tháng', 'skvn-marine' ); ?></span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Modify product tabs: remove Reviews, rename Description and Additional Information.
 *
 * @param array<string,array<string,mixed>> $tabs WooCommerce product tabs.
 * @return array<string,array<string,mixed>>
 */
function skvn_marine_woocommerce_product_tabs( array $tabs ): array {
	// Remove Reviews tab — B2B context, no review surface needed.
	unset( $tabs['reviews'] );

	// Rename Description → Vietnamese label.
	if ( isset( $tabs['description'] ) ) {
		$tabs['description']['title'] = __( 'Mô tả sản phẩm', 'skvn-marine' );
	}

	// Rename Additional Information → Thông số kỹ thuật.
	if ( isset( $tabs['additional_information'] ) ) {
		$tabs['additional_information']['title'] = __( 'Thông số kỹ thuật', 'skvn-marine' );
	}

	// Add Documents & Certifications tab.
	$tabs['skvn_documents'] = array(
		'title'    => __( 'Tài liệu & Chứng nhận', 'skvn-marine' ),
		'priority' => 50,
		'callback' => 'skvn_marine_woocommerce_documents_tab_content',
	);

	return $tabs;
}

/**
 * Render the Documents & Certifications tab content.
 *
 * V1: static placeholder — invite users to contact for full documents.
 *
 * @return void
 */
function skvn_marine_woocommerce_documents_tab_content(): void {
	echo '<div class="skvn-product-tab-documents">';
	echo '<h2>' . esc_html__( 'Tài liệu & Chứng nhận', 'skvn-marine' ) . '</h2>';
	echo '<p>' . esc_html__( 'Vui lòng liên hệ để nhận bộ tài liệu đầy đủ bao gồm:', 'skvn-marine' ) . '</p>';
	echo '<ul>';
	$docs = array(
		__( 'Chứng nhận VSATTP — Bộ Y Tế (PDF)', 'skvn-marine' ),
		__( 'Certificate of Conformity — EC 853/2004 (PDF)', 'skvn-marine' ),
		__( 'Manual lắp đặt & vận hành (Tiếng Việt)', 'skvn-marine' ),
		__( 'Sơ đồ điện và hệ thống lạnh', 'skvn-marine' ),
		__( 'Báo cáo kiểm tra xuất xưởng', 'skvn-marine' ),
	);
	foreach ( $docs as $doc ) {
		echo '<li>' . esc_html( $doc ) . '</li>';
	}
	echo '</ul>';
	echo '</div>';
}

/**
 * Replace WooCommerce placeholder image with branded navy placeholder.
 *
 * @param string $html     Original placeholder HTML.
 * @param mixed  $size     Image size requested.
 * @param mixed  $classes  Additional CSS classes.
 * @return string
 */
function skvn_marine_woocommerce_placeholder( string $html, $size, $classes ): string {
	return '<div class="skvn-product-placeholder" aria-label="' . esc_attr__( 'Hình sắp cập nhật', 'skvn-marine' ) . '"><span>' . esc_html__( 'Hình sắp cập nhật', 'skvn-marine' ) . '</span></div>';
}
