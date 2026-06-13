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
	$heading = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$intro   = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';

	ob_start();
	?>
	<section class="skvn-collection skvn-collection--product skvn-collection--grid">
		<?php if ( '' !== $heading ) : ?>
			<h2 class="skvn-collection__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<div class="skvn-collection__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
		<?php endif; ?>
		<?php if ( ! function_exists( 'wc_get_products' ) ) : ?>
			<p class="skvn-collection__empty">
				<?php esc_html_e( 'Product collections need WooCommerce to be active.', 'skvn-marine-blocks' ); ?>
			</p>
		<?php else : ?>
			<p class="skvn-collection__empty">
				<?php esc_html_e( 'Product collection renderer is ready for the next phase.', 'skvn-marine-blocks' ); ?>
			</p>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}
