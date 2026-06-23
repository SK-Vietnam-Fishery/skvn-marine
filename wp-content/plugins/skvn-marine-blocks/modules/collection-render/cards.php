<?php
/**
 * Shared collection card render helpers.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render a post card.
 *
 * @param WP_Post $post Post object.
 * @return string
 */
function skvn_marine_blocks_render_collection_post_card( $post, $attributes = array() ) {
	$title          = get_the_title( $post );
	$url            = get_permalink( $post );
	$action_mode    = isset( $attributes['postActionMode'] ) ? sanitize_key( $attributes['postActionMode'] ) : 'read';
	$custom_url     = isset( $attributes['customActionUrl'] ) ? esc_url_raw( $attributes['customActionUrl'] ) : '';
	$action_url     = 'custom' === $action_mode && '' !== $custom_url ? $custom_url : $url;
	$show_image     = skvn_marine_blocks_collection_bool( $attributes, 'showImage', true );
	$show_date      = skvn_marine_blocks_collection_bool( $attributes, 'showDate', true );
	$show_author    = skvn_marine_blocks_collection_bool( $attributes, 'showAuthor', false );
	$show_excerpt   = skvn_marine_blocks_collection_bool( $attributes, 'showExcerpt', true );
	$read_more      = isset( $attributes['readMoreLabel'] ) && '' !== $attributes['readMoreLabel']
		? sanitize_text_field( $attributes['readMoreLabel'] )
		: __( 'Đọc thêm →', 'skvn-marine-blocks' );

	ob_start();
	?>
	<article class="skvn-collection-card skvn-collection-card--post">
		<?php if ( $show_image ) : ?>
			<a class="skvn-collection-card__media" href="<?php echo esc_url( $url ); ?>">
				<?php if ( has_post_thumbnail( $post ) ) : ?>
					<?php echo get_the_post_thumbnail( $post, 'medium_large', array( 'class' => 'skvn-collection-card__image' ) ); ?>
				<?php else : ?>
					<span class="skvn-collection-card__fallback" aria-hidden="true"></span>
				<?php endif; ?>
				<?php echo skvn_marine_blocks_render_collection_term_badges( $post->ID, 'category', $attributes, 'showPostCategories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</a>
		<?php endif; ?>
		<div class="skvn-collection-card__body">
			<?php if ( $show_date ) : ?>
				<span class="skvn-collection-card__date"><?php echo esc_html( get_the_date( '', $post ) ); ?></span>
			<?php endif; ?>
			<?php if ( $show_author ) : ?>
				<span class="skvn-collection-card__author"><?php echo esc_html( get_the_author_meta( 'display_name', (int) $post->post_author ) ); ?></span>
			<?php endif; ?>
			<h3 class="skvn-collection-card__title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<?php if ( $show_excerpt ) : ?>
				<div class="skvn-collection-card__excerpt">
					<?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 22 ) ); ?>
				</div>
			<?php endif; ?>
			<a class="skvn-collection-card__read-more" href="<?php echo esc_url( $action_url ); ?>">
				<?php echo esc_html( $read_more ); ?>
			</a>
		</div>
	</article>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render a product card.
 *
 * @param WC_Product $product Product object.
 * @param array      $attributes Block attributes.
 * @return string
 */
function skvn_marine_blocks_render_collection_product_card( $product, $attributes = array() ) {
	$product_id  = $product->get_id();
	$title       = $product->get_name();
	$url         = get_permalink( $product_id );
	$action_mode = isset( $attributes['productActionMode'] ) ? sanitize_key( $attributes['productActionMode'] ) : 'quote';
	$custom_url  = isset( $attributes['customActionUrl'] ) ? esc_url_raw( $attributes['customActionUrl'] ) : '';
	$show_image  = skvn_marine_blocks_collection_bool( $attributes, 'showImage', true );
	$chip_style  = isset( $attributes['chipStyle'] ) ? sanitize_key( $attributes['chipStyle'] ) : 'tag';
	$chip_color  = isset( $attributes['chipColorScheme'] ) ? sanitize_key( $attributes['chipColorScheme'] ) : '';

	$card_classes = array( 'skvn-collection-card', 'skvn-collection-card--product' );
	if ( 'tag' !== $chip_style ) {
		$card_classes[] = 'skvn-collection-card--chip-' . $chip_style;
	}
	if ( '' !== $chip_color ) {
		$card_classes[] = 'skvn-chips--' . $chip_color;
	}

	// Placeholder meta — render if present, hide if empty.
	$certs     = get_post_meta( $product_id, '_skvn_certifications', true );
	$moq       = get_post_meta( $product_id, '_skvn_moq', true );
	$lead_time = get_post_meta( $product_id, '_skvn_lead_time', true );
	$pdf_url   = get_post_meta( $product_id, '_skvn_spec_sheet_url', true );

	ob_start();
	?>
	<article class="<?php echo esc_attr( implode( ' ', $card_classes ) ); ?>">
		<?php if ( $show_image ) : ?>
			<a class="skvn-collection-card__media" href="<?php echo esc_url( $url ); ?>">
				<?php echo skvn_marine_blocks_get_product_image_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php
				// Badge overlay: product_tag (Wild Caught, Farmed) — always on image.
				echo skvn_marine_blocks_render_collection_term_badges( $product_id, 'product_tag', $attributes, 'showProductTags' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</a>
		<?php endif; ?>
		<div class="skvn-collection-card__body">
			<h3 class="skvn-collection-card__title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showSpecChips', true ) ) : ?>
				<?php
				$product_attrs = $product->get_attributes();
				$chip_values   = array();
				foreach ( $product_attrs as $attr ) {
					if ( ! $attr->get_visible() ) {
						continue;
					}
					if ( $attr->is_taxonomy() ) {
						$terms = $attr->get_terms();
						if ( $terms ) {
							foreach ( $terms as $term ) {
								$chip_values[] = $term->name;
							}
						}
					} else {
						foreach ( $attr->get_options() as $option ) {
							$chip_values[] = $option;
						}
					}
				}
				if ( ! empty( $chip_values ) ) :
					?>
					<div class="skvn-collection-card__specs">
						<?php foreach ( $chip_values as $chip_value ) : ?>
							<span class="skvn-collection-card__spec-tag"><?php echo esc_html( $chip_value ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php // Catalog slot — owned by woo-catalog (1.5.0). Move this div + CSS to woo-catalog plugin when ready. See docs/decisions/woo-catalog-css-migration-1.5.0.md ?>
			<div class="skvn-collection-card__catalog">
				<?php if ( ! empty( $certs ) && is_array( $certs ) ) : ?>
					<div class="skvn-collection-card__certs">
						<?php foreach ( $certs as $cert ) : ?>
							<span class="skvn-collection-card__cert-dot"><?php echo esc_html( $cert ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $moq || '' !== $lead_time ) : ?>
					<div class="skvn-collection-card__stats">
						<?php if ( '' !== $moq ) : ?>
							<div>
								<span class="skvn-collection-card__stat-label"><?php esc_html_e( 'MOQ', 'skvn-marine-blocks' ); ?></span>
								<span class="skvn-collection-card__stat-value"><?php echo esc_html( $moq ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( '' !== $lead_time ) : ?>
							<div>
								<span class="skvn-collection-card__stat-label"><?php esc_html_e( 'Lead time', 'skvn-marine-blocks' ); ?></span>
								<span class="skvn-collection-card__stat-value"><?php echo esc_html( $lead_time ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<?php if ( '' !== $pdf_url ) : ?>
					<a class="skvn-collection-card__pdf" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener noreferrer">
						<?php esc_html_e( 'Download spec sheet (PDF)', 'skvn-marine-blocks' ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( in_array( $action_mode, array( 'quote', 'both' ), true ) ) : ?>
				<a class="skvn-collection-card__cta" href="<?php echo esc_url( skvn_marine_blocks_get_product_quote_url( $product ) ); ?>">
					<?php esc_html_e( 'Request quote', 'skvn-marine-blocks' ); ?>
				</a>
			<?php elseif ( 'view' === $action_mode ) : ?>
				<a class="skvn-collection-card__cta" href="<?php echo esc_url( $url ); ?>">
					<?php esc_html_e( 'View product', 'skvn-marine-blocks' ); ?>
				</a>
			<?php elseif ( 'custom' === $action_mode && '' !== $custom_url ) : ?>
				<a class="skvn-collection-card__cta" href="<?php echo esc_url( skvn_marine_blocks_maybe_append_product_context( $custom_url, $product, $attributes ) ); ?>">
					<?php esc_html_e( 'Learn more', 'skvn-marine-blocks' ); ?>
				</a>
			<?php endif; ?>
		</div>
	</article>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render taxonomy badges for a card.
 *
 * @param int    $object_id Object ID.
 * @param string $taxonomy  Taxonomy name.
 * @param array  $attributes Block attributes.
 * @param string $visibility_key Visibility attribute key.
 * @return string
 */
function skvn_marine_blocks_render_collection_term_badges( $object_id, $taxonomy, $attributes, $visibility_key ) {
	if ( ! skvn_marine_blocks_collection_bool( $attributes, $visibility_key, true ) ) {
		return '';
	}

	$terms = get_the_terms( $object_id, $taxonomy );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	$link_badges = isset( $attributes['badgeBehavior'] ) && 'archive-link' === $attributes['badgeBehavior'];

	ob_start();
	?>
	<div class="skvn-collection-card__badges">
		<?php foreach ( $terms as $term ) : ?>
			<?php if ( $link_badges ) : ?>
				<a class="skvn-collection-card__badge" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
			<?php else : ?>
				<span class="skvn-collection-card__badge"><?php echo esc_html( $term->name ); ?></span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render shared collection footer (pagination slot + catalog CTA + archive link).
 *
 * @param array  $attributes Block attributes.
 * @param string $context    'carousel' | 'grid'.
 * @return string
 */
function skvn_marine_blocks_render_collection_footer( $attributes, $context = 'grid' ) {
	$show_pagination  = skvn_marine_blocks_collection_bool( $attributes, 'showPagination', true );
	$show_catalog_cta = skvn_marine_blocks_collection_bool( $attributes, 'showCatalogCta', false );
	$catalog_cta_url  = isset( $attributes['catalogCtaUrl'] ) ? esc_url_raw( $attributes['catalogCtaUrl'] ) : '';
	$catalog_cta_label = isset( $attributes['catalogCtaLabel'] ) && '' !== $attributes['catalogCtaLabel']
		? sanitize_text_field( $attributes['catalogCtaLabel'] )
		: __( 'Tải catalog', 'skvn-marine-blocks' );
	$archive_url      = isset( $attributes['archiveUrl'] ) ? esc_url_raw( $attributes['archiveUrl'] ) : '';
	$archive_label    = isset( $attributes['archiveLabel'] ) && '' !== $attributes['archiveLabel']
		? sanitize_text_field( $attributes['archiveLabel'] )
		: __( 'View all', 'skvn-marine-blocks' );

	$has_left  = 'carousel' === $context && $show_pagination;
	$has_right = ( $show_catalog_cta && '' !== $catalog_cta_url ) || '' !== $archive_url;

	if ( ! $has_left && ! $has_right ) {
		return '';
	}

	ob_start();
	?>
	<div class="skvn-collection__footer">
		<div class="skvn-collection__footer-left">
			<?php if ( $has_left ) : ?>
				<div class="skvn-collection__pagination" role="group" aria-label="<?php esc_attr_e( 'Slides', 'skvn-marine-blocks' ); ?>"></div>
			<?php endif; ?>
		</div>
		<div class="skvn-collection__footer-right">
			<?php if ( $show_catalog_cta && '' !== $catalog_cta_url ) : ?>
				<a class="skvn-collection__catalog-cta" href="<?php echo esc_url( $catalog_cta_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $catalog_cta_label ); ?>
				</a>
			<?php endif; ?>
			<?php if ( '' !== $archive_url ) : ?>
				<a class="skvn-collection__archive-link" href="<?php echo esc_url( $archive_url ); ?>">
					<?php echo esc_html( $archive_label ); ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render the section header (eyebrow + heading + intro + optional nav arrows).
 *
 * In carousel mode the nav arrows are placed here (top-right) rather than
 * floating on the sides of the track. JS finds them via
 * closest('.skvn-collection') so they can live outside carousel-outer.
 *
 * @param array $attributes  Block attributes.
 * @param bool  $is_carousel Whether to include nav arrows.
 * @return string
 */
function skvn_marine_blocks_render_collection_header( array $attributes, bool $is_carousel = false ): string {
	$eyebrow      = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : '';
	$show_heading = skvn_marine_blocks_collection_bool( $attributes, 'showHeading', true );
	$heading      = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$intro        = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$show_arrows  = $is_carousel && skvn_marine_blocks_collection_bool( $attributes, 'showArrows', true );

	$has_text = ( '' !== $eyebrow ) || ( $show_heading && '' !== $heading ) || ( '' !== $intro );

	if ( ! $has_text && ! $show_arrows ) {
		return '';
	}

	ob_start();
	?>
	<div class="skvn-collection__header">
		<?php if ( $has_text ) : ?>
		<div class="skvn-collection__header-text">
			<?php if ( '' !== $eyebrow ) : ?>
				<p class="skvn-collection__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $show_heading && '' !== $heading ) : ?>
				<h2 class="skvn-collection__heading"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $intro ) : ?>
				<div class="skvn-collection__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if ( $show_arrows ) : ?>
		<div class="skvn-collection__header-nav">
			<button class="skvn-collection__arrow skvn-collection__arrow--prev" aria-label="<?php esc_attr_e( 'Previous', 'skvn-marine-blocks' ); ?>">&#8249;</button>
			<button class="skvn-collection__arrow skvn-collection__arrow--next" aria-label="<?php esc_attr_e( 'Next', 'skvn-marine-blocks' ); ?>">&#8250;</button>
		</div>
		<?php endif; ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render the carousel wrapper (swiper container + slides + controls).
 *
 * Arrows are NOT rendered here — they live in the section header produced by
 * skvn_marine_blocks_render_collection_header(). JS locates them by walking
 * up to the nearest .skvn-collection ancestor.
 *
 * @param array  $items       WP_Post[] or WC_Product[] array.
 * @param array  $attributes  Block attributes.
 * @param string $content_type 'post' | 'product'.
 * @param string $footer_html  Pre-rendered footer HTML from render_collection_footer().
 * @return string
 */
function skvn_marine_blocks_render_collection_carousel( $items, $attributes, $content_type, $footer_html = '' ) {
	$preset = isset( $attributes['responsivePreset'] ) ? sanitize_text_field( $attributes['responsivePreset'] ) : '3-2-1';
	$parts  = explode( '-', $preset, 3 );
	$desktop = isset( $parts[0] ) ? absint( $parts[0] ) : 3;
	$tablet  = isset( $parts[1] ) ? absint( $parts[1] ) : 2;
	$mobile  = isset( $parts[2] ) ? absint( $parts[2] ) : 1;

	$show_arrows     = skvn_marine_blocks_collection_bool( $attributes, 'showArrows', true );
	$show_pagination = skvn_marine_blocks_collection_bool( $attributes, 'showPagination', true );
	$autoplay        = skvn_marine_blocks_collection_bool( $attributes, 'autoplay', false );
	$autoplay_delay  = isset( $attributes['autoplayDelay'] ) ? absint( $attributes['autoplayDelay'] ) : 5000;
	$autoplay_delay  = min( 10000, max( 3000, $autoplay_delay ) );

	$config = array(
		'slidesPerViewDesktop' => $desktop ?: 1,
		'slidesPerViewTablet'  => $tablet  ?: 1,
		'slidesPerViewMobile'  => $mobile  ?: 1,
		'showArrows'           => $show_arrows,
		'showPagination'       => $show_pagination,
		'autoplay'             => $autoplay,
		'autoplayDelay'        => $autoplay_delay,
	);

	ob_start();
	?>
	<div class="skvn-collection__carousel-outer" data-skvn-collection-carousel="<?php echo esc_attr( (string) wp_json_encode( $config ) ); ?>">
		<div class="skvn-collection__carousel swiper">
			<div class="swiper-wrapper">
				<?php foreach ( $items as $item ) : ?>
					<div class="swiper-slide">
						<?php
						if ( 'post' === $content_type ) {
							echo skvn_marine_blocks_render_collection_post_card( $item, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo skvn_marine_blocks_render_collection_product_card( $item, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $autoplay ) : ?>
				<button
					class="skvn-collection__pause-btn"
					aria-label="<?php esc_attr_e( 'Pause slideshow', 'skvn-marine-blocks' ); ?>"
					aria-pressed="true"
					aria-live="polite"
				><?php esc_html_e( 'Pause', 'skvn-marine-blocks' ); ?></button>
			<?php endif; ?>
		</div>
		<?php echo $footer_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>
	<?php

	return (string) ob_get_clean();
}

/**
 * Read a boolean collection attribute.
 *
 * @param array  $attributes Block attributes.
 * @param string $key Attribute key.
 * @param bool   $default Default value.
 * @return bool
 */
function skvn_marine_blocks_collection_bool( $attributes, $key, $default ) {
	return array_key_exists( $key, $attributes ) ? (bool) $attributes[ $key ] : $default;
}

/**
 * Get product image HTML with WooCommerce and SKVN fallback.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function skvn_marine_blocks_get_product_image_html( $product ) {
	$image_id = $product->get_image_id();

	if ( $image_id ) {
		return wp_get_attachment_image( $image_id, 'medium_large', false, array( 'class' => 'skvn-collection-card__image' ) );
	}

	if ( function_exists( 'wc_placeholder_img' ) ) {
		return wc_placeholder_img( 'medium_large', array( 'class' => 'skvn-collection-card__image' ) );
	}

	return '<span class="skvn-collection-card__fallback" aria-hidden="true"></span>';
}

/**
 * Build the approved quote URL for a product.
 *
 * @param WC_Product $product Product object.
 * @return string
 */
function skvn_marine_blocks_get_product_quote_url( $product ) {
	return skvn_marine_blocks_maybe_append_product_context( home_url( '/request-a-quote/' ), $product, array( 'appendQuoteContext' => true ) );
}

/**
 * Append product context query arguments when enabled.
 *
 * @param string     $url Product action URL.
 * @param WC_Product $product Product object.
 * @param array      $attributes Block attributes.
 * @return string
 */
function skvn_marine_blocks_maybe_append_product_context( $url, $product, $attributes ) {
	if ( ! skvn_marine_blocks_collection_bool( $attributes, 'appendQuoteContext', true ) ) {
		return $url;
	}

	$product_url = get_permalink( $product->get_id() );

	return add_query_arg(
		array(
			'product_id'   => $product->get_id(),
			'product_sku'  => rawurlencode( (string) $product->get_sku() ),
			'product_name' => rawurlencode( $product->get_name() ),
			'product_url'  => rawurlencode( $product_url ),
			'source_url'   => rawurlencode( skvn_marine_blocks_get_current_source_url() ),
		),
		$url
	);
}

/**
 * Get current page URL for quote source context.
 *
 * Builds URLs from WordPress routing (home_url, permalinks, queried objects).
 * Do not use $_SERVER['HTTP_HOST'] — it is client-controlled and can poison
 * quote hidden-field context.
 *
 * @return string
 */
function skvn_marine_blocks_get_current_source_url() {
	if ( is_singular() ) {
		$permalink = get_permalink();

		if ( is_string( $permalink ) && '' !== $permalink ) {
			return esc_url_raw( $permalink );
		}
	}

	if ( is_search() ) {
		return esc_url_raw(
			add_query_arg(
				array(
					's' => get_search_query(),
				),
				home_url( '/' )
			)
		);
	}

	if ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		$post_type = is_array( $post_type ) ? reset( $post_type ) : $post_type;
		$link      = get_post_type_archive_link( (string) $post_type );

		if ( is_string( $link ) && '' !== $link ) {
			return esc_url_raw( $link );
		}
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			$link = get_term_link( $term );

			if ( ! is_wp_error( $link ) ) {
				return esc_url_raw( $link );
			}
		}
	}

	global $wp;

	if ( isset( $wp->request ) && is_string( $wp->request ) && '' !== $wp->request ) {
		return esc_url_raw( home_url( user_trailingslashit( $wp->request ) ) );
	}

	return esc_url_raw( home_url( '/' ) );
}
