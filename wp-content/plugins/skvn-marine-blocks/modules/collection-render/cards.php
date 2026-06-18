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
	$title         = get_the_title( $post );
	$url           = get_permalink( $post );
	$action_mode   = isset( $attributes['postActionMode'] ) ? sanitize_key( $attributes['postActionMode'] ) : 'read';
	$custom_url    = isset( $attributes['customActionUrl'] ) ? esc_url_raw( $attributes['customActionUrl'] ) : '';
	$action_url    = 'custom' === $action_mode && '' !== $custom_url ? $custom_url : $url;
	$card_style    = isset( $attributes['cardStyle'] ) ? sanitize_key( $attributes['cardStyle'] ) : 'default';
	$show_image    = skvn_marine_blocks_collection_bool( $attributes, 'showImage', true );
	$overlay_badges = 'featured' === $card_style && $show_image;

	ob_start();
	?>
	<article class="skvn-collection-card<?php echo 'featured' === $card_style ? ' skvn-collection-card--featured' : ''; ?>">
		<?php if ( $show_image ) : ?>
			<a class="skvn-collection-card__media" href="<?php echo esc_url( $url ); ?>">
				<?php if ( has_post_thumbnail( $post ) ) : ?>
					<?php echo get_the_post_thumbnail( $post, 'medium_large', array( 'class' => 'skvn-collection-card__image' ) ); ?>
				<?php else : ?>
					<span class="skvn-collection-card__fallback" aria-hidden="true"></span>
				<?php endif; ?>
				<?php if ( $overlay_badges ) : ?>
					<?php echo skvn_marine_blocks_render_collection_term_badges( $post->ID, 'category', $attributes, 'showPostCategories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo skvn_marine_blocks_render_collection_term_badges( $post->ID, 'post_tag', $attributes, 'showPostTags' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="skvn-collection-card__body">
			<h3 class="skvn-collection-card__title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showDate', true ) || skvn_marine_blocks_collection_bool( $attributes, 'showAuthor', true ) ) : ?>
				<div class="skvn-collection-card__meta">
					<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showDate', true ) ) : ?>
						<span><?php echo esc_html( get_the_date( '', $post ) ); ?></span>
					<?php endif; ?>
					<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showAuthor', true ) ) : ?>
						<span><?php echo esc_html( get_the_author_meta( 'display_name', (int) $post->post_author ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php if ( ! $overlay_badges ) : ?>
				<?php echo skvn_marine_blocks_render_collection_term_badges( $post->ID, 'category', $attributes, 'showPostCategories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo skvn_marine_blocks_render_collection_term_badges( $post->ID, 'post_tag', $attributes, 'showPostTags' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showExcerpt', true ) ) : ?>
				<div class="skvn-collection-card__excerpt">
					<?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 22 ) ); ?>
				</div>
			<?php endif; ?>
			<?php if ( 'custom' !== $action_mode || '' !== $custom_url ) : ?>
				<a class="skvn-collection-card__action" href="<?php echo esc_url( $action_url ); ?>">
					<?php echo esc_html( 'custom' === $action_mode ? __( 'Learn more', 'skvn-marine-blocks' ) : __( 'Read more', 'skvn-marine-blocks' ) ); ?>
				</a>
			<?php endif; ?>
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
	$product_id    = $product->get_id();
	$title         = $product->get_name();
	$url           = get_permalink( $product_id );
	$action_mode   = isset( $attributes['productActionMode'] ) ? sanitize_key( $attributes['productActionMode'] ) : 'quote';
	$custom_url    = isset( $attributes['customActionUrl'] ) ? esc_url_raw( $attributes['customActionUrl'] ) : '';
	$card_style    = isset( $attributes['cardStyle'] ) ? sanitize_key( $attributes['cardStyle'] ) : 'default';
	$show_image    = skvn_marine_blocks_collection_bool( $attributes, 'showImage', true );
	$overlay_badges = 'featured' === $card_style && $show_image;

	ob_start();
	?>
	<article class="skvn-collection-card<?php echo 'featured' === $card_style ? ' skvn-collection-card--featured' : ''; ?>">
		<?php if ( $show_image ) : ?>
			<a class="skvn-collection-card__media" href="<?php echo esc_url( $url ); ?>">
				<?php echo skvn_marine_blocks_get_product_image_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $overlay_badges ) : ?>
					<?php echo skvn_marine_blocks_render_collection_term_badges( $product_id, 'product_cat', $attributes, 'showProductCategories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo skvn_marine_blocks_render_collection_term_badges( $product_id, 'product_tag', $attributes, 'showProductTags' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
			</a>
		<?php endif; ?>
		<div class="skvn-collection-card__body">
			<h3 class="skvn-collection-card__title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showPrice', true ) ) : ?>
				<div class="skvn-collection-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<?php endif; ?>
			<?php if ( ! $overlay_badges ) : ?>
				<?php echo skvn_marine_blocks_render_collection_term_badges( $product_id, 'product_cat', $attributes, 'showProductCategories' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php echo skvn_marine_blocks_render_collection_term_badges( $product_id, 'product_tag', $attributes, 'showProductTags' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showSku', false ) && $product->get_sku() ) : ?>
				<div class="skvn-collection-card__meta"><?php echo esc_html( sprintf( __( 'SKU: %s', 'skvn-marine-blocks' ), $product->get_sku() ) ); ?></div>
			<?php endif; ?>
			<?php if ( skvn_marine_blocks_collection_bool( $attributes, 'showStock', false ) ) : ?>
				<div class="skvn-collection-card__meta"><?php echo esc_html( wc_get_stock_html( $product ) ? wp_strip_all_tags( wc_get_stock_html( $product ) ) : $product->get_stock_status() ); ?></div>
			<?php endif; ?>
			<div class="skvn-collection-card__actions">
				<?php if ( in_array( $action_mode, array( 'view', 'both' ), true ) ) : ?>
					<a class="skvn-collection-card__action skvn-collection-card__action--secondary" href="<?php echo esc_url( $url ); ?>">
						<?php esc_html_e( 'View product', 'skvn-marine-blocks' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( in_array( $action_mode, array( 'quote', 'both' ), true ) ) : ?>
					<a class="skvn-collection-card__action" href="<?php echo esc_url( skvn_marine_blocks_get_product_quote_url( $product ) ); ?>">
						<?php esc_html_e( 'Request quote', 'skvn-marine-blocks' ); ?>
					</a>
				<?php endif; ?>
				<?php if ( 'custom' === $action_mode && '' !== $custom_url ) : ?>
					<a class="skvn-collection-card__action" href="<?php echo esc_url( skvn_marine_blocks_maybe_append_product_context( $custom_url, $product, $attributes ) ); ?>">
						<?php esc_html_e( 'Learn more', 'skvn-marine-blocks' ); ?>
					</a>
				<?php endif; ?>
			</div>
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
 * Render the carousel wrapper (swiper container + slides + controls).
 *
 * @param array  $items       WP_Post[] or WC_Product[] array.
 * @param array  $attributes  Block attributes.
 * @param string $content_type 'post' | 'product'.
 * @return string
 */
function skvn_marine_blocks_render_collection_carousel( $items, $attributes, $content_type ) {
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
		<?php if ( $show_arrows ) : ?>
			<button class="skvn-collection__arrow skvn-collection__arrow--prev" aria-label="<?php esc_attr_e( 'Previous', 'skvn-marine-blocks' ); ?>">&#8249;</button>
			<button class="skvn-collection__arrow skvn-collection__arrow--next" aria-label="<?php esc_attr_e( 'Next', 'skvn-marine-blocks' ); ?>">&#8250;</button>
		<?php endif; ?>
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
			<?php if ( $show_pagination ) : ?>
				<div class="skvn-collection__pagination" role="group" aria-label="<?php esc_attr_e( 'Slides', 'skvn-marine-blocks' ); ?>"></div>
			<?php endif; ?>
			<?php if ( $autoplay ) : ?>
				<button
					class="skvn-collection__pause-btn"
					aria-label="<?php esc_attr_e( 'Pause slideshow', 'skvn-marine-blocks' ); ?>"
					aria-pressed="true"
					aria-live="polite"
				><?php esc_html_e( 'Pause', 'skvn-marine-blocks' ); ?></button>
			<?php endif; ?>
		</div>
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
