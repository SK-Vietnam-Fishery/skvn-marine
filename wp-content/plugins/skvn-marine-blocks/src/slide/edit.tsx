import {
	InnerBlocks,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, RangeControl } from '@wordpress/components';
import { store as coreDataStore } from '@wordpress/core-data';
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
	const attachment = useSelect(
		( select ) => {
			if ( ! attributes.backgroundImageId ) {
				return null;
			}

			const coreData = select( coreDataStore ) as {
				getMedia: (
					id: number
				) => { source_url?: string } | null | undefined;
			};

			return coreData.getMedia( attributes.backgroundImageId );
		},
		[ attributes.backgroundImageId ]
	);
	const resolvedImageUrl =
		attachment?.source_url || attributes.backgroundImageUrl;
	const hasImage = Boolean(
		attributes.backgroundImageId || attributes.backgroundImageUrl
	);
	const parentSlider = useSelect(
		( select ) => {
			const editor = select( blockEditorStore ) as {
				getBlockAttributes: (
					blockClientId: string
				) => Record< string, unknown > | null;
				getBlockName: ( blockClientId: string ) => string | null;
				getBlockParents: ( blockClientId: string ) => string[];
			};
			const parentClientId =
				editor
					.getBlockParents( clientId )
					.find(
						( parentId ) =>
							editor.getBlockName( parentId ) ===
							'skvn-marine/slider'
					) || '';

			return {
				clientId: parentClientId,
				preset: parentClientId
					? String(
							editor.getBlockAttributes( parentClientId )
								?.preset || ''
					  )
					: '',
			};
		},
		[ clientId ]
	);
	const supportsBackgroundImage = ! [
		'product-showcase',
		'card-carousel',
	].includes( parentSlider.preset );
	const showsBackgroundImage =
		supportsBackgroundImage && Boolean( resolvedImageUrl );
	const { selectBlock } = useDispatch( blockEditorStore ) as {
		selectBlock: ( blockClientId: string ) => void;
	};
	const blockProps = useBlockProps( {
		className: `skvn-slide skvn-slide--editor${
			showsBackgroundImage ? ' skvn-slide--has-background' : ''
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
						disabled={ ! parentSlider.clientId }
						onClick={ () => selectBlock( parentSlider.clientId ) }
						variant="secondary"
					>
						{ __( 'Open Slider settings', 'skvn-marine-blocks' ) }
					</Button>
				</PanelBody>
				{ supportsBackgroundImage ? (
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
								{ __(
									'Remove image',
									'skvn-marine-blocks'
								) }
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
				) : (
					hasImage && (
						<PanelBody
							title={ __(
								'Unused background image',
								'skvn-marine-blocks'
							) }
						>
							<p>
								{ __(
									'This Slider preset uses its Image block instead of a Slide background image.',
									'skvn-marine-blocks'
								) }
							</p>
							<Button
								isDestructive
								onClick={ removeImage }
								variant="secondary"
							>
								{ __(
									'Remove unused background',
									'skvn-marine-blocks'
								) }
							</Button>
						</PanelBody>
					)
				) }
			</InspectorControls>
			{ showsBackgroundImage ? (
				<>
					<img
						alt={ attributes.backgroundImageAlt }
						className="skvn-slide__background-image"
						src={ resolvedImageUrl }
					/>
					<span
						aria-hidden="true"
						className="skvn-slide__overlay"
						style={ { opacity: attributes.overlayOpacity / 100 } }
					/>
				</>
			) : supportsBackgroundImage ? (
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
			) : null }
			<div className="skvn-slide__content">
				<InnerBlocks template={ TEMPLATE } />
			</div>
		</div>
	);
}
