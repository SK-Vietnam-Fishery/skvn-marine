import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import type { FeatureItem, FeatureShowcaseAttributes } from './types';

type FeatureShowcaseEditProps = {
	attributes: FeatureShowcaseAttributes;
	setAttributes: ( attributes: Partial< FeatureShowcaseAttributes > ) => void;
};

type SelectedImage = {
	id: number;
	url: string;
	alt?: string;
};

function createItem( index: number ): FeatureItem {
	const itemNumber = String( index + 1 ).padStart( 2, '0' );

	return {
		kicker: `${ itemNumber } - New feature`,
		heading: 'Feature heading',
		copy: 'Describe the capability, process, or outcome represented by this panel.',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	};
}

function getClassName( attributes: FeatureShowcaseAttributes ) {
	return [
		'skvn-feature-showcase',
		'skvn-feature-showcase--editor',
		`skvn-feature-showcase--${ attributes.desktopLayout }`,
		`skvn-feature-showcase--mobile-${ attributes.mobileBehavior }`,
		attributes.gradientPreset
			? `skvn-feature-showcase--gradient-${ attributes.gradientPreset }`
			: '',
	]
		.filter( Boolean )
		.join( ' ' );
}

export function Edit( { attributes, setAttributes }: FeatureShowcaseEditProps ) {
	const blockProps = useBlockProps( {
		className: getClassName( attributes ),
	} );
	const items = attributes.items || [];
	const setItem = ( index: number, itemPatch: Partial< FeatureItem > ) => {
		setAttributes( {
			items: items.map( ( item, itemIndex ) =>
				itemIndex === index ? { ...item, ...itemPatch } : item
			),
		} );
	};
	const moveItem = ( index: number, offset: -1 | 1 ) => {
		const targetIndex = index + offset;

		if ( targetIndex < 0 || targetIndex >= items.length ) {
			return;
		}

		const nextItems = [ ...items ];
		[ nextItems[ index ], nextItems[ targetIndex ] ] = [
			nextItems[ targetIndex ],
			nextItems[ index ],
		];
		setAttributes( { items: nextItems } );
	};

	return (
		<section { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Layout', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Desktop direction', 'skvn-marine-blocks' ) }
						onChange={ ( desktopLayout ) =>
							setAttributes( {
								desktopLayout:
									desktopLayout as FeatureShowcaseAttributes[ 'desktopLayout' ],
							} )
						}
						options={ [
							{
								label: __( 'Horizontal panels', 'skvn-marine-blocks' ),
								value: 'horizontal',
							},
							{
								label: __( 'Vertical panels', 'skvn-marine-blocks' ),
								value: 'vertical',
							},
						] }
						value={ attributes.desktopLayout }
					/>
					<SelectControl
						label={ __( 'Mobile behavior', 'skvn-marine-blocks' ) }
						onChange={ ( mobileBehavior ) =>
							setAttributes( {
								mobileBehavior:
									mobileBehavior as FeatureShowcaseAttributes[ 'mobileBehavior' ],
							} )
						}
						options={ [
							{
								label: __( 'Tap to reveal', 'skvn-marine-blocks' ),
								value: 'accordion',
							},
							{
								label: __( 'Hide on mobile', 'skvn-marine-blocks' ),
								value: 'hidden',
							},
						] }
						value={ attributes.mobileBehavior }
					/>
					<SelectControl
						label={ __( 'Initially open panel', 'skvn-marine-blocks' ) }
						onChange={ ( defaultOpen ) =>
							setAttributes( {
								defaultOpen:
									defaultOpen as FeatureShowcaseAttributes[ 'defaultOpen' ],
							} )
						}
						options={ [
							{
								label: __( 'Last panel', 'skvn-marine-blocks' ),
								value: 'last',
							},
							{
								label: __( 'First panel', 'skvn-marine-blocks' ),
								value: 'first',
							},
							{
								label: __( 'No panel', 'skvn-marine-blocks' ),
								value: 'none',
							},
						] }
						value={ attributes.defaultOpen }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Style', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Gradient background', 'skvn-marine-blocks' ) }
						onChange={ ( gradientPreset ) =>
							setAttributes( {
								gradientPreset:
									gradientPreset as FeatureShowcaseAttributes[ 'gradientPreset' ],
							} )
						}
						options={ [
							{
								label: __( 'Ocean', 'skvn-marine-blocks' ),
								value: '',
							},
							{
								label: __( 'Deep navy', 'skvn-marine-blocks' ),
								value: 'deep-navy',
							},
							{
								label: __( 'Marine teal', 'skvn-marine-blocks' ),
								value: 'marine-teal',
							},
							{
								label: __( 'Fresh sky', 'skvn-marine-blocks' ),
								value: 'fresh-sky',
							},
						] }
						value={ attributes.gradientPreset }
					/>
				</PanelBody>
			</InspectorControls>
			<div className="skvn-feature-showcase__items">
					{ items.map( ( item, index ) => (
						<article
							className="skvn-feature-showcase__item"
							key={ index }
						>
							{ item.imageUrl && (
								<img
									alt={ item.imageAlt }
									className="skvn-feature-showcase__image"
									src={ item.imageUrl }
								/>
							) }
							<div className="skvn-feature-showcase__shade" />
							<RichText
								allowedFormats={ [] }
								className="skvn-feature-showcase__label"
								onChange={ ( kicker ) =>
									setItem( index, { kicker } )
								}
								tagName="p"
								value={ item.kicker }
							/>
							<div className="skvn-feature-showcase__content">
								<RichText
									allowedFormats={ [] }
									className="skvn-feature-showcase__title"
									onChange={ ( heading ) =>
										setItem( index, { heading } )
									}
									tagName="h3"
									value={ item.heading }
								/>
								<RichText
									className="skvn-feature-showcase__copy"
									onChange={ ( copy ) =>
										setItem( index, { copy } )
									}
									tagName="p"
									value={ item.copy }
								/>
								<MediaUploadCheck>
									<MediaUpload
										allowedTypes={ [ 'image' ] }
										onSelect={ ( image: SelectedImage ) =>
											setItem( index, {
												imageAlt: image.alt || '',
												imageId: image.id,
												imageUrl: image.url,
											} )
										}
										render={ ( { open } ) => (
											<Button
												onClick={ open }
												variant="secondary"
											>
												{ item.imageUrl
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
										value={ item.imageId }
									/>
								</MediaUploadCheck>
								{ item.imageUrl && (
									<>
										<TextControl
											label={ __(
												'Image alt text',
												'skvn-marine-blocks'
											) }
											onChange={ ( imageAlt ) =>
												setItem( index, { imageAlt } )
											}
											value={ item.imageAlt }
										/>
										<Button
											isDestructive
											onClick={ () =>
												setItem( index, {
													imageAlt: '',
													imageId: 0,
													imageUrl: '',
												} )
											}
											variant="link"
										>
											{ __(
											'Remove image',
											'skvn-marine-blocks'
										) }
										</Button>
									</>
								) }
								<div className="skvn-feature-showcase__item-actions">
									<Button
										disabled={ index === 0 }
										onClick={ () => moveItem( index, -1 ) }
										variant="secondary"
									>
										{ __( 'Move earlier', 'skvn-marine-blocks' ) }
									</Button>
									<Button
										disabled={ index === items.length - 1 }
										onClick={ () => moveItem( index, 1 ) }
										variant="secondary"
									>
										{ __( 'Move later', 'skvn-marine-blocks' ) }
									</Button>
									<Button
										disabled={ items.length === 1 }
										isDestructive
										onClick={ () =>
											setAttributes( {
												items: items.filter(
													( _, itemIndex ) => itemIndex !== index
												),
											} )
										}
										variant="link"
									>
										{ __( 'Remove panel', 'skvn-marine-blocks' ) }
									</Button>
								</div>
							</div>
						</article>
					) ) }
			</div>
			<Button
				className="skvn-feature-showcase__add"
				onClick={ () =>
					setAttributes( {
						items: [ ...items, createItem( items.length ) ],
					} )
				}
				variant="primary"
			>
				{ __( 'Add panel', 'skvn-marine-blocks' ) }
			</Button>
		</section>
	);
}
