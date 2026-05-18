import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const TEMPLATE = [
	['core/heading', { level: 3, placeholder: 'Slide heading' }],
	['core/paragraph', { placeholder: 'Slide copy' }],
];

export function Edit() {
	const blockProps = useBlockProps({ className: 'skvn-slide' });

	return (
		<div {...blockProps}>
			<InnerBlocks template={TEMPLATE} />
		</div>
	);
}
