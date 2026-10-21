export type CollectionLayout = 'grid' | 'carousel';
export type CollectionContentWidth = 'normal' | 'wide' | 'full';
export type CollectionOrderMode =
	| 'featured'
	| 'newest'
	| 'manual'
	| 'shuffle-balanced';
export type ResponsivePreset = '1-1-1' | '2-1-1' | '3-2-1' | '4-2-1' | '5-3-1';
export type CollectionRelation = 'AND' | 'OR';
export type ImageRatio = '1:1' | '4:3' | '3:2' | '16:9' | 'auto';
export type BadgeBehavior = 'display' | 'archive-link';
export type CardStyle = 'default' | 'featured';
export type PostActionMode = 'read' | 'custom';
export type ProductActionMode = 'view' | 'quote' | 'both' | 'custom';
export type ChipStyle = 'tag' | 'hashtag' | 'dot' | 'plain';

export type MediaLayout = 'flush' | 'padded' | 'floating';
export type ShadowUnit = 'px' | 'rem';
export type PaddingUnit = 'px' | 'rem';

export type CardRoleSize = 'sm' | 'base' | 'lg' | 'xl' | '2xl';
export type DetailsSize = 'sm' | 'base' | 'lg';
export type FontFamily = 'skvn-system' | 'skvn-heading' | 'skvn-ui';
export type ColorSlug = string;

export type CollectionAttributes = {
	layout: CollectionLayout;
	contentWidth?: CollectionContentWidth;
	specValuesLimit?: number;
	eyebrow: string;
	heading: string;
	showHeading: boolean;
	intro: string;
	accessibleLabel: string;
	archiveUrl: string;
	archiveLabel: string;
	catalogCtaUrl: string;
	showCatalogCta: boolean;
	catalogCtaLabel: string;
	categories: string[];
	tags: string[];
	relation: CollectionRelation;
	orderMode: CollectionOrderMode;
	itemsToShow: number;
	responsivePreset: ResponsivePreset;
	showImage: boolean;
	imageRatio: ImageRatio;
	cardStyle: CardStyle;
	equalHeight: boolean;
	badgeBehavior: BadgeBehavior;
	showDate: boolean;
	showAuthor: boolean;
	showPostCategories: boolean;
	showPostTags: boolean;
	showExcerpt: boolean;
	postActionMode: PostActionMode;
	showPrice: boolean;
	showSku: boolean;
	showStock: boolean;
	showProductCategories: boolean;
	showProductTags: boolean;
	productActionMode: ProductActionMode;
	customActionUrl: string;
	appendQuoteContext: boolean;
	showSpecChips: boolean;
	chipStyle: ChipStyle;
	chipColorScheme: string;
	readMoreLabel: string;
	showArrows: boolean;
	showPagination: boolean;
	autoplay: boolean;
	autoplayDelay: number;
	// Typography theo vai trò (S5). Mặc định undefined → kế thừa theme.
	titleFontFamily?: FontFamily;
	titleFontSize?: CardRoleSize;
	titleColor?: ColorSlug;
	detailsFontFamily?: FontFamily;
	detailsFontSize?: DetailsSize;
	detailsColor?: ColorSlug;
	actionFontFamily?: FontFamily;
	actionFontSize?: CardRoleSize;
	actionBackgroundColor?: ColorSlug;
	actionTextColor?: ColorSlug;
	actionHoverBackgroundColor?: ColorSlug;
	actionHoverTextColor?: ColorSlug;
	// Media presets (S6 / P4, Product only). Default undefined → 'flush' in
	// PHP/JS so legacy blocks keep identical output. Post does not use these.
	mediaLayout?: MediaLayout;
	mediaWidthPercent?: number; // % of card width, floating only
	mediaOffset?: number; // translateY px (negative = lift up), floating only
	mediaShadowToggle?: boolean; // floating only
	mediaShadowOffsetX?: number; // signed px/rem, shadow only
	mediaShadowOffsetY?: number; // signed px/rem, shadow only
	mediaShadowUnit?: ShadowUnit; // shadow only
	mediaShadowBlur?: number; // shadow only
	mediaShadowOpacity?: number; // 0-100 %, shadow only
	mediaPadding?: number; // padded only
	mediaPaddingUnit?: PaddingUnit; // padded only
};
