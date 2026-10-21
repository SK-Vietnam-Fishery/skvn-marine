import { __ } from '@wordpress/i18n';
import type { CollectionAttributes } from './types';

export const LAYOUT_OPTIONS = [
	{ label: __( 'Grid', 'skvn-marine-blocks' ), value: 'grid' },
	{ label: __( 'Carousel', 'skvn-marine-blocks' ), value: 'carousel' },
] as const;

export const CONTENT_WIDTH_OPTIONS = [
	{ label: __( 'Normal (content width)', 'skvn-marine-blocks' ), value: 'normal' },
	{ label: __( 'Wide (wide width)', 'skvn-marine-blocks' ), value: 'wide' },
	{ label: __( 'Full (page canvas)', 'skvn-marine-blocks' ), value: 'full' },
] as const;

/**
 * Max visible attribute values per group (Product Carousel).
 *
 * The allowlist and the fallback are duplicated in PHP
 * (skvn_marine_blocks_collection_spec_values_limit). Keep both in sync.
 *
 * Fallback = 3: human decision 2026-10-19. It applies when a block has no
 * `specValuesLimit` (legacy, or a new carousel the editor has not set yet) or
 * when a stored value is outside 2/3/4. Inserter Carousel variations do not
 * pre-write the attribute, so nothing is force-saved into content.
 */
export const SPEC_VALUES_LIMIT_VALUES = [ 2, 3, 4 ] as const;
export const SPEC_VALUES_LIMIT_DEFAULT = 3;

export const SPEC_VALUES_LIMIT_OPTIONS = [
	{ label: __( '2 values per group', 'skvn-marine-blocks' ), value: 2 },
	{ label: __( '3 values per group', 'skvn-marine-blocks' ), value: 3 },
	{ label: __( '4 values per group', 'skvn-marine-blocks' ), value: 4 },
] as const;

export function resolveSpecValuesLimit( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	return ( SPEC_VALUES_LIMIT_VALUES as readonly number[] ).includes( parsed )
		? parsed
		: SPEC_VALUES_LIMIT_DEFAULT;
}

export const ORDER_MODE_OPTIONS = [
	{ label: __( 'Featured', 'skvn-marine-blocks' ), value: 'featured' },
	{ label: __( 'Newest', 'skvn-marine-blocks' ), value: 'newest' },
	{ label: __( 'Manual order', 'skvn-marine-blocks' ), value: 'manual' },
	{
		label: __( 'Shuffle balanced', 'skvn-marine-blocks' ),
		value: 'shuffle-balanced',
	},
] as const;

export const RESPONSIVE_PRESET_OPTIONS = [
	{ label: '1-1-1', value: '1-1-1' },
	{ label: '2-1-1', value: '2-1-1' },
	{ label: '3-2-1', value: '3-2-1' },
	{ label: '4-2-1', value: '4-2-1' },
	{ label: '5-3-1', value: '5-3-1' },
] as const;

export const RELATION_OPTIONS = [
	{ label: __( 'Match any selected term', 'skvn-marine-blocks' ), value: 'OR' },
	{ label: __( 'Match all selected terms', 'skvn-marine-blocks' ), value: 'AND' },
] as const;

export const IMAGE_RATIO_OPTIONS = [
	{ label: '1:1', value: '1:1' },
	{ label: '4:3', value: '4:3' },
	{ label: '3:2', value: '3:2' },
	{ label: '16:9', value: '16:9' },
	{ label: __( 'Auto', 'skvn-marine-blocks' ), value: 'auto' },
] as const;

export const BADGE_BEHAVIOR_OPTIONS = [
	{ label: __( 'Display only', 'skvn-marine-blocks' ), value: 'display' },
	{ label: __( 'Archive links', 'skvn-marine-blocks' ), value: 'archive-link' },
] as const;

export const POST_ACTION_OPTIONS = [
	{ label: __( 'Read more', 'skvn-marine-blocks' ), value: 'read' },
	{ label: __( 'Custom URL', 'skvn-marine-blocks' ), value: 'custom' },
] as const;

export const PRODUCT_ACTION_OPTIONS = [
	{ label: __( 'View product', 'skvn-marine-blocks' ), value: 'view' },
	{ label: __( 'Request quote', 'skvn-marine-blocks' ), value: 'quote' },
	{ label: __( 'Both', 'skvn-marine-blocks' ), value: 'both' },
	{ label: __( 'Custom URL', 'skvn-marine-blocks' ), value: 'custom' },
] as const;

export const CARD_STYLE_OPTIONS = [
	{ label: __( 'Default', 'skvn-marine-blocks' ), value: 'default' },
	{ label: __( 'Featured (badge overlay)', 'skvn-marine-blocks' ), value: 'featured' },
] as const;

export const CHIP_STYLE_OPTIONS = [
	{ label: __( 'Tag (pill + border)', 'skvn-marine-blocks' ), value: 'tag' },
	{ label: __( 'Hashtag (#)', 'skvn-marine-blocks' ), value: 'hashtag' },
	{ label: __( 'Dot (•)', 'skvn-marine-blocks' ), value: 'dot' },
	{ label: __( 'Plain (/)', 'skvn-marine-blocks' ), value: 'plain' },
] as const;

/**
 * Typography roles (S5). Bounded options lấy từ theme.json thực có.
 * Không expose display/3xl/4xl/5xl cho card trong đợt này.
 */
export const FONT_FAMILY_OPTIONS = [
	{ label: __( 'Inherit (theme)', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'SKVN System', 'skvn-marine-blocks' ), value: 'skvn-system' },
	{ label: __( 'SKVN Heading', 'skvn-marine-blocks' ), value: 'skvn-heading' },
	{ label: __( 'SKVN UI', 'skvn-marine-blocks' ), value: 'skvn-ui' },
] as const;

export const TITLE_SIZE_OPTIONS = [
	{ label: __( 'Inherit', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'Base', 'skvn-marine-blocks' ), value: 'base' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
	{ label: __( 'XL', 'skvn-marine-blocks' ), value: 'xl' },
	{ label: __( '2XL', 'skvn-marine-blocks' ), value: '2xl' },
] as const;

export const DETAILS_SIZE_OPTIONS = [
	{ label: __( 'Inherit', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'Small', 'skvn-marine-blocks' ), value: 'sm' },
	{ label: __( 'Base', 'skvn-marine-blocks' ), value: 'base' },
	{ label: __( 'Large', 'skvn-marine-blocks' ), value: 'lg' },
] as const;

/**
 * Palette colors từ theme.json. Mỗi option là slug; CSS resolve qua
 * var(--wp--preset--color--{slug}). Không thêm bảng màu riêng.
 */
export const COLOR_PALETTE_OPTIONS = [
	{ label: __( 'Inherit (default)', 'skvn-marine-blocks' ), value: '' },
	{ label: __( 'SKVN Blue 950', 'skvn-marine-blocks' ), value: 'skvn-blue-950' },
	{ label: __( 'SKVN Blue 900', 'skvn-marine-blocks' ), value: 'skvn-blue-900' },
	{ label: __( 'SKVN Blue 700', 'skvn-marine-blocks' ), value: 'skvn-blue-700' },
	{ label: __( 'SKVN Mint 100', 'skvn-marine-blocks' ), value: 'skvn-mint-100' },
	{ label: __( 'SKVN Gold 300', 'skvn-marine-blocks' ), value: 'skvn-gold-300' },
	{ label: __( 'SKVN Teal 600', 'skvn-marine-blocks' ), value: 'skvn-teal-600' },
	{ label: __( 'SKVN Sky 50', 'skvn-marine-blocks' ), value: 'skvn-sky-50' },
	{ label: __( 'SKVN Slate 700', 'skvn-marine-blocks' ), value: 'skvn-slate-700' },
	{ label: __( 'SKVN Slate 900', 'skvn-marine-blocks' ), value: 'skvn-slate-900' },
	{ label: __( 'SKVN White', 'skvn-marine-blocks' ), value: 'skvn-white' },
] as const;

export function resolveFontSize( value: unknown, allowed: readonly string[] ): string {
	const parsed = String( value || '' );
	return allowed.includes( parsed ) ? parsed : '';
}

/**
 * Media presets (S6 / P4, Product only).
 *
 * Width % is relative to the card (not image height), so cards of different
 * sizes stay consistent. Offset and padding keep geometry inside the card.
 * All defaults preserve legacy output when the attribute is missing:
 * mediaLayout resolves to 'flush' and no float/shadow/padding classes apply.
 */

/** Media layout options. 'flush' keeps existing card/image behavior. */
export const MEDIA_LAYOUT_OPTIONS = [
	{ label: __( 'Flush', 'skvn-marine-blocks' ), value: 'flush' },
	{ label: __( 'Padded', 'skvn-marine-blocks' ), value: 'padded' },
	{ label: __( 'Floating', 'skvn-marine-blocks' ), value: 'floating' },
] as const;
export const MEDIA_LAYOUT_DEFAULT: MediaLayout = 'flush';

/** Floating: image width as % of card width. Max 100% so it never breaks
 *  out of the card horizontally. */
export const MEDIA_WIDTH_MIN = 60;
export const MEDIA_WIDTH_MAX = 100;
export const MEDIA_WIDTH_STEP = 5;
export const MEDIA_WIDTH_DEFAULT = 100;

/** Floating: lift offset in px (negative = up). Floor -60 so most of the
 *  image stays visible and it does not collide with section heading/nav. */
export const MEDIA_OFFSET_MIN = -60;
export const MEDIA_OFFSET_MAX = 0;
export const MEDIA_OFFSET_STEP = 1;
export const MEDIA_OFFSET_DEFAULT = -12;

/** Shadow offsets (signed), blur, opacity. Unit is px or rem. */
export const MEDIA_SHADOW_OFFSET_MIN = -30;
export const MEDIA_SHADOW_OFFSET_MAX = 30;
export const MEDIA_SHADOW_OFFSET_STEP = 1;
export const MEDIA_SHADOW_OFFSET_DEFAULT = 0;

export const MEDIA_SHADOW_BLUR_MIN = 0;
export const MEDIA_SHADOW_BLUR_MAX = 40;
export const MEDIA_SHADOW_BLUR_STEP = 1;
export const MEDIA_SHADOW_BLUR_DEFAULT = 8;

export const MEDIA_SHADOW_OPACITY_MIN = 0;
export const MEDIA_SHADOW_OPACITY_MAX = 100;
export const MEDIA_SHADOW_OPACITY_STEP = 1;
export const MEDIA_SHADOW_OPACITY_DEFAULT = 30;

export const MEDIA_SHADOW_UNIT_OPTIONS = [
	{ label: 'px', value: 'px' },
	{ label: 'rem', value: 'rem' },
] as const;
export const MEDIA_SHADOW_UNIT_DEFAULT: ShadowUnit = 'px';

/** Padded: frame padding on top/left/right. Unit is px or rem. */
export const MEDIA_PADDING_MIN = 0;
export const MEDIA_PADDING_MAX = 40;
export const MEDIA_PADDING_STEP = 1;
export const MEDIA_PADDING_DEFAULT = 8;

export const MEDIA_PADDING_UNIT_OPTIONS = [
	{ label: 'px', value: 'px' },
	{ label: 'rem', value: 'rem' },
] as const;
export const MEDIA_PADDING_UNIT_DEFAULT: PaddingUnit = 'rem';

export type MediaLayoutValue = 'flush' | 'padded' | 'floating' | '';
export type ShadowUnitValue = 'px' | 'rem' | '';
export type PaddingUnitValue = 'px' | 'rem' | '';

export function resolveMediaLayout( value: unknown ): MediaLayout {
	const v = String( value || '' );
	return ( [ 'flush', 'padded', 'floating' ] as readonly string[] ).includes( v )
		? ( v as MediaLayout )
		: MEDIA_LAYOUT_DEFAULT;
}

export function resolveMediaWidthPercent( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_WIDTH_DEFAULT;
	}
	return Math.min( MEDIA_WIDTH_MAX, Math.max( MEDIA_WIDTH_MIN, parsed ) );
}

export function resolveMediaOffset( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_OFFSET_DEFAULT;
	}
	return Math.min( MEDIA_OFFSET_MAX, Math.max( MEDIA_OFFSET_MIN, parsed ) );
}

export function resolveMediaShadowOffset( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_SHADOW_OFFSET_DEFAULT;
	}
	return Math.min( MEDIA_SHADOW_OFFSET_MAX, Math.max( MEDIA_SHADOW_OFFSET_MIN, parsed ) );
}

export function resolveMediaShadowBlur( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_SHADOW_BLUR_DEFAULT;
	}
	return Math.min( MEDIA_SHADOW_BLUR_MAX, Math.max( MEDIA_SHADOW_BLUR_MIN, parsed ) );
}

export function resolveMediaShadowOpacity( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_SHADOW_OPACITY_DEFAULT;
	}
	return Math.min( MEDIA_SHADOW_OPACITY_MAX, Math.max( MEDIA_SHADOW_OPACITY_MIN, parsed ) );
}

export function resolveMediaPadding( value: unknown ): number {
	const parsed = typeof value === 'number' ? value : parseInt( String( value ), 10 );
	if ( ! Number.isFinite( parsed ) ) {
		return MEDIA_PADDING_DEFAULT;
	}
	return Math.min( MEDIA_PADDING_MAX, Math.max( MEDIA_PADDING_MIN, parsed ) );
}

export function resolveShadowUnit( value: unknown ): ShadowUnit {
	const v = String( value || '' );
	return ( [ 'px', 'rem' ] as readonly string[] ).includes( v ) ? ( v as ShadowUnit ) : MEDIA_SHADOW_UNIT_DEFAULT;
}

export function resolvePaddingUnit( value: unknown ): PaddingUnit {
	const v = String( value || '' );
	return ( [ 'px', 'rem' ] as readonly string[] ).includes( v ) ? ( v as PaddingUnit ) : MEDIA_PADDING_UNIT_DEFAULT;
}

/**
 * Build the floating image box-shadow from shadow settings. Returns 'none'
 * when shadow is off or layout is not floating, so no shadow leaks into
 * flush/padded presets.
 */
export function resolveMediaShadowCSS( attributes: CollectionAttributes ): string {
	if ( attributes.mediaShadowToggle !== true ) {
		return 'none';
	}
	const unit = resolveShadowUnit( attributes.mediaShadowUnit );
	const x =
		resolveMediaShadowOffset( attributes.mediaShadowOffsetX ) + unit;
	const y =
		resolveMediaShadowOffset( attributes.mediaShadowOffsetY ) + unit;
	const blur = resolveMediaShadowBlur( attributes.mediaShadowBlur ) + unit;
	const opacity =
		resolveMediaShadowOpacity( attributes.mediaShadowOpacity ) / 100;
	return `${ x } ${ y } ${ blur } rgba(0, 0, 0, ${ opacity })`;
}
