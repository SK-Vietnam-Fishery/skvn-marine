import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type CardAttributes = {
	variant: string;
	stepNumber: string;
	featured: boolean;
	imageTreatment: string;
};

type CardSaveProps = {
	attributes: CardAttributes;
};

function getCardClassName({ variant, featured, imageTreatment }: CardAttributes) {
	return [
		'skvn-card',
		variant && variant !== 'default' ? `skvn-card--${variant}` : '',
		featured ? 'skvn-card--featured' : '',
		imageTreatment ? `skvn-card--image-${imageTreatment}` : '',
	]
		.filter(Boolean)
		.join(' ');
}

export function save({ attributes }: CardSaveProps) {
	const blockProps = useBlockProps.save({
		className: getCardClassName(attributes),
	});

	return (
		<div {...blockProps}>
			{attributes.variant === 'process-step' && attributes.stepNumber && (
				<span aria-hidden="true" className="skvn-card__step-number">
					{attributes.stepNumber}
				</span>
			)}
			<InnerBlocks.Content />
		</div>
	);
}
