<?php
/**
 * Header action settings for SKVN Marine Blocks.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SKVN_MARINE_BLOCKS_HEADER_ACTIONS_OPTION = 'skvn_header_actions';

add_action( 'admin_menu', 'skvn_marine_blocks_header_settings_menu' );
add_action( 'admin_init', 'skvn_marine_blocks_register_header_settings' );

/**
 * Register the header settings admin page.
 *
 * @return void
 */
function skvn_marine_blocks_header_settings_menu() {
	add_submenu_page(
		'skvn-marine',
		esc_html__( 'SKVN Header Settings', 'skvn-marine-blocks' ),
		esc_html__( 'Header', 'skvn-marine-blocks' ),
		'manage_options',
		'skvn-marine-header',
		'skvn_marine_blocks_render_header_settings_page'
	);
}

/**
 * Register header action settings.
 *
 * @return void
 */
function skvn_marine_blocks_register_header_settings() {
	register_setting(
		'skvn_marine_blocks_header_settings',
		SKVN_MARINE_BLOCKS_HEADER_ACTIONS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'skvn_marine_blocks_sanitize_header_actions',
			'default'           => skvn_marine_blocks_get_default_header_actions(),
		)
	);

	add_settings_section(
		'skvn_marine_blocks_header_settings_main',
		esc_html__( 'Header Actions', 'skvn-marine-blocks' ),
		'skvn_marine_blocks_render_header_settings_section',
		'skvn-marine-header-settings'
	);

	$fields = array(
		'enabled'                => esc_html__( 'Header actions enabled', 'skvn-marine-blocks' ),
		'product_search_enabled' => esc_html__( 'Product search enabled', 'skvn-marine-blocks' ),
		'post_search_enabled'    => esc_html__( 'Post/site search enabled', 'skvn-marine-blocks' ),
		'default_search_target'  => esc_html__( 'Default search target', 'skvn-marine-blocks' ),
		'contact_enabled'        => esc_html__( 'Contact button enabled', 'skvn-marine-blocks' ),
		'contact_label'          => esc_html__( 'Contact button label', 'skvn-marine-blocks' ),
		'contact_url'            => esc_html__( 'Contact button URL', 'skvn-marine-blocks' ),
		'quote_enabled'          => esc_html__( 'Request Quote button enabled', 'skvn-marine-blocks' ),
		'quote_label'            => esc_html__( 'Request Quote label', 'skvn-marine-blocks' ),
		'quote_url'              => esc_html__( 'Request Quote URL', 'skvn-marine-blocks' ),
		'layout'                 => esc_html__( 'Header action layout', 'skvn-marine-blocks' ),
	);

	foreach ( $fields as $field => $label ) {
		add_settings_field(
			$field,
			$label,
			'skvn_marine_blocks_render_header_action_field',
			'skvn-marine-header-settings',
			'skvn_marine_blocks_header_settings_main',
			array(
				'field' => $field,
			)
		);
	}
}

/**
 * Get default header action settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_default_header_actions() {
	return array(
		'enabled'                => false,
		'product_search_enabled' => false,
		'post_search_enabled'    => false,
		'default_search_target'  => 'products',
		'contact_enabled'        => false,
		'contact_label'          => esc_html__( 'Contact', 'skvn-marine-blocks' ),
		'contact_url'            => home_url( '/contact/' ),
		'quote_enabled'          => false,
		'quote_label'            => esc_html__( 'Request Quote', 'skvn-marine-blocks' ),
		'quote_url'              => home_url( '/request-a-quote/' ),
		'layout'                 => 'compact',
	);
}

/**
 * Sanitize header action settings.
 *
 * @param mixed $value Raw option value.
 * @return array<string,mixed>
 */
function skvn_marine_blocks_sanitize_header_actions( $value ) {
	$defaults = skvn_marine_blocks_get_default_header_actions();
	$value    = is_array( $value ) ? wp_unslash( $value ) : array();

	$settings = array(
		'enabled'                => ! empty( $value['enabled'] ),
		'product_search_enabled' => ! empty( $value['product_search_enabled'] ),
		'post_search_enabled'    => ! empty( $value['post_search_enabled'] ),
		'contact_enabled'        => ! empty( $value['contact_enabled'] ),
		'quote_enabled'          => ! empty( $value['quote_enabled'] ),
	);

	$target = isset( $value['default_search_target'] ) ? sanitize_key( $value['default_search_target'] ) : $defaults['default_search_target'];
	if ( ! in_array( $target, array( 'products', 'articles', 'all' ), true ) ) {
		$target = $defaults['default_search_target'];
	}

	$layout = isset( $value['layout'] ) ? sanitize_key( $value['layout'] ) : $defaults['layout'];
	if ( ! in_array( $layout, array( 'compact', 'full' ), true ) ) {
		$layout = $defaults['layout'];
	}

	$settings['default_search_target'] = $target;
	$settings['contact_label']         = isset( $value['contact_label'] ) ? sanitize_text_field( $value['contact_label'] ) : $defaults['contact_label'];
	$settings['contact_url']           = isset( $value['contact_url'] ) ? esc_url_raw( $value['contact_url'] ) : $defaults['contact_url'];
	$settings['quote_label']           = isset( $value['quote_label'] ) ? sanitize_text_field( $value['quote_label'] ) : $defaults['quote_label'];
	$settings['quote_url']             = isset( $value['quote_url'] ) ? esc_url_raw( $value['quote_url'] ) : $defaults['quote_url'];
	$settings['layout']                = $layout;

	foreach ( array( 'contact_label', 'contact_url', 'quote_label', 'quote_url' ) as $key ) {
		if ( '' === $settings[ $key ] ) {
			$settings[ $key ] = $defaults[ $key ];
		}
	}

	return $settings;
}

/**
 * Get sanitized header action settings.
 *
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_header_actions() {
	return skvn_marine_blocks_sanitize_header_actions(
		get_option( SKVN_MARINE_BLOCKS_HEADER_ACTIONS_OPTION, skvn_marine_blocks_get_default_header_actions() )
	);
}

/**
 * Render the settings page intro.
 *
 * @return void
 */
function skvn_marine_blocks_render_header_settings_section() {
	echo '<p>' . esc_html__( 'Configure governed header actions without replacing the GeneratePress header shell.', 'skvn-marine-blocks' ) . '</p>';
}

/**
 * Render a header action field.
 *
 * @param array<string,string> $args Field args.
 * @return void
 */
function skvn_marine_blocks_render_header_action_field( $args ) {
	$field    = isset( $args['field'] ) ? sanitize_key( $args['field'] ) : '';
	$settings = skvn_marine_blocks_get_header_actions();
	$name     = SKVN_MARINE_BLOCKS_HEADER_ACTIONS_OPTION . '[' . $field . ']';
	$value    = isset( $settings[ $field ] ) ? $settings[ $field ] : '';

	if ( in_array( $field, array( 'enabled', 'product_search_enabled', 'post_search_enabled', 'contact_enabled', 'quote_enabled' ), true ) ) {
		printf(
			'<label><input type="checkbox" name="%1$s" value="1" %2$s> %3$s</label>',
			esc_attr( $name ),
			checked( ! empty( $value ), true, false ),
			esc_html__( 'Enabled', 'skvn-marine-blocks' )
		);
		return;
	}

	if ( 'default_search_target' === $field ) {
		skvn_marine_blocks_render_select_field(
			$name,
			(string) $value,
			array(
				'products' => esc_html__( 'Products', 'skvn-marine-blocks' ),
				'articles' => esc_html__( 'Articles', 'skvn-marine-blocks' ),
				'all'      => esc_html__( 'All site', 'skvn-marine-blocks' ),
			)
		);
		return;
	}

	if ( 'layout' === $field ) {
		skvn_marine_blocks_render_select_field(
			$name,
			(string) $value,
			array(
				'compact' => esc_html__( 'Compact', 'skvn-marine-blocks' ),
				'full'    => esc_html__( 'Full', 'skvn-marine-blocks' ),
			)
		);
		return;
	}

	printf(
		'<input type="text" class="regular-text" name="%1$s" value="%2$s">',
		esc_attr( $name ),
		esc_attr( (string) $value )
	);
}

/**
 * Render a preset select field.
 *
 * @param string               $name    Field name.
 * @param string               $current Current value.
 * @param array<string,string> $options Select options.
 * @return void
 */
function skvn_marine_blocks_render_select_field( $name, $current, $options ) {
	?>
	<select name="<?php echo esc_attr( $name ); ?>">
		<?php foreach ( $options as $value => $label ) : ?>
			<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current, $value ); ?>>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<?php
}

/**
 * Render the header settings admin page.
 *
 * @return void
 */
function skvn_marine_blocks_render_header_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'SKVN Marine Header', 'skvn-marine-blocks' ); ?></h1>
		<form action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>" method="post">
			<?php
			settings_fields( 'skvn_marine_blocks_header_settings' );
			do_settings_sections( 'skvn-marine-header-settings' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}
