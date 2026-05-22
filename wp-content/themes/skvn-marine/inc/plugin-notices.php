<?php
/**
 * Admin plugin notices for SKVN Marine.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_notices', 'skvn_marine_plugin_notices' );

/**
 * Check whether a plugin file is active.
 *
 * @param string $plugin_file Plugin basename, for example woocommerce/woocommerce.php.
 * @return bool
 */
function skvn_marine_is_plugin_active( $plugin_file ) {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return is_plugin_active( $plugin_file );
}

/**
 * Create plugin install search links for notice output.
 *
 * @param array<int,array{name:string,search:string}> $plugins Plugin data.
 * @return string
 */
function skvn_marine_get_plugin_notice_links( $plugins ) {
	$links = array();

	foreach ( $plugins as $plugin ) {
		$url = add_query_arg(
			array(
				'tab'  => 'search',
				'type' => 'term',
				's'    => $plugin['search'],
			),
			admin_url( 'plugin-install.php' )
		);

		$links[] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( $url ),
			esc_html( $plugin['name'] )
		);
	}

	return implode( ', ', $links );
}

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
		$required[] = array(
			'name'   => 'WooCommerce',
			'search' => 'woocommerce',
		);
	}

	if ( ! defined( 'WINDPRESS_VERSION' ) && ! class_exists( 'WindPress\WindPress' ) ) {
		$recommended[] = array(
			'name'   => 'WindPress',
			'search' => 'windpress',
		);
	}

	if ( ! defined( 'RANK_MATH_VERSION' ) && ! class_exists( 'RankMath' ) ) {
		$recommended[] = array(
			'name'   => 'Rank Math SEO',
			'search' => 'seo-by-rank-math',
		);
	}

	if ( ! defined( 'WPCF7_VERSION' ) ) {
		$recommended[] = array(
			'name'   => 'Contact Form 7',
			'search' => 'contact-form-7',
		);
	}

	if ( ! skvn_marine_is_plugin_active( 'contact-form-cfdb7/contact-form-cfdb-7.php' ) ) {
		$recommended[] = array(
			'name'   => 'Contact Form CFDB7',
			'search' => 'contact-form-cfdb7',
		);
	}

	if ( ! skvn_marine_is_plugin_active( 'antispam-bee/antispam_bee.php' ) ) {
		$recommended[] = array(
			'name'   => 'Antispam Bee',
			'search' => 'antispam-bee',
		);
	}

	if ( ! skvn_marine_is_plugin_active( 'ootb-openstreetmap/ootb-openstreetmap.php' ) ) {
		$recommended[] = array(
			'name'   => 'Out of the Block: OpenStreetMap',
			'search' => 'ootb-openstreetmap',
		);
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
				printf(
					/* translators: %s: comma-separated plugin links. */
					esc_html__( 'Required plugin missing: %s.', 'skvn-marine' ),
					wp_kses_post( skvn_marine_get_plugin_notice_links( $required ) )
				);
				?>
			</p>
		<?php endif; ?>

		<?php if ( ! empty( $recommended ) ) : ?>
			<p>
				<?php
				printf(
					/* translators: %s: comma-separated plugin links. */
					esc_html__( 'Recommended plugins not detected: %s.', 'skvn-marine' ),
					wp_kses_post( skvn_marine_get_plugin_notice_links( $recommended ) )
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
