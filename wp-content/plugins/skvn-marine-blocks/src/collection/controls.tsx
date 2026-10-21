import {
	InspectorControls,
	RichText,
	useBlockProps,
	useSettings,
} from '@wordpress/block-editor';
import {
	CheckboxControl,
	FormTokenField,
	NumberControl,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import {
	BADGE_BEHAVIOR_OPTIONS,
	CARD_STYLE_OPTIONS,
	CHIP_STYLE_OPTIONS,
	COLOR_PALETTE_OPTIONS,
	CONTENT_WIDTH_OPTIONS,
	DETAILS_SIZE_OPTIONS,
	FONT_FAMILY_OPTIONS,
	IMAGE_RATIO_OPTIONS,
	LAYOUT_OPTIONS,
	MEDIA_LAYOUT_DEFAULT,
	MEDIA_LAYOUT_OPTIONS,
	MEDIA_OFFSET_DEFAULT,
	MEDIA_OFFSET_MAX,
	MEDIA_OFFSET_MIN,
	MEDIA_OFFSET_STEP,
	MEDIA_PADDING_DEFAULT,
	MEDIA_PADDING_MAX,
	MEDIA_PADDING_MIN,
	MEDIA_PADDING_STEP,
	MEDIA_PADDING_UNIT_DEFAULT,
	MEDIA_PADDING_UNIT_OPTIONS,
	MEDIA_SHADOW_BLUR_DEFAULT,
	MEDIA_SHADOW_BLUR_MAX,
	MEDIA_SHADOW_BLUR_MIN,
	MEDIA_SHADOW_BLUR_STEP,
	MEDIA_SHADOW_OFFSET_DEFAULT,
	MEDIA_SHADOW_OFFSET_MAX,
	MEDIA_SHADOW_OFFSET_MIN,
	MEDIA_SHADOW_OFFSET_STEP,
	MEDIA_SHADOW_OPACITY_DEFAULT,
	MEDIA_SHADOW_OPACITY_MAX,
	MEDIA_SHADOW_OPACITY_MIN,
	MEDIA_SHADOW_OPACITY_STEP,
	MEDIA_SHADOW_UNIT_DEFAULT,
	MEDIA_SHADOW_UNIT_OPTIONS,
	MEDIA_WIDTH_DEFAULT,
	MEDIA_WIDTH_MAX,
	MEDIA_WIDTH_MIN,
	MEDIA_WIDTH_STEP,
	resolveMediaLayout,
	resolveMediaOffset,
	resolveMediaPadding,
	resolveMediaShadowBlur,
	resolveMediaShadowOffset,
	resolveMediaShadowOpacity,
	resolveMediaWidthPercent,
	resolveMediaShadowCSS,
	resolvePaddingUnit,
	resolveShadowUnit,
	ORDER_MODE_OPTIONS,
	POST_ACTION_OPTIONS,
	PRODUCT_ACTION_OPTIONS,
	RESPONSIVE_PRESET_OPTIONS,
	RELATION_OPTIONS,
	SPEC_VALUES_LIMIT_OPTIONS,
	TITLE_SIZE_OPTIONS,
	resolveSpecValuesLimit,
} from './constants';
import type {
	BadgeBehavior,
	CardRoleSize,
	CardStyle,
	ChipStyle,
	CollectionAttributes,
	CollectionContentWidth,
	CollectionLayout,
	CollectionOrderMode,
	CollectionRelation,
	ColorSlug,
	DetailsSize,
	FontFamily,
	ImageRatio,
	MediaLayout,
	PaddingUnit,
	ShadowUnit,
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
			// A2 editor parity: same width class the PHP renderer emits.
			// Legacy (unset) and explicit Normal render without a width class.
			attributes.contentWidth === 'wide' ? 'alignwide' : '',
			attributes.contentWidth === 'full' ? 'alignfull' : '',
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
						max={ contentType === 'product' ? 20 : attributes.layout === 'carousel' ? 10 : 9 }
						min={ 1 }
						onChange={ ( itemsToShow ) =>
							setAttributes( { itemsToShow: itemsToShow || 3 } )
						}
						value={ attributes.itemsToShow || 3 }
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
					{ contentType === 'product' && attributes.showImage !== false && (
						<>
							<SelectControl
								label={ __( 'Media layout', 'skvn-marine-blocks' ) }
								onChange={ ( mediaLayout ) =>
									setAttributes( { mediaLayout: mediaLayout as MediaLayout } )
								}
								options={ MEDIA_LAYOUT_OPTIONS }
								value={ attributes.mediaLayout ?? MEDIA_LAYOUT_DEFAULT }
							/>
							{ attributes.mediaLayout === 'floating' && (
								<>
									<RangeControl
										label={ __(
											'Image width (% of card width)',
											'skvn-marine-blocks'
										) }
										min={ MEDIA_WIDTH_MIN }
										max={ MEDIA_WIDTH_MAX }
										step={ MEDIA_WIDTH_STEP }
										onChange={ ( v ) =>
											setAttributes( { mediaWidthPercent: v } )
										}
										value={
											attributes.mediaWidthPercent ??
											MEDIA_WIDTH_DEFAULT
										}
									/>
									<RangeControl
										label={ __(
											'Lift up from top edge (px)',
											'skvn-marine-blocks'
										) }
										min={ MEDIA_OFFSET_MIN }
										max={ MEDIA_OFFSET_MAX }
										step={ MEDIA_OFFSET_STEP }
										onChange={ ( v ) =>
											setAttributes( { mediaOffset: v } )
										}
										value={ attributes.mediaOffset ?? MEDIA_OFFSET_DEFAULT }
									/>
									<ToggleControl
										checked={ attributes.mediaShadowToggle === true }
										label={ __( 'Image shadow', 'skvn-marine-blocks' ) }
										onChange={ ( mediaShadowToggle ) =>
											setAttributes( { mediaShadowToggle } )
										}
									/>
									{ attributes.mediaShadowToggle === true && (
										<>
											<NumberControl
												label={ __(
													'Shadow offset X',
													'skvn-marine-blocks'
												) }
												min={ MEDIA_SHADOW_OFFSET_MIN }
												max={ MEDIA_SHADOW_OFFSET_MAX }
												step={ MEDIA_SHADOW_OFFSET_STEP }
												onChange={ ( v ) =>
													setAttributes( {
														mediaShadowOffsetX: v,
													} )
												}
												value={
													attributes.mediaShadowOffsetX ??
													MEDIA_SHADOW_OFFSET_DEFAULT
												}
											/>
											<NumberControl
												label={ __(
													'Shadow offset Y',
													'skvn-marine-blocks'
												) }
												min={ MEDIA_SHADOW_OFFSET_MIN }
												max={ MEDIA_SHADOW_OFFSET_MAX }
												step={ MEDIA_SHADOW_OFFSET_STEP }
												onChange={ ( v ) =>
													setAttributes( {
														mediaShadowOffsetY: v,
													} )
												}
												value={
													attributes.mediaShadowOffsetY ??
													MEDIA_SHADOW_OFFSET_DEFAULT
												}
											/>
											<SelectControl
												label={ __(
													'Shadow unit',
													'skvn-marine-blocks'
												) }
												onChange={ ( mediaShadowUnit ) =>
													setAttributes( {
														mediaShadowUnit: mediaShadowUnit as ShadowUnit,
													} )
												}
												options={ MEDIA_SHADOW_UNIT_OPTIONS }
												value={
													attributes.mediaShadowUnit ??
													MEDIA_SHADOW_UNIT_DEFAULT
												}
											/>
											<RangeControl
												label={ __(
													'Shadow blur',
													'skvn-marine-blocks'
												) }
												min={ MEDIA_SHADOW_BLUR_MIN }
												max={ MEDIA_SHADOW_BLUR_MAX }
												step={ MEDIA_SHADOW_BLUR_STEP }
												onChange={ ( v ) =>
													setAttributes( { mediaShadowBlur: v } )
												}
												value={
													attributes.mediaShadowBlur ??
													MEDIA_SHADOW_BLUR_DEFAULT
												}
											/>
											<RangeControl
												label={ __(
													'Shadow opacity (%)',
													'skvn-marine-blocks'
												) }
												min={ MEDIA_SHADOW_OPACITY_MIN }
												max={ MEDIA_SHADOW_OPACITY_MAX }
												step={ MEDIA_SHADOW_OPACITY_STEP }
												onChange={ ( v ) =>
													setAttributes( { mediaShadowOpacity: v } )
												}
												value={
													attributes.mediaShadowOpacity ??
													MEDIA_SHADOW_OPACITY_DEFAULT
												}
											/>
										</>
									) }
								</>
							) }
							{ attributes.mediaLayout === 'padded' && (
								<>
									<NumberControl
										label={ __(
											'Frame padding',
											'skvn-marine-blocks'
										) }
										min={ MEDIA_PADDING_MIN }
										max={ MEDIA_PADDING_MAX }
										step={ MEDIA_PADDING_STEP }
										onChange={ ( v ) =>
											setAttributes( { mediaPadding: v } )
										}
										value={
											attributes.mediaPadding ??
											MEDIA_PADDING_DEFAULT
										}
									/>
									<SelectControl
										label={ __(
											'Padding unit',
											'skvn-marine-blocks'
										) }
										onChange={ ( mediaPaddingUnit ) =>
											setAttributes( {
												mediaPaddingUnit: mediaPaddingUnit as PaddingUnit,
											} )
										}
										options={ MEDIA_PADDING_UNIT_OPTIONS }
										value={
											attributes.mediaPaddingUnit ??
											MEDIA_PADDING_UNIT_DEFAULT
										}
									/>
								</>
							) }
						</>
					) }
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
					<SelectControl
						help={ __(
							'New carousels start Wide. Existing blocks keep their layout until you change this.',
							'skvn-marine-blocks'
						) }
						label={ __( 'Content width', 'skvn-marine-blocks' ) }
						onChange={ ( contentWidth ) =>
							setAttributes( {
								contentWidth:
									contentWidth as CollectionContentWidth,
							} )
						}
						options={ CONTENT_WIDTH_OPTIONS }
						value={ attributes.contentWidth || 'normal' }
					/>
					{ attributes.layout === 'carousel' && (
						<>
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
							{ contentType !== 'product' && (
								<ToggleControl
									checked={ attributes.autoplay === true }
									label={ __( 'Autoplay', 'skvn-marine-blocks' ) }
									onChange={ ( autoplay ) =>
										setAttributes( { autoplay } )
									}
								/>
							) }
							{ contentType !== 'product' && attributes.autoplay && (
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
						</>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Style', 'skvn-marine-blocks' ) }>
					<p className="skvn-collection__editor-section-heading">
						{ __( 'Typography', 'skvn-marine-blocks' ) }
					</p>
					{/* Title role */}
						<SelectControl
							label={ __( 'Title font', 'skvn-marine-blocks' ) }
							onChange={ ( titleFontFamily ) =>
								setAttributes( {
									titleFontFamily: titleFontFamily as FontFamily,
								} )
							}
							options={ FONT_FAMILY_OPTIONS }
							value={ attributes.titleFontFamily || '' }
						/>
						<SelectControl
							label={ __( 'Title size', 'skvn-marine-blocks' ) }
							onChange={ ( titleFontSize ) =>
								setAttributes( {
									titleFontSize: resolveFontSize(
										titleFontSize,
										[ 'base', 'lg', 'xl', '2xl' ]
									),
								} )
							}
							options={ TITLE_SIZE_OPTIONS }
							value={ attributes.titleFontSize || '' }
						/>
						<SelectControl
							label={ __( 'Title color', 'skvn-marine-blocks' ) }
							onChange={ ( titleColor ) =>
								setAttributes( { titleColor: titleColor as ColorSlug } )
							}
							options={ COLOR_PALETTE_OPTIONS }
							value={ attributes.titleColor || '' }
						/>
						{/* Details role */}
						<SelectControl
							label={ __( 'Details font', 'skvn-marine-blocks' ) }
							onChange={ ( detailsFontFamily ) =>
								setAttributes( {
									detailsFontFamily: detailsFontFamily as FontFamily,
								} )
							}
							options={ FONT_FAMILY_OPTIONS }
							value={ attributes.detailsFontFamily || '' }
						/>
						<SelectControl
							label={ __( 'Details size', 'skvn-marine-blocks' ) }
							onChange={ ( detailsFontSize ) =>
								setAttributes( {
									detailsFontSize: resolveFontSize(
										detailsFontSize,
										[ 'sm', 'base', 'lg' ]
									),
								} )
							}
							options={ DETAILS_SIZE_OPTIONS }
							value={ attributes.detailsFontSize || '' }
						/>
						<SelectControl
							label={ __( 'Details color', 'skvn-marine-blocks' ) }
							onChange={ ( detailsColor ) =>
								setAttributes( {
									detailsColor: detailsColor as ColorSlug,
								} )
							}
							options={ COLOR_PALETTE_OPTIONS }
							value={ attributes.detailsColor || '' }
						/>
						{/* Action role */}
						<SelectControl
							label={ __( 'Action font', 'skvn-marine-blocks' ) }
							onChange={ ( actionFontFamily ) =>
								setAttributes( {
									actionFontFamily: actionFontFamily as FontFamily,
								} )
							}
							options={ FONT_FAMILY_OPTIONS }
							value={ attributes.actionFontFamily || '' }
						/>
						<SelectControl
							label={ __( 'Action size', 'skvn-marine-blocks' ) }
							onChange={ ( actionFontSize ) =>
								setAttributes( {
									actionFontSize: resolveFontSize(
										actionFontSize,
										[ 'base', 'lg', 'xl', '2xl' ]
									),
								} )
							}
							options={ TITLE_SIZE_OPTIONS }
							value={ attributes.actionFontSize || '' }
						/>
						<SelectControl
							label={ __( 'Action background', 'skvn-marine-blocks' ) }
							onChange={ ( actionBackgroundColor ) =>
								setAttributes( {
									actionBackgroundColor:
										actionBackgroundColor as ColorSlug,
								} )
							}
							options={ COLOR_PALETTE_OPTIONS }
							value={ attributes.actionBackgroundColor || '' }
						/>
						<SelectControl
							label={ __( 'Action text', 'skvn-marine-blocks' ) }
							onChange={ ( actionTextColor ) =>
								setAttributes( {
									actionTextColor: actionTextColor as ColorSlug,
								} )
							}
							options={ COLOR_PALETTE_OPTIONS }
							value={ attributes.actionTextColor || '' }
						/>
				</PanelBody>
				<PanelBody title={ __( 'Advanced', 'skvn-marine-blocks' ) }>
					<TextControl
						label={ __( 'Accessible label', 'skvn-marine-blocks' ) }
						onChange={ ( accessibleLabel ) =>
							setAttributes( { accessibleLabel } )
						}
						value={ attributes.accessibleLabel || '' }
					/>
					{ contentType === 'product' &&
						attributes.layout === 'carousel' &&
						attributes.showSpecChips !== false && (
							<SelectControl
								help={ __(
									'Limits values per attribute group, not the total number of chips. Full details stay on the product page. +N is information only — it does not expand.',
									'skvn-marine-blocks'
								) }
								label={ __(
									'Max values per attribute group',
									'skvn-marine-blocks'
								) }
								onChange={ ( specValuesLimit ) =>
									setAttributes( {
										specValuesLimit: Number( specValuesLimit ),
									} )
								}
								options={ SPEC_VALUES_LIMIT_OPTIONS as unknown as {
									label: string;
									value: number;
								}[] }
								value={ resolveSpecValuesLimit( attributes.specValuesLimit ) }
							/>
						) }
				</PanelBody>
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
		per_page: Math.min( attributes.itemsToShow || 3, contentType === 'product' ? 20 : 5 ),
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

/**
 * Illustrative sample only. Group names and counts mirror a realistic seafood
 * product (5/3/1 values) so the per-group limit and "+N" are visible in the
 * editor without a per-product API request. Never presented as real data.
 */
const PREVIEW_SPEC_GROUPS: PreviewSpecGroup[] = [
	{
		label: __( 'Size', 'skvn-marine-blocks' ),
		values: [
			'100–200 g',
			'200–300 g',
			'300–500 g',
			'500–700 g',
			'700 g+',
		],
	},
	{
		label: __( 'Pack', 'skvn-marine-blocks' ),
		values: [ 'IQF', 'Frozen block', 'Vacuum pack' ],
	},
	{
		label: __( 'Freezing', 'skvn-marine-blocks' ),
		values: [ 'IQF' ],
	},
];

type PreviewSpecGroup = {
	label: string;
	values: string[];
};

function PreviewSpecGroups( {
	groups,
	limit,
}: {
	groups: PreviewSpecGroup[];
	limit: number;
} ) {
	const safeLimit = Math.max( 1, limit );

	return (
		<div className="skvn-collection-card__specs skvn-collection-card__specs--grouped">
			{ groups.map( ( group ) => {
				const visible = group.values.slice( 0, safeLimit );
				const remaining = group.values.length - visible.length;

				return (
					<div
						className="skvn-collection-card__spec-group"
						key={ group.label }
					>
						<span className="skvn-collection-card__spec-group-label">
							{ group.label }
						</span>
						<div className="skvn-collection-card__spec-values">
							{ visible.map( ( value ) => (
								<span
									className="skvn-collection-card__spec-tag"
									key={ value }
								>
									{ value }
								</span>
							) ) }
							{ remaining > 0 && (
								<span className="skvn-collection-card__spec-more">
									{ `+${ remaining }` }
									<span className="skvn-collection-card__visually-hidden">
										{ sprintf(
											__( 'còn %d giá trị', 'skvn-marine-blocks' ),
											remaining
										) }
									</span>
								</span>
							) }
						</div>
					</div>
				);
			} ) }
		</div>
	);
}

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

	// Media layout class is Product-only; Post keeps its existing image
	// handling and never receives the floating/padded preset.
	const mediaLayout = resolveMediaLayout( attributes.mediaLayout );
	const mediaLayoutClass =
		contentType === 'product'
			? `skvn-collection-card--media-${ mediaLayout }`
			: '';
	const cardClass = [ 'skvn-collection-card', chipClass, chipColorClass, mediaLayoutClass ]
		.filter( Boolean )
		.join( ' ' );

	// Style panel preview: ánh xạ attr sang class để editor phản ánh gần frontend.
	// Invalid slug sẽ không khớp rule CSS nào → fallback theme, không vỡ layout.
	const titleClasses =
		( attributes.titleFontFamily ? ` skvn-font-family-${ attributes.titleFontFamily }` : '' ) +
		( attributes.titleFontSize ? ` skvn-font-size-${ attributes.titleFontSize }` : '' ) +
		( attributes.titleColor ? ` skvn-collection-card__title--color-${ attributes.titleColor }` : '' );
	const detailsClasses =
		( attributes.detailsFontFamily ? ` skvn-font-family-${ attributes.detailsFontFamily }` : '' ) +
		( attributes.detailsFontSize ? ` skvn-font-size-${ attributes.detailsFontSize }` : '' ) +
		( attributes.detailsColor ? ` skvn-collection-card__details--color-${ attributes.detailsColor }` : '' );
	const actionClasses =
		( attributes.actionFontFamily ? ` skvn-font-family-${ attributes.actionFontFamily }` : '' ) +
		( attributes.actionFontSize ? ` skvn-font-size-${ attributes.actionFontSize }` : '' ) +
		( attributes.actionBackgroundColor ? ` skvn-collection-card__cta--bg-${ attributes.actionBackgroundColor }` : '' ) +
		( attributes.actionTextColor ? ` skvn-collection-card__cta--text-${ attributes.actionTextColor }` : '' );

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
					{ attributes.showImage !== false && (
						<div className="skvn-collection-card__image skvn-collection-card__image--placeholder" />
					) }
				</div>
				<div className="skvn-collection-card__body">
					<h3 className={`skvn-collection-card__title${ titleClasses }`}>
						{ __( 'Post title', 'skvn-marine-blocks' ) }
					</h3>
					{ showDate && (
						<span className={`skvn-collection-card__date${ detailsClasses }`}>Jan 1, 2025</span>
					) }
					{ showExcerpt && (
						<p className={`skvn-collection-card__excerpt${ detailsClasses }`}>
							{ __( 'Post excerpt preview…', 'skvn-marine-blocks' ) }
						</p>
					) }
					<span className={`skvn-collection-card__read-more${ actionClasses }`}>{ readMoreText }</span>
				</div>
			</div>
		);
	}

	// product
	const showTags  = attributes.showProductTags !== false;
	const showSpecs = attributes.showSpecChips !== false;
	const actionMode = attributes.productActionMode || 'quote';
	const isCarousel = attributes.layout === 'carousel';
	const ctaText =
		actionMode === 'quote' ? __( 'Request quote', 'skvn-marine-blocks' ) :
		actionMode === 'none'  ? null :
		__( 'View details', 'skvn-marine-blocks' );

	const isFloating = mediaLayout === 'floating';
	const floatStyle = isFloating
		? {
				'--skvn-media-float-width': `${ resolveMediaWidthPercent(
					attributes.mediaWidthPercent
				) }%`,
				'--skvn-media-float-offset': `${ resolveMediaOffset(
					attributes.mediaOffset
				) }px`,
				'--skvn-media-float-shadow': resolveMediaShadowCSS( attributes ),
		  }
		: {};

	const isPadded = mediaLayout === 'padded';
	const paddedStyle = isPadded
		? {
				'--skvn-media-padding-top': `${ resolveMediaPadding(
					attributes.mediaPadding
				) }${ resolvePaddingUnit( attributes.mediaPaddingUnit ) }`,
				'--skvn-media-padding-side': `${ resolveMediaPadding(
					attributes.mediaPadding
				) }${ resolvePaddingUnit( attributes.mediaPaddingUnit ) }`,
		  }
		: {};

	return (
		<div className={ cardClass }>
			<div className="skvn-collection-card__media" style={ paddedStyle }>
				{ showTags && (
					<div className="skvn-collection-card__badges">
						<span className="skvn-collection-card__badge">Product tag</span>
					</div>
				) }
				{ attributes.showImage !== false && (
					<div className="skvn-collection-card__image skvn-collection-card__image--placeholder" style={ floatStyle } />
				) }
			</div>
			<div className="skvn-collection-card__body">
				<h3 className={`skvn-collection-card__title${ titleClasses }`}>
					{ __( 'Product name', 'skvn-marine-blocks' ) }
				</h3>
				{ showSpecs && isCarousel && (
					<>
						<PreviewSpecGroups
							groups={ PREVIEW_SPEC_GROUPS }
							limit={ resolveSpecValuesLimit( attributes.specValuesLimit ) }
						/>
						<p className="skvn-collection__editor-note">
							{ __(
								'Sample data — real values appear on the frontend.',
								'skvn-marine-blocks'
							) }
						</p>
					</>
				) }
				{ showSpecs && ! isCarousel && (
					<div className={`skvn-collection-card__specs${ detailsClasses }`}>
						{ DUMMY_SPECS.map( ( s ) => (
							<span key={ s } className="skvn-collection-card__spec-tag">
								{ s }
							</span>
						) ) }
					</div>
				) }
				{ ctaText && (
					<span className={`skvn-collection-card__cta${ actionClasses }`}>{ ctaText }</span>
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
			<p className="skvn-collection__editor-info-heading skvn-collection__editor-info-heading--products">
				{ isProduct
					? __( 'Products in query:', 'skvn-marine-blocks' )
					: __( 'Posts in query:', 'skvn-marine-blocks' ) }
			</p>
			{ items === null ? (
				<p className="skvn-collection__editor-product-status">
					{ __( 'Loading query...', 'skvn-marine-blocks' ) }
				</p>
			) : items.length === 0 ? (
				<p className="skvn-collection__editor-product-status">
					{ isProduct
						? __( 'No products match.', 'skvn-marine-blocks' )
						: __( 'No posts match.', 'skvn-marine-blocks' ) }
				</p>
			) : (
				<>
					<p className="skvn-collection__editor-product-count">
						{ sprintf(
							__(
								'%d item in query preview',
								'skvn-marine-blocks'
							),
							items.length
						) }
					</p>
					<div className="skvn-collection__editor-product-grid">
						{ items.map( ( p ) => (
							<div key={ p.id } className="skvn-collection__editor-product-item">
								{ p.title.rendered }
							</div>
						) ) }
					</div>
					<p className="skvn-collection__editor-product-note">
						{ __( '* Approximate list — editor-side preview may differ from frontend query.', 'skvn-marine-blocks' ) }
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
