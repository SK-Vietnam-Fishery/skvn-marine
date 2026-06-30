<?php
/**
 * Single post sidebar content settings (BẢN TẠM).
 *
 * Trang admin tạm để đổi nội dung/màu/thứ tự các island ở sidebar single blog
 * post. Milestone đúng (1.4–1.6.x) sẽ refactor thành block-config + drag-drop và
 * bỏ module này. Theme render qua getter skvn_marine_blocks_get_sidebar_content()
 * với function_exists() guard → plugin off thì theme fallback default.
 *
 * Phase 1: chỉ global settings + getter. Metabox TOC (Phase 2) và theme render
 * (Phase 3) làm sau.
 *
 * @package skvn-marine-blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_BLOCKS_SIDEBAR_CONTENT_OPTION = 'skvn_sidebar_content';

add_action( 'admin_menu', 'skvn_marine_blocks_sidebar_content_menu' );
add_action( 'admin_init', 'skvn_marine_blocks_register_sidebar_content' );

/**
 * Register the sidebar content settings submenu.
 *
 * @return void
 */
function skvn_marine_blocks_sidebar_content_menu() {
	add_submenu_page(
		'skvn-marine',
		esc_html__( 'Single post sidebar content', 'skvn-marine-blocks' ),
		esc_html__( 'Blog sidebar', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine-blog-sidebar',
		'skvn_marine_blocks_render_sidebar_content_page'
	);
}

/**
 * Register the sidebar content setting.
 *
 * @return void
 */
function skvn_marine_blocks_register_sidebar_content() {
	register_setting(
		'skvn_marine_blocks_sidebar_content',
		SKVN_MARINE_BLOCKS_SIDEBAR_CONTENT_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'skvn_marine_blocks_sanitize_sidebar_content',
			'default'           => skvn_marine_blocks_get_default_sidebar_content(),
		)
	);
}

/**
 * Default sidebar content settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_default_sidebar_content() {
	return array(
		'cta'      => array(
			'eyebrow'     => esc_html__( 'Tư vấn miễn phí', 'skvn-marine-blocks' ),
			'heading'     => esc_html__( 'Thiết bị cold chain đạt chuẩn xuất khẩu EU', 'skvn-marine-blocks' ),
			'label'       => esc_html__( 'Yêu cầu báo giá', 'skvn-marine-blocks' ),
			'note'        => esc_html__( 'Phản hồi trong 24h làm việc', 'skvn-marine-blocks' ),
			'url'         => home_url( '/request-a-quote/' ),
			'bg_type'     => 'gradient',
			'bg_solid'    => 'skvn-blue-950',
			'bg_gradient' => 'skvn-navy-deep',
			'text_color'  => 'skvn-white',
			'btn_base'    => 'skvn-gold-300',
			'btn_text'    => 'skvn-blue-950',
		),
		'toc'      => array(
			'label' => esc_html__( 'Mục lục', 'skvn-marine-blocks' ),
			'order' => 1,
			'html'  => '',
		),
		'category' => array(
			'label'   => esc_html__( 'Danh mục', 'skvn-marine-blocks' ),
			'cat_ids' => array(),
			'enabled' => true,
			'order'   => 2,
		),
		'related'  => array(
			'label'   => esc_html__( 'Bài viết cùng chủ đề', 'skvn-marine-blocks' ),
			'enabled' => true,
			'order'   => 3,
		),
	);
}

/**
 * Palette color slug => label map from the active theme.json.
 *
 * @return array<string,string>
 */
function skvn_marine_blocks_sidebar_palette_options() {
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
 * Gradient slug => label map from the active theme.json.
 *
 * @return array<string,string>
 */
function skvn_marine_blocks_sidebar_gradient_options() {
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
 * Sanitize the sidebar content option.
 *
 * Color fields are whitelisted against the theme palette/gradient slugs — no
 * free-form hex accepted. Invalid values fall back to defaults.
 *
 * @param mixed $value Raw option value.
 * @return array<string,mixed>
 */
function skvn_marine_blocks_sanitize_sidebar_content( $value ) {
	$defaults  = skvn_marine_blocks_get_default_sidebar_content();
	$value     = is_array( $value ) ? wp_unslash( $value ) : array();
	$palette   = array_keys( skvn_marine_blocks_sidebar_palette_options() );
	$gradients = array_keys( skvn_marine_blocks_sidebar_gradient_options() );

	$cta_in  = isset( $value['cta'] ) && is_array( $value['cta'] ) ? $value['cta'] : array();
	$cat_in  = isset( $value['category'] ) && is_array( $value['category'] ) ? $value['category'] : array();
	$rel_in  = isset( $value['related'] ) && is_array( $value['related'] ) ? $value['related'] : array();
	$toc_in  = isset( $value['toc'] ) && is_array( $value['toc'] ) ? $value['toc'] : array();

	$pick_color = static function ( $raw, $allowed, $fallback ) {
		$raw = is_string( $raw ) ? sanitize_text_field( $raw ) : '';
		return in_array( $raw, $allowed, true ) ? $raw : $fallback;
	};

	$text = static function ( $raw, $fallback ) {
		$raw = isset( $raw ) ? sanitize_text_field( $raw ) : '';
		return '' === $raw ? $fallback : $raw;
	};

	$bg_type = isset( $cta_in['bg_type'] ) ? sanitize_key( $cta_in['bg_type'] ) : $defaults['cta']['bg_type'];
	if ( ! in_array( $bg_type, array( 'solid', 'gradient' ), true ) ) {
		$bg_type = $defaults['cta']['bg_type'];
	}

	$cat_ids = isset( $cat_in['cat_ids'] ) ? (array) $cat_in['cat_ids'] : array();
	$cat_ids = array_values( array_unique( array_filter( array_map( 'absint', $cat_ids ) ) ) );

	return array(
		'cta'      => array(
			'eyebrow'     => $text( $cta_in['eyebrow'] ?? '', $defaults['cta']['eyebrow'] ),
			'heading'     => $text( $cta_in['heading'] ?? '', $defaults['cta']['heading'] ),
			'label'       => $text( $cta_in['label'] ?? '', $defaults['cta']['label'] ),
			'note'        => $text( $cta_in['note'] ?? '', $defaults['cta']['note'] ),
			'url'         => isset( $cta_in['url'] ) && '' !== $cta_in['url'] ? esc_url_raw( $cta_in['url'] ) : $defaults['cta']['url'],
			'bg_type'     => $bg_type,
			'bg_solid'    => $pick_color( $cta_in['bg_solid'] ?? '', $palette, $defaults['cta']['bg_solid'] ),
			'bg_gradient' => $pick_color( $cta_in['bg_gradient'] ?? '', $gradients, $defaults['cta']['bg_gradient'] ),
			'text_color'  => $pick_color( $cta_in['text_color'] ?? '', $palette, $defaults['cta']['text_color'] ),
			'btn_base'    => $pick_color( $cta_in['btn_base'] ?? '', $palette, $defaults['cta']['btn_base'] ),
			'btn_text'    => $pick_color( $cta_in['btn_text'] ?? '', $palette, $defaults['cta']['btn_text'] ),
		),
		'toc'      => array(
			'label' => $text( $toc_in['label'] ?? '', $defaults['toc']['label'] ),
			'order' => isset( $toc_in['order'] ) ? absint( $toc_in['order'] ) : $defaults['toc']['order'],
			// Raw block HTML / shortcode (KSES sanitization deferred, admin-entered).
			'html'  => isset( $toc_in['html'] ) ? (string) $toc_in['html'] : '',
		),
		'category' => array(
			'label'   => $text( $cat_in['label'] ?? '', $defaults['category']['label'] ),
			'cat_ids' => $cat_ids,
			'enabled' => ! empty( $cat_in['enabled'] ),
			'order'   => isset( $cat_in['order'] ) ? absint( $cat_in['order'] ) : $defaults['category']['order'],
		),
		'related'  => array(
			'label'   => $text( $rel_in['label'] ?? '', $defaults['related']['label'] ),
			'enabled' => ! empty( $rel_in['enabled'] ),
			'order'   => isset( $rel_in['order'] ) ? absint( $rel_in['order'] ) : $defaults['related']['order'],
		),
	);
}

/**
 * Get sanitized sidebar content settings.
 *
 * Public getter consumed by the theme (guarded with function_exists()).
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_sidebar_content() {
	return skvn_marine_blocks_sanitize_sidebar_content(
		get_option( SKVN_MARINE_BLOCKS_SIDEBAR_CONTENT_OPTION, skvn_marine_blocks_get_default_sidebar_content() )
	);
}

// ---------------------------------------------------------------------------
// TOC per-post metabox (Phase 2)
// ---------------------------------------------------------------------------

const SKVN_MARINE_BLOCKS_TOC_META_KEY = '_skvn_toc_blocks';

add_action( 'add_meta_boxes', 'skvn_marine_blocks_sidebar_toc_metabox' );
add_action( 'save_post', 'skvn_marine_blocks_sidebar_save_toc', 10, 2 );

/**
 * Register the TOC metabox on the post editor.
 *
 * @return void
 */
function skvn_marine_blocks_sidebar_toc_metabox() {
	add_meta_box(
		'skvn-toc-blocks',
		esc_html__( 'Sidebar TOC (mục lục)', 'skvn-marine-blocks' ),
		'skvn_marine_blocks_render_toc_metabox',
		'post',
		'normal',
		'default'
	);
}

/**
 * Render the TOC metabox.
 *
 * @param WP_Post $post Current post.
 * @return void
 */
function skvn_marine_blocks_render_toc_metabox( $post ) {
	wp_nonce_field( 'skvn_marine_blocks_save_toc', 'skvn_marine_blocks_toc_nonce' );
	$value = get_post_meta( $post->ID, SKVN_MARINE_BLOCKS_TOC_META_KEY, true );
	?>
	<p class="description">
		<?php esc_html_e( 'Dán block HTML / shortcode của mục lục. Để trống → island TOC không hiện cho bài này.', 'skvn-marine-blocks' ); ?>
	</p>
	<textarea name="skvn_toc_blocks" rows="8" class="large-text code" style="font-family:monospace;"><?php echo esc_textarea( (string) $value ); ?></textarea>
	<?php
}

/**
 * Persist the TOC metabox content.
 *
 * KSES sanitization is intentionally DEFERRED (bản tạm) — content is stored raw
 * and rendered by the theme via do_blocks()/do_shortcode(). Proper filtering
 * lands at 1.4–1.6.x. Editing is gated by edit_post capability + nonce.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @return void
 */
function skvn_marine_blocks_sidebar_save_toc( $post_id, $post ) {
	if ( ! isset( $_POST['skvn_marine_blocks_toc_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_key( $_POST['skvn_marine_blocks_toc_nonce'] ), 'skvn_marine_blocks_save_toc' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( 'post' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['skvn_toc_blocks'] ) ? wp_unslash( $_POST['skvn_toc_blocks'] ) : '';

	if ( '' === trim( $raw ) ) {
		delete_post_meta( $post_id, SKVN_MARINE_BLOCKS_TOC_META_KEY );
		return;
	}

	update_post_meta( $post_id, SKVN_MARINE_BLOCKS_TOC_META_KEY, $raw );
}

/**
 * Get the raw TOC block content for a post.
 *
 * Public getter consumed by the theme (guarded with function_exists()).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function skvn_marine_blocks_get_post_toc( $post_id ) {
	$value = get_post_meta( (int) $post_id, SKVN_MARINE_BLOCKS_TOC_META_KEY, true );
	return is_string( $value ) ? $value : '';
}

/**
 * Render a text input field.
 *
 * @param string $name  Field name attribute.
 * @param string $value Current value.
 * @return void
 */
function skvn_marine_blocks_sidebar_text_field( $name, $value ) {
	printf(
		'<input type="text" class="regular-text" name="%1$s" value="%2$s">',
		esc_attr( $name ),
		esc_attr( (string) $value )
	);
}

/**
 * Render a color/gradient select field.
 *
 * @param string               $name    Field name attribute.
 * @param string               $current Current slug.
 * @param array<string,string> $options Slug => label.
 * @return void
 */
function skvn_marine_blocks_sidebar_select_field( $name, $current, $options ) {
	?>
	<select name="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $options as $slug => $label ) : ?>
			<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $current, $slug ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Render the sidebar content settings page.
 *
 * @return void
 */
function skvn_marine_blocks_render_sidebar_content_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings = skvn_marine_blocks_get_sidebar_content();
	$opt      = SKVN_MARINE_BLOCKS_SIDEBAR_CONTENT_OPTION;
	$palette  = skvn_marine_blocks_sidebar_palette_options();
	$gradient = skvn_marine_blocks_sidebar_gradient_options();
	$cats     = get_categories( array( 'hide_empty' => false, 'parent' => 0 ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Single post sidebar content', 'skvn-marine-blocks' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Bản tạm — quản nội dung sidebar của single blog post. Thứ tự: số nhỏ hiện trên (CTA luôn cố định trên cùng). Trùng số sẽ tự đẩy sang số trống khi render.', 'skvn-marine-blocks' ); ?>
		</p>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php settings_fields( 'skvn_marine_blocks_sidebar_content' ); ?>

			<h2><?php esc_html_e( 'CTA island (luôn trên cùng)', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Eyebrow', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[cta][eyebrow]", $settings['cta']['eyebrow'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Heading', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[cta][heading]", $settings['cta']['heading'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Button label', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[cta][label]", $settings['cta']['label'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Note', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[cta][note]", $settings['cta']['note'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Button URL', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[cta][url]", $settings['cta']['url'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Background type', 'skvn-marine-blocks' ); ?></th>
					<td>
						<?php
						skvn_marine_blocks_sidebar_select_field(
							"{$opt}[cta][bg_type]",
							$settings['cta']['bg_type'],
							array(
								'solid'    => esc_html__( 'Solid', 'skvn-marine-blocks' ),
								'gradient' => esc_html__( 'Gradient', 'skvn-marine-blocks' ),
							)
						);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Background solid', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_select_field( "{$opt}[cta][bg_solid]", $settings['cta']['bg_solid'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Background gradient', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_select_field( "{$opt}[cta][bg_gradient]", $settings['cta']['bg_gradient'], $gradient ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Text color', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_select_field( "{$opt}[cta][text_color]", $settings['cta']['text_color'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Button base color', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_select_field( "{$opt}[cta][btn_base]", $settings['cta']['btn_base'], $palette ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Button text color', 'skvn-marine-blocks' ); ?></th>
					<td>
						<?php skvn_marine_blocks_sidebar_select_field( "{$opt}[cta][btn_text]", $settings['cta']['btn_text'], $palette ); ?>
						<p class="description"><?php esc_html_e( 'Hover tự derive từ 2 màu button — không chỉnh riêng.', 'skvn-marine-blocks' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'TOC island (mục lục)', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Label', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[toc][label]", $settings['toc']['label'] ); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'TOC HTML / shortcode', 'skvn-marine-blocks' ); ?></th>
						<td>
							<textarea name="<?php echo esc_attr( "{$opt}[toc][html]" ); ?>" rows="6" class="large-text code" style="font-family:monospace;"><?php echo esc_textarea( (string) $settings['toc']['html'] ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Dán HTML/shortcode/block mục lục (sinh từ plugin TOC khác). Trống → ưu tiên metabox per-post nếu có; không có cả hai → ẩn island.', 'skvn-marine-blocks' ); ?></p>
						</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Order', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="1" name="<?php echo esc_attr( "{$opt}[toc][order]" ); ?>" value="<?php echo esc_attr( (string) $settings['toc']['order'] ); ?>" class="small-text"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Category island', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Label', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[category][label]", $settings['category']['label'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Categories (no children)', 'skvn-marine-blocks' ); ?></th>
					<td>
						<?php if ( empty( $cats ) ) : ?>
							<p class="description"><?php esc_html_e( 'Chưa có category top-level.', 'skvn-marine-blocks' ); ?></p>
						<?php else : ?>
							<fieldset>
								<?php foreach ( $cats as $cat ) : ?>
									<label style="display:block;">
										<input type="checkbox" name="<?php echo esc_attr( "{$opt}[category][cat_ids][]" ); ?>" value="<?php echo esc_attr( (string) $cat->term_id ); ?>" <?php checked( in_array( $cat->term_id, $settings['category']['cat_ids'], true ) ); ?>>
										<?php echo esc_html( $cat->name ); ?>
									</label>
								<?php endforeach; ?>
							</fieldset>
							<p class="description"><?php esc_html_e( 'Không chọn → fallback về primary category của bài.', 'skvn-marine-blocks' ); ?></p>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enabled', 'skvn-marine-blocks' ); ?></th>
					<td><label><input type="checkbox" name="<?php echo esc_attr( "{$opt}[category][enabled]" ); ?>" value="1" <?php checked( $settings['category']['enabled'] ); ?>> <?php esc_html_e( 'Hiện island', 'skvn-marine-blocks' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Order', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="1" name="<?php echo esc_attr( "{$opt}[category][order]" ); ?>" value="<?php echo esc_attr( (string) $settings['category']['order'] ); ?>" class="small-text"></td>
				</tr>
			</table>

			<h2><?php esc_html_e( 'Related posts island', 'skvn-marine-blocks' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Label', 'skvn-marine-blocks' ); ?></th>
					<td><?php skvn_marine_blocks_sidebar_text_field( "{$opt}[related][label]", $settings['related']['label'] ); ?></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Enabled', 'skvn-marine-blocks' ); ?></th>
					<td><label><input type="checkbox" name="<?php echo esc_attr( "{$opt}[related][enabled]" ); ?>" value="1" <?php checked( $settings['related']['enabled'] ); ?>> <?php esc_html_e( 'Hiện island', 'skvn-marine-blocks' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e( 'Order', 'skvn-marine-blocks' ); ?></th>
					<td><input type="number" min="1" name="<?php echo esc_attr( "{$opt}[related][order]" ); ?>" value="<?php echo esc_attr( (string) $settings['related']['order'] ); ?>" class="small-text"></td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
