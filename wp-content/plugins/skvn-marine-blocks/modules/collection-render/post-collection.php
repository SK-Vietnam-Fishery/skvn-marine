<?php
/**
 * Post Collection dynamic renderer.
 *
 * @package SKVNMarineBlocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Post Collection block.
 *
 * @param array $attributes Block attributes.
 * @return string
 */
function skvn_marine_blocks_render_post_collection( $attributes ) {
	$heading  = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$intro    = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$layout   = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'grid';
	$preset   = isset( $attributes['responsivePreset'] ) ? sanitize_text_field( $attributes['responsivePreset'] ) : '3-2-1';
	$posts    = skvn_marine_blocks_query_collection_posts( $attributes );
	$classes  = implode(
		' ',
		array_filter(
			array(
				'skvn-collection',
				'skvn-collection--post',
				'skvn-collection--' . $layout,
				'skvn-collection--preset-' . $preset,
				skvn_marine_blocks_collection_bool( $attributes, 'equalHeight', true ) ? 'skvn-collection--equal-height' : '',
			)
		)
	);

	$accessible_label = isset( $attributes['accessibleLabel'] ) ? sanitize_text_field( $attributes['accessibleLabel'] ) : '';
	$aria_label       = '' !== $heading ? $heading : $accessible_label;
	$aria_attr        = '' !== $aria_label ? ' aria-label="' . esc_attr( $aria_label ) . '"' : '';

	if ( 'carousel' === $layout ) {
		skvn_marine_blocks_maybe_enqueue_collection_view();
	}

	ob_start();
	?>
	<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $aria_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php if ( '' !== $heading ) : ?>
			<h2 class="skvn-collection__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<div class="skvn-collection__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
		<?php endif; ?>
		<?php if ( empty( $posts ) ) : ?>
			<p class="skvn-collection__empty"><?php esc_html_e( 'No posts found.', 'skvn-marine-blocks' ); ?></p>
		<?php elseif ( 'carousel' === $layout ) : ?>
			<?php echo skvn_marine_blocks_render_collection_carousel( $posts, $attributes, 'post' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="skvn-collection__grid">
				<?php
				foreach ( $posts as $post ) {
					echo skvn_marine_blocks_render_collection_post_card( $post, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}
