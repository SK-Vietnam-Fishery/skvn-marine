<?php
/**
 * Template Name: SKVN Full Width
 * Template Post Type: page
 *
 * Full-width Gutenberg canvas for SKVN pages.
 *
 * @package skvn-marine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main skvn-full-width-template">
	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'skvn-full-width-page' ); ?>>
			<div class="entry-content skvn-full-width-content">
				<?php
				the_content();
				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'skvn-marine' ),
						'after'  => '</div>',
					)
				);
				?>
			</div>
		</article>

	<?php endwhile; ?>
</main>

<?php
get_footer();
