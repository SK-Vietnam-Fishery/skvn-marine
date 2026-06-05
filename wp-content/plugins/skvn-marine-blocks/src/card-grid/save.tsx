import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type CardGridAttributes = {
	columns: number;
	mobileColumns: number;
	gap: string;
	cardStyle: string;
	inset: string;
	contentAlign: string;
	equalHeights: boolean;
};

type CardGridSaveProps = {
	attributes: CardGridAttributes;
};

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

export function save({ attributes }: CardGridSaveProps) {
	const blockProps = useBlockProps.save({
		className: getCardGridClassName(attributes),
	});

	return (
		<div {...blockProps}>
			<InnerBlocks.Content />
		</div>
	);
}
