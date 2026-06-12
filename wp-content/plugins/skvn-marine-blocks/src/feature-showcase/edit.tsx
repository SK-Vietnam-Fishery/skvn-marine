import {
	InspectorControls,
	__experimentalLinkControl as LinkControl,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	Popover,
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { useState } from '@wordpress/element';
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
		linkUrl: '',
		linkText: '',
		linkTarget: '_self',
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
		attributes.labelRotation === '180'
			? 'skvn-feature-showcase--label-rotate-180'
			: '',
	]
		.filter( Boolean )
		.join( ' ' );
}

export function Edit( { attributes, setAttributes }: FeatureShowcaseEditProps ) {
	const [ linkPanelIndex, setLinkPanelIndex ] = useState< number | null >(
		null
	);
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
				<PanelBody
					initialOpen={ false }
					title={ __( 'Interaction', 'skvn-marine-blocks' ) }
				>
					<SelectControl
						label={ __( 'Interaction mode', 'skvn-marine-blocks' ) }
						onChange={ ( interactionMode ) =>
							setAttributes( {
								interactionMode:
									interactionMode as FeatureShowcaseAttributes[ 'interactionMode' ],
							} )
						}
						options={ [
							{
								label: __( 'Hover', 'skvn-marine-blocks' ),
								value: 'hover',
							},
							{
								label: __( 'Autoplay', 'skvn-marine-blocks' ),
								value: 'autoplay',
							},
						] }
						value={ attributes.interactionMode }
					/>
					{ attributes.interactionMode === 'autoplay' && (
						<RangeControl
							help={ __(
								'Autoplay runs only on the frontend and pauses for pointer, keyboard focus, hidden tabs, and reduced motion.',
								'skvn-marine-blocks'
							) }
							label={ __(
								'Panel duration',
								'skvn-marine-blocks'
							) }
							marks={ [
								{ label: '3s', value: 3000 },
								{ label: '5s', value: 5000 },
								{ label: '7s', value: 7000 },
								{ label: '9s', value: 9000 },
							] }
							max={ 9000 }
							min={ 3000 }
							onChange={ ( autoplayDelay ) =>
								setAttributes( {
									autoplayDelay: ( autoplayDelay ||
										5000 ) as FeatureShowcaseAttributes[ 'autoplayDelay' ],
								} )
							}
							step={ 2000 }
							value={ attributes.autoplayDelay }
						/>
					) }
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
					<SelectControl
						disabled={ attributes.desktopLayout === 'vertical' }
						help={ __(
							attributes.desktopLayout === 'vertical'
								? 'Available only for horizontal desktop panels.'
								: 'Applies to vertical labels on horizontal desktop panels.',
							'skvn-marine-blocks'
						) }
						label={ __( 'Label direction', 'skvn-marine-blocks' ) }
						onChange={ ( labelRotation ) =>
							setAttributes( {
								labelRotation:
									labelRotation as FeatureShowcaseAttributes[ 'labelRotation' ],
							} )
						}
						options={ [
							{
								label: __( 'As shown', 'skvn-marine-blocks' ),
								value: 'default',
							},
							{
								label: __( 'Rotate 180°', 'skvn-marine-blocks' ),
								value: '180',
							},
						] }
						value={ attributes.labelRotation }
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
								<TextControl
									label={ __(
										'Link text',
										'skvn-marine-blocks'
									) }
									onChange={ ( linkText ) =>
										setItem( index, { linkText } )
									}
									value={ item.linkText || '' }
								/>
								<div className="skvn-feature-showcase__link-control">
									<Button
										onClick={ () =>
											setLinkPanelIndex(
												linkPanelIndex === index
													? null
													: index
											)
										}
										variant="secondary"
									>
										{ item.linkUrl
											? __(
													'Edit destination',
													'skvn-marine-blocks'
											  )
											: __(
													'Choose destination',
													'skvn-marine-blocks'
											  ) }
									</Button>
									{ linkPanelIndex === index && (
										<Popover
											onClose={ () =>
												setLinkPanelIndex( null )
											}
											placement="bottom-start"
										>
											<LinkControl
												onChange={ ( value: {
													opensInNewTab?: boolean;
													url?: string;
												} ) =>
													setItem( index, {
														linkTarget:
															value.opensInNewTab
																? '_blank'
																: '_self',
														linkText:
															item.linkText ||
															( value.url
																? __(
																		'Learn more',
																		'skvn-marine-blocks'
																  )
																: '' ),
														linkUrl:
															value.url || '',
													} )
												}
												settings={ [
													{
														id: 'opensInNewTab',
														title: __(
															'Open in new tab',
															'skvn-marine-blocks'
														),
													},
												] }
												value={ {
													opensInNewTab:
														item.linkTarget ===
														'_blank',
													url: item.linkUrl || '',
												} }
											/>
										</Popover>
									) }
									{ item.linkUrl && (
										<Button
											isDestructive
											onClick={ () =>
												setItem( index, {
													linkTarget: '_self',
													linkUrl: '',
												} )
											}
											variant="link"
										>
											{ __(
												'Remove destination',
												'skvn-marine-blocks'
											) }
										</Button>
									) }
								</div>
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
