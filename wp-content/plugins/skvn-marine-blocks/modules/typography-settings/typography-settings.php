<?php
/**
 * Typography settings for SKVN Marine Blocks.
 *
 * Owns: admin submenu page, register_setting, sanitize, option read/write.
 * Theme reads the same option key via inc/typography.php to inject CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION = 'skvn_typography';

add_action( 'admin_menu', 'skvn_marine_blocks_typography_menu' );
add_action( 'admin_init', 'skvn_marine_blocks_register_typography_settings' );

// ---------------------------------------------------------------------------
// Defaults and getter
// ---------------------------------------------------------------------------

/**
 * Default typography values.
 *
 * Must stay in sync with skvn_marine_get_default_typography() in theme.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_default_typography() {
	return array(
		'palette' => array(
			'primary' => '#0369a1',
			'accent'  => '#0d9488',
			'surface' => '#eaf7ff',
			'text'    => '#334155',
		),
		'heading' => array(
			'h1' => array( 'size' => '3rem',     'weight' => '800' ),
			'h2' => array( 'size' => '2.25rem',  'weight' => '700' ),
			'h3' => array( 'size' => '1.875rem', 'weight' => '600' ),
			'h4' => array( 'size' => '1.5rem',   'weight' => '600' ),
		),
	);
}

/**
 * Get saved typography settings run through sanitize with defaults as fallback.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_typography() {
	return skvn_marine_blocks_sanitize_typography(
		get_option( SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION, skvn_marine_blocks_get_default_typography() )
	);
}

// ---------------------------------------------------------------------------
// Sanitize
// ---------------------------------------------------------------------------

/**
 * Sanitize the full typography option array.
 *
 * Registered as sanitize_callback with register_setting so WordPress calls
 * this whenever the option is updated from any source.
 *
 * @param mixed $value Raw option value.
 * @return array<string,mixed>
 */
function skvn_marine_blocks_sanitize_typography( $value ) {
	$defaults = skvn_marine_blocks_get_default_typography();
	$value    = is_array( $value ) ? wp_unslash( $value ) : array();

	$saved = array(
		'palette' => array(),
		'heading' => array(),
	);

	// Palette slots.
	foreach ( array( 'primary', 'accent', 'surface', 'text' ) as $slot ) {
		$raw   = isset( $value['palette'][ $slot ] ) ? $value['palette'][ $slot ] : '';
		$hex   = sanitize_hex_color( $raw );
		$saved['palette'][ $slot ] = $hex ? $hex : $defaults['palette'][ $slot ];
	}

	// Heading levels.
	foreach ( array( 'h1', 'h2', 'h3', 'h4' ) as $level ) {
		$raw_size   = isset( $value['heading'][ $level ]['size'] )   ? $value['heading'][ $level ]['size']   : '';
		$raw_weight = isset( $value['heading'][ $level ]['weight'] ) ? $value['heading'][ $level ]['weight'] : '';

		$size   = skvn_marine_blocks_sanitize_css_size( $raw_size );
		$weight = skvn_marine_blocks_sanitize_font_weight( $raw_weight );

		$saved['heading'][ $level ] = array(
			'size'   => $size   ? $size   : $defaults['heading'][ $level ]['size'],
			'weight' => $weight ? $weight : $defaults['heading'][ $level ]['weight'],
		);
	}

	return $saved;
}

/**
 * Allow rem or px values only.
 *
 * @param string $value Raw input.
 * @return string Sanitized value or empty string.
 */
function skvn_marine_blocks_sanitize_css_size( $value ) {
	$value = sanitize_text_field( wp_unslash( $value ) );

	if ( preg_match( '/^\d+(\.\d+)?(rem|px)$/', $value ) ) {
		return $value;
	}

	return '';
}

/**
 * Allow numeric font-weight values only.
 *
 * @param string $value Raw input.
 * @return string Sanitized value or empty string.
 */
function skvn_marine_blocks_sanitize_font_weight( $value ) {
	$value   = sanitize_text_field( wp_unslash( $value ) );
	$allowed = array( '100', '200', '300', '400', '500', '600', '700', '800', '900' );

	return in_array( $value, $allowed, true ) ? $value : '';
}

// ---------------------------------------------------------------------------
// Admin menu
// ---------------------------------------------------------------------------

/**
 * Register Typography submenu under the SKVN Marine top-level menu.
 *
 * @return void
 */
function skvn_marine_blocks_typography_menu() {
	add_submenu_page(
		'skvn-marine',
		esc_html__( 'Typography Settings', 'skvn-marine-blocks' ),
		esc_html__( 'Typography', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine-typography',
		'skvn_marine_blocks_render_typography_page'
	);
}

// ---------------------------------------------------------------------------
// Register settings — admin_init
// ---------------------------------------------------------------------------

/**
 * Register setting, sections, and fields for the Typography settings page.
 *
 * @return void
 */
function skvn_marine_blocks_register_typography_settings() {
	register_setting(
		'skvn_marine_blocks_typography_settings',
		SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'skvn_marine_blocks_sanitize_typography',
			'default'           => skvn_marine_blocks_get_default_typography(),
		)
	);

	// Section: Brand Palette.
	add_settings_section(
		'skvn_marine_blocks_typography_palette',
		esc_html__( 'Brand Palette', 'skvn-marine-blocks' ),
		'skvn_marine_blocks_render_typography_palette_section',
		'skvn-marine-typography-settings'
	);

	$palette_labels = array(
		'primary' => esc_html__( 'Primary — heading, button, link', 'skvn-marine-blocks' ),
		'accent'  => esc_html__( 'Accent — hover, badge, highlight', 'skvn-marine-blocks' ),
		'surface' => esc_html__( 'Surface — card bg, section bg', 'skvn-marine-blocks' ),
		'text'    => esc_html__( 'Text — body, label, caption', 'skvn-marine-blocks' ),
	);

	foreach ( $palette_labels as $slot => $label ) {
		add_settings_field(
			'skvn_typography_palette_' . $slot,
			$label,
			'skvn_marine_blocks_render_palette_field',
			'skvn-marine-typography-settings',
			'skvn_marine_blocks_typography_palette',
			array( 'slot' => $slot )
		);
	}

	// Section: Heading Scale.
	add_settings_section(
		'skvn_marine_blocks_typography_heading',
		esc_html__( 'Heading Scale', 'skvn-marine-blocks' ),
		'skvn_marine_blocks_render_typography_heading_section',
		'skvn-marine-typography-settings'
	);

	foreach ( array( 'h1', 'h2', 'h3', 'h4' ) as $level ) {
		add_settings_field(
			'skvn_typography_heading_' . $level,
			esc_html( strtoupper( $level ) ),
			'skvn_marine_blocks_render_heading_field',
			'skvn-marine-typography-settings',
			'skvn_marine_blocks_typography_heading',
			array( 'level' => $level )
		);
	}
}

// ---------------------------------------------------------------------------
// Section render callbacks
// ---------------------------------------------------------------------------

/**
 * Render the palette section description.
 *
 * @return void
 */
function skvn_marine_blocks_render_typography_palette_section() {
	echo '<p>' . esc_html__( 'Semantic color slots applied site-wide. Heading, button, and link colors follow the Primary slot.', 'skvn-marine-blocks' ) . '</p>';
}

/**
 * Render the heading scale section description.
 *
 * @return void
 */
function skvn_marine_blocks_render_typography_heading_section() {
	echo '<p>' . esc_html__( 'Global defaults for each heading level. Use rem or px (e.g. 2.25rem). Per-block overrides in the editor still apply.', 'skvn-marine-blocks' ) . '</p>';
}

// ---------------------------------------------------------------------------
// Field render callbacks
// ---------------------------------------------------------------------------

/**
 * Render a palette color input field.
 *
 * @param array<string,string> $args Field args — expects 'slot' key.
 * @return void
 */
function skvn_marine_blocks_render_palette_field( $args ) {
	$slot     = isset( $args['slot'] ) ? sanitize_key( $args['slot'] ) : '';
	$settings = skvn_marine_blocks_get_typography();
	$value    = isset( $settings['palette'][ $slot ] ) ? $settings['palette'][ $slot ] : '';
	$id       = 'skvn_typography_palette_' . $slot;
	$name     = SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION . '[palette][' . $slot . ']';
	?>
	<input
		type="color"
		id="<?php echo esc_attr( $id ); ?>"
		name="<?php echo esc_attr( $name ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
	>
	<code><?php echo esc_html( $value ); ?></code>
	<?php
}

/**
 * Render a heading size and weight field.
 *
 * @param array<string,string> $args Field args — expects 'level' key.
 * @return void
 */
function skvn_marine_blocks_render_heading_field( $args ) {
	$level    = isset( $args['level'] ) ? sanitize_key( $args['level'] ) : '';
	$settings = skvn_marine_blocks_get_typography();
	$size     = isset( $settings['heading'][ $level ]['size'] )   ? $settings['heading'][ $level ]['size']   : '';
	$weight   = isset( $settings['heading'][ $level ]['weight'] ) ? $settings['heading'][ $level ]['weight'] : '';

	$size_id     = 'skvn_typography_heading_' . $level . '_size';
	$weight_id   = 'skvn_typography_heading_' . $level . '_weight';
	$size_name   = SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION . '[heading][' . $level . '][size]';
	$weight_name = SKVN_MARINE_BLOCKS_TYPOGRAPHY_OPTION . '[heading][' . $level . '][weight]';

	$weight_options = array(
		'400' => __( '400 — Regular', 'skvn-marine-blocks' ),
		'500' => __( '500 — Medium', 'skvn-marine-blocks' ),
		'600' => __( '600 — Semi Bold', 'skvn-marine-blocks' ),
		'700' => __( '700 — Bold', 'skvn-marine-blocks' ),
		'800' => __( '800 — Extra Bold', 'skvn-marine-blocks' ),
	);
	?>
	<label for="<?php echo esc_attr( $size_id ); ?>"><?php esc_html_e( 'Size', 'skvn-marine-blocks' ); ?></label>
	<input
		type="text"
		id="<?php echo esc_attr( $size_id ); ?>"
		name="<?php echo esc_attr( $size_name ); ?>"
		value="<?php echo esc_attr( $size ); ?>"
		placeholder="<?php echo esc_attr__( 'e.g. 2.25rem', 'skvn-marine-blocks' ); ?>"
		class="small-text"
	>
	&nbsp;&nbsp;
	<label for="<?php echo esc_attr( $weight_id ); ?>"><?php esc_html_e( 'Weight', 'skvn-marine-blocks' ); ?></label>
	<select
		id="<?php echo esc_attr( $weight_id ); ?>"
		name="<?php echo esc_attr( $weight_name ); ?>"
	>
		<?php foreach ( $weight_options as $opt_val => $opt_label ) : ?>
			<option value="<?php echo esc_attr( $opt_val ); ?>" <?php selected( $weight, $opt_val ); ?>>
				<?php echo esc_html( $opt_label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

// ---------------------------------------------------------------------------
// Page render
// ---------------------------------------------------------------------------

/**
 * Render the Typography settings admin page.
 *
 * @return void
 */
function skvn_marine_blocks_render_typography_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Typography Settings', 'skvn-marine-blocks' ); ?></h1>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php
			settings_fields( 'skvn_marine_blocks_typography_settings' );
			do_settings_sections( 'skvn-marine-typography-settings' );
			submit_button( __( 'Save Typography Settings', 'skvn-marine-blocks' ) );
			?>
		</form>
	</div>
	<?php
}
