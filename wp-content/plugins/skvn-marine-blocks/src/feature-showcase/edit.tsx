import {
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type FeatureItem = {
	kicker: string;
	heading: string;
	copy: string;
	imageId: number;
	imageUrl: string;
	imageAlt: string;
};

type FeatureShowcaseAttributes = {
	eyebrow: string;
	headingBefore: string;
	headingAccent: string;
	headingAfter: string;
	intro: string;
	metaLabel: string;
	metaText: string;
	items: FeatureItem[];
};

type FeatureShowcaseEditProps = {
	attributes: FeatureShowcaseAttributes;
	setAttributes: ( attributes: Partial< FeatureShowcaseAttributes > ) => void;
};

type SelectedImage = {
	id: number;
	url: string;
	alt?: string;
};

const EMPTY_ITEMS: FeatureItem[] = [];

function getItems( items: FeatureItem[] = EMPTY_ITEMS ) {
	return items.slice( 0, 4 );
}

export function Edit( { attributes, setAttributes }: FeatureShowcaseEditProps ) {
	const blockProps = useBlockProps( { className: 'skvn-feature-showcase' } );
	const items = getItems( attributes.items );
	const setItem = ( index: number, itemPatch: Partial< FeatureItem > ) => {
		setAttributes( {
			items: items.map( ( item, itemIndex ) =>
				itemIndex === index ? { ...item, ...itemPatch } : item
			),
		} );
	};

	return (
		<section { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Content', 'skvn-marine-blocks' ) }>
					<TextControl
						label={ __( 'Eyebrow', 'skvn-marine-blocks' ) }
						onChange={ ( eyebrow ) => setAttributes( { eyebrow } ) }
						value={ attributes.eyebrow }
					/>
					<TextControl
						label={ __( 'Meta label', 'skvn-marine-blocks' ) }
						onChange={ ( metaLabel ) =>
							setAttributes( { metaLabel } )
						}
						value={ attributes.metaLabel }
					/>
					<TextControl
						label={ __( 'Meta text', 'skvn-marine-blocks' ) }
						onChange={ ( metaText ) =>
							setAttributes( { metaText } )
						}
						value={ attributes.metaText }
					/>
				</PanelBody>
			</InspectorControls>
			<div className="skvn-feature-showcase__grid">
				<div className="skvn-feature-showcase__intro">
					<RichText
						allowedFormats={ [] }
						className="skvn-feature-showcase__eyebrow"
						onChange={ ( eyebrow ) => setAttributes( { eyebrow } ) }
						tagName="p"
						value={ attributes.eyebrow }
					/>
					<h2 className="skvn-feature-showcase__heading">
						<RichText
							allowedFormats={ [] }
							onChange={ ( headingBefore ) =>
								setAttributes( { headingBefore } )
							}
							tagName="span"
							value={ attributes.headingBefore }
						/>
						<RichText
							allowedFormats={ [] }
							className="skvn-feature-showcase__heading-accent"
							onChange={ ( headingAccent ) =>
								setAttributes( { headingAccent } )
							}
							tagName="strong"
							value={ attributes.headingAccent }
						/>
						<RichText
							allowedFormats={ [] }
							onChange={ ( headingAfter ) =>
								setAttributes( { headingAfter } )
							}
							tagName="span"
							value={ attributes.headingAfter }
						/>
					</h2>
					<RichText
						className="skvn-feature-showcase__copy"
						onChange={ ( intro ) => setAttributes( { intro } ) }
						tagName="p"
						value={ attributes.intro }
					/>
					<div className="skvn-feature-showcase__meta">
						<RichText
							allowedFormats={ [] }
							className="skvn-feature-showcase__meta-label"
							onChange={ ( metaLabel ) =>
								setAttributes( { metaLabel } )
							}
							tagName="p"
							value={ attributes.metaLabel }
						/>
						<RichText
							allowedFormats={ [] }
							className="skvn-feature-showcase__meta-text"
							onChange={ ( metaText ) =>
								setAttributes( { metaText } )
							}
							tagName="p"
							value={ attributes.metaText }
						/>
					</div>
				</div>
				<div className="skvn-feature-showcase__panels">
					{ items.map( ( item, index ) => (
						<article
							className="skvn-feature-showcase__panel"
							key={ index }
							tabIndex={ 0 }
						>
							{ item.imageUrl && (
								<img
									alt={ item.imageAlt }
									className="skvn-feature-showcase__image"
									src={ item.imageUrl }
								/>
							) }
							<div className="skvn-feature-showcase__panel-shade" />
							<RichText
								allowedFormats={ [] }
								className="skvn-feature-showcase__panel-kicker"
								onChange={ ( kicker ) =>
									setItem( index, { kicker } )
								}
								tagName="p"
								value={ item.kicker }
							/>
							<div className="skvn-feature-showcase__panel-body">
								<RichText
									allowedFormats={ [] }
									className="skvn-feature-showcase__panel-title"
									onChange={ ( heading ) =>
										setItem( index, { heading } )
									}
									tagName="h3"
									value={ item.heading }
								/>
								<RichText
									className="skvn-feature-showcase__panel-copy"
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
							</div>
						</article>
					) ) }
				</div>
			</div>
		</section>
	);
}
