import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export function save() {
	const blockProps = useBlockProps.save({ className: 'skvn-slide swiper-slide' });

	return (
		<div {...blockProps}>
			<InnerBlocks.Content />
		</div>
	);
}
