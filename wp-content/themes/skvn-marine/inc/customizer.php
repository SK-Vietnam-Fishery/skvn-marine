<?php
/**
 * Font preset Customizer control for SKVN Marine.
 *
 * Registers a radio control under Appearance > Customize > Typography.
 * Selected preset drives a <link> Google Fonts enqueue and inline CSS that
 * writes --skvn-font-heading / --skvn-font-body to :root.
 *
 * Cascade intention: plugin CSS reads --skvn-font-* as fallback tokens;
 * this inline CSS overrides them at the theme layer without touching plugin code.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Preset definitions
// ---------------------------------------------------------------------------

/**
 * Return the four governed font presets.
 *
 * @return array<string, array{heading: string, body: string, gfonts: string|null, label: string}>
 */
function skvn_marine_font_presets(): array {
	return array(
		'instrument' => array(
			'label'   => 'Instrument Serif (mặc định)',
			'heading' => "'Instrument Serif', Georgia, serif",
			'body'    => 'system-ui, sans-serif',
			'gfonts'  => 'https://fonts.googleapis.com/css2?family=Instrument+Serif&subset=vietnamese&display=swap',
		),
		'lora-inter' => array(
			'label'   => 'Lora + Inter',
			'heading' => "'Lora', Georgia, serif",
			'body'    => "'Inter', system-ui, sans-serif",
			'gfonts'  => 'https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=Inter:wght@400;500&subset=vietnamese&display=swap',
		),
		'barlow'     => array(
			'label'   => 'Barlow (sans-serif)',
			'heading' => "'Barlow', system-ui, sans-serif",
			'body'    => "'Barlow', system-ui, sans-serif",
			'gfonts'  => 'https://fonts.googleapis.com/css2?family=Barlow:wght@400;600;700&subset=vietnamese&display=swap',
		),
		'system'     => array(
			'label'   => 'System (không cần Google Fonts)',
			'heading' => 'system-ui, -apple-system, sans-serif',
			'body'    => 'system-ui, -apple-system, sans-serif',
			'gfonts'  => null,
		),
	);
}

/**
 * Valid heading scope values.
 *
 * @return array<string, string>
 */
function skvn_marine_heading_scope_choices(): array {
	return array(
		'h1'    => 'H1 only',
		'h1-h3' => 'H1 – H3',
		'all'   => 'H1 – H6',
	);
}

/**
 * Get the active font preset key, falling back to 'instrument'.
 *
 * @return string
 */
function skvn_marine_get_font_preset(): string {
	$saved   = get_theme_mod( 'skvn_font_preset', 'instrument' );
	$presets = skvn_marine_font_presets();

	return array_key_exists( $saved, $presets ) ? $saved : 'instrument';
}

/**
 * Get the active heading scope, falling back to 'h1-h3'.
 *
 * @return string
 */
function skvn_marine_get_heading_scope(): string {
	$saved = get_theme_mod( 'skvn_font_heading_scope', 'h1-h3' );
	return array_key_exists( $saved, skvn_marine_heading_scope_choices() ) ? $saved : 'h1-h3';
}

// ---------------------------------------------------------------------------
// Customizer registration
// ---------------------------------------------------------------------------

add_action( 'customize_register', 'skvn_marine_register_font_customizer' );

/**
 * Register the font preset section and control in the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 * @return void
 */
function skvn_marine_register_font_customizer( WP_Customize_Manager $wp_customize ): void {
	$wp_customize->add_section(
		'skvn_typography',
		array(
			'title'    => 'Typography (SKVN Marine)',
			'priority' => 30,
		)
	);

	// --- Font preset ---
	$wp_customize->add_setting(
		'skvn_font_preset',
		array(
			'default'           => 'instrument',
			'sanitize_callback' => 'skvn_marine_sanitize_font_preset',
			'transport'         => 'refresh',
		)
	);

	$choices = array();
	foreach ( skvn_marine_font_presets() as $key => $preset ) {
		$choices[ $key ] = esc_html( $preset['label'] );
	}

	$wp_customize->add_control(
		'skvn_font_preset',
		array(
			'label'   => 'Font preset',
			'section' => 'skvn_typography',
			'type'    => 'radio',
			'choices' => $choices,
		)
	);

	// --- Heading scope ---
	$wp_customize->add_setting(
		'skvn_font_heading_scope',
		array(
			'default'           => 'h1-h3',
			'sanitize_callback' => 'skvn_marine_sanitize_heading_scope',
			'transport'         => 'refresh',
		)
	);

	$wp_customize->add_control(
		'skvn_font_heading_scope',
		array(
			'label'       => 'Áp dụng heading font cho',
			'description' => 'Giới hạn font chữ heading — serif thường xấu ở size nhỏ.',
			'section'     => 'skvn_typography',
			'type'        => 'radio',
			'choices'     => skvn_marine_heading_scope_choices(),
		)
	);
}

/**
 * Sanitize the font preset choice — accept only known preset keys.
 *
 * @param string $value Incoming value.
 * @return string Sanitized value or 'instrument' fallback.
 */
function skvn_marine_sanitize_font_preset( string $value ): string {
	return array_key_exists( $value, skvn_marine_font_presets() ) ? $value : 'instrument';
}

/**
 * Sanitize the heading scope choice.
 *
 * @param string $value Incoming value.
 * @return string Sanitized value or 'h1-h3' fallback.
 */
function skvn_marine_sanitize_heading_scope( string $value ): string {
	return array_key_exists( $value, skvn_marine_heading_scope_choices() ) ? $value : 'h1-h3';
}

// ---------------------------------------------------------------------------
// CSS helper — build heading selector from scope setting
// ---------------------------------------------------------------------------

/**
 * Return the CSS selector string for the active heading scope.
 *
 * @param string $scope One of 'h1', 'h1-h3', 'all'.
 * @return string CSS selector.
 */
function skvn_marine_heading_selector( string $scope ): string {
	switch ( $scope ) {
		case 'h1':
			return 'h1';
		case 'all':
			return 'h1,h2,h3,h4,h5,h6';
		default: // 'h1-h3'
			return 'h1,h2,h3';
	}
}

// ---------------------------------------------------------------------------
// Frontend output — Google Fonts link + inline CSS tokens
// ---------------------------------------------------------------------------

add_action( 'wp_enqueue_scripts', 'skvn_marine_enqueue_font_preset', 15 );

/**
 * Enqueue Google Fonts link (when needed) and inject --skvn-font-* tokens.
 *
 * Priority 15 runs after the main stylesheet (priority 10) so the inline CSS
 * attaches to 'skvn-marine-style' and inherits the correct cascade position.
 *
 * @return void
 */
function skvn_marine_enqueue_font_preset(): void {
	$key     = skvn_marine_get_font_preset();
	$presets = skvn_marine_font_presets();
	$preset  = $presets[ $key ];

	// Enqueue Google Fonts only when the preset needs it.
	if ( ! empty( $preset['gfonts'] ) ) {
		wp_enqueue_style(
			'skvn-gfonts-' . $key,
			$preset['gfonts'],
			array(),
			null // Google Fonts manages its own cache headers.
		);
	}

	// Inject CSS custom properties + scoped heading font-family rule.
	// Values come from our own controlled preset array — no user input, no escaping needed.
	// esc_attr() would corrupt single-quoted font names into HTML entities (invalid CSS).
	$scope    = skvn_marine_get_heading_scope();
	$selector = skvn_marine_heading_selector( $scope );
	$css      = ':root{--skvn-font-heading:' . $preset['heading'] . ';--skvn-font-body:' . $preset['body'] . ';}';
	$css     .= $selector . '{font-family:var(--skvn-font-heading);}';

	if ( wp_style_is( 'skvn-marine-style', 'enqueued' ) ) {
		wp_add_inline_style( 'skvn-marine-style', $css );
	}
}

// ---------------------------------------------------------------------------
// Editor output — same tokens so the block editor preview matches frontend
// ---------------------------------------------------------------------------

add_action( 'enqueue_block_editor_assets', 'skvn_marine_enqueue_font_preset_editor', 15 );

/**
 * Inject font tokens into the block editor so headings preview correctly.
 *
 * @return void
 */
function skvn_marine_enqueue_font_preset_editor(): void {
	$key     = skvn_marine_get_font_preset();
	$presets = skvn_marine_font_presets();
	$preset  = $presets[ $key ];

	$scope    = skvn_marine_get_heading_scope();
	$selector = skvn_marine_heading_selector( $scope );
	$css      = ':root{--skvn-font-heading:' . $preset['heading'] . ';--skvn-font-body:' . $preset['body'] . ';}';
	$css     .= $selector . '{font-family:var(--skvn-font-heading);}';

	wp_register_style( 'skvn-marine-font-editor', false, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters
	wp_enqueue_style( 'skvn-marine-font-editor' );
	wp_add_inline_style( 'skvn-marine-font-editor', $css );
}
