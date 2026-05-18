import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type SliderAttributes = {
	autoplay: boolean;
	delay: number;
	loop: boolean;
	arrows: boolean;
	dots: boolean;
	effect: string;
	slidesPerView: number;
};

type SliderSaveProps = {
	attributes: SliderAttributes;
};

export function save({ attributes }: SliderSaveProps) {
	const blockProps = useBlockProps.save({
		className: 'skvn-slider swiper',
		'data-skvn-slider': JSON.stringify({
			autoplay: attributes.autoplay,
			delay: attributes.delay,
			loop: attributes.loop,
			arrows: attributes.arrows,
			dots: attributes.dots,
			effect: attributes.effect,
			slidesPerView: attributes.slidesPerView,
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
