import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';

type AccordionAttributes = {
	heading: string;
	tone: string;
	spacing: string;
	width: string;
	ariaLabel: string;
};

type AccordionSaveProps = {
	attributes: AccordionAttributes;
};

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

export function save({ attributes }: AccordionSaveProps) {
	const blockProps = useBlockProps.save({
		'aria-label': attributes.ariaLabel || undefined,
		className: getAccordionClassName(attributes),
	});

	return (
		<section {...blockProps}>
			<h3 className="skvn-accordion__heading">
				<RichText.Content value={attributes.heading} />
			</h3>
			<div className="skvn-accordion__panel">
				<InnerBlocks.Content />
			</div>
		</section>
	);
}
