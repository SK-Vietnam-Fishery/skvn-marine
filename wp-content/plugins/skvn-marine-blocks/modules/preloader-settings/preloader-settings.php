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
		'bg_type'      => 'gradient',
		'bg_solid'     => 'skvn-blue-950',
		'bg_gradient'  => 'skvn-navy-deep',
		'text_color'   => 'skvn-white',
		'accent_color' => 'skvn-gold-300',
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

	$choice = static function ( $raw, $allowed, $fallback ) {
		$raw = is_string( $raw ) ? sanitize_key( $raw ) : '';
		return in_array( $raw, $allowed, true ) ? $raw : $fallback;
	};
	$color = static function ( $raw, $allowed, $fallback ) {
		$raw = is_string( $raw ) ? sanitize_text_field( $raw ) : '';
		return in_array( $raw, $allowed, true ) ? $raw : $fallback;
	};

	return array(
		'type'         => $choice( $value['type'] ?? '', array( 'preloader', 'splash', 'brandbar', 'off' ), $defaults['type'] ),
		'scope'        => $choice( $value['scope'] ?? '', array( 'front', 'site' ), $defaults['scope'] ),
		'dismiss'      => $choice( $value['dismiss'] ?? '', array( 'hero', 'window', 'time' ), $defaults['dismiss'] ),
		'min_display'  => max( 0, min( 5000, isset( $value['min_display'] ) ? absint( $value['min_display'] ) : 0 ) ),
		'mark_text'    => isset( $value['mark_text'] ) ? sanitize_text_field( $value['mark_text'] ) : '',
		'tagline'      => isset( $value['tagline'] ) ? sanitize_text_field( $value['tagline'] ) : '',
		'use_logo'     => ! empty( $value['use_logo'] ),
		'logo_id'      => isset( $value['logo_id'] ) ? absint( $value['logo_id'] ) : 0,
		'bg_type'      => $choice( $value['bg_type'] ?? '', array( 'solid', 'gradient' ), $defaults['bg_type'] ),
		'bg_solid'     => $color( $value['bg_solid'] ?? '', $palette, $defaults['bg_solid'] ),
		'bg_gradient'  => $color( $value['bg_gradient'] ?? '', $gradients, $defaults['bg_gradient'] ),
		'text_color'   => $color( $value['text_color'] ?? '', $palette, $defaults['text_color'] ),
		'accent_color' => $color( $value['accent_color'] ?? '', $palette, $defaults['accent_color'] ),
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
		<p class="description"><?php esc_html_e( 'Màn tải trang chủ. Chọn loại + sửa nội dung; chọn "Off" để tắt hẳn.', 'skvn-marine-blocks' ); ?></p>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php settings_fields( 'skvn_marine_blocks_preloader' ); ?>

			<h2><?php esc_html_e( 'Loại & phạm vi', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Loại', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[type]", $s['type'], array(
						'preloader' => esc_html__( 'Preloader (overlay + spinner)', 'skvn-marine-blocks' ),
						'splash'    => esc_html__( 'Splash (chỉ 1 lần / phiên)', 'skvn-marine-blocks' ),
						'brandbar'  => esc_html__( 'Brand bar (thanh mỏng, không che)', 'skvn-marine-blocks' ),
						'off'       => esc_html__( 'Off (tắt)', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Phạm vi', 'skvn-marine-blocks' ); ?></th>
					<td>
						<?php skvn_marine_blocks_preloader_select( "{$opt}[scope]", $s['scope'], array(
							'front' => esc_html__( 'Chỉ trang chủ', 'skvn-marine-blocks' ),
							'site'  => esc_html__( 'Toàn site', 'skvn-marine-blocks' ),
						) ); ?>
						<p class="description"><?php esc_html_e( 'Splash luôn chỉ hiện 1 lần/phiên bất kể phạm vi.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Khi nào ẩn (Preloader)', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[dismiss]", $s['dismiss'], array(
						'hero'   => esc_html__( 'Khi ảnh hero load xong', 'skvn-marine-blocks' ),
						'window' => esc_html__( 'Khi trang load xong', 'skvn-marine-blocks' ),
						'time'   => esc_html__( 'Sau thời gian tối thiểu', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Thời gian hiện tối thiểu (ms)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="0" max="5000" step="100" class="small-text" name="<?php echo esc_attr( "{$opt}[min_display]" ); ?>" value="<?php echo esc_attr( (string) $s['min_display'] ); ?>"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Nội dung', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Chữ hiển thị', 'skvn-marine-blocks' ); ?></th>
					<td>
						<input type="text" class="regular-text" name="<?php echo esc_attr( "{$opt}[mark_text]" ); ?>" value="<?php echo esc_attr( $s['mark_text'] ); ?>" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						<p class="description"><?php esc_html_e( 'Trống → dùng tên site.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Tagline (phụ)', 'skvn-marine-blocks' ); ?></th>
					<td><input type="text" class="regular-text" name="<?php echo esc_attr( "{$opt}[tagline]" ); ?>" value="<?php echo esc_attr( $s['tagline'] ); ?>"></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Dùng logo ảnh', 'skvn-marine-blocks' ); ?></th>
					<td><label><input type="checkbox" name="<?php echo esc_attr( "{$opt}[use_logo]" ); ?>" value="1" <?php checked( $s['use_logo'] ); ?>> <?php esc_html_e( 'Hiện logo ảnh thay cho chữ', 'skvn-marine-blocks' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Logo', 'skvn-marine-blocks' ); ?></th>
					<td>
						<input type="hidden" id="skvn-preloader-logo-id" name="<?php echo esc_attr( "{$opt}[logo_id]" ); ?>" value="<?php echo esc_attr( (string) $s['logo_id'] ); ?>">
						<img id="skvn-preloader-logo-preview" src="<?php echo esc_url( $logo_src ); ?>" alt="" style="max-height:60px;display:<?php echo $logo_src ? 'block' : 'none'; ?>;margin-bottom:8px;">
						<button type="button" class="button" id="skvn-preloader-logo-pick"><?php esc_html_e( 'Chọn / tải logo', 'skvn-marine-blocks' ); ?></button>
						<button type="button" class="button" id="skvn-preloader-logo-clear"><?php esc_html_e( 'Xoá', 'skvn-marine-blocks' ); ?></button>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Màu (theme palette)', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Loại nền', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_type]", $s['bg_type'], array(
						'solid'    => esc_html__( 'Solid', 'skvn-marine-blocks' ),
						'gradient' => esc_html__( 'Gradient', 'skvn-marine-blocks' ),
					) ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Nền solid', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_solid]", $s['bg_solid'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Nền gradient', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[bg_gradient]", $s['bg_gradient'], $gradient ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Màu chữ', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[text_color]", $s['text_color'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Màu nhấn (spinner / bar)', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_preloader_select( "{$opt}[accent_color]", $s['accent_color'], $palette ); ?></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<script>
	( function () {
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
