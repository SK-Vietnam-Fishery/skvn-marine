import { __ } from '@wordpress/i18n';

export const LAYOUT_OPTIONS = [
	{ label: __( 'Grid', 'skvn-marine-blocks' ), value: 'grid' },
	{ label: __( 'Carousel', 'skvn-marine-blocks' ), value: 'carousel' },
] as const;

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
