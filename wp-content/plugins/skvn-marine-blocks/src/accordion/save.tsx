import { InnerBlocks, RichText, useBlockProps } from '@wordpress/block-editor';

type AccordionAttributes = {
	heading: string;
};

type AccordionSaveProps = {
	attributes: AccordionAttributes;
};

export function save({ attributes }: AccordionSaveProps) {
	const blockProps = useBlockProps.save({ className: 'skvn-accordion' });

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
