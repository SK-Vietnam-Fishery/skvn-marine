import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

type SliderAttributes = {
	autoplay: boolean;
	delay: number;
	loop: boolean;
	arrows: boolean;
	dots: boolean;
	effect: string;
	slidesPerView: number;
};

type SliderEditProps = {
	attributes: SliderAttributes;
	setAttributes: (attributes: Partial<SliderAttributes>) => void;
};

const TEMPLATE = [['skvn-marine/slide'], ['skvn-marine/slide']];

export function Edit({ attributes, setAttributes }: SliderEditProps) {
	const blockProps = useBlockProps({ className: 'skvn-slider skvn-slider--editor' });

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Slider settings', 'skvn-marine-blocks')}>
					<ToggleControl
						checked={attributes.autoplay}
						label={__('Autoplay', 'skvn-marine-blocks')}
						onChange={(autoplay) => setAttributes({ autoplay })}
					/>
					<RangeControl
						label={__('Delay', 'skvn-marine-blocks')}
						max={12000}
						min={1000}
						onChange={(delay) => setAttributes({ delay: delay || 5000 })}
						step={500}
						value={attributes.delay}
					/>
					<ToggleControl
						checked={attributes.loop}
						label={__('Loop', 'skvn-marine-blocks')}
						onChange={(loop) => setAttributes({ loop })}
					/>
					<ToggleControl
						checked={attributes.arrows}
						label={__('Arrows', 'skvn-marine-blocks')}
						onChange={(arrows) => setAttributes({ arrows })}
					/>
					<ToggleControl
						checked={attributes.dots}
						label={__('Dots', 'skvn-marine-blocks')}
						onChange={(dots) => setAttributes({ dots })}
					/>
					<SelectControl
						label={__('Effect', 'skvn-marine-blocks')}
						onChange={(effect) => setAttributes({ effect })}
						options={[
							{ label: __('Slide', 'skvn-marine-blocks'), value: 'slide' },
							{ label: __('Fade', 'skvn-marine-blocks'), value: 'fade' },
						]}
						value={attributes.effect}
					/>
					<RangeControl
						label={__('Slides per view', 'skvn-marine-blocks')}
						max={4}
						min={1}
						onChange={(slidesPerView) => setAttributes({ slidesPerView: slidesPerView || 1 })}
						value={attributes.slidesPerView}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="skvn-slider__editor-stack">
				<InnerBlocks allowedBlocks={['skvn-marine/slide']} template={TEMPLATE} />
			</div>
		</div>
	);
}
