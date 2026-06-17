import { createBlock, registerBlockPattern, serialize } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

function button( text: string, url: string, className = '' ) {
	return createBlock(
		'core/buttons',
		{},
		[
			createBlock( 'core/button', {
				text,
				url,
				...( className ? { className } : {} ),
			} ),
		]
	);
}

function heroSlide(
	heading: string,
	copy: string,
	cta: string,
	url: string,
	imageUrl: string,
	imageAlt: string,
	level = 2
) {
	return createBlock(
		'skvn-marine/slide',
		{
			overlayOpacity: 45,
			backgroundImageUrl: imageUrl,
			backgroundImageAlt: imageAlt,
		},
		[
			createBlock( 'core/heading', { content: heading, level } ),
			createBlock( 'core/paragraph', { content: copy } ),
			button( cta, url ),
		]
	);
}

export function registerPageUnderConstructionPattern() {
	registerBlockPattern( 'skvn-marine/page-under-construction', {
		title: __( 'SKVN Under Construction Page', 'skvn-marine-blocks' ),
		description: __(
			'Placeholder page with a hero slider for sections still in progress. Replace images, copy, and CTAs before publishing.',
			'skvn-marine-blocks'
		),
		categories: [ 'skvn-marine' ],
		content: serialize(
			createBlock(
				'core/group',
				{
					align: 'full',
					className: 'skvn-under-construction-page',
					layout: { type: 'default' },
				},
				[
					createBlock(
						'skvn-marine/slider',
						{
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
						[
							heroSlide(
								__(
									'This page is under construction',
									'skvn-marine-blocks'
								),
								__(
									'We are preparing complete and accurate information for export buyers and partners.',
									'skvn-marine-blocks'
								),
								__( 'Back to homepage', 'skvn-marine-blocks' ),
								'/',
								'https://images.unsplash.com/photo-1565680018434-b513d5e5fd47?auto=format&fit=crop&w=1920&q=80',
								__(
									'Seafood packed for export',
									'skvn-marine-blocks'
								),
								1
							),
							heroSlide(
								__( 'Coming soon', 'skvn-marine-blocks' ),
								__(
									'Product details, process information, and export documentation will be published here shortly.',
									'skvn-marine-blocks'
								),
								__( 'View products', 'skvn-marine-blocks' ),
								'/products/',
								'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1920&q=80',
								__(
									'Cold storage and processing plant',
									'skvn-marine-blocks'
								)
							),
							heroSlide(
								__( 'Need help now?', 'skvn-marine-blocks' ),
								__(
									'While this page is being built, contact our sales team or send a quote request.',
									'skvn-marine-blocks'
								),
								__( 'Request a quote', 'skvn-marine-blocks' ),
								'/request-a-quote/',
								'https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1920&q=80',
								__( 'Fishing vessel at sea', 'skvn-marine-blocks' )
							),
						]
					),
					createBlock( 'core/pattern', {
						slug: 'skvn-marine/trust-strip',
					} ),
					createBlock(
						'core/group',
						{
							className:
								'skvn-section skvn-under-construction-page__note',
							layout: { type: 'constrained' },
						},
						[
							createBlock( 'core/paragraph', {
								content: __( 'Notice', 'skvn-marine-blocks' ),
								className: 'skvn-section__eyebrow',
							} ),
							createBlock( 'core/heading', {
								content: __(
									'Content is being updated',
									'skvn-marine-blocks'
								),
								level: 2,
								className: 'skvn-section__heading',
							} ),
							createBlock( 'core/paragraph', {
								content: __(
									'This is a placeholder page. When the final content is ready, replace the slider, copy, and links or switch to a full page pattern.',
									'skvn-marine-blocks'
								),
								className: 'skvn-section__lead',
							} ),
						]
					),
				]
			)
		),
	} );
}