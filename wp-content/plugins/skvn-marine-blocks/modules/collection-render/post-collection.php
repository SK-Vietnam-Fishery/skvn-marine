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
	$heading = isset( $attributes['heading'] ) ? sanitize_text_field( $attributes['heading'] ) : '';
	$intro   = isset( $attributes['intro'] ) ? wp_kses_post( $attributes['intro'] ) : '';
	$posts   = skvn_marine_blocks_query_collection_posts( $attributes );
	$classes = array(
		'skvn-collection',
		'skvn-collection--post',
		'skvn-collection--grid',
	);

	ob_start();
	?>
	<section class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( '' !== $heading ) : ?>
			<h2 class="skvn-collection__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( '' !== $intro ) : ?>
			<div class="skvn-collection__intro"><?php echo wp_kses_post( wpautop( $intro ) ); ?></div>
		<?php endif; ?>
		<?php if ( empty( $posts ) ) : ?>
			<p class="skvn-collection__empty"><?php esc_html_e( 'No posts found.', 'skvn-marine-blocks' ); ?></p>
		<?php else : ?>
			<div class="skvn-collection__grid">
				<?php
				foreach ( $posts as $post ) {
					echo skvn_marine_blocks_render_collection_post_card( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		<?php endif; ?>
	</section>
	<?php

	return (string) ob_get_clean();
}
