import { __ } from '@wordpress/i18n';
import { COLOR_PALETTE_OPTIONS } from '../collection/constants';
import type {
	CategoryColumns,
	CategoryGap,
	CategoryGridAttributes,
	CategoryMediaFit,
	CategoryPadding,
	CategoryRadius,
	CategoryShadow,
} from './types';

/** Palette slugs allowed by the wrapper/role classes (mirrors PHP). */
export const CATEGORY_COLOR_SLUGS = COLOR_PALETTE_OPTIONS.map(
	( option ) => option.value as string
).filter( ( value ) => value !== '' );

export const GAP_OPTIONS: { label: string; value: CategoryGap }[] = [
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'small' },
	{ label: __( 'Medium', 'skvn-marine-blocks' ), value: 'medium' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'large' },
];

export const MEDIA_FIT_OPTIONS: { label: string; value: CategoryMediaFit }[] = [
	{ label: __( 'Contain (1:1 frame)', 'skvn-marine-blocks' ), value: 'contain' },
	{ label: __( 'Cover (1:1 frame)', 'skvn-marine-blocks' ), value: 'cover' },
];

export const RADIUS_OPTIONS: { label: string; value: CategoryRadius }[] = [
	{ label: __( 'None', 'skvn-marine-blocks' ), value: 'none' },
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'sm' },
	{ label: __( 'Medium', 'skvn-marine-blocks' ), value: 'md' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
];

export const PADDING_OPTIONS: { label: string; value: CategoryPadding }[] = [
	{ label: __( 'None', 'skvn-marine-blocks' ), value: 'none' },
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'sm' },
	{ label: __( 'Medium', 'skvn-marine-blocks' ), value: 'md' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
];

export const SHADOW_OPTIONS: { label: string; value: CategoryShadow }[] = [
	{ label: __( 'None', 'skvn-marine-blocks' ), value: 'none' },
	{ label: __( 'Soft', 'skvn-marine-blocks' ), value: 'soft' },
	{ label: __( 'Strong', 'skvn-marine-blocks' ), value: 'strong' },
];

export const NAME_SIZE_OPTIONS = [
	{ label: __( 'Inherit', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'sm' },
	{ label: __( 'Base', 'skvn-marine-blocks' ), value: 'base' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
	{ label: __( 'Extra large', 'skvn-marine-blocks' ), value: 'xl' },
];

export const DETAILS_SIZE_OPTIONS = [
	{ label: __( 'Inherit', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'sm' },
	{ label: __( 'Base', 'skvn-marine-blocks' ), value: 'base' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
];

export const FONT_FAMILY_OPTIONS = [
	{ label: __( 'Inherit', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'System', 'skvn-marine-blocks' ), value: 'skvn-system' },
	{ label: __( 'Heading', 'skvn-marine-blocks' ), value: 'skvn-heading' },
	{ label: __( 'UI', 'skvn-marine-blocks' ), value: 'skvn-ui' },
];

export const ACTION_DESTINATION_OPTIONS = [
	{ label: __( 'Category archive', 'skvn-marine-blocks' ), value: 'archive' },
	{ label: __( 'Custom URL', 'skvn-marine-blocks' ), value: 'custom' },
];

function allowed<T extends string>(
	value: unknown,
	values: readonly T[],
	fallback: T
): T {
	const parsed = String( value || '' ) as T;
	return values.includes( parsed ) ? parsed : fallback;
}

function clampColumns( value: unknown, fallback: CategoryColumns ): CategoryColumns {
	const parsed = Number( value );
	const safe = Number.isFinite( parsed ) ? Math.round( parsed ) : fallback;
	return Math.min( 4, Math.max( 1, safe ) ) as CategoryColumns;
}

/**
 * Wrapper class list. Must stay in sync with
 * `skvn_marine_blocks_category_grid_wrapper_class()` in PHP.
 */
export function buildWrapperClass( attributes: CategoryGridAttributes ): string {
	const gap = allowed<CategoryGap>(
		attributes.gap,
		[ 'small', 'medium', 'large' ],
		'medium'
	);
	const fit = allowed<CategoryMediaFit>(
		attributes.mediaFit,
		[ 'contain', 'cover' ],
		'contain'
	);
	const radius = allowed<CategoryRadius>(
		attributes.cardRadius,
		[ 'none', 'sm', 'md', 'lg' ],
		'md'
	);
	const padding = allowed<CategoryPadding>(
		attributes.cardPadding,
		[ 'none', 'sm', 'md', 'lg' ],
		'md'
	);
	const shadow = allowed<CategoryShadow>(
		attributes.cardShadow,
		[ 'none', 'soft', 'strong' ],
		'soft'
	);

	const classes = [
		'skvn-category-grid',
		`skvn-category-grid--gap-${ gap }`,
		`skvn-category-grid--media-${ fit }`,
		`skvn-category-grid--radius-${ radius }`,
		`skvn-category-grid--padding-${ padding }`,
		`skvn-category-grid--shadow-${ shadow }`,
	];

	const background = allowed( attributes.cardBackground, CATEGORY_COLOR_SLUGS, '' );
	if ( background ) {
		classes.push( `skvn-category-grid--card-bg-${ background }` );
	}

	return classes.join( ' ' );
}

/**
 * Column-tier class list for the inner grid element.
 *
 * Container queries only style descendants of the container, so the tier
 * classes cannot live on the wrapper that owns `container-type`.
 * Mirrors `skvn_marine_blocks_category_grid_grid_class()` in PHP.
 */
export function buildGridClass( attributes: CategoryGridAttributes ): string {
	return [
		'skvn-category-grid__grid',
		`skvn-category-grid--cols-d${ clampColumns( attributes.columnsDesktop, 3 ) }`,
		`skvn-category-grid--cols-t${ clampColumns( attributes.columnsTablet, 2 ) }`,
		`skvn-category-grid--cols-m${ clampColumns( attributes.columnsMobile, 1 ) }`,
	].join( ' ' );
}

/**
 * Role class list (color / font family / size) for one card part.
 * Mirrors `skvn_marine_blocks_category_grid_role_class()` in PHP.
 *
 * The action role is emitted as `__cta--*` to match the card markup class.
 */
export function roleClass(
	value: unknown,
	role: 'name' | 'details' | 'action',
	modifier: string,
	values: readonly string[]
): string {
	const parsed = allowed( value, values, '' );
	const prefix = role === 'action' ? 'cta' : role;

	return parsed ? ` skvn-category-grid__${ prefix }--${ modifier }-${ parsed }` : '';
}
