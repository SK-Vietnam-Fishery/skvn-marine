<?php
/**
 * Front-page loading screen settings.
 *
 * Quản lý preloader/splash/brand-bar cho theme: chọn loại, sửa text, màu
 * (whitelist theme.json palette/gradient), logo ảnh. Option global
 * `skvn_preloader`; theme render qua getter skvn_marine_blocks_get_preloader()
 * với function_exists() guard → plugin off thì theme fallback default.
 *
 * Phase 1: settings + getter + media picker. Theme render = Phase 2.
 *
 * @package skvn-marine-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_BLOCKS_PRELOADER_OPTION = 'skvn_preloader';

add_action( 'admin_menu', 'skvn_marine_blocks_preloader_menu' );
add_action( 'admin_init', 'skvn_marine_blocks_register_preloader' );

/**
 * Register the preloader settings submenu + media picker assets.
 *
 * @return void
 */
function skvn_marine_blocks_preloader_menu() {
	$hook = add_submenu_page(
		'skvn-marine',
		esc_html__( 'Loading screen', 'skvn-marine-blocks' ),
		esc_html__( 'Loading screen', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine-preloader',
		'skvn_marine_blocks_render_preloader_page'
	);

	add_action(
		'admin_enqueue_scripts',
		function ( $current ) use ( $hook ) {
			if ( $current === $hook ) {
				wp_enqueue_media();
			}
		}
	);
}

/**
 * Register the preloader setting.
 *
 * @return void
 */
function skvn_marine_blocks_register_preloader() {
	register_setting(
		'skvn_marine_blocks_preloader',
		SKVN_MARINE_BLOCKS_PRELOADER_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'skvn_marine_blocks_sanitize_preloader',
			'default'           => skvn_marine_blocks_get_default_preloader(),
		)
	);
}

/**
 * Default preloader settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_default_preloader() {
	return array(
		'type'         => 'preloader',
		'scope'        => 'front',
		'dismiss'      => 'hero',
		'min_display'  => 0,
		'mark_text'    => '',
		'tagline'      => '',
		'use_logo'     => false,
		'logo_id'      => 0,
		'bg_type'               => 'gradient',
		'bg_solid'              => 'skvn-blue-950',
		'bg_gradient'           => 'skvn-navy-deep',
		'text_color'            => 'skvn-white',
		'accent_color'          => 'skvn-gold-300',
		'smooth_initial_loading'=> false,
		'page_transition'       => 'none',
		'frequency'             => 'once',
		'loading_show_after'    => 150,
		'loading_fade_duration' => 200,
		'page_transition_duration'=> 180,
	);
}

/**
 * Palette slug => label from active theme.json.
 *
 * @return array<string,string>
 */
function skvn_marine_blocks_preloader_palette_options() {
	$palette = wp_get_global_settings( array( 'color', 'palette' ) );
	$palette = isset( $palette['theme'] ) && is_array( $palette['theme'] ) ? $palette['theme'] : array();
	$options = array();
	foreach ( $palette as $color ) {
		if ( isset( $color['slug'], $color['name'] ) ) {
			$options[ $color['slug'] ] = $color['name'];
		}
	}
	return $options;
}

/**
 * Gradient slug => label from active theme.json.
 *
 * @return array<string,string>
 */
function skvn_marine_blocks_preloader_gradient_options() {
	$gradients = wp_get_global_settings( array( 'color', 'gradients' ) );
	$gradients = isset( $gradients['theme'] ) && is_array( $gradients['theme'] ) ? $gradients['theme'] : array();
	$options = array();
	foreach ( $gradients as $gradient ) {
		if ( isset( $gradient['slug'], $gradient['name'] ) ) {
			$options[ $gradient['slug'] ] = $gradient['name'];
		}
	}
	return $options;
}

/**
 * Sanitize the preloader option.
 *
 * @param mixed $value Raw option value.
 * @return array<string,mixed>
 */
function skvn_marine_blocks_sanitize_preloader( $value ) {
	$defaults  = skvn_marine_blocks_get_default_preloader();
	$value     = is_array( $value ) ? wp_unslash( $value ) : array();
	$palette   = array_keys( skvn_marine_blocks_preloader_palette_options() );
	$gradients = array_keys( skvn_marine_blocks_preloader_gradient_options() );
	$saved     = (array) get_option( SKVN_MARINE_BLOCKS_PRELOADER_OPTION, array() );

	$choice = static function ( $raw, $allowed, $fallback ) {
		$raw = is_string( $raw ) ? sanitize_key( $raw ) : '';
		return in_array( $raw, $allowed, true ) ? $raw : $fallback;
	};
	$color = static function ( $raw, $allowed, $fallback ) {
		$raw = is_string( $raw ) ? sanitize_text_field( $raw ) : '';
		return in_array( $raw, $allowed, true ) ? $raw : $fallback;
	};

	return array(
		'type'         => $choice( $value['type'] ?? '', array( 'preloader', 'splash', 'skeleton', 'brandbar', 'off' ), $defaults['type'] ),
		'scope'        => $choice( $value['scope'] ?? '', array( 'front', 'site' ), $defaults['scope'] ),
		'dismiss'      => $choice( isset( $value['dismiss'] ) ? $value['dismiss'] : ( $saved['dismiss'] ?? '' ), array( 'hero', 'window', 'time' ), $defaults['dismiss'] ),
		'min_display'  => max( 0, min( 5000, isset( $value['min_display'] ) ? absint( $value['min_display'] ) : ( isset( $saved['min_display'] ) ? absint( $saved['min_display'] ) : 0 ) ) ),
		'mark_text'    => isset( $value['mark_text'] ) ? sanitize_text_field( $value['mark_text'] ) : '',
		'tagline'      => isset( $value['tagline'] ) ? sanitize_text_field( $value['tagline'] ) : '',
		'use_logo'     => ! empty( $value['use_logo'] ),
		'logo_id'      => isset( $value['logo_id'] ) ? absint( $value['logo_id'] ) : 0,
		'bg_type'      => $choice( $value['bg_type'] ?? '', array( 'solid', 'gradient' ), $defaults['bg_type'] ),
		'bg_solid'     => $color( $value['bg_solid'] ?? '', $palette, $defaults['bg_solid'] ),
		'bg_gradient'  => $color( $value['bg_gradient'] ?? '', $gradients, $defaults['bg_gradient'] ),
		'text_color'               => $color( $value['text_color'] ?? '', $palette, $defaults['text_color'] ),
		'accent_color'             => $color( $value['accent_color'] ?? '', $palette, $defaults['accent_color'] ),
		'smooth_initial_loading'   => ! empty( $value['smooth_initial_loading'] ),
		'page_transition'          => $choice( $value['page_transition'] ?? '', array( 'none', 'fade' ), $defaults['page_transition'] ),
		'frequency'                => $choice( $value['frequency'] ?? '', array( 'once', 'every' ), $defaults['frequency'] ),
		'loading_show_after'       => max( 0, min( 500, absint( $value['loading_show_after'] ?? $saved['loading_show_after'] ?? $defaults['loading_show_after'] ) ) ),
		'loading_fade_duration'    => max( 100, min( 500, absint( $value['loading_fade_duration'] ?? $saved['loading_fade_duration'] ?? $defaults['loading_fade_duration'] ) ) ),
		'page_transition_duration' => max( 100, min( 400, isset( $value['page_transition_duration'] ) ? absint( $value['page_transition_duration'] ) : $defaults['page_transition_duration'] ) ),
	);
}

/**
 * Get sanitized preloader settings (theme-facing getter).
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_preloader() {
	return skvn_marine_blocks_sanitize_preloader(
		get_option( SKVN_MARINE_BLOCKS_PRELOADER_OPTION, skvn_marine_blocks_get_default_preloader() )
	);
}

/**
 * Render a color/choice select.
 *
 * @param string               $name    Field name.
 * @param string               $current Current value.
 * @param array<string,string> $options Options map.
 * @return void
 */
function skvn_marine_blocks_preloader_select( $name, $current, $options ) {
	echo '<select name="' . esc_attr( $name ) . '">';
	foreach ( $options as $val => $label ) {
		printf(
			'<option value="%1$s" %2$s>%3$s</option>',
			esc_attr( $val ),
			selected( $current, $val, false ),
			esc_html( $label )
		);
	}
	echo '</select>';
}

/**
 * Render the preloader settings page.
 *
 * @return void
 */
function skvn_marine_blocks_render_preloader_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$s        = skvn_marine_blocks_get_preloader();
	$opt      = SKVN_MARINE_BLOCKS_PRELOADER_OPTION;
	$palette  = skvn_marine_blocks_preloader_palette_options();
	$gradient = skvn_marine_blocks_preloader_gradient_options();
	$logo_src = $s['logo_id'] ? wp_get_attachment_image_url( $s['logo_id'], 'medium' ) : '';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Loading screen', 'skvn-marine-blocks' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Choose a loading screen and edit its content. Select Off to disable loading screens.', 'skvn-marine-blocks' ); ?></p>
		<form id="skvn-loading-settings" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php settings_fields( 'skvn_marine_blocks_preloader' ); ?>

			<h2><?php esc_html_e( 'Type and scope', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Type', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[type]", $s['type'], array(
						'preloader' => esc_html__( 'Preloader (overlay + spinner)', 'skvn-marine-blocks' ),
						'splash'    => esc_html__( 'Splash (once per session)', 'skvn-marine-blocks' ),
						'skeleton'  => esc_html__( 'Skeleton (shimmer placeholders)', 'skvn-marine-blocks' ),
						'brandbar'  => esc_html__( 'Brand bar (non-blocking)', 'skvn-marine-blocks' ),
						'off'       => esc_html__( 'Off', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Scope', 'skvn-marine-blocks' ); ?></th>
					<td>
						<?php skvn_marine_blocks_preloader_select( "{$opt}[scope]", $s['scope'], array(
							'front' => esc_html__( 'Homepage only', 'skvn-marine-blocks' ),
							'site'  => esc_html__( 'Entire site', 'skvn-marine-blocks' ),
						) ); ?>
						<p class="description"><?php esc_html_e( 'Splash runs once per session regardless of scope.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Dismiss preloader', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[dismiss]", $s['dismiss'], array(
						'hero'   => esc_html__( 'When the hero image loads', 'skvn-marine-blocks' ),
						'window' => esc_html__( 'When the page loads', 'skvn-marine-blocks' ),
						'time'   => esc_html__( 'After the minimum display time', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Minimum display time (ms)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="0" max="5000" step="100" class="small-text" name="<?php echo esc_attr( "{$opt}[min_display]" ); ?>" value="<?php echo esc_attr( (string) $s['min_display'] ); ?>"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Content', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Brand text', 'skvn-marine-blocks' ); ?></th>
					<td>
						<input type="text" class="regular-text" name="<?php echo esc_attr( "{$opt}[mark_text]" ); ?>" value="<?php echo esc_attr( $s['mark_text'] ); ?>" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						<p class="description"><?php esc_html_e( 'Leave blank to use the site name.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Tagline', 'skvn-marine-blocks' ); ?></th>
					<td><input type="text" class="regular-text" name="<?php echo esc_attr( "{$opt}[tagline]" ); ?>" value="<?php echo esc_attr( $s['tagline'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Use logo image', 'skvn-marine-blocks' ); ?></th>
					<td><label><input type="checkbox" name="<?php echo esc_attr( "{$opt}[use_logo]" ); ?>" value="1" <?php checked( $s['use_logo'] ); ?>> <?php esc_html_e( 'Show a logo image instead of brand text', 'skvn-marine-blocks' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Logo', 'skvn-marine-blocks' ); ?></th>
					<td>
						<input type="hidden" id="skvn-preloader-logo-id" name="<?php echo esc_attr( "{$opt}[logo_id]" ); ?>" value="<?php echo esc_attr( (string) $s['logo_id'] ); ?>">
						<img id="skvn-preloader-logo-preview" src="<?php echo esc_url( $logo_src ); ?>" alt="" style="max-height:60px;display:<?php echo $logo_src ? 'block' : 'none'; ?>;margin-bottom:8px;">
						<button type="button" class="button" id="skvn-preloader-logo-pick"><?php esc_html_e( 'Choose or upload logo', 'skvn-marine-blocks' ); ?></button>
						<button type="button" class="button" id="skvn-preloader-logo-clear"><?php esc_html_e( 'Remove', 'skvn-marine-blocks' ); ?></button>
						<p class="description"><?php esc_html_e( 'Image source: selected attachment → site logo → site name text. A broken image falls back to text; the page is not held waiting for the logo.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Colors (theme palette)', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Background type', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_type]", $s['bg_type'], array(
						'solid'    => esc_html__( 'Solid', 'skvn-marine-blocks' ),
						'gradient' => esc_html__( 'Gradient', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Solid background', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_solid]", $s['bg_solid'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Gradient background', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_gradient]", $s['bg_gradient'], $gradient ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Text color', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[text_color]", $s['text_color'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Accent color (spinner / bar)', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[accent_color]", $s['accent_color'], $palette ); ?></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Initial loading', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Smooth initial loading', 'skvn-marine-blocks' ); ?></th>
					<td><label><input type="checkbox" name="<?php echo esc_attr( "{$opt}[smooth_initial_loading]" ); ?>" value="1" <?php checked( $s['smooth_initial_loading'] ); ?>> <?php esc_html_e( 'Enable smooth initial loading. On the homepage, reveal the main Hero Slider with a 750ms upward curtain; begin fallback reveal after 1500ms if it is not ready.', 'skvn-marine-blocks' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Frequency', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[frequency]", $s['frequency'], array(
						'once'  => esc_html__( 'Once per session', 'skvn-marine-blocks' ),
						'every' => esc_html__( 'Every page load', 'skvn-marine-blocks' ),
					) ); ?>
						<p class="description"><?php esc_html_e( 'Once per session runs at most once per tab/session. Every page load runs on every eligible visit. The homepage curtain covers immediately; other pages use the show-after threshold. Smooth loading manages dismissal without a minimum hold.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Page transitions', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Page transition', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[page_transition]", $s['page_transition'], array(
						'none' => esc_html__( 'None', 'skvn-marine-blocks' ),
						'fade' => esc_html__( 'Fade', 'skvn-marine-blocks' ),
					) ); ?>
						<p class="description"><?php esc_html_e( 'Native cross-document fade when navigating between eligible frontend pages. Unsupported browsers and reduced motion use normal navigation.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Advanced', 'skvn-marine-blocks' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Times use whole milliseconds. Show-after and loading fade apply outside the homepage curtain. When Smooth loading is enabled for the homepage only, these two fields are managed automatically; saved values are retained. Page transition duration is independent.', 'skvn-marine-blocks' ); ?></p>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Show loading after (ms)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="0" max="500" step="1" class="small-text" name="<?php echo esc_attr( "{$opt}[loading_show_after]" ); ?>" value="<?php echo esc_attr( (string) $s['loading_show_after'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Loading fade duration (ms)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="100" max="500" step="1" class="small-text" name="<?php echo esc_attr( "{$opt}[loading_fade_duration]" ); ?>" value="<?php echo esc_attr( (string) $s['loading_fade_duration'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Page transition duration (ms)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="100" max="400" step="1" class="small-text" name="<?php echo esc_attr( "{$opt}[page_transition_duration]" ); ?>" value="<?php echo esc_attr( (string) $s['page_transition_duration'] ); ?>"></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	( function () {
		var form = document.getElementById( 'skvn-loading-settings' );
		var option = <?php echo wp_json_encode( $opt ); ?>;
		function field( key ) { return form.elements.namedItem( option + '[' + key + ']' ); }
		function syncControls() {
			var smooth = field( 'type' ).value === 'preloader' && field( 'smooth_initial_loading' ).checked;
			field( 'dismiss' ).disabled = smooth;
			field( 'min_display' ).disabled = smooth;
			var curtainOnly = smooth && field( 'scope' ).value === 'front';
			field( 'loading_show_after' ).disabled = curtainOnly;
			field( 'loading_fade_duration' ).disabled = curtainOnly;
		}
		form.addEventListener( 'change', syncControls );
		syncControls();
		var pick = document.getElementById( 'skvn-preloader-logo-pick' );
		var clear = document.getElementById( 'skvn-preloader-logo-clear' );
		var idField = document.getElementById( 'skvn-preloader-logo-id' );
		var preview = document.getElementById( 'skvn-preloader-logo-preview' );
		var frame;
		if ( pick ) {
			pick.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( frame ) { frame.open(); return; }
				frame = wp.media( { title: 'Logo', multiple: false, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					idField.value = att.id;
					var url = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
					preview.src = url;
					preview.style.display = 'block';
				} );
				frame.open();
			} );
		}
		if ( clear ) {
			clear.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				idField.value = '';
				preview.src = '';
				preview.style.display = 'none';
			} );
		}
	} )();
	</script>
	<?php
}
