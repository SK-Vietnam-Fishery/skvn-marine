import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type CardGridAttributes = {
	columns: number;
	mobileColumns: number;
	gap: string;
	cardStyle: string;
	inset: string;
	contentAlign: string;
	equalHeights: boolean;
};

type CardGridEditProps = {
	attributes: CardGridAttributes;
	setAttributes: (attributes: Partial<CardGridAttributes>) => void;
};

const TEMPLATE = [
	['skvn-marine/card'],
	['skvn-marine/card'],
	['skvn-marine/card'],
];

const GAP_OPTIONS = [
	{ label: __('Small', 'skvn-marine-blocks'), value: 'sm' },
	{ label: __('Medium', 'skvn-marine-blocks'), value: 'md' },
	{ label: __('Large', 'skvn-marine-blocks'), value: 'lg' },
];

const CARD_STYLE_OPTIONS = [
	{ label: __('Default', 'skvn-marine-blocks'), value: 'default' },
	{ label: __('Elevated', 'skvn-marine-blocks'), value: 'elevated' },
	{ label: __('Bordered', 'skvn-marine-blocks'), value: 'bordered' },
	{ label: __('Featured', 'skvn-marine-blocks'), value: 'featured' },
];

const INSET_OPTIONS = [
	{ label: __('None', 'skvn-marine-blocks'), value: 'none' },
	{ label: __('Small', 'skvn-marine-blocks'), value: 'sm' },
	{ label: __('Medium', 'skvn-marine-blocks'), value: 'md' },
	{ label: __('Large', 'skvn-marine-blocks'), value: 'lg' },
];

const ALIGN_OPTIONS = [
	{ label: __('Left', 'skvn-marine-blocks'), value: 'left' },
	{ label: __('Center', 'skvn-marine-blocks'), value: 'center' },
	{ label: __('Right', 'skvn-marine-blocks'), value: 'right' },
	{ label: __('Justify copy', 'skvn-marine-blocks'), value: 'justify' },
];

function getCardGridClassName({
	columns,
	mobileColumns,
	gap,
	cardStyle,
	inset,
	contentAlign,
	equalHeights,
}: CardGridAttributes) {
	return [
		'skvn-card-grid',
		`skvn-card-grid--${columns || 3}`,
		`skvn-card-grid--mobile-${mobileColumns || 1}`,
		gap && gap !== 'md' ? `skvn-card-grid--gap-${gap}` : '',
		cardStyle && cardStyle !== 'default' ? `skvn-card-grid--card-style-${cardStyle}` : '',
		inset ? `skvn-card-grid--inset-${inset}` : '',
		contentAlign ? `skvn-card-grid--content-${contentAlign}` : '',
		equalHeights ? 'skvn-card-grid--equal-heights' : '',
	]
		.filter(Boolean)
		.join(' ');
}

export function Edit({ attributes, setAttributes }: CardGridEditProps) {
	const blockProps = useBlockProps({
		className: getCardGridClassName(attributes),
	});

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Layout', 'skvn-marine-blocks')}>
					<RangeControl
						label={__('Desktop columns', 'skvn-marine-blocks')}
						max={5}
						min={2}
						onChange={(columns) => setAttributes({ columns: columns || 3 })}
						value={attributes.columns}
					/>
					<RangeControl
						label={__('Mobile columns', 'skvn-marine-blocks')}
						max={2}
						min={1}
						onChange={(mobileColumns) => setAttributes({ mobileColumns: mobileColumns || 1 })}
						value={attributes.mobileColumns}
					/>
					<SelectControl
						label={__('Gap', 'skvn-marine-blocks')}
						onChange={(gap) => setAttributes({ gap })}
						options={GAP_OPTIONS}
						value={attributes.gap}
					/>
					<SelectControl
						label={__('Inset', 'skvn-marine-blocks')}
						onChange={(inset) => setAttributes({ inset })}
						options={INSET_OPTIONS}
						value={attributes.inset}
					/>
					<ToggleControl
						checked={attributes.equalHeights}
						label={__('Equal height cards', 'skvn-marine-blocks')}
						onChange={(equalHeights) => setAttributes({ equalHeights })}
					/>
				</PanelBody>
				<PanelBody title={__('Style', 'skvn-marine-blocks')}>
					<SelectControl
						label={__('Card style', 'skvn-marine-blocks')}
						onChange={(cardStyle) => setAttributes({ cardStyle })}
						options={CARD_STYLE_OPTIONS}
						value={attributes.cardStyle}
					/>
					<SelectControl
						label={__('Content alignment', 'skvn-marine-blocks')}
						onChange={(contentAlign) => setAttributes({ contentAlign })}
						options={ALIGN_OPTIONS}
						value={attributes.contentAlign}
					/>
				</PanelBody>
			</InspectorControls>
			<InnerBlocks allowedBlocks={['skvn-marine/card', 'core/group']} template={TEMPLATE} />
		</div>
	);
}
