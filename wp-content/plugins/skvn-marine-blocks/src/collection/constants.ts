import { __ } from '@wordpress/i18n';

export const LAYOUT_OPTIONS = [
	{ label: __( 'Grid', 'skvn-marine-blocks' ), value: 'grid' },
	{ label: __( 'Carousel', 'skvn-marine-blocks' ), value: 'carousel' },
] as const;

export const ORDER_MODE_OPTIONS = [
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
