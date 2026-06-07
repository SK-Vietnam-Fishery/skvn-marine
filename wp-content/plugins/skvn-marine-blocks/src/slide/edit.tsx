import {
	InnerBlocks,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, RangeControl } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

const TEMPLATE = [
	[ 'core/heading', { level: 3, placeholder: 'Slide heading' } ],
	[ 'core/paragraph', { placeholder: 'Slide copy' } ],
	[ 'core/buttons', {}, [ [ 'core/button', { text: 'Learn more' } ] ] ],
];

type SlideAttributes = {
	backgroundImageId: number;
	backgroundImageUrl: string;
	backgroundImageAlt: string;
	overlayOpacity: number;
};

type SlideEditProps = {
	attributes: SlideAttributes;
	clientId: string;
	setAttributes: ( attributes: Partial< SlideAttributes > ) => void;
};

type SelectedImage = {
	id: number;
	url: string;
	alt?: string;
};

export function Edit( { attributes, clientId, setAttributes }: SlideEditProps ) {
	const hasImage = Boolean( attributes.backgroundImageUrl );
	const parentSliderClientId = useSelect(
		( select ) => {
			const editor = select( blockEditorStore ) as {
				getBlockName: ( blockClientId: string ) => string | null;
				getBlockParents: ( blockClientId: string ) => string[];
			};

			return (
				editor
					.getBlockParents( clientId )
					.find(
						( parentClientId ) =>
							editor.getBlockName( parentClientId ) ===
							'skvn-marine/slider'
					) || ''
			);
		},
		[ clientId ]
	);
	const { selectBlock } = useDispatch( blockEditorStore ) as {
		selectBlock: ( blockClientId: string ) => void;
	};
	const blockProps = useBlockProps( {
		className: `skvn-slide skvn-slide--editor${
			hasImage ? ' skvn-slide--has-background' : ''
		}`,
	} );
	const selectImage = ( image: SelectedImage ) => {
		setAttributes( {
			backgroundImageAlt: image.alt || '',
			backgroundImageId: image.id,
			backgroundImageUrl: image.url,
		} );
	};
	const removeImage = () => {
		setAttributes( {
			backgroundImageAlt: '',
			backgroundImageId: 0,
			backgroundImageUrl: '',
		} );
	};

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Slider settings', 'skvn-marine-blocks' ) }>
					<Button
						disabled={ ! parentSliderClientId }
						onClick={ () => selectBlock( parentSliderClientId ) }
						variant="secondary"
					>
						{ __( 'Open Slider settings', 'skvn-marine-blocks' ) }
					</Button>
				</PanelBody>
				<PanelBody
					title={ __( 'Background image', 'skvn-marine-blocks' ) }
				>
					<MediaUploadCheck>
						<MediaUpload
							allowedTypes={ [ 'image' ] }
							onSelect={ selectImage }
							render={ ( { open } ) => (
								<Button onClick={ open } variant="secondary">
									{ hasImage
										? __(
												'Replace image',
												'skvn-marine-blocks'
										  )
										: __(
												'Choose image',
												'skvn-marine-blocks'
										  ) }
								</Button>
							) }
							value={ attributes.backgroundImageId }
						/>
					</MediaUploadCheck>
					{ hasImage && (
						<Button
							isDestructive
							onClick={ removeImage }
							variant="link"
						>
							{ __( 'Remove image', 'skvn-marine-blocks' ) }
						</Button>
					) }
					{ hasImage && (
						<RangeControl
							label={ __(
								'Overlay opacity',
								'skvn-marine-blocks'
							) }
							max={ 80 }
							min={ 0 }
							onChange={ ( overlayOpacity ) =>
								setAttributes( {
									overlayOpacity: overlayOpacity || 0,
								} )
							}
							step={ 5 }
							value={ attributes.overlayOpacity }
						/>
					) }
				</PanelBody>
			</InspectorControls>
			{ hasImage ? (
				<>
					<img
						alt={ attributes.backgroundImageAlt }
						className="skvn-slide__background-image"
						src={ attributes.backgroundImageUrl }
					/>
					<span
						aria-hidden="true"
						className="skvn-slide__overlay"
						style={ { opacity: attributes.overlayOpacity / 100 } }
					/>
				</>
			) : (
				<MediaUploadCheck>
					<MediaUpload
						allowedTypes={ [ 'image' ] }
						onSelect={ selectImage }
						render={ ( { open } ) => (
							<Button
								className="skvn-slide__choose-image"
								onClick={ open }
								variant="secondary"
							>
								{ __(
									'Choose background image',
									'skvn-marine-blocks'
								) }
							</Button>
						) }
					/>
				</MediaUploadCheck>
			) }
			<div className="skvn-slide__content">
				<InnerBlocks template={ TEMPLATE } />
			</div>
		</div>
	);
}
