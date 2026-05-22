import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type AccordionAttributes = {
	heading: string;
};

type AccordionEditProps = {
	attributes: AccordionAttributes;
	setAttributes: (attributes: Partial<AccordionAttributes>) => void;
};

export function Edit({ attributes, setAttributes }: AccordionEditProps) {
	const blockProps = useBlockProps({ className: 'skvn-accordion' });

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Accordion settings', 'skvn-marine-blocks')}>
					<TextControl
						label={__('Heading', 'skvn-marine-blocks')}
						value={attributes.heading}
						onChange={(heading) => setAttributes({ heading })}
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
