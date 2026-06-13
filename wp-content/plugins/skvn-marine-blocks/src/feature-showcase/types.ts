export type FeatureItem = {
	kicker: string;
	heading: string;
	copy: string;
	imageId: number;
	imageUrl: string;
	imageAlt: string;
	linkUrl?: string;
	linkText?: string;
	linkTarget?: '_self' | '_blank';
};

export type FeatureShowcaseAttributes = {
	desktopLayout: 'horizontal' | 'vertical';
	mobileBehavior: 'accordion' | 'hidden';
	defaultOpen: 'first' | 'last' | 'none';
	gradientPreset: '' | 'deep-navy' | 'marine-teal' | 'fresh-sky';
	labelRotation: 'default' | '180';
	outerRadius: number;
	interactionMode: 'hover' | 'autoplay';
	autoplayDelay: 3000 | 5000 | 7000 | 9000;
	items: FeatureItem[];
};
