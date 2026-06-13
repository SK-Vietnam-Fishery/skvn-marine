import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type CardAttributes = {
	variant: string;
	stepNumber: string;
	featured: boolean;
	imageTreatment: string;
	motionPreset: string;
	motionTrigger: string;
	motionDevices: {
		desktop: boolean;
		tablet: boolean;
		mobile: boolean;
	};
};

type CardSaveProps = {
	attributes: CardAttributes;
};

function getCardClassName( {
	variant,
	featured,
	imageTreatment,
}: CardAttributes ) {
	return [
		'skvn-card',
		variant && variant !== 'default' ? `skvn-card--${ variant }` : '',
		featured && variant !== 'featured' ? 'skvn-card--featured' : '',
		imageTreatment ? `skvn-card--image-${ imageTreatment }` : '',
	]
		.filter( Boolean )
		.join( ' ' );
}

export function save( { attributes }: CardSaveProps ) {
	const hasMotion =
		attributes.motionPreset && attributes.motionPreset !== 'none';
	const motionDevices = attributes.motionDevices || {
		desktop: true,
		tablet: true,
		mobile: false,
	};
	const enabledDevices = Object.entries( motionDevices )
		.filter( ( [ , enabled ] ) => enabled )
		.map( ( [ device ] ) => device )
		.join( ' ' );
	const blockProps = useBlockProps.save( {
		className: getCardClassName( attributes ),
		'data-skvn-motion': hasMotion ? attributes.motionPreset : undefined,
		'data-skvn-motion-devices': hasMotion ? enabledDevices : undefined,
		'data-skvn-motion-trigger': hasMotion
			? attributes.motionTrigger
			: undefined,
	} );

	return (
		<div { ...blockProps }>
			{ attributes.variant === 'process-step' &&
				attributes.stepNumber && (
					<span aria-hidden="true" className="skvn-card__step-number">
						{ attributes.stepNumber }
					</span>
				) }
			<InnerBlocks.Content />
		</div>
	);
}
