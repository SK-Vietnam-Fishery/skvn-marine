export type CollectionLayout = 'grid' | 'carousel';
export type CollectionOrderMode =
	| 'featured'
	| 'newest'
	| 'manual'
	| 'shuffle-balanced';
export type ResponsivePreset = '1-1-1' | '2-1-1' | '3-2-1' | '4-2-1' | '5-3-1';

export type CollectionAttributes = {
	layout: CollectionLayout;
	heading: string;
	intro: string;
	accessibleLabel: string;
	orderMode: CollectionOrderMode;
	itemsToShow: number;
	responsivePreset: ResponsivePreset;
};
