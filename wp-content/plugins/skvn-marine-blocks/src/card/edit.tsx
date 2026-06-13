import {
	InnerBlocks,
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type CardAttributes = {
	variant: string;
	stepNumber: string;
	featured: boolean;
	imageTreatment: string;
	motionPreset: string;
	motionTrigger: string;
	motionDevices: MotionDevices;
};

type MotionDevices = {
	desktop: boolean;
	tablet: boolean;
	mobile: boolean;
};

type CardEditProps = {
	attributes: CardAttributes;
	setAttributes: ( attributes: Partial< CardAttributes > ) => void;
};

const TEMPLATE = [
	[ 'core/image', { sizeSlug: 'large' } ],
	[ 'core/heading', { level: 3, placeholder: 'Card heading' } ],
	[ 'core/paragraph', { placeholder: 'Card copy' } ],
	[ 'core/buttons' ],
];

const VARIANT_OPTIONS = [
	{ label: __( 'Default', 'skvn-marine-blocks' ), value: 'default' },
	{ label: __( 'Featured', 'skvn-marine-blocks' ), value: 'featured' },
	{
		label: __( 'Process step', 'skvn-marine-blocks' ),
		value: 'process-step',
	},
	{ label: __( 'Pricing', 'skvn-marine-blocks' ), value: 'pricing' },
];

const IMAGE_TREATMENT_OPTIONS = [
	{ label: __( 'Full bleed', 'skvn-marine-blocks' ), value: 'full-bleed' },
	{ label: __( 'Inset', 'skvn-marine-blocks' ), value: 'inset' },
];

const MOTION_PRESET_OPTIONS = [
	{ label: __( 'None', 'skvn-marine-blocks' ), value: 'none' },
	{ label: __( 'Fade up', 'skvn-marine-blocks' ), value: 'fade-up' },
	{ label: __( 'Fade in', 'skvn-marine-blocks' ), value: 'fade-in' },
	{ label: __( 'Hover lift', 'skvn-marine-blocks' ), value: 'hover-lift' },
];

const ENTRANCE_TRIGGER_OPTIONS = [
	{ label: __( 'On scroll', 'skvn-marine-blocks' ), value: 'on-scroll' },
	{ label: __( 'Always', 'skvn-marine-blocks' ), value: 'always' },
];

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

export function Edit( { attributes, setAttributes }: CardEditProps ) {
	const motionDevices = attributes.motionDevices || {
		desktop: true,
		tablet: true,
		mobile: false,
	};
	const blockProps = useBlockProps( {
		className: getCardClassName( attributes ),
	} );
	const setMotionDevice = (
		device: keyof MotionDevices,
		enabled: boolean
	) => {
		setAttributes( {
			motionDevices: {
				...motionDevices,
				[ device ]: enabled,
			},
		} );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Style', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Variant', 'skvn-marine-blocks' ) }
						onChange={ ( variant ) => setAttributes( { variant } ) }
						options={ VARIANT_OPTIONS }
						value={ attributes.variant }
					/>
					<ToggleControl
						checked={ attributes.featured }
						label={ __(
							'Featured emphasis',
							'skvn-marine-blocks'
						) }
						onChange={ ( featured ) =>
							setAttributes( { featured } )
						}
					/>
					<SelectControl
						label={ __( 'Image treatment', 'skvn-marine-blocks' ) }
						onChange={ ( imageTreatment ) =>
							setAttributes( { imageTreatment } )
						}
						options={ IMAGE_TREATMENT_OPTIONS }
						value={ attributes.imageTreatment }
					/>
				</PanelBody>
				{ attributes.variant === 'process-step' && (
					<PanelBody title={ __( 'Content', 'skvn-marine-blocks' ) }>
						<TextControl
							label={ __( 'Step number', 'skvn-marine-blocks' ) }
							onChange={ ( stepNumber ) =>
								setAttributes( { stepNumber } )
							}
							value={ attributes.stepNumber }
						/>
					</PanelBody>
				) }
				<PanelBody
					title={ __( 'Motion', 'skvn-marine-blocks' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Motion preset', 'skvn-marine-blocks' ) }
						onChange={ ( motionPreset ) =>
							setAttributes( {
								motionPreset,
								motionTrigger:
									motionPreset === 'hover-lift'
										? 'on-hover'
										: attributes.motionTrigger ===
										  'on-hover'
										? 'on-scroll'
										: attributes.motionTrigger,
							} )
						}
						options={ MOTION_PRESET_OPTIONS }
						value={ attributes.motionPreset }
					/>
					{ attributes.motionPreset !== 'none' && (
						<>
							{ attributes.motionPreset !== 'hover-lift' && (
								<SelectControl
									label={ __(
										'Trigger',
										'skvn-marine-blocks'
									) }
									onChange={ ( motionTrigger ) =>
										setAttributes( { motionTrigger } )
									}
									options={ ENTRANCE_TRIGGER_OPTIONS }
									value={ attributes.motionTrigger }
								/>
							) }
							<ToggleControl
								checked={ motionDevices.desktop }
								label={ __( 'Desktop', 'skvn-marine-blocks' ) }
								onChange={ ( enabled ) =>
									setMotionDevice( 'desktop', enabled )
								}
							/>
							<ToggleControl
								checked={ motionDevices.tablet }
								label={ __( 'Tablet', 'skvn-marine-blocks' ) }
								onChange={ ( enabled ) =>
									setMotionDevice( 'tablet', enabled )
								}
							/>
							<ToggleControl
								checked={ motionDevices.mobile }
								label={ __( 'Mobile', 'skvn-marine-blocks' ) }
								onChange={ ( enabled ) =>
									setMotionDevice( 'mobile', enabled )
								}
							/>
						</>
					) }
				</PanelBody>
			</InspectorControls>
			{ attributes.variant === 'process-step' &&
				attributes.stepNumber && (
					<span aria-hidden="true" className="skvn-card__step-number">
						{ attributes.stepNumber }
					</span>
				) }
			<InnerBlocks template={ TEMPLATE } />
		</div>
	);
}
