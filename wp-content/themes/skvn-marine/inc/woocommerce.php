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
 * Render the single product quote CTA.
 *
 * @return void
 */
function skvn_marine_woocommerce_single_quote_cta() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	printf(
		'<p class="skvn-product-quote-cta"><a class="skvn-button skvn-button--primary" href="%1$s">%2$s</a></p>',
		esc_url( skvn_marine_get_product_quote_url( $product ) ),
		esc_html__( 'Request a Quote', 'skvn-marine' )
	);
}
