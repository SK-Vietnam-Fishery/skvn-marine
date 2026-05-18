<?php
/**
 * Admin plugin notices for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_notices', 'skvn_marine_plugin_notices' );

/**
 * Show admin notices for missing required/recommended plugins.
 *
 * @return void
 */
function skvn_marine_plugin_notices() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$screen = get_current_screen();

	if ( $screen && 'themes' !== $screen->base && 'dashboard' !== $screen->base ) {
		return;
	}

	$required    = array();
	$recommended = array();

	if ( ! class_exists( 'WooCommerce' ) ) {
		$required[] = 'WooCommerce';
	}

	if ( ! defined( 'WINDPRESS_VERSION' ) && ! class_exists( 'WindPress\WindPress' ) ) {
		$recommended[] = 'WindPress';
	}

	if ( ! defined( 'RANK_MATH_VERSION' ) && ! class_exists( 'RankMath' ) ) {
		$recommended[] = 'Rank Math SEO';
	}

	if ( ! defined( 'WPCF7_VERSION' ) ) {
		$recommended[] = 'Contact Form 7';
	}

	if ( ! defined( 'CFDB7_VERSION' ) && ! class_exists( 'CFDB7' ) ) {
		$recommended[] = 'Contact Form CFDB7';
	}

	if ( empty( $required ) && empty( $recommended ) ) {
		return;
	}

	?>
	<div class="notice notice-warning">
		<p>
			<strong><?php echo esc_html__( 'SKVN Marine plugin setup', 'skvn-marine' ); ?></strong>
		</p>

		<?php if ( ! empty( $required ) ) : ?>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: comma-separated plugin names. */
						__( 'Required plugin missing: %s.', 'skvn-marine' ),
						implode( ', ', $required )
					)
				);
				?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $recommended ) ) : ?>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: %s: comma-separated plugin names. */
						__( 'Recommended plugins not detected: %s.', 'skvn-marine' ),
						implode( ', ', $recommended )
					)
				);
				?>
			</p>
		<?php endif; ?>

		<p>
			<?php echo esc_html__( 'Install and activate these plugins from Plugins > Add New when their related site features are needed.', 'skvn-marine' ); ?>
		</p>
	</div>
	<?php
}
