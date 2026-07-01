<?php
/**
 * Core Control admin page for SKVN Marine Blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the Core Control settings page.
 *
 * @return void
 */
function skvn_marine_blocks_render_core_control_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$registry = skvn_marine_blocks_core_control_registry();
	$enabled  = skvn_marine_blocks_get_core_controls();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'SKVN Core Control', 'skvn-marine-blocks' ); ?></h1>
		<p><?php echo esc_html__( 'Opt-in enhancements for WordPress core blocks and the Gutenberg editor. All features are disabled by default.', 'skvn-marine-blocks' ); ?></p>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php settings_fields( 'skvn_core_controls_group' ); ?>
			<table class="form-table" role="presentation">
				<tbody>
				<?php foreach ( $registry as $id => $feature ) : ?>
					<tr>
						<th scope="row">
							<label for="skvn_core_controls_<?php echo esc_attr( $id ); ?>">
								<?php echo esc_html( $feature['label'] ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="skvn_core_controls_<?php echo esc_attr( $id ); ?>"
								name="<?php echo esc_attr( SKVN_MARINE_BLOCKS_CORE_CONTROLS_OPTION . '[' . $id . ']' ); ?>"
								value="1"
								<?php checked( ! empty( $enabled[ $id ] ) ); ?>
							/>
							<p class="description"><?php echo esc_html( $feature['description'] ); ?></p>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php
			$hn     = skvn_marine_blocks_get_heading_number();
			$hn_opt = SKVN_MARINE_BLOCKS_HEADING_NUMBER_OPTION;
			?>
			<h2><?php esc_html_e( 'Blog heading numbering', 'skvn-marine-blocks' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Áp dụng khi feature "Blog heading numbering" ở trên được bật.', 'skvn-marine-blocks' ); ?></p>
			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><label for="skvn_hn_depth"><?php esc_html_e( 'Độ sâu', 'skvn-marine-blocks' ); ?></label></th>
						<td>
							<select id="skvn_hn_depth" name="<?php echo esc_attr( $hn_opt . '[depth]' ); ?>">
								<option value="h3" <?php selected( $hn['depth'], 'h3' ); ?>><?php esc_html_e( 'Tới H3', 'skvn-marine-blocks' ); ?></option>
								<option value="h4" <?php selected( $hn['depth'], 'h4' ); ?>><?php esc_html_e( 'Tới H4', 'skvn-marine-blocks' ); ?></option>
								<option value="h5" <?php selected( $hn['depth'], 'h5' ); ?>><?php esc_html_e( 'Tới H5', 'skvn-marine-blocks' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="skvn_hn_style"><?php esc_html_e( 'Kiểu số', 'skvn-marine-blocks' ); ?></label></th>
						<td>
							<select id="skvn_hn_style" name="<?php echo esc_attr( $hn_opt . '[style]' ); ?>">
								<option value="decimal" <?php selected( $hn['style'], 'decimal' ); ?>><?php esc_html_e( 'Decimal — 1, 1.1, 1.1.1', 'skvn-marine-blocks' ); ?></option>
								<option value="mixed" <?php selected( $hn['style'], 'mixed' ); ?>><?php esc_html_e( 'Mixed — I, 1, a', 'skvn-marine-blocks' ); ?></option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
