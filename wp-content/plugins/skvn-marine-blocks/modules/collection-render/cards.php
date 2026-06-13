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
function skvn_marine_blocks_render_collection_post_card( $post ) {
	$title = get_the_title( $post );
	$url   = get_permalink( $post );

	ob_start();
	?>
	<article class="skvn-collection-card">
		<?php if ( has_post_thumbnail( $post ) ) : ?>
			<a class="skvn-collection-card__media" href="<?php echo esc_url( $url ); ?>">
				<?php echo get_the_post_thumbnail( $post, 'medium_large', array( 'class' => 'skvn-collection-card__image' ) ); ?>
			</a>
		<?php endif; ?>
		<div class="skvn-collection-card__body">
			<h3 class="skvn-collection-card__title">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
			</h3>
			<div class="skvn-collection-card__meta">
				<?php echo esc_html( get_the_date( '', $post ) ); ?>
			</div>
			<div class="skvn-collection-card__excerpt">
				<?php echo esc_html( wp_trim_words( get_the_excerpt( $post ), 22 ) ); ?>
			</div>
			<a class="skvn-collection-card__action" href="<?php echo esc_url( $url ); ?>">
				<?php esc_html_e( 'Read more', 'skvn-marine-blocks' ); ?>
			</a>
		</div>
	</article>
	<?php

	return (string) ob_get_clean();
}
