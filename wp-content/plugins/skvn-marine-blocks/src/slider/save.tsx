import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type SliderAttributes = {
	autoplay: boolean;
	autoplayDelay: number;
	loop: boolean;
	showArrows: boolean;
	arrowStyle: string;
	arrowPosition: string;
	showPagination: boolean;
	paginationStyle: string;
	paginationPosition: string;
	effect: string;
	slidesPerView: number;
	preset: string;
	responsiveSlides: string;
};

type SliderSaveProps = {
	attributes: SliderAttributes;
};

export function save({ attributes }: SliderSaveProps) {
	const presetClass = attributes.preset
		? ` skvn-slider--${ attributes.preset }`
		: '';
	const blockProps = useBlockProps.save({
		className: `skvn-slider swiper${ presetClass }`,
		'data-skvn-slider': JSON.stringify({
			autoplay: attributes.autoplay,
			autoplayDelay: attributes.autoplayDelay,
			loop: attributes.loop,
			showArrows: attributes.showArrows,
			arrowStyle: attributes.arrowStyle,
			arrowPosition: attributes.arrowPosition,
			showPagination: attributes.showPagination,
			paginationStyle: attributes.paginationStyle,
			paginationPosition: attributes.paginationPosition,
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
			{(attributes.showArrows || attributes.showPagination) && (
				<div className="skvn-slider__controls">
					{attributes.showArrows && (
						<div className="skvn-slider__arrows">
					<button className="skvn-slider__arrow skvn-slider__arrow--prev swiper-button-prev" type="button" />
					<button className="skvn-slider__arrow skvn-slider__arrow--next swiper-button-next" type="button" />
						</div>
					)}
					{attributes.showPagination && <div className="skvn-slider__pagination swiper-pagination" />}
				</div>
			)}
		</div>
	);
}
