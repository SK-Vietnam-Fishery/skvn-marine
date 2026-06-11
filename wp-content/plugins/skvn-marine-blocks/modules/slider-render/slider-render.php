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
 * Return an approved string attribute value.
 *
 * @param mixed  $value   Raw value.
 * @param array  $allowed Allowed values.
 * @param string $default Default value.
 * @return string
 */
function skvn_marine_blocks_normalize_slider_choice( $value, $allowed, $default ) {
	$value = is_string( $value ) ? $value : '';

	return in_array( $value, $allowed, true ) ? $value : $default;
}

/**
 * Read current or legacy Slider attributes without changing saved content.
 *
 * @param array    $attributes Prepared block attributes.
 * @param WP_Block $block      Block instance.
 * @return array
 */
function skvn_marine_blocks_get_slider_attributes( $attributes, $block ) {
	$raw_attributes = $block instanceof WP_Block && isset( $block->parsed_block['attrs'] )
		? $block->parsed_block['attrs']
		: array();
	$legacy_markup  = $block instanceof WP_Block &&
		isset( $block->parsed_block['innerHTML'] ) &&
		false === strpos( $block->parsed_block['innerHTML'], 'skvn-slider__controls' );

	$attributes = array_merge( $raw_attributes, $attributes );

	if ( ! array_key_exists( 'showArrows', $raw_attributes ) && array_key_exists( 'arrows', $raw_attributes ) ) {
		$attributes['showArrows'] = (bool) $raw_attributes['arrows'];
	}

	if ( ! array_key_exists( 'showPagination', $raw_attributes ) && array_key_exists( 'dots', $raw_attributes ) ) {
		$attributes['showPagination'] = (bool) $raw_attributes['dots'];
	}

	if ( ! array_key_exists( 'autoplayDelay', $raw_attributes ) ) {
		if ( array_key_exists( 'delay', $raw_attributes ) ) {
			$attributes['autoplayDelay'] = $raw_attributes['delay'];
		} elseif ( $legacy_markup ) {
			$attributes['autoplayDelay'] = 5000;
		}
	}

	return $attributes;
}

/**
 * Render one arrow control family.
 *
 * @param string $style    Arrow style.
 * @param string $position Arrow position.
 * @return string
 */
function skvn_marine_blocks_render_slider_arrows( $style, $position ) {
	$class_name  = 'skvn-slider__arrows';
	$class_name .= ' skvn-slider__arrows--' . $style;
	$class_name .= ' skvn-slider__arrows--' . $position;
	$output      = '<div class="' . esc_attr( $class_name ) . '">';
	$output     .= sprintf(
		'<button class="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" type="button" aria-label="%s"></button>',
		esc_attr__( 'Previous slide', 'skvn-marine-blocks' )
	);
	$output     .= sprintf(
		'<button class="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" type="button" aria-label="%s"></button>',
		esc_attr__( 'Next slide', 'skvn-marine-blocks' )
	);
	$output     .= '</div>';

	return $output;
}

/**
 * Render the pagination mount point.
 *
 * @param string $style    Pagination style.
 * @param string $position Pagination position.
 * @return string
 */
function skvn_marine_blocks_render_slider_pagination( $style, $position ) {
	$class_name  = 'skvn-slider__pagination swiper-pagination';
	$class_name .= ' skvn-slider__pagination--' . $style;
	$class_name .= ' skvn-slider__pagination--' . $position;

	return '<div class="' . esc_attr( $class_name ) . '"></div>';
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

	$attributes        = skvn_marine_blocks_get_slider_attributes( $attributes, $block );
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

	$slide_count         = $block instanceof WP_Block ? count( $block->inner_blocks ) : 0;
	$has_multiple_slides = $slide_count > 1;
	$show_arrows         = isset( $attributes['showArrows'] )
		? (bool) $attributes['showArrows']
		: ( isset( $attributes['arrows'] ) ? (bool) $attributes['arrows'] : true );
	$show_pagination     = isset( $attributes['showPagination'] )
		? (bool) $attributes['showPagination']
		: ( isset( $attributes['dots'] ) ? (bool) $attributes['dots'] : true );
	$arrow_style         = skvn_marine_blocks_normalize_slider_choice(
		$attributes['arrowStyle'] ?? 'circle',
		array( 'minimal', 'circle', 'pill' ),
		'circle'
	);
	$arrow_position      = skvn_marine_blocks_normalize_slider_choice(
		$attributes['arrowPosition'] ?? 'side-center',
		array( 'side-center', 'bottom-left', 'bottom-center', 'bottom-right' ),
		'side-center'
	);
	$pagination_style    = skvn_marine_blocks_normalize_slider_choice(
		$attributes['paginationStyle'] ?? 'dots',
		array( 'dots', 'fraction', 'timed-fraction', 'timed-segments' ),
		'dots'
	);
	$pagination_position = skvn_marine_blocks_normalize_slider_choice(
		$attributes['paginationPosition'] ?? 'bottom-center',
		array( 'bottom-left', 'bottom-center', 'bottom-right' ),
		'bottom-center'
	);

	if ( 'pill' === $arrow_style && 'side-center' === $arrow_position ) {
		$arrow_position = 'bottom-left';
	}

	$show_arrows     = $show_arrows && $has_multiple_slides;
	$show_pagination = $show_pagination && $has_multiple_slides;
	$autoplay_delay  = $attributes['autoplayDelay'] ?? ( $attributes['delay'] ?? 7000 );
	$autoplay        = isset( $attributes['autoplay'] ) ? (bool) $attributes['autoplay'] : true;
	$loop            = isset( $attributes['loop'] ) ? (bool) $attributes['loop'] : true;
	$autoplay        = $autoplay && $has_multiple_slides;
	$loop            = $loop && $has_multiple_slides;
	$cluster_position = $show_arrows &&
		$show_pagination &&
		'side-center' !== $arrow_position &&
		$arrow_position === $pagination_position
			? $arrow_position
			: '';

	$config = array(
		'autoplay'          => $autoplay,
		'autoplayDelay'     => skvn_marine_blocks_normalize_slider_integer(
			$autoplay_delay,
			7000,
			1000,
			12000
		),
		'loop'              => $loop,
		'showArrows'        => $show_arrows,
		'arrowStyle'        => $arrow_style,
		'arrowPosition'     => $arrow_position,
		'showPagination'    => $show_pagination,
		'paginationStyle'   => $pagination_style,
		'paginationPosition' => $pagination_position,
		'effect'            => $effect,
		'slidesPerView'     => $slides_per_view,
		'slideCount'        => $slide_count,
	);

	if ( '3-2-1' === $responsive_slides ) {
		$config['responsiveSlides'] = $responsive_slides;
	}

	$class_name = 'skvn-slider swiper alignfull';

	if ( $preset ) {
		$class_name .= ' skvn-slider--' . $preset;
	}

	if ( $show_arrows ) {
		$class_name .= ' skvn-slider--has-arrows';
		$class_name .= ' skvn-slider--arrows-' . $arrow_position;
		$class_name .= ' skvn-slider--arrows-' . $arrow_style;
	}

	if ( $show_pagination ) {
		$class_name .= ' skvn-slider--has-pagination';
		$class_name .= ' skvn-slider--pagination-' . $pagination_position;
		$class_name .= ' skvn-slider--pagination-' . $pagination_style;
	}

	$wrapper_attributes = get_block_wrapper_attributes(
		array(
			'class'            => $class_name,
			'data-skvn-slider' => wp_json_encode( $config ),
			'aria-label'       => esc_attr__( 'SKVN content slider', 'skvn-marine-blocks' ),
		)
	);
	$slides             = skvn_marine_blocks_render_slider_inner_blocks( $block );
	$output             = '<div ' . $wrapper_attributes . '>';
	$output            .= '<div class="skvn-slider__wrapper swiper-wrapper">' . $slides . '</div>';

	if ( $show_arrows || $show_pagination ) {
		$controls_class = 'skvn-slider__controls';

		if ( $cluster_position ) {
			$controls_class .= ' skvn-slider__controls--cluster';
			$controls_class .= ' skvn-slider__controls--' . $cluster_position;
		}

		$output .= '<div class="' . esc_attr( $controls_class ) . '">';

		if ( $show_arrows ) {
			$output .= skvn_marine_blocks_render_slider_arrows( $arrow_style, $arrow_position );
		}

		if ( $cluster_position ) {
			$output .= '<span aria-hidden="true" class="skvn-slider__controls-separator"></span>';
		}

		if ( $show_pagination ) {
			$output .= skvn_marine_blocks_render_slider_pagination( $pagination_style, $pagination_position );
		}

		$output .= '</div>';
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
