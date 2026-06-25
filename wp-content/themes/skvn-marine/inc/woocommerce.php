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
 * Modify product tabs: replace WC defaults with 4 SKVN tabs.
 *
 * @param array<string,array<string,mixed>> $tabs WooCommerce product tabs.
 * @return array<string,array<string,mixed>>
 */
function skvn_marine_woocommerce_product_tabs( array $tabs ): array {
	unset( $tabs['description'], $tabs['additional_information'], $tabs['reviews'] );

	$tabs['skvn_product_info'] = array(
		'title'    => __( 'Product Info', 'skvn-marine' ),
		'priority' => 10,
		'callback' => 'skvn_marine_tab_product_info',
	);

	$tabs['skvn_documents'] = array(
		'title'    => __( 'Certifications & Docs', 'skvn-marine' ),
		'priority' => 20,
		'callback' => 'skvn_marine_tab_documents',
	);

	$tabs['skvn_packaging'] = array(
		'title'    => __( 'Packaging & Cold Chain', 'skvn-marine' ),
		'priority' => 30,
		'callback' => 'skvn_marine_tab_packaging',
	);

	$tabs['skvn_articles'] = array(
		'title'    => __( 'Related Articles', 'skvn-marine' ),
		'priority' => 40,
		'callback' => 'skvn_marine_tab_articles',
	);

	return $tabs;
}

/**
 * Tab 0 — Product Info: description + WC attributes + right column.
 *
 * @return void
 */
function skvn_marine_tab_product_info(): void {
	global $product;
	?>
	<div class="skvn-tab-product-info">
		<div class="skvn-tab-product-info__main">
			<div class="skvn-tab-product-info__description">
				<?php the_content(); ?>
			</div>

			<?php if ( $product instanceof WC_Product ) : ?>
				<div class="skvn-tab-product-info__specs">
					<h3 class="skvn-tab-section-heading"><?php esc_html_e( 'Technical Specifications', 'skvn-marine' ); ?></h3>
					<?php wc_display_product_attributes( $product ); ?>
				</div>
			<?php endif; ?>
		</div>

		<aside class="skvn-tab-product-info__sidebar">
			<div class="skvn-tab-sidebar-card">
				<h4 class="skvn-tab-sidebar-card__heading"><?php esc_html_e( 'Certifications', 'skvn-marine' ); ?></h4>
				<ul class="skvn-tab-sidebar-card__list">
					<li>HACCP (Codex Alimentarius)</li>
					<li>BRC Food Safety Grade A</li>
					<li>FDA Registered Facility</li>
					<li>EU IUU Regulation Compliant</li>
					<li>Heavy Metal Tested (Hg / Pb)</li>
				</ul>
			</div>

			<div class="skvn-tab-sidebar-card">
				<h4 class="skvn-tab-sidebar-card__heading"><?php esc_html_e( 'Export Market Notes', 'skvn-marine' ); ?></h4>
				<ul class="skvn-tab-sidebar-card__list">
					<li><strong>USA:</strong> FDA labeling + net weight accuracy required</li>
					<li><strong>EU:</strong> Strict Hg/Pb testing + valid DL 887 mandatory</li>
					<li><strong>Japan:</strong> Extreme sensory uniformity standards</li>
				</ul>
			</div>

			<div class="skvn-tab-sidebar-card skvn-tab-sidebar-card--highlight">
				<h4 class="skvn-tab-sidebar-card__heading"><?php esc_html_e( 'Yield from Whole Fish', 'skvn-marine' ); ?></h4>
				<p class="skvn-tab-sidebar-card__yield">35–40%</p>
				<p class="skvn-tab-sidebar-card__yield-note"><?php esc_html_e( 'A 1% yield improvement from optimized cutting can represent thousands of dollars in recovered profit per container.', 'skvn-marine' ); ?></p>
			</div>
		</aside>
	</div>
	<?php
}

/**
 * Tab 1 — Certifications & Documents.
 *
 * @return void
 */
function skvn_marine_tab_documents(): void {
	global $product;
	$quote_url = $product instanceof WC_Product ? esc_url( skvn_marine_get_product_quote_url( $product ) ) : esc_url( home_url( '/request-a-quote/' ) );
	?>
	<div class="skvn-tab-documents">
		<div class="skvn-tab-documents__cert-grid">
			<div class="skvn-cert-card">
				<div class="skvn-cert-card__badge">HACCP</div>
				<div class="skvn-cert-card__body">
					<strong><?php esc_html_e( 'HACCP', 'skvn-marine' ); ?></strong>
					<span><?php esc_html_e( 'Codex Alimentarius', 'skvn-marine' ); ?></span>
				</div>
			</div>
			<div class="skvn-cert-card">
				<div class="skvn-cert-card__badge">BRC</div>
				<div class="skvn-cert-card__body">
					<strong><?php esc_html_e( 'BRC Food Safety', 'skvn-marine' ); ?></strong>
					<span><?php esc_html_e( 'Grade A · Valid 2024–2025', 'skvn-marine' ); ?></span>
				</div>
			</div>
			<div class="skvn-cert-card">
				<div class="skvn-cert-card__badge">FDA</div>
				<div class="skvn-cert-card__body">
					<strong><?php esc_html_e( 'FDA Registered', 'skvn-marine' ); ?></strong>
					<span><?php esc_html_e( 'Active · Reg. #19123456', 'skvn-marine' ); ?></span>
				</div>
			</div>
			<div class="skvn-cert-card">
				<div class="skvn-cert-card__badge">EU</div>
				<div class="skvn-cert-card__body">
					<strong><?php esc_html_e( 'EU IUU Compliant', 'skvn-marine' ); ?></strong>
					<span><?php esc_html_e( 'DL 887 · FAO Area 71', 'skvn-marine' ); ?></span>
				</div>
			</div>
		</div>

		<div class="skvn-tab-documents__doc-list">
			<h3 class="skvn-tab-section-heading"><?php esc_html_e( 'Available Documents', 'skvn-marine' ); ?></h3>
			<table class="skvn-doc-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Document', 'skvn-marine' ); ?></th>
						<th><?php esc_html_e( 'Status', 'skvn-marine' ); ?></th>
						<th><?php esc_html_e( 'Description', 'skvn-marine' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Certificate of Analysis (COA)', 'skvn-marine' ); ?></strong></td>
						<td><span class="skvn-doc-status skvn-doc-status--current"><?php esc_html_e( 'CURRENT', 'skvn-marine' ); ?></span></td>
						<td><?php esc_html_e( 'TVB-N · Salmonella/Listeria · Heavy Metals · Moisture · Sensory — Q2/2025', 'skvn-marine' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'HACCP Plan & Annual Audit', 'skvn-marine' ); ?></strong></td>
						<td><span class="skvn-doc-status skvn-doc-status--nda"><?php esc_html_e( 'NDA REQUIRED', 'skvn-marine' ); ?></span></td>
						<td><?php esc_html_e( 'CCPs · Monitoring procedures · IQF tunnel calibration records', 'skvn-marine' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'BRC Grade A Certificate', 'skvn-marine' ); ?></strong></td>
						<td><span class="skvn-doc-status skvn-doc-status--current"><?php esc_html_e( 'CURRENT', 'skvn-marine' ); ?></span></td>
						<td><?php esc_html_e( 'Scope: Processing of frozen fish fillets · Vung Tau (DL 887)', 'skvn-marine' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'EU IUU Catch Certificate', 'skvn-marine' ); ?></strong></td>
						<td><span class="skvn-doc-status skvn-doc-status--per-shipment"><?php esc_html_e( 'PER SHIPMENT', 'skvn-marine' ); ?></span></td>
						<td><?php esc_html_e( 'Vessel · Landing site · FAO Area 71 — Sample available', 'skvn-marine' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Heavy Metal Report (Hg / Pb)', 'skvn-marine' ); ?></strong></td>
						<td><span class="skvn-doc-status skvn-doc-status--passed"><?php esc_html_e( 'PASSED', 'skvn-marine' ); ?></span></td>
						<td><?php esc_html_e( 'Below EC 1881/2006 limits · Hg <0.5 mg/kg · Pb <0.3 mg/kg', 'skvn-marine' ); ?></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Product Spec Sheet', 'skvn-marine' ); ?></strong></td>
						<td>—</td>
						<td><?php esc_html_e( 'Full attribute table · Pack specs · Label template · Version 2025', 'skvn-marine' ); ?></td>
					</tr>
				</tbody>
			</table>
			<p class="skvn-tab-documents__custom-note"><?php esc_html_e( 'Custom documentation available per shipment: Health certificates · FDA prior notice · Country-of-origin affidavits · SIMP entry data.', 'skvn-marine' ); ?></p>
		</div>

		<div class="skvn-tab-documents__cta">
			<a href="<?php echo $quote_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="skvn-button skvn-button--primary">
				<?php esc_html_e( 'Request Full Document Package', 'skvn-marine' ); ?>
			</a>
			<p><?php esc_html_e( 'Full COA and lab test reports available upon request. Quote response within 1 business day.', 'skvn-marine' ); ?></p>
		</div>
	</div>
	<?php
}

/**
 * Tab 2 — Packaging & Cold Chain.
 *
 * @return void
 */
function skvn_marine_tab_packaging(): void {
	global $product;
	$quote_url = $product instanceof WC_Product ? esc_url( skvn_marine_get_product_quote_url( $product ) ) : esc_url( home_url( '/request-a-quote/' ) );
	?>
	<div class="skvn-tab-packaging">
		<div class="skvn-tab-packaging__photo-grid">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<div class="skvn-packaging-photo-placeholder">
					<svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="1.5"/><circle cx="8.5" cy="8.5" r="1.5" stroke-width="1.5"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 15l-5-5L5 21"/></svg>
					<span><?php esc_html_e( 'Photo coming soon', 'skvn-marine' ); ?></span>
				</div>
			<?php endfor; ?>
		</div>
		<p class="skvn-tab-packaging__assets-note"><?php esc_html_e( 'Hi-res assets available: Factory, processing line, IQF tunnel, and cold chain documentation upon request.', 'skvn-marine' ); ?></p>

		<div class="skvn-cold-chain-banner">
			<h3 class="skvn-cold-chain-banner__heading"><?php esc_html_e( 'Cold Chain & Pallet Specification', 'skvn-marine' ); ?></h3>
			<div class="skvn-cold-chain-banner__specs">
				<div class="skvn-cold-chain-spec">
					<span class="skvn-cold-chain-spec__value">10 kg</span>
					<span class="skvn-cold-chain-spec__label"><?php esc_html_e( 'Master Carton', 'skvn-marine' ); ?></span>
				</div>
				<div class="skvn-cold-chain-spec">
					<span class="skvn-cold-chain-spec__value">40</span>
					<span class="skvn-cold-chain-spec__label"><?php esc_html_e( 'Cases / Pallet', 'skvn-marine' ); ?></span>
				</div>
				<div class="skvn-cold-chain-spec">
					<span class="skvn-cold-chain-spec__value">~720 kg</span>
					<span class="skvn-cold-chain-spec__label"><?php esc_html_e( 'Net Weight / Pallet', 'skvn-marine' ); ?></span>
				</div>
				<div class="skvn-cold-chain-spec">
					<span class="skvn-cold-chain-spec__value">≤ −18°C</span>
					<span class="skvn-cold-chain-spec__label"><?php esc_html_e( 'Storage Temp', 'skvn-marine' ); ?></span>
				</div>
				<div class="skvn-cold-chain-spec">
					<span class="skvn-cold-chain-spec__value">24 mo</span>
					<span class="skvn-cold-chain-spec__label"><?php esc_html_e( 'Shelf Life', 'skvn-marine' ); ?></span>
				</div>
			</div>
		</div>

		<div class="skvn-tab-packaging__cta">
			<a href="<?php echo $quote_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" class="skvn-button skvn-button--outline">
				<?php esc_html_e( 'Request Hi-Res Assets & Specs', 'skvn-marine' ); ?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Tab 3 — Related Articles.
 *
 * Queries posts by taxonomy when available; falls back to static cards.
 *
 * @return void
 */
function skvn_marine_tab_articles(): void {
	$static_articles = array(
		array(
			'title'    => 'What Is IQF Freezing — And Why It Matters for Premium White Fish Quality',
			'category' => 'IQF Technology',
			'date'     => 'Jun 10, 2025',
			'read'     => '6 min read',
			'excerpt'  => 'IQF (Individual Quick Freezing) is the primary determinant of fillet texture and drip loss after thawing. Explains the science behind tunnel contact time, ice crystal size, and why slow-frozen block fish underperforms.',
		),
		array(
			'title'    => 'Vessel to Pallet: Our Full EU IUU Traceability Process',
			'category' => 'Traceability',
			'date'     => 'May 22, 2025',
			'read'     => '8 min read',
			'excerpt'  => 'From catch certificate intake at the landing site to lot-coded master cartons — how the internal traceability chain eliminates port-of-entry rejection risk in EU markets.',
		),
		array(
			'title'    => 'Reading a Grouper COA: Every Parameter Your Import Team Needs to Verify',
			'category' => 'COA & Quality',
			'date'     => 'Apr 15, 2025',
			'read'     => '7 min read',
			'excerpt'  => 'TVB-N limits, Salmonella zero-tolerance thresholds, Hg/Pb ceilings under EC 1881/2006, moisture retention — what each means for US/EU import compliance.',
		),
	);

	$articles = array();
	?>
	<div class="skvn-tab-articles">
		<div class="skvn-article-grid">
			<?php
			if ( ! empty( $articles ) ) :
				foreach ( $articles as $post ) :
					setup_postdata( $post );
					?>
					<article class="skvn-article-card">
						<div class="skvn-article-card__meta">
							<span class="skvn-article-card__cat"><?php the_category( ', ' ); ?></span>
							<span class="skvn-article-card__date"><?php echo esc_html( get_the_date() ); ?></span>
						</div>
						<h3 class="skvn-article-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="skvn-article-card__excerpt"><?php the_excerpt(); ?></p>
						<a href="<?php the_permalink(); ?>" class="skvn-article-card__link"><?php esc_html_e( 'Read article →', 'skvn-marine' ); ?></a>
					</article>
					<?php
				endforeach;
				wp_reset_postdata();
			else :
				foreach ( $static_articles as $article ) :
					?>
					<article class="skvn-article-card">
						<div class="skvn-article-card__meta">
							<span class="skvn-article-card__cat"><?php echo esc_html( $article['category'] ); ?></span>
							<span class="skvn-article-card__date"><?php echo esc_html( $article['date'] ); ?> · <?php echo esc_html( $article['read'] ); ?></span>
						</div>
						<h3 class="skvn-article-card__title"><?php echo esc_html( $article['title'] ); ?></h3>
						<p class="skvn-article-card__excerpt"><?php echo esc_html( $article['excerpt'] ); ?></p>
					</article>
					<?php
				endforeach;
			endif;
			?>
		</div>
	</div>
	<?php
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
