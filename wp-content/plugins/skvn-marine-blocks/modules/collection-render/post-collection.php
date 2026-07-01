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
	$heading      = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$eyebrow      = isset( $attributes['eyebrow'] ) ? sanitize_text_field( $attributes['eyebrow'] ) : '';
	$show_heading = skvn_marine_blocks_collection_bool( $attributes, 'showHeading', true );
	$intro        = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$layout       = isset( $attributes['layout'] ) ? sanitize_key( $attributes['layout'] ) : 'grid';
	$preset       = isset( $attributes['responsivePreset'] ) ? sanitize_text_field( $attributes['responsivePreset'] ) : '3-2-1';
	$ratio        = isset( $attributes['imageRatio'] ) ? 'skvn-collection--ratio-' . str_replace( ':', '-', $attributes['imageRatio'] ) : '';
	$archive_url  = isset( $attributes['archiveUrl'] ) ? esc_url_raw( $attributes['archiveUrl'] ) : '';
	$archive_label = isset( $attributes['archiveLabel'] ) ? sanitize_text_field( $attributes['archiveLabel'] ) : '';
	$posts    = skvn_marine_blocks_query_collection_posts( $attributes );
	$classes  = implode(
		' ',
		array_filter(
			array(
				'skvn-collection',
				'skvn-collection--post',
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

	if ( 'carousel' === $layout ) {
		skvn_marine_blocks_maybe_enqueue_collection_view();
	}

	$footer_html = skvn_marine_blocks_render_collection_footer( $attributes, 'carousel' === $layout ? 'carousel' : 'grid' );

	ob_start();
	?>
	<section class="<?php echo esc_attr( $classes ); ?>"<?php echo $aria_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<?php echo skvn_marine_blocks_render_collection_header( $attributes, 'carousel' === $layout ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( empty( $posts ) ) : ?>
			<p class="skvn-collection__empty"><?php esc_html_e( 'No posts found.', 'skvn-marine-blocks' ); ?></p>
		<?php elseif ( 'carousel' === $layout ) : ?>
			<?php echo skvn_marine_blocks_render_collection_carousel( $posts, $attributes, 'post', $footer_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="skvn-collection__grid">
				<?php
				foreach ( $posts as $post ) {
					echo skvn_marine_blocks_render_collection_post_card( $post, $attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
			<?php echo $footer_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}
