import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

type BlockTemplate = [
	string,
	Record< string, unknown >?,
	BlockTemplate[]?,
];

const button = ( text: string ): BlockTemplate => [
	'core/buttons',
	{},
	[ [ 'core/button', { text } ] ],
];

const heroSlide = (
	heading: string,
	copy: string,
	cta: string
): BlockTemplate => [
	'skvn-marine/slide',
	{ overlayOpacity: 40 },
	[
		[ 'core/heading', { content: heading, level: 2 } ],
		[ 'core/paragraph', { content: copy } ],
		button( cta ),
	],
];

const showcaseSlide = (
	heading: string,
	copy: string,
	cta: string
): BlockTemplate => [
	'skvn-marine/slide',
	{ overlayOpacity: 0 },
	[
		[
			'core/columns',
			{ className: 'skvn-slider__showcase-layout' },
			[
				[
					'core/column',
					{},
					[
						[
							'core/image',
							{
								alt: '',
								caption: __(
									'Choose a product or service image.',
									'skvn-marine-blocks'
								),
							},
						],
					],
				],
				[
					'core/column',
					{},
					[
						[ 'core/heading', { content: heading, level: 3 } ],
						[ 'core/paragraph', { content: copy } ],
						button( cta ),
					],
				],
			],
		],
	],
];

const cardSlide = (
	heading: string,
	copy: string,
	cta: string
): BlockTemplate => [
	'skvn-marine/slide',
	{ overlayOpacity: 0 },
	[
		[
			'core/image',
			{
				alt: '',
				caption: __(
					'Choose an image for this card.',
					'skvn-marine-blocks'
				),
			},
		],
		[ 'core/heading', { content: heading, level: 3 } ],
		[ 'core/paragraph', { content: copy } ],
		button( cta ),
	],
];

export function registerSliderVariations() {
	registerBlockVariation( 'skvn-marine/slider', {
		name: 'skvn-hero-slider',
		title: __( 'SKVN Hero Slider', 'skvn-marine-blocks' ),
		description: __(
			'Campaign hero with two editable slides, primary calls to action, and fade transition.',
			'skvn-marine-blocks'
		),
		category: 'skvn-marine',
		icon: 'cover-image',
		scope: [ 'inserter' ],
		isDefault: true,
		attributes: {
			autoplay: true,
			autoplayDelay: 7000,
			loop: true,
			showArrows: true,
			arrowStyle: 'circle',
			arrowPosition: 'side-center',
			showPagination: true,
			paginationStyle: 'dots',
			paginationPosition: 'bottom-center',
			effect: 'fade',
			slidesPerView: 1,
			preset: 'hero',
			responsiveSlides: 'uniform',
		},
		innerBlocks: [
			heroSlide(
				__( 'Built for demanding marine operations', 'skvn-marine-blocks' ),
				__(
					'Present a clear campaign message, capability, or flagship solution here.',
					'skvn-marine-blocks'
				),
				__( 'Explore solutions', 'skvn-marine-blocks' )
			),
			heroSlide(
				__( 'Reliable support from shore to vessel', 'skvn-marine-blocks' ),
				__(
					'Replace this copy and background with a second proof point for your audience.',
					'skvn-marine-blocks'
				),
				__( 'Contact our team', 'skvn-marine-blocks' )
			),
		],
	} );

	registerBlockVariation( 'skvn-marine/slider', {
		name: 'skvn-product-showcase',
		title: __( 'SKVN Product Showcase', 'skvn-marine-blocks' ),
		description: __(
			'Flow-based image and content split for products, services, or capabilities.',
			'skvn-marine-blocks'
		),
		category: 'skvn-marine',
		icon: 'columns',
		scope: [ 'inserter' ],
		attributes: {
			autoplay: false,
			loop: true,
			showArrows: true,
			arrowStyle: 'circle',
			arrowPosition: 'side-center',
			showPagination: true,
			paginationStyle: 'dots',
			paginationPosition: 'bottom-center',
			effect: 'slide',
			slidesPerView: 1,
			preset: 'product-showcase',
			responsiveSlides: 'uniform',
		},
		innerBlocks: [
			showcaseSlide(
				__( 'Featured marine solution', 'skvn-marine-blocks' ),
				__(
					'Explain the product advantage, operating context, and the outcome it delivers.',
					'skvn-marine-blocks'
				),
				__( 'View product', 'skvn-marine-blocks' )
			),
			showcaseSlide(
				__( 'Engineering and service capability', 'skvn-marine-blocks' ),
				__(
					'Use the second slide for a related service, process, or technical capability.',
					'skvn-marine-blocks'
				),
				__( 'Request information', 'skvn-marine-blocks' )
			),
		],
	} );

	registerBlockVariation( 'skvn-marine/slider', {
		name: 'skvn-card-carousel',
		title: __( 'SKVN Card Carousel', 'skvn-marine-blocks' ),
		description: __(
			'Responsive carousel showing three cards on desktop, two on tablet, and one on mobile.',
			'skvn-marine-blocks'
		),
		category: 'skvn-marine',
		icon: 'grid-view',
		scope: [ 'inserter' ],
		attributes: {
			autoplay: false,
			loop: true,
			showArrows: true,
			arrowStyle: 'circle',
			arrowPosition: 'side-center',
			showPagination: true,
			paginationStyle: 'dots',
			paginationPosition: 'bottom-center',
			effect: 'slide',
			slidesPerView: 3,
			preset: 'card-carousel',
			responsiveSlides: '3-2-1',
		},
		innerBlocks: [
			cardSlide(
				__( 'Product highlight', 'skvn-marine-blocks' ),
				__( 'Summarize one useful product benefit.', 'skvn-marine-blocks' ),
				__( 'Learn more', 'skvn-marine-blocks' )
			),
			cardSlide(
				__( 'Service capability', 'skvn-marine-blocks' ),
				__( 'Introduce a service or operating capability.', 'skvn-marine-blocks' ),
				__( 'View service', 'skvn-marine-blocks' )
			),
			cardSlide(
				__( 'Technical expertise', 'skvn-marine-blocks' ),
				__( 'Share a concise technical or industry proof point.', 'skvn-marine-blocks' ),
				__( 'Read more', 'skvn-marine-blocks' )
			),
			cardSlide(
				__( 'Project support', 'skvn-marine-blocks' ),
				__( 'Describe how your team supports customers.', 'skvn-marine-blocks' ),
				__( 'Contact us', 'skvn-marine-blocks' )
			),
		],
	} );
}
