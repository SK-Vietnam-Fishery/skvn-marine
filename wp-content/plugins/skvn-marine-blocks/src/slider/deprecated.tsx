import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type LegacySliderAttributes = {
	autoplay: boolean;
	delay: number;
	loop: boolean;
	arrows: boolean;
	dots: boolean;
	effect: string;
	slidesPerView: number;
	preset: string;
	responsiveSlides: string;
};

function save({ attributes }: { attributes: LegacySliderAttributes }) {
	const presetClass = attributes.preset
		? ` skvn-slider--${ attributes.preset }`
		: '';
	const blockProps = useBlockProps.save({
		className: `skvn-slider swiper${ presetClass }`,
		'data-skvn-slider': JSON.stringify({
			autoplay: attributes.autoplay,
			delay: attributes.delay,
			loop: attributes.loop,
			arrows: attributes.arrows,
			dots: attributes.dots,
			effect: attributes.effect,
			slidesPerView: attributes.slidesPerView,
			...(attributes.responsiveSlides === '3-2-1'
				? { responsiveSlides: attributes.responsiveSlides }
				: {}),
		}),
	});

	return (
		<div {...blockProps}>
			<div className="skvn-slider__wrapper swiper-wrapper">
				<InnerBlocks.Content />
			</div>
			{attributes.arrows && (
				<>
					<button className="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" type="button" />
					<button className="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" type="button" />
				</>
			)}
			{attributes.dots && <div className="skvn-slider__pagination swiper-pagination" />}
		</div>
	);
}

const deprecated = [
	{
		attributes: {
			autoplay: { type: 'boolean', default: true },
			delay: { type: 'number', default: 5000 },
			loop: { type: 'boolean', default: true },
			arrows: { type: 'boolean', default: true },
			dots: { type: 'boolean', default: true },
			effect: { type: 'string', default: 'slide' },
			slidesPerView: { type: 'number', default: 1 },
			preset: { type: 'string', default: '' },
			responsiveSlides: { type: 'string', default: 'uniform' },
		},
		migrate: ( attributes: LegacySliderAttributes ) => ( {
			autoplay: attributes.autoplay,
			autoplayDelay: attributes.delay,
			loop: attributes.loop,
			showArrows: attributes.arrows,
			arrowStyle: 'circle',
			arrowPosition: 'side-center',
			showPagination: attributes.dots,
			paginationStyle: 'dots',
			paginationPosition: 'bottom-center',
			effect: attributes.effect,
			slidesPerView: attributes.slidesPerView,
			preset: attributes.preset,
			responsiveSlides: attributes.responsiveSlides,
		} ),
		save,
	},
];

export default deprecated;
