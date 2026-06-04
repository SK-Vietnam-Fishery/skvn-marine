<?php
/**
 * Footer page rendering for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_FOOTER_PAGE_OPTION = 'skvn_footer_page_id';

add_action( 'wp', 'skvn_marine_maybe_replace_generatepress_footer' );
add_filter( 'body_class', 'skvn_marine_footer_body_class' );

/**
 * Replace the default GeneratePress footer only when a valid footer page is selected.
 *
 * @return void
 */
function skvn_marine_maybe_replace_generatepress_footer() {
	if ( ! skvn_marine_get_footer_page() ) {
		return;
	}

	remove_action( 'generate_footer', 'generate_construct_footer_widgets', 5 );
	remove_action( 'generate_footer', 'generate_construct_footer' );
	add_action( 'generate_footer', 'skvn_marine_render_footer_page', 10 );
}

/**
 * Add a body class when the reusable footer page is active.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function skvn_marine_footer_body_class( $classes ) {
	if ( skvn_marine_get_footer_page() ) {
		$classes[] = 'skvn-has-footer-page';
	}

	return $classes;
}

/**
 * Get the selected published footer page.
 *
 * @return WP_Post|null
 */
function skvn_marine_get_footer_page() {
	$page_id = absint( get_option( SKVN_MARINE_FOOTER_PAGE_OPTION, 0 ) );

	if ( 0 === $page_id ) {
		return null;
	}

	$page = get_post( $page_id );

	if ( ! $page || 'page' !== $page->post_type || 'publish' !== $page->post_status ) {
		return null;
	}

	return $page;
}

/**
 * Render the selected footer page through the GeneratePress footer surface.
 *
 * @return void
 */
function skvn_marine_render_footer_page() {
	$page = skvn_marine_get_footer_page();

	if ( ! $page ) {
		return;
	}

	$content = apply_filters( 'the_content', $page->post_content );

	if ( '' === trim( $content ) ) {
		return;
	}
	?>
	<footer class="skvn-footer-page" aria-label="<?php echo esc_attr__( 'Site footer', 'skvn-marine' ); ?>">
		<?php echo wp_kses_post( $content ); ?>
	</footer>
	<?php
}
