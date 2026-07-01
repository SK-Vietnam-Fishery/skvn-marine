import {
	InspectorControls,
	RichText,
	useBlockProps,
	useSettings,
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
	CHIP_STYLE_OPTIONS,
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
	ChipStyle,
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
					<ToggleControl
						checked={ attributes.showCatalogCta === true }
						label={ __( 'Show catalog CTA', 'skvn-marine-blocks' ) }
						onChange={ ( showCatalogCta ) =>
							setAttributes( { showCatalogCta } )
						}
					/>
					{ attributes.showCatalogCta && (
						<>
							<TextControl
								label={ __( 'Catalog CTA URL', 'skvn-marine-blocks' ) }
								onChange={ ( catalogCtaUrl ) =>
									setAttributes( { catalogCtaUrl } )
								}
								value={ attributes.catalogCtaUrl || '' }
							/>
							<TextControl
								label={ __( 'Catalog CTA label', 'skvn-marine-blocks' ) }
								onChange={ ( catalogCtaLabel ) =>
									setAttributes( { catalogCtaLabel } )
								}
								value={ attributes.catalogCtaLabel || '' }
							/>
						</>
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
			<div className="skvn-collection__editor-body">
				<PlaceholderCard
					attributes={ attributes }
					contentType={ contentType }
				/>
				<EditorInfoPanel
					attributes={ attributes }
					contentType={ contentType }
					categoryTerms={ categoryTerms }
					tagTerms={ tagTerms }
				/>
			</div>
		</section>
	);
}

type TermRecord = {
	id: number;
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

// ── Editor preview components ────────────────────────────────────────────────

type PostRecord = {
	id: number;
	title: { rendered: string };
};

function useQueryPreview(
	contentType: 'post' | 'product',
	attributes: CollectionAttributes,
	categoryTerms: TermRecord[],
	tagTerms: TermRecord[]
): PostRecord[] | null {
	const catIds = ( attributes.categories || [] )
		.map( ( slug ) => categoryTerms.find( ( t ) => t.slug === slug )?.id )
		.filter( ( id ): id is number => id !== undefined );
	const tagIds = ( attributes.tags || [] )
		.map( ( slug ) => tagTerms.find( ( t ) => t.slug === slug )?.id )
		.filter( ( id ): id is number => id !== undefined );

	const orderMap: Record< string, { orderby: string; order: string } > = {
		newest:             { orderby: 'date',       order: 'desc' },
		featured:           { orderby: 'menu_order', order: 'asc' },
		manual:             { orderby: 'menu_order', order: 'asc' },
		'shuffle-balanced': { orderby: 'rand',       order: 'desc' },
	};
	const { orderby, order } =
		orderMap[ attributes.orderMode || 'newest' ] ||
		{ orderby: 'date', order: 'desc' };

	const queryParams: Record< string, unknown > = {
		per_page: Math.min( attributes.itemsToShow || 3, 5 ),
		status:   'publish',
		orderby,
		order,
	};

	if ( contentType === 'product' ) {
		if ( catIds.length > 0 ) queryParams.product_cat = catIds.join( ',' );
		if ( tagIds.length > 0 ) queryParams.product_tag = tagIds.join( ',' );
	} else {
		if ( catIds.length > 0 ) queryParams.categories = catIds;
		if ( tagIds.length > 0 ) queryParams.tags = tagIds;
	}

	const queryKey = contentType + JSON.stringify( queryParams );

	return useSelect(
		( select ) =>
			( select( 'core' ).getEntityRecords(
				'postType',
				contentType === 'product' ? 'product' : 'post',
				queryParams
			) as PostRecord[] | null ),
		// eslint-disable-next-line react-hooks/exhaustive-deps
		[ queryKey ]
	);
}

const DUMMY_SPECS = [ 'Product attribute', 'Loin', 'Vacuum Pack' ];

function PlaceholderCard( {
	attributes,
	contentType,
}: {
	attributes: CollectionAttributes;
	contentType: 'post' | 'product';
} ) {
	const chipClass = attributes.chipStyle
		? `skvn-collection-card--chip-${ attributes.chipStyle }`
		: '';
	const chipColorClass = attributes.chipColorScheme
		? `skvn-chips--${ attributes.chipColorScheme }`
		: '';
	const cardClass = [ 'skvn-collection-card', chipClass, chipColorClass ]
		.filter( Boolean )
		.join( ' ' );

	if ( contentType === 'post' ) {
		const showCategories = attributes.showPostCategories !== false;
		const showExcerpt    = attributes.showExcerpt !== false;
		const showDate       = attributes.showDate !== false;
		const readMoreText   = ( attributes.readMoreLabel || '' ).trim() || __( 'Read more →', 'skvn-marine-blocks' );

		return (
			<div className={ cardClass }>
				<div className="skvn-collection-card__media">
					{ showCategories && (
						<div className="skvn-collection-card__badges">
							<span className="skvn-collection-card__badge">Category</span>
						</div>
					) }
					<div className="skvn-collection-card__image skvn-collection-card__image--placeholder" />
				</div>
				<div className="skvn-collection-card__body">
					<h3 className="skvn-collection-card__title">
						{ __( 'Post title', 'skvn-marine-blocks' ) }
					</h3>
					{ showDate && (
						<span className="skvn-collection-card__date">Jan 1, 2025</span>
					) }
					{ showExcerpt && (
						<p className="skvn-collection-card__excerpt">
							{ __( 'Post excerpt preview…', 'skvn-marine-blocks' ) }
						</p>
					) }
					<span className="skvn-collection-card__read-more">{ readMoreText }</span>
				</div>
			</div>
		);
	}

	// product
	const showTags  = attributes.showProductTags !== false;
	const showSpecs = attributes.showSpecChips !== false;
	const actionMode = attributes.productActionMode || 'quote';
	const ctaText =
		actionMode === 'quote' ? __( 'Request quote', 'skvn-marine-blocks' ) :
		actionMode === 'none'  ? null :
		__( 'View details', 'skvn-marine-blocks' );

	return (
		<div className={ cardClass }>
			<div className="skvn-collection-card__media">
				{ showTags && (
					<div className="skvn-collection-card__badges">
						<span className="skvn-collection-card__badge">Product tag</span>
					</div>
				) }
				<div className="skvn-collection-card__image skvn-collection-card__image--placeholder" />
			</div>
			<div className="skvn-collection-card__body">
				<h3 className="skvn-collection-card__title">
					{ __( 'Product name', 'skvn-marine-blocks' ) }
				</h3>
				{ showSpecs && (
					<div className="skvn-collection-card__specs">
						{ DUMMY_SPECS.map( ( s ) => (
							<span key={ s } className="skvn-collection-card__spec-tag">
								{ s }
							</span>
						) ) }
					</div>
				) }
				<div className="skvn-collection-card__catalog skvn-collection-card__catalog--placeholder">
					<span className="skvn-collection-card__catalog-label">
						Catalog data — woo-catalog 1.5.0
					</span>
				</div>
				{ ctaText && (
					<span className="skvn-collection-card__cta">{ ctaText }</span>
				) }
			</div>
		</div>
	);
}

function EditorInfoPanel( {
	attributes,
	contentType,
	categoryTerms,
	tagTerms,
}: {
	attributes: CollectionAttributes;
	contentType: 'post' | 'product';
	categoryTerms: TermRecord[];
	tagTerms: TermRecord[];
} ) {
	const items = useQueryPreview( contentType, attributes, categoryTerms, tagTerms );
	const isProduct = contentType === 'product';

	const fields = [
		{ label: 'Image',        active: attributes.showImage !== false },
		...( isProduct
			? [
				{ label: 'Spec chips', active: attributes.showSpecChips !== false },
				{ label: 'Tags badge', active: attributes.showProductTags !== false },
			  ]
			: [
				{ label: 'Excerpt',    active: attributes.showExcerpt !== false },
				{ label: 'Date',       active: attributes.showDate !== false },
				{ label: 'Author',     active: attributes.showAuthor === true },
			  ]
		),
		isProduct
			? { label: `CTA: ${ actionModeLabel( attributes.productActionMode || 'quote' ) }`, active: ( attributes.productActionMode || 'quote' ) !== 'none' }
			: { label: `CTA: ${ actionModeLabel( attributes.postActionMode || 'read' ) }`,    active: true },
		{ label: 'Equal height', active: attributes.equalHeight !== false },
	];

	const catalogFields = [ 'Certifications', 'MOQ / Lead time', 'PDF link' ];

	return (
		<div className="skvn-collection__editor-info">
			<p className="skvn-collection__editor-info-heading">
				{ __( 'Card data:', 'skvn-marine-blocks' ) }
			</p>
			<ul className="skvn-collection__editor-field-list">
				{ fields.map( ( f ) => (
					<li
						key={ f.label }
						className={ `skvn-collection__editor-field${ f.active ? ' is-active' : '' }` }
					>
						<span className="skvn-collection__editor-field-icon">
							{ f.active ? '✓' : '✗' }
						</span>
						{ f.label }
					</li>
				) ) }
			</ul>
			{ isProduct && (
				<>
					<p className="skvn-collection__editor-info-subheading">
						— woo-catalog 1.5.0 —
					</p>
					<ul className="skvn-collection__editor-field-list skvn-collection__editor-field-list--deferred">
						{ catalogFields.map( ( f ) => (
							<li key={ f } className="skvn-collection__editor-field">
								<span className="skvn-collection__editor-field-icon">·</span>
								{ f }
							</li>
						) ) }
					</ul>
				</>
			) }
			<p className="skvn-collection__editor-info-heading skvn-collection__editor-info-heading--products">
				{ isProduct
					? __( 'Products in query:', 'skvn-marine-blocks' )
					: __( 'Posts in query:', 'skvn-marine-blocks' ) }
			</p>
			{ items === null ? (
				<p className="skvn-collection__editor-product-status">
					{ __( 'Loading...', 'skvn-marine-blocks' ) }
				</p>
			) : items.length === 0 ? (
				<p className="skvn-collection__editor-product-status">
					{ isProduct
						? __( 'No products match.', 'skvn-marine-blocks' )
						: __( 'No posts match.', 'skvn-marine-blocks' ) }
				</p>
			) : (
				<>
					<ol className="skvn-collection__editor-product-list">
						{ items.map( ( p ) => (
							<li key={ p.id }>{ p.title.rendered }</li>
						) ) }
					</ol>
					<p className="skvn-collection__editor-product-note">
						{ __( '* Approximate list', 'skvn-marine-blocks' ) }
					</p>
				</>
			) }
		</div>
	);
}

function actionModeLabel( mode: string ): string {
	const map: Record< string, string > = {
		quote:  'Quote',
		view:   'View',
		both:   'Quote + View',
		custom: 'Custom',
		none:   'Hidden',
		read:   'Read more',
	};
	return map[ mode ] || mode;
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
				checked={ attributes.showAuthor === true }
				label={ __( 'Show author', 'skvn-marine-blocks' ) }
				onChange={ ( showAuthor ) => setAttributes( { showAuthor } ) }
			/>
			<ToggleControl
				checked={ attributes.showPostCategories !== false }
				label={ __( 'Show categories (badge overlay)', 'skvn-marine-blocks' ) }
				onChange={ ( showPostCategories ) =>
					setAttributes( { showPostCategories } )
				}
			/>
			<ToggleControl
				checked={ attributes.showExcerpt !== false }
				label={ __( 'Show excerpt', 'skvn-marine-blocks' ) }
				onChange={ ( showExcerpt ) => setAttributes( { showExcerpt } ) }
			/>
			<TextControl
				label={ __( 'Read more label', 'skvn-marine-blocks' ) }
				onChange={ ( readMoreLabel ) => setAttributes( { readMoreLabel } ) }
				value={ attributes.readMoreLabel || '' }
			/>
		</>
	);
}

function ProductCardControls( {
	attributes,
	setAttributes,
}: Pick< CollectionEditProps, 'attributes' | 'setAttributes' > ) {
	const [ colorPalette ] = useSettings( 'color.palette' ) as Array<
		Array< { slug: string; name: string; color: string } >
	>;
	const chipColorOptions = [
		{ label: __( 'Default', 'skvn-marine-blocks' ), value: '' },
		...( colorPalette || [] ).map( ( c ) => ( {
			label: c.name,
			value: c.slug,
		} ) ),
	];

	return (
		<>
			<ToggleControl
				checked={ attributes.showProductTags !== false }
				label={ __( 'Show tags (badge overlay)', 'skvn-marine-blocks' ) }
				onChange={ ( showProductTags ) =>
					setAttributes( { showProductTags } )
				}
			/>
			<ToggleControl
				checked={ attributes.showSpecChips !== false }
				label={ __( 'Show spec chips', 'skvn-marine-blocks' ) }
				onChange={ ( showSpecChips ) => setAttributes( { showSpecChips } ) }
			/>
			{ attributes.showSpecChips !== false && (
				<>
					<SelectControl
						label={ __( 'Chip style', 'skvn-marine-blocks' ) }
						onChange={ ( chipStyle ) =>
							setAttributes( { chipStyle: chipStyle as ChipStyle } )
						}
						options={ CHIP_STYLE_OPTIONS as unknown as { label: string; value: string }[] }
						value={ attributes.chipStyle || 'tag' }
					/>
					<SelectControl
						label={ __( 'Chip color scheme', 'skvn-marine-blocks' ) }
						onChange={ ( chipColorScheme ) =>
							setAttributes( { chipColorScheme } )
						}
						options={ chipColorOptions }
						value={ attributes.chipColorScheme || '' }
					/>
				</>
			) }
		</>
	);
}
