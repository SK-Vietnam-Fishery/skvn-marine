<?php
/**
 * Product Collection dynamic renderer.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Product Collection block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function skvn_marine_blocks_render_product_collection( $attributes ) {
	$heading      = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$eyebrow      = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : '';
	$show_heading = skvn_marine_blocks_collection_bool( $attributes, 'showHeading', true );
	$intro        = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$layout       = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'grid';
	$preset       = isset( $attributes['responsivePreset'] ) ? sanitize_text_field( $attributes['responsivePreset'] ) : '3-2-1';
	$ratio        = isset( $attributes['imageRatio'] ) ? 'skvn-collection--ratio-' . str_replace( ':', '-', $attributes['imageRatio'] ) : '';
	$archive_url  = isset( $attributes['archiveUrl'] ) ? esc_url_raw( $attributes['archiveUrl'] ) : '';
	$archive_label = isset( $attributes['archiveLabel'] ) ? sanitize_text_field( $attributes['archiveLabel'] ) : '';
	$classes  = implode(
		' ',
		array_filter(
			array(
				'skvn-collection',
				'skvn-collection--product',
				'skvn-collection--' . $layout,
				'skvn-collection--preset-' . $preset,
				$ratio,
				skvn_marine_blocks_collection_bool( $attributes, 'equalHeight', true ) ? 'skvn-collection--equal-height' : '',
			)
		)
	);

	$accessible_label = isset( $attributes['accessibleLabel'] ) ? sanitize_text_field( $attributes['accessibleLabel'] ) : '';
	$aria_label       = '' !== $heading ? $heading : $accessible_label;
	$aria_attr        = '' !== $aria_label ? ' aria-label="' . esc_attr( $aria_label ) . '"' : '';

	ob_start();
	?>
	<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $aria_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="skvn-collection__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<?php if ( $show_heading && '' !== $heading ) : ?>
			<h2 class="skvn-collection__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<div class="skvn-collection__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
		<?php endif; ?>
		<?php if ( ! function_exists( 'wc_get_products' ) ) : ?>
			<p class="skvn-collection__empty">
				<?php esc_html_e( 'Product collections require WooCommerce to be active.', 'skvn-marine-blocks' ); ?>
			</p>
		<?php else : ?>
			<?php
			$products = skvn_marine_blocks_query_collection_products( $attributes );

			if ( empty( $products ) ) :
				?>
				<p class="skvn-collection__empty"><?php esc_html_e( 'No products found.', 'skvn-marine-blocks' ); ?></p>
			<?php elseif ( 'carousel' === $layout ) : ?>
				<?php
				skvn_marine_blocks_maybe_enqueue_collection_view();
				$footer_html = skvn_marine_blocks_render_collection_footer( $attributes, 'carousel' );
				echo skvn_marine_blocks_render_collection_carousel( $products, $attributes, 'product', $footer_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			<?php else : ?>
				<div class="skvn-collection__grid">
					<?php
					foreach ( $products as $product ) {
						echo skvn_marine_blocks_render_collection_product_card( $product, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<?php echo skvn_marine_blocks_render_collection_footer( $attributes, 'grid' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}
