export type CollectionLayout = 'grid' | 'carousel';
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

export type CollectionAttributes = {
	layout: CollectionLayout;
	eyebrow: string;
	heading: string;
	showHeading: boolean;
	intro: string;
	accessibleLabel: string;
	archiveUrl: string;
	archiveLabel: string;
	catalogPdfUrl: string;
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
	showArrows: boolean;
	showPagination: boolean;
	autoplay: boolean;
	autoplayDelay: number;
};
