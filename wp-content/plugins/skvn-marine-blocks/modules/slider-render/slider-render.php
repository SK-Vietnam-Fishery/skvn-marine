<?php
/**
 * Dynamic rendering for the SKVN Slider block family.
 *
 * @package SKVN_Marine_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an approved Slider preset.
 *
 * @param mixed $value Raw preset value.
 * @return string
 */
function skvn_marine_blocks_normalize_slider_preset( $value ) {
	$allowed = array( '', 'hero', 'product-showcase', 'card-carousel' );
	$value   = is_string( $value ) ? $value : '';

	return in_array( $value, $allowed, true ) ? $value : '';
}

/**
 * Normalize a bounded integer attribute.
 *
 * @param mixed $value    Raw value.
 * @param int   $default  Default value.
 * @param int   $minimum  Minimum value.
 * @param int   $maximum  Maximum value.
 * @param int   $step     Allowed increment.
 * @return int
 */
function skvn_marine_blocks_normalize_slider_integer( $value, $default, $minimum, $maximum, $step = 1 ) {
	if ( ! is_numeric( $value ) ) {
		return $default;
	}

	$value = (int) $value;
	$value = max( $minimum, min( $maximum, $value ) );

	if ( $step > 1 ) {
		$value = $minimum + (int) round( ( $value - $minimum ) / $step ) * $step;
		$value = max( $minimum, min( $maximum, $value ) );
	}

	return $value;
}

/**
 * Render a block's parsed InnerBlocks exactly once.
 *
 * @param WP_Block $block Block instance.
 * @return string
 */
function skvn_marine_blocks_render_slider_inner_blocks( $block ) {
	if ( ! $block instanceof WP_Block ) {
		return '';
	}

	$content = '';

	foreach ( $block->inner_blocks as $inner_block ) {
		$content .= $inner_block->render();
	}

	return $content;
}

/**
 * Render the Slider shell and controls.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Legacy saved content. Not used for frontend output.
 * @param WP_Block $block      Block instance.
 * @return string
 */
function skvn_marine_blocks_render_slider( $attributes, $content, $block ) {
	unset( $content );

	$preset            = skvn_marine_blocks_normalize_slider_preset( $attributes['preset'] ?? '' );
	$effect            = isset( $attributes['effect'] ) && 'fade' === $attributes['effect'] ? 'fade' : 'slide';
	$responsive_slides = isset( $attributes['responsiveSlides'] ) && '3-2-1' === $attributes['responsiveSlides']
		? '3-2-1'
		: 'uniform';
	$slides_per_view   = skvn_marine_blocks_normalize_slider_integer(
		$attributes['slidesPerView'] ?? 1,
		1,
		1,
		4
	);

	if ( in_array( $preset, array( 'hero', 'product-showcase' ), true ) ) {
		$slides_per_view   = 1;
		$responsive_slides = 'uniform';
	}

	if ( 'card-carousel' === $preset ) {
		$effect            = 'slide';
		$slides_per_view   = 3;
		$responsive_slides = '3-2-1';
	}

	$config = array(
		'autoplay'      => isset( $attributes['autoplay'] ) ? (bool) $attributes['autoplay'] : true,
		'delay'         => skvn_marine_blocks_normalize_slider_integer(
			$attributes['delay'] ?? 5000,
			5000,
			1000,
			12000
		),
		'loop'          => isset( $attributes['loop'] ) ? (bool) $attributes['loop'] : true,
		'arrows'        => isset( $attributes['arrows'] ) ? (bool) $attributes['arrows'] : true,
		'dots'          => isset( $attributes['dots'] ) ? (bool) $attributes['dots'] : true,
		'effect'        => $effect,
		'slidesPerView' => $slides_per_view,
	);

	if ( '3-2-1' === $responsive_slides ) {
		$config['responsiveSlides'] = $responsive_slides;
	}

	$class_name = 'skvn-slider swiper alignfull';

	if ( $preset ) {
		$class_name .= ' skvn-slider--' . $preset;
	}

	if ( $config['arrows'] ) {
		$class_name .= ' skvn-slider--has-arrows';
	}

	if ( $config['dots'] ) {
		$class_name .= ' skvn-slider--has-pagination';
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class'            => $class_name,
			'data-skvn-slider' => wp_json_encode( $config ),
		)
	);
	$slides             = skvn_marine_blocks_render_slider_inner_blocks( $block );
	$output             = '<div ' . $wrapper_attributes . '>';
	$output            .= '<div class="skvn-slider__wrapper swiper-wrapper">' . $slides . '</div>';

	if ( $config['arrows'] ) {
		$output .= sprintf(
			'<button class="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" type="button" aria-label="%s"></button>',
			esc_attr__( 'Previous slide', 'skvn-marine-blocks' )
		);
		$output .= sprintf(
			'<button class="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" type="button" aria-label="%s"></button>',
			esc_attr__( 'Next slide', 'skvn-marine-blocks' )
		);
	}

	if ( $config['dots'] ) {
		$output .= '<div class="skvn-slider__pagination swiper-pagination"></div>';
	}

	$output .= '</div>';

	return $output;
}

/**
 * Render a Slide frame with preset-aware media.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Legacy saved content. Not used for frontend output.
 * @param WP_Block $block      Block instance.
 * @return string
 */
function skvn_marine_blocks_render_slide( $attributes, $content, $block ) {
	unset( $content );

	$preset          = skvn_marine_blocks_normalize_slider_preset(
		$block instanceof WP_Block ? ( $block->context['skvn-marine/sliderPreset'] ?? '' ) : ''
	);
	$image_id        = isset( $attributes['backgroundImageId'] ) ? absint( $attributes['backgroundImageId'] ) : 0;
	$legacy_image_url = isset( $attributes['backgroundImageUrl'] ) && is_string( $attributes['backgroundImageUrl'] )
		? esc_url_raw( $attributes['backgroundImageUrl'] )
		: '';
	$image_url       = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
	$image_url       = is_string( $image_url ) && '' !== $image_url ? $image_url : $legacy_image_url;
	$image_alt       = isset( $attributes['backgroundImageAlt'] ) && is_string( $attributes['backgroundImageAlt'] )
		? sanitize_text_field( $attributes['backgroundImageAlt'] )
		: '';

	if ( '' === $image_alt && $image_id ) {
		$image_alt = sanitize_text_field( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
	}
	$overlay_opacity = skvn_marine_blocks_normalize_slider_integer(
		$attributes['overlayOpacity'] ?? 35,
		35,
		0,
		80,
		5
	);
	$supports_background = ! in_array( $preset, array( 'product-showcase', 'card-carousel' ), true );
	$has_image           = $supports_background && '' !== $image_url;
	$class_name      = 'skvn-slide swiper-slide';

	if ( $has_image ) {
		$class_name .= ' skvn-slide--has-background';
	}

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $class_name ) );
	$inner_content      = skvn_marine_blocks_render_slider_inner_blocks( $block );
	$output             = '<div ' . $wrapper_attributes . '>';
	$output            .= '<div class="skvn-slide__media">';

	if ( $has_image ) {
		$output .= sprintf(
			'<img alt="%1$s" class="skvn-slide__background-image" src="%2$s" />',
			esc_attr( $image_alt ),
			esc_url( $image_url )
		);
		$output .= sprintf(
			'<span aria-hidden="true" class="skvn-slide__overlay" style="opacity:%s"></span>',
			esc_attr( number_format( $overlay_opacity / 100, 2, '.', '' ) )
		);
	}

	$output .= '</div>';
	$output .= '<div class="skvn-slide__content">' . $inner_content . '</div>';
	$output .= '</div>';

	return $output;
}
