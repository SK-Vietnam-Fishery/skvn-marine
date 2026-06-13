import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type AccordionAttributes = {
	heading: string;
	tone: string;
	spacing: string;
	width: string;
	ariaLabel: string;
};

type AccordionEditProps = {
	attributes: AccordionAttributes;
	setAttributes: (attributes: Partial<AccordionAttributes>) => void;
};

const TONE_OPTIONS = [
	{ label: __('Default', 'skvn-marine-blocks'), value: 'default' },
	{ label: __('Fresh', 'skvn-marine-blocks'), value: 'fresh' },
	{ label: __('Trust', 'skvn-marine-blocks'), value: 'trust' },
	{ label: __('Navy', 'skvn-marine-blocks'), value: 'navy' },
];

const SPACING_OPTIONS = [
	{ label: __('Small', 'skvn-marine-blocks'), value: 'sm' },
	{ label: __('Medium', 'skvn-marine-blocks'), value: 'md' },
	{ label: __('Large', 'skvn-marine-blocks'), value: 'lg' },
];

const WIDTH_OPTIONS = [
	{ label: __('Content', 'skvn-marine-blocks'), value: 'content' },
	{ label: __('Wide', 'skvn-marine-blocks'), value: 'wide' },
	{ label: __('Full', 'skvn-marine-blocks'), value: 'full' },
];

function getAccordionClassName({ tone, spacing, width }: AccordionAttributes) {
	return [
		'skvn-accordion',
		tone && tone !== 'default' ? `skvn-accordion--tone-${tone}` : '',
		spacing && spacing !== 'md' ? `skvn-accordion--spacing-${spacing}` : '',
		width && width !== 'content' ? `skvn-accordion--width-${width}` : '',
	]
		.filter(Boolean)
		.join(' ');
}

export function Edit({ attributes, setAttributes }: AccordionEditProps) {
	const blockProps = useBlockProps({
		'aria-label': attributes.ariaLabel || undefined,
		className: getAccordionClassName(attributes),
	});

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Content', 'skvn-marine-blocks')}>
					<TextControl
						label={__('Heading', 'skvn-marine-blocks')}
						value={attributes.heading}
						onChange={(heading) => setAttributes({ heading })}
					/>
				</PanelBody>
				<PanelBody title={__('Style', 'skvn-marine-blocks')}>
					<SelectControl
						label={__('Tone', 'skvn-marine-blocks')}
						value={attributes.tone}
						options={TONE_OPTIONS}
						onChange={(tone) => setAttributes({ tone })}
					/>
				</PanelBody>
				<PanelBody title={__('Layout', 'skvn-marine-blocks')}>
					<SelectControl
						label={__('Spacing', 'skvn-marine-blocks')}
						value={attributes.spacing}
						options={SPACING_OPTIONS}
						onChange={(spacing) => setAttributes({ spacing })}
					/>
					<SelectControl
						label={__('Width', 'skvn-marine-blocks')}
						value={attributes.width}
						options={WIDTH_OPTIONS}
						onChange={(width) => setAttributes({ width })}
					/>
				</PanelBody>
				<PanelBody title={__('Advanced', 'skvn-marine-blocks')} initialOpen={false}>
					<TextControl
						help={__('Optional accessible label for this accordion group.', 'skvn-marine-blocks')}
						label={__('ARIA label', 'skvn-marine-blocks')}
						value={attributes.ariaLabel}
						onChange={(ariaLabel) => setAttributes({ ariaLabel })}
					/>
				</PanelBody>
			</InspectorControls>
			<RichText
				allowedFormats={[]}
				className="skvn-accordion__heading"
				onChange={(heading) => setAttributes({ heading })}
				placeholder={__('Accordion heading', 'skvn-marine-blocks')}
				tagName="h3"
				value={attributes.heading}
			/>
			<div className="skvn-accordion__panel">
				<InnerBlocks />
			</div>
		</div>
	);
}
