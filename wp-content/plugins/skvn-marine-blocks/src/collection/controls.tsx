import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	CheckboxControl,
	FormTokenField,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import {
	BADGE_BEHAVIOR_OPTIONS,
	CARD_STYLE_OPTIONS,
	IMAGE_RATIO_OPTIONS,
	LAYOUT_OPTIONS,
	ORDER_MODE_OPTIONS,
	POST_ACTION_OPTIONS,
	PRODUCT_ACTION_OPTIONS,
	RESPONSIVE_PRESET_OPTIONS,
	RELATION_OPTIONS,
} from './constants';
import type {
	BadgeBehavior,
	CardStyle,
	CollectionAttributes,
	CollectionLayout,
	CollectionOrderMode,
	CollectionRelation,
	ImageRatio,
	PostActionMode,
	ProductActionMode,
	ResponsivePreset,
} from './types';

type CollectionEditProps = {
	attributes: CollectionAttributes;
	contentType: 'post' | 'product';
	setAttributes: ( attributes: Partial< CollectionAttributes > ) => void;
};

export function CollectionEdit( {
	attributes,
	contentType,
	setAttributes,
}: CollectionEditProps ) {
	const categoryTaxonomy =
		contentType === 'product' ? 'product_cat' : 'category';
	const tagTaxonomy = contentType === 'product' ? 'product_tag' : 'post_tag';
	const categoryTerms = useTaxonomyTerms( categoryTaxonomy );
	const tagTerms = useTaxonomyTerms( tagTaxonomy );
	const blockProps = useBlockProps( {
		className: [
			'skvn-collection',
			`skvn-collection--${ contentType }`,
			`skvn-collection--${ attributes.layout || 'grid' }`,
			`skvn-collection--preset-${ attributes.responsivePreset || '3-2-1' }`,
			`skvn-collection--ratio-${ sanitizeClassPart(
				attributes.imageRatio || ( contentType === 'product' ? '1:1' : '16:9' )
			) }`,
			attributes.equalHeight !== false
				? 'skvn-collection--equal-height'
				: '',
			'skvn-collection--editor-preview',
		]
			.filter( Boolean )
			.join( ' ' ),
	} );

	return (
		<section { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Content', 'skvn-marine-blocks' ) }>
					<TextControl
						label={ __( 'Eyebrow label', 'skvn-marine-blocks' ) }
						onChange={ ( eyebrow ) => setAttributes( { eyebrow } ) }
						value={ attributes.eyebrow || '' }
					/>
					<ToggleControl
						checked={ attributes.showHeading !== false }
						label={ __( 'Show heading', 'skvn-marine-blocks' ) }
						onChange={ ( showHeading ) => setAttributes( { showHeading } ) }
					/>
					<TextControl
						label={ __( 'Archive URL', 'skvn-marine-blocks' ) }
						onChange={ ( archiveUrl ) => setAttributes( { archiveUrl } ) }
						value={ attributes.archiveUrl || '' }
					/>
					{ ( attributes.archiveUrl || '' ) !== '' && (
						<TextControl
							label={ __( 'Archive link label', 'skvn-marine-blocks' ) }
							onChange={ ( archiveLabel ) =>
								setAttributes( { archiveLabel } )
							}
							value={ attributes.archiveLabel || '' }
						/>
					) }
					{ contentType === 'product' && (
						<TextControl
							label={ __( 'Catalog PDF URL', 'skvn-marine-blocks' ) }
							onChange={ ( catalogPdfUrl ) =>
								setAttributes( { catalogPdfUrl } )
							}
							value={ attributes.catalogPdfUrl || '' }
						/>
					) }
					<TextControl
						label={ __( 'Accessible label', 'skvn-marine-blocks' ) }
						onChange={ ( accessibleLabel ) =>
							setAttributes( { accessibleLabel } )
						}
						value={ attributes.accessibleLabel || '' }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Query', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Order', 'skvn-marine-blocks' ) }
						onChange={ ( orderMode ) =>
							setAttributes( {
								orderMode: orderMode as CollectionOrderMode,
							} )
						}
						options={ ORDER_MODE_OPTIONS }
						value={ attributes.orderMode || 'newest' }
					/>
					<FormTokenField
						label={
							contentType === 'product'
								? __( 'Product categories', 'skvn-marine-blocks' )
								: __( 'Post categories', 'skvn-marine-blocks' )
						}
						onChange={ ( categories ) =>
							setAttributes( {
								categories: normalizeSelectedTerms(
									categories,
									categoryTerms
								),
							} )
						}
						suggestions={ categoryTerms.map( ( term ) => term.name ) }
						value={ selectedTermNames(
							attributes.categories || [],
							categoryTerms
						) }
					/>
					<FormTokenField
						label={
							contentType === 'product'
								? __( 'Product tags', 'skvn-marine-blocks' )
								: __( 'Post tags', 'skvn-marine-blocks' )
						}
						onChange={ ( tags ) =>
							setAttributes( {
								tags: normalizeSelectedTerms( tags, tagTerms ),
							} )
						}
						suggestions={ tagTerms.map( ( term ) => term.name ) }
						value={ selectedTermNames( attributes.tags || [], tagTerms ) }
					/>
					<SelectControl
						label={ __( 'Taxonomy relation', 'skvn-marine-blocks' ) }
						onChange={ ( relation ) =>
							setAttributes( {
								relation: relation as CollectionRelation,
							} )
						}
						options={ RELATION_OPTIONS }
						value={ attributes.relation || 'OR' }
					/>
					<RangeControl
						label={ __( 'Items to show', 'skvn-marine-blocks' ) }
						max={ attributes.layout === 'carousel' ? 10 : 9 }
						min={ 1 }
						onChange={ ( itemsToShow ) =>
							setAttributes( { itemsToShow: itemsToShow || 3 } )
						}
						value={ attributes.itemsToShow || 3 }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Layout', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Layout', 'skvn-marine-blocks' ) }
						onChange={ ( layout ) =>
							setAttributes( {
								layout: layout as CollectionLayout,
							} )
						}
						options={ LAYOUT_OPTIONS }
						value={ attributes.layout || 'grid' }
					/>
					<SelectControl
						label={ __( 'Responsive preset', 'skvn-marine-blocks' ) }
						onChange={ ( responsivePreset ) =>
							setAttributes( {
								responsivePreset:
									responsivePreset as ResponsivePreset,
							} )
						}
						options={ RESPONSIVE_PRESET_OPTIONS }
						value={ attributes.responsivePreset || '3-2-1' }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Card', 'skvn-marine-blocks' ) }>
					<ToggleControl
						checked={ attributes.showImage !== false }
						label={ __( 'Show image', 'skvn-marine-blocks' ) }
						onChange={ ( showImage ) => setAttributes( { showImage } ) }
					/>
					<SelectControl
						label={ __( 'Image ratio', 'skvn-marine-blocks' ) }
						onChange={ ( imageRatio ) =>
							setAttributes( { imageRatio: imageRatio as ImageRatio } )
						}
						options={ IMAGE_RATIO_OPTIONS }
						value={ attributes.imageRatio || ( contentType === 'product' ? '1:1' : '16:9' ) }
					/>
					<SelectControl
						label={ __( 'Card style', 'skvn-marine-blocks' ) }
						onChange={ ( cardStyle ) =>
							setAttributes( { cardStyle: cardStyle as CardStyle } )
						}
						options={ CARD_STYLE_OPTIONS }
						value={ attributes.cardStyle || 'default' }
					/>
					<ToggleControl
						checked={ attributes.equalHeight !== false }
						label={ __( 'Equal height cards', 'skvn-marine-blocks' ) }
						onChange={ ( equalHeight ) => setAttributes( { equalHeight } ) }
					/>
					<SelectControl
						label={ __( 'Badge behavior', 'skvn-marine-blocks' ) }
						onChange={ ( badgeBehavior ) =>
							setAttributes( {
								badgeBehavior: badgeBehavior as BadgeBehavior,
							} )
						}
						options={ BADGE_BEHAVIOR_OPTIONS }
						value={ attributes.badgeBehavior || 'display' }
					/>
					{ contentType === 'post' ? (
						<PostCardControls
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					) : (
						<ProductCardControls
							attributes={ attributes }
							setAttributes={ setAttributes }
						/>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Actions', 'skvn-marine-blocks' ) }>
					{ contentType === 'post' ? (
						<>
							<SelectControl
								label={ __( 'Post action', 'skvn-marine-blocks' ) }
								onChange={ ( postActionMode ) =>
									setAttributes( {
										postActionMode:
											postActionMode as PostActionMode,
									} )
								}
								options={ POST_ACTION_OPTIONS }
								value={ attributes.postActionMode || 'read' }
							/>
							{ attributes.postActionMode === 'custom' && (
								<TextControl
									label={ __( 'Custom action URL', 'skvn-marine-blocks' ) }
									onChange={ ( customActionUrl ) =>
										setAttributes( { customActionUrl } )
									}
									value={ attributes.customActionUrl || '' }
								/>
							) }
						</>
					) : (
						<>
							<SelectControl
								label={ __( 'Product action', 'skvn-marine-blocks' ) }
								onChange={ ( productActionMode ) =>
									setAttributes( {
										productActionMode:
											productActionMode as ProductActionMode,
									} )
								}
								options={ PRODUCT_ACTION_OPTIONS }
								value={ attributes.productActionMode || 'quote' }
							/>
							{ attributes.productActionMode === 'custom' && (
								<TextControl
									label={ __( 'Custom action URL', 'skvn-marine-blocks' ) }
									onChange={ ( customActionUrl ) =>
										setAttributes( { customActionUrl } )
									}
									value={ attributes.customActionUrl || '' }
								/>
							) }
							<CheckboxControl
								checked={ attributes.appendQuoteContext !== false }
								label={ __( 'Append quote context', 'skvn-marine-blocks' ) }
								onChange={ ( appendQuoteContext ) =>
									setAttributes( { appendQuoteContext } )
								}
							/>
						</>
					) }
				</PanelBody>
				{ attributes.layout === 'carousel' && (
					<PanelBody title={ __( 'Carousel', 'skvn-marine-blocks' ) }>
						<ToggleControl
							checked={ attributes.showArrows !== false }
							label={ __( 'Show arrows', 'skvn-marine-blocks' ) }
							onChange={ ( showArrows ) =>
								setAttributes( { showArrows } )
							}
						/>
						<ToggleControl
							checked={ attributes.showPagination !== false }
							label={ __( 'Show pagination', 'skvn-marine-blocks' ) }
							onChange={ ( showPagination ) =>
								setAttributes( { showPagination } )
							}
						/>
						<ToggleControl
							checked={ attributes.autoplay === true }
							label={ __( 'Autoplay', 'skvn-marine-blocks' ) }
							onChange={ ( autoplay ) =>
								setAttributes( { autoplay } )
							}
						/>
						{ attributes.autoplay && (
							<RangeControl
								label={ __(
									'Autoplay delay (seconds)',
									'skvn-marine-blocks'
								) }
								max={ 10 }
								min={ 3 }
								onChange={ ( val ) =>
									setAttributes( {
										autoplayDelay: ( val || 5 ) * 1000,
									} )
								}
								value={ Math.round(
									( attributes.autoplayDelay || 5000 ) / 1000
								) }
							/>
						) }
					</PanelBody>
				) }
				<PanelBody title={ __( 'Advanced', 'skvn-marine-blocks' ) } />
			</InspectorControls>
			{ ( attributes.eyebrow || '' ) !== '' && (
				<p className="skvn-collection__eyebrow">{ attributes.eyebrow }</p>
			) }
			{ attributes.showHeading !== false && (
				<RichText
					className="skvn-collection__heading"
					onChange={ ( heading ) => setAttributes( { heading } ) }
					placeholder={ __( 'Collection heading...', 'skvn-marine-blocks' ) }
					tagName="h2"
					value={ attributes.heading }
				/>
			) }
			<RichText
				className="skvn-collection__intro"
				onChange={ ( intro ) => setAttributes( { intro } ) }
				placeholder={ __( 'Optional intro...', 'skvn-marine-blocks' ) }
				tagName="p"
				value={ attributes.intro }
			/>
			<div className="skvn-collection__notice">
				{ contentType === 'post'
					? __(
							'Live post grid renders on the frontend.',
							'skvn-marine-blocks'
					  )
					: __(
							'Product collection is guarded for WooCommerce availability.',
							'skvn-marine-blocks'
					  ) }
			</div>
		</section>
	);
}

type TermRecord = {
	name: string;
	slug: string;
};

function useTaxonomyTerms( taxonomy: string ): TermRecord[] {
	return useSelect(
		( select ) => {
			const records = select( 'core' ).getEntityRecords(
				'taxonomy',
				taxonomy,
				{
					hide_empty: false,
					per_page: 100,
				}
			) as TermRecord[] | null;

			return records || [];
		},
		[ taxonomy ]
	);
}

function selectedTermNames( slugs: string[], terms: TermRecord[] ) {
	return slugs.map(
		( slug ) => terms.find( ( term ) => term.slug === slug )?.name || slug
	);
}

function normalizeSelectedTerms(
	selectedTerms: Array< string | { value?: string } >,
	terms: TermRecord[]
) {
	return selectedTerms
		.map( ( item ) => ( typeof item === 'string' ? item : item.value || '' ) )
		.map( ( value ) => {
			const match = terms.find(
				( term ) => term.name === value || term.slug === value
			);
			return match?.slug || value;
		} )
		.map( ( value ) => value.trim() )
		.filter( Boolean );
}

function sanitizeClassPart( value: string ) {
	return value.replace( /[^a-z0-9-]/gi, '-' ).toLowerCase();
}

function PostCardControls( {
	attributes,
	setAttributes,
}: Pick< CollectionEditProps, 'attributes' | 'setAttributes' > ) {
	return (
		<>
			<ToggleControl
				checked={ attributes.showDate !== false }
				label={ __( 'Show date', 'skvn-marine-blocks' ) }
				onChange={ ( showDate ) => setAttributes( { showDate } ) }
			/>
			<ToggleControl
				checked={ attributes.showAuthor !== false }
				label={ __( 'Show author', 'skvn-marine-blocks' ) }
				onChange={ ( showAuthor ) => setAttributes( { showAuthor } ) }
			/>
			<ToggleControl
				checked={ attributes.showPostCategories !== false }
				label={ __( 'Show categories', 'skvn-marine-blocks' ) }
				onChange={ ( showPostCategories ) =>
					setAttributes( { showPostCategories } )
				}
			/>
			<ToggleControl
				checked={ attributes.showPostTags === true }
				label={ __( 'Show tags', 'skvn-marine-blocks' ) }
				onChange={ ( showPostTags ) => setAttributes( { showPostTags } ) }
			/>
			<ToggleControl
				checked={ attributes.showExcerpt !== false }
				label={ __( 'Show excerpt', 'skvn-marine-blocks' ) }
				onChange={ ( showExcerpt ) => setAttributes( { showExcerpt } ) }
			/>
		</>
	);
}

function ProductCardControls( {
	attributes,
	setAttributes,
}: Pick< CollectionEditProps, 'attributes' | 'setAttributes' > ) {
	return (
		<>
			<ToggleControl
				checked={ attributes.showPrice !== false }
				label={ __( 'Show price', 'skvn-marine-blocks' ) }
				onChange={ ( showPrice ) => setAttributes( { showPrice } ) }
			/>
			<ToggleControl
				checked={ attributes.showSku === true }
				label={ __( 'Show SKU', 'skvn-marine-blocks' ) }
				onChange={ ( showSku ) => setAttributes( { showSku } ) }
			/>
			<ToggleControl
				checked={ attributes.showStock === true }
				label={ __( 'Show stock', 'skvn-marine-blocks' ) }
				onChange={ ( showStock ) => setAttributes( { showStock } ) }
			/>
			<ToggleControl
				checked={ attributes.showProductCategories !== false }
				label={ __( 'Show categories', 'skvn-marine-blocks' ) }
				onChange={ ( showProductCategories ) =>
					setAttributes( { showProductCategories } )
				}
			/>
			<ToggleControl
				checked={ attributes.showProductTags === true }
				label={ __( 'Show tags', 'skvn-marine-blocks' ) }
				onChange={ ( showProductTags ) =>
					setAttributes( { showProductTags } )
				}
			/>
		</>
	);
}
