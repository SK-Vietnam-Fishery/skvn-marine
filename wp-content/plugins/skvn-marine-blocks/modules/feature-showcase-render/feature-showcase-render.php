<?php
/**
 * Dynamic rendering for the SKVN Feature Showcase block.
 *
 * @package SKVN_Marine_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Normalize a governed string attribute against an allowlist.
 *
 * @param mixed  $value   Raw value.
 * @param array  $allowed Allowed values.
 * @param string $default Default value.
 * @return string
 */
function skvn_marine_blocks_normalize_feature_showcase_choice( $value, $allowed, $default ) {
	$value = is_string( $value ) ? $value : '';

	return in_array( $value, $allowed, true ) ? $value : $default;
}

/**
 * Normalize one feature showcase panel item.
 *
 * @param mixed $item Raw item attributes.
 * @return array<string,mixed>|null
 */
function skvn_marine_blocks_normalize_feature_showcase_item( $item ) {
	if ( ! is_array( $item ) ) {
		return null;
	}

	$link_url = isset( $item['linkUrl'] ) ? esc_url_raw( (string) $item['linkUrl'] ) : '';

	return array(
		'kicker'     => isset( $item['kicker'] ) ? wp_kses_post( (string) $item['kicker'] ) : '',
		'heading'    => isset( $item['heading'] ) ? wp_kses_post( (string) $item['heading'] ) : '',
		'copy'       => isset( $item['copy'] ) ? wp_kses_post( (string) $item['copy'] ) : '',
		'imageId'    => isset( $item['imageId'] ) ? absint( $item['imageId'] ) : 0,
		'imageAlt'   => isset( $item['imageAlt'] ) ? sanitize_text_field( (string) $item['imageAlt'] ) : '',
		'linkUrl'    => $link_url,
		'linkText'   => isset( $item['linkText'] ) ? sanitize_text_field( (string) $item['linkText'] ) : '',
		'linkTarget' => isset( $item['linkTarget'] ) && '_blank' === $item['linkTarget'] ? '_blank' : '_self',
	);
}

/**
 * Normalize feature showcase block attributes for frontend render.
 *
 * @param array $attributes Raw block attributes.
 * @return array<string,mixed>
 */
function skvn_marine_blocks_get_feature_showcase_attributes( $attributes ) {
	$attributes = is_array( $attributes ) ? $attributes : array();
	$items      = array();

	if ( isset( $attributes['items'] ) && is_array( $attributes['items'] ) ) {
		foreach ( $attributes['items'] as $item ) {
			$normalized = skvn_marine_blocks_normalize_feature_showcase_item( $item );

			if ( null !== $normalized ) {
				$items[] = $normalized;
			}
		}
	}

	$outer_radius = isset( $attributes['outerRadius'] ) ? absint( $attributes['outerRadius'] ) : 0;
	$outer_radius = min( 50, max( 0, $outer_radius ) );

	$autoplay_delay = isset( $attributes['autoplayDelay'] ) ? absint( $attributes['autoplayDelay'] ) : 5000;
	if ( ! in_array( $autoplay_delay, array( 3000, 5000, 7000, 9000 ), true ) ) {
		$autoplay_delay = 5000;
	}

	return array(
		'desktopLayout'   => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['desktopLayout'] ?? 'horizontal',
			array( 'horizontal', 'vertical' ),
			'horizontal'
		),
		'mobileBehavior'  => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['mobileBehavior'] ?? 'accordion',
			array( 'accordion', 'hidden' ),
			'accordion'
		),
		'defaultOpen'     => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['defaultOpen'] ?? 'last',
			array( 'first', 'last', 'none' ),
			'last'
		),
		'gradientPreset'  => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['gradientPreset'] ?? '',
			array( '', 'deep-navy', 'marine-teal', 'fresh-sky' ),
			''
		),
		'labelRotation'   => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['labelRotation'] ?? 'default',
			array( 'default', '180' ),
			'default'
		),
		'outerRadius'     => $outer_radius,
		'interactionMode' => skvn_marine_blocks_normalize_feature_showcase_choice(
			$attributes['interactionMode'] ?? 'hover',
			array( 'hover', 'autoplay' ),
			'hover'
		),
		'autoplayDelay'   => $autoplay_delay,
		'items'           => $items,
	);
}

/**
 * Build the showcase wrapper class list.
 *
 * @param array<string,mixed> $attributes Normalized attributes.
 * @return string
 */
function skvn_marine_blocks_get_feature_showcase_class_name( $attributes ) {
	$classes = array(
		'skvn-feature-showcase',
		'skvn-feature-showcase--' . $attributes['desktopLayout'],
		'skvn-feature-showcase--mobile-' . $attributes['mobileBehavior'],
	);

	if ( '' !== $attributes['gradientPreset'] ) {
		$classes[] = 'skvn-feature-showcase--gradient-' . $attributes['gradientPreset'];
	}

	if ( '180' === $attributes['labelRotation'] ) {
		$classes[] = 'skvn-feature-showcase--label-rotate-180';
	}

	return implode( ' ', $classes );
}

/**
 * Whether a panel should render open by default.
 *
 * @param int    $index       Panel index.
 * @param int    $item_count  Total panels.
 * @param string $default_open Default open mode.
 * @return bool
 */
function skvn_marine_blocks_feature_showcase_is_initially_open( $index, $item_count, $default_open ) {
	if ( 'first' === $default_open ) {
		return 0 === $index;
	}

	if ( 'last' === $default_open ) {
		return $item_count > 0 && $index === $item_count - 1;
	}

	return false;
}

/**
 * Render a governed attachment image for one panel.
 *
 * @param array<string,mixed> $item Normalized panel item.
 * @return string
 */
function skvn_marine_blocks_render_feature_showcase_image( $item ) {
	$image_id = absint( $item['imageId'] );

	if ( 0 === $image_id ) {
		return '';
	}

	$alt = $item['imageAlt'];

	if ( '' === $alt ) {
		$alt = sanitize_text_field( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) );
	}

	return wp_get_attachment_image(
		$image_id,
		'large',
		false,
		array(
			'class' => 'skvn-feature-showcase__image',
			'alt'   => $alt,
		)
	);
}

/**
 * Render the Feature Showcase block on the frontend.
 *
 * @param array  $attributes Block attributes.
 * @param string $content    Saved block content (unused for dynamic render).
 * @return string
 */
function skvn_marine_blocks_render_feature_showcase( $attributes, $content ) {
	unset( $content );

	$attributes = skvn_marine_blocks_get_feature_showcase_attributes( $attributes );
	$items      = $attributes['items'];
	$item_count = count( $items );

	$wrapper_attributes = array(
		'class'                  => skvn_marine_blocks_get_feature_showcase_class_name( $attributes ),
		'data-skvn-autoplay-delay' => (string) $attributes['autoplayDelay'],
		'data-skvn-interaction'  => $attributes['interactionMode'],
	);

	if ( $attributes['outerRadius'] > 0 ) {
		$wrapper_attributes['style'] = '--skvn-feature-outer-radius: ' . $attributes['outerRadius'] . 'px';
	}

	ob_start();
	?>
	<section <?php echo get_block_wrapper_attributes( $wrapper_attributes ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="skvn-feature-showcase__items">
			<?php foreach ( $items as $index => $item ) : ?>
				<details
					class="skvn-feature-showcase__item"
					<?php echo skvn_marine_blocks_feature_showcase_is_initially_open( $index, $item_count, $attributes['defaultOpen'] ) ? 'open' : ''; ?>
				>
					<summary class="skvn-feature-showcase__summary">
						<span class="skvn-feature-showcase__index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<span class="skvn-feature-showcase__label"><?php echo wp_kses_post( $item['kicker'] ); ?></span>
					</summary>
					<div class="skvn-feature-showcase__body">
						<?php
						$image_html = skvn_marine_blocks_render_feature_showcase_image( $item );
						if ( '' !== $image_html ) {
							echo $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image()
						}
						?>
						<span aria-hidden="true" class="skvn-feature-showcase__shade"></span>
						<div class="skvn-feature-showcase__content">
							<h3 class="skvn-feature-showcase__title"><?php echo wp_kses_post( $item['heading'] ); ?></h3>
							<p class="skvn-feature-showcase__copy"><?php echo wp_kses_post( $item['copy'] ); ?></p>
							<?php if ( '' !== $item['linkUrl'] && '' !== $item['linkText'] ) : ?>
								<a
									class="skvn-feature-showcase__cta"
									href="<?php echo esc_url( $item['linkUrl'] ); ?>"
									<?php echo '_blank' === $item['linkTarget'] ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
								>
									<?php echo esc_html( $item['linkText'] ); ?>
									<?php if ( '_blank' === $item['linkTarget'] ) : ?>
										<span class="screen-reader-text"><?php echo esc_html__( ' (opens in a new tab)', 'skvn-marine-blocks' ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</section>
	<?php

	return (string) ob_get_clean();
}