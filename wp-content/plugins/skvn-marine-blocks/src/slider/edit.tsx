import {
	InnerBlocks,
	InspectorControls,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import {
	Button,
	Notice,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { GovernedTimeControl } from '../shared/governed-time-control';
import {
	SLIDER_AUTOPLAY_TIME,
	SLIDER_TRANSITION_TIME,
} from './time';

type SliderAttributes = {
	autoplay: boolean;
	autoplayDelay: number;
	loop: boolean;
	showArrows: boolean;
	arrowStyle: 'minimal' | 'circle' | 'pill';
	arrowPosition:
		| 'side-center'
		| 'bottom-left'
		| 'bottom-center'
		| 'bottom-right';
	showPagination: boolean;
	paginationStyle:
		| 'dots'
		| 'fraction'
		| 'timed-fraction'
		| 'timed-segments';
	paginationPosition: 'bottom-left' | 'bottom-center' | 'bottom-right';
	effect: string;
	transitionStyle: '' | 'directional-wipe' | 'fade' | 'zoom-out';
	transitionDuration: number;
	heightPreset:
		| 'default'
		| 'content'
		| 'medium'
		| 'tall'
		| 'viewport-below-header';
	slidesPerView: number;
	preset: string;
	responsiveSlides: string;
};

type SliderEditProps = {
	attributes: SliderAttributes;
	clientId: string;
	setAttributes: (attributes: Partial<SliderAttributes>) => void;
};

const TEMPLATE = [['skvn-marine/slide'], ['skvn-marine/slide']];

export function Edit({ attributes, clientId, setAttributes }: SliderEditProps) {
	const presetClass = attributes.preset
		? ` skvn-slider--${ attributes.preset }`
		: '';
	const slideCount = useSelect(
		( select ) => {
			const editor = select( blockEditorStore ) as {
				getBlockCount: ( blockClientId: string ) => number;
			};

			return editor.getBlockCount( clientId );
		},
		[ clientId ]
	);
	const hasFiveSlideLimit = [ 'hero', 'product-showcase' ].includes(
		attributes.preset
	);
	const hasGovernedSlidesPerView = [
		'hero',
		'product-showcase',
		'card-carousel',
	].includes( attributes.preset );
	const hasMultiViewTransition = attributes.preset === 'card-carousel' ||
		( ! hasGovernedSlidesPerView && attributes.slidesPerView > 1 );
	const reachedSlideLimit = hasFiveSlideLimit && slideCount >= 5;
	const effectiveTransitionStyle =
		attributes.transitionStyle ||
		(attributes.effect === 'fade' ? 'fade' : 'directional-wipe');
	const allowedBlocks =
		reachedSlideLimit ? [] : [ 'skvn-marine/slide' ];
	const { insertBlock, selectBlock } = useDispatch( blockEditorStore ) as {
		insertBlock: (
			block: ReturnType< typeof createBlock >,
			index: number,
			rootClientId: string
		) => void;
		selectBlock: ( blockClientId: string ) => void;
	};
	const addSlide = () => {
		if ( reachedSlideLimit ) {
			return;
		}

		const slide = createBlock( 'skvn-marine/slide' );
		insertBlock( slide, slideCount, clientId );
		selectBlock( slide.clientId );
	};
	const blockProps = useBlockProps({
		className: `skvn-slider skvn-slider--editor skvn-slider--height-${ attributes.heightPreset }${ presetClass }`,
	});
	const controlsCluster =
		attributes.showArrows &&
		attributes.showPagination &&
		attributes.arrowPosition !== 'side-center' &&
		attributes.arrowPosition === attributes.paginationPosition;
	const staticControlsClass = controlsCluster
		? `skvn-slider__controls skvn-slider__controls--cluster skvn-slider__controls--${ attributes.arrowPosition }`
		: 'skvn-slider__controls';

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Slider settings', 'skvn-marine-blocks')}>
					<ToggleControl
						checked={attributes.autoplay}
						label={__('Autoplay', 'skvn-marine-blocks')}
						onChange={(autoplay) => setAttributes({ autoplay })}
					/>
					<GovernedTimeControl
						config={ SLIDER_AUTOPLAY_TIME }
						label={__('Autoplay duration', 'skvn-marine-blocks')}
						help={__(
							'One duration applies to every slide.',
							'skvn-marine-blocks'
						)}
						onChange={(autoplayDelay) =>
							setAttributes({
								autoplayDelay,
							})
						}
						value={ attributes.autoplayDelay }
					/>
					<ToggleControl
						checked={attributes.loop}
						label={__('Loop', 'skvn-marine-blocks')}
						onChange={(loop) => setAttributes({ loop })}
					/>
				</PanelBody>
				<PanelBody
					initialOpen={false}
					title={__('Navigation', 'skvn-marine-blocks')}
				>
					<ToggleControl
						checked={attributes.showArrows}
						label={__('Show arrows', 'skvn-marine-blocks')}
						onChange={(showArrows) => setAttributes({ showArrows })}
					/>
					{attributes.showArrows && (
						<>
							<SelectControl
								label={__('Arrow style', 'skvn-marine-blocks')}
								onChange={(arrowStyle) =>
									setAttributes({
										arrowStyle:
											arrowStyle as SliderAttributes['arrowStyle'],
									})
								}
								options={[
									{ label: __('Minimal', 'skvn-marine-blocks'), value: 'minimal' },
									{ label: __('Circle', 'skvn-marine-blocks'), value: 'circle' },
									{
										disabled:
											attributes.arrowPosition ===
											'side-center',
										label: __('Pill', 'skvn-marine-blocks'),
										value: 'pill',
									},
								]}
								value={attributes.arrowStyle}
							/>
							<SelectControl
								help={
									attributes.arrowStyle === 'pill'
										? __(
												'Pill navigation is a bottom control and cannot use Side center.',
												'skvn-marine-blocks'
										  )
										: undefined
								}
								label={__('Arrow position', 'skvn-marine-blocks')}
								onChange={(arrowPosition) =>
									setAttributes({
										arrowPosition:
											arrowPosition as SliderAttributes['arrowPosition'],
									})
								}
								options={[
									{
										disabled:
											attributes.arrowStyle === 'pill',
										label: __('Side center', 'skvn-marine-blocks'),
										value: 'side-center',
									},
									{ label: __('Bottom left', 'skvn-marine-blocks'), value: 'bottom-left' },
									{ label: __('Bottom center', 'skvn-marine-blocks'), value: 'bottom-center' },
									{ label: __('Bottom right', 'skvn-marine-blocks'), value: 'bottom-right' },
								]}
								value={attributes.arrowPosition}
							/>
						</>
					)}
				</PanelBody>
				<PanelBody
					initialOpen={false}
					title={__('Pagination', 'skvn-marine-blocks')}
				>
					<ToggleControl
						checked={attributes.showPagination}
						label={__('Show pagination', 'skvn-marine-blocks')}
						onChange={(showPagination) =>
							setAttributes({ showPagination })
						}
					/>
					{attributes.showPagination && (
						<>
							<SelectControl
								label={__('Pagination style', 'skvn-marine-blocks')}
								onChange={(paginationStyle) =>
									setAttributes({
										paginationStyle:
											paginationStyle as SliderAttributes['paginationStyle'],
									})
								}
								options={[
									{ label: __('Dots', 'skvn-marine-blocks'), value: 'dots' },
									{ label: __('Fraction', 'skvn-marine-blocks'), value: 'fraction' },
									{ label: __('Timed fraction', 'skvn-marine-blocks'), value: 'timed-fraction' },
									{ label: __('Timed segments', 'skvn-marine-blocks'), value: 'timed-segments' },
								]}
								value={attributes.paginationStyle}
							/>
							<SelectControl
								label={__('Pagination position', 'skvn-marine-blocks')}
								onChange={(paginationPosition) =>
									setAttributes({
										paginationPosition:
											paginationPosition as SliderAttributes['paginationPosition'],
									})
								}
								options={[
									{ label: __('Bottom left', 'skvn-marine-blocks'), value: 'bottom-left' },
									{ label: __('Bottom center', 'skvn-marine-blocks'), value: 'bottom-center' },
									{ label: __('Bottom right', 'skvn-marine-blocks'), value: 'bottom-right' },
								]}
								value={attributes.paginationPosition}
							/>
						</>
					)}
				</PanelBody>
				<PanelBody
					initialOpen={false}
					title={__('Presentation', 'skvn-marine-blocks')}
				>
					<p>
						{__(
							'How the slider presents itself.',
							'skvn-marine-blocks'
						)}
					</p>
					{hasMultiViewTransition ? (
						<Notice isDismissible={false} status="info">
							{__(
								'Multi-view Sliders use standard directional movement. Directional wipe, Fade, and Zoom out are available when one Slide is shown at a time.',
								'skvn-marine-blocks'
							)}
						</Notice>
					) : (
						<>
							<SelectControl
								label={__('Transition', 'skvn-marine-blocks')}
								onChange={(transitionStyle) =>
									setAttributes({
										transitionStyle:
											transitionStyle as SliderAttributes['transitionStyle'],
									})
								}
								options={[
									{
										label: __('Directional wipe', 'skvn-marine-blocks'),
										value: 'directional-wipe',
									},
									{ label: __('Fade', 'skvn-marine-blocks'), value: 'fade' },
									{ label: __('Zoom out', 'skvn-marine-blocks'), value: 'zoom-out' },
								]}
								value={effectiveTransitionStyle}
							/>
							<GovernedTimeControl
								config={ SLIDER_TRANSITION_TIME }
								label={__('Transition duration', 'skvn-marine-blocks')}
								onChange={(transitionDuration) =>
									setAttributes({
										transitionDuration,
									})
								}
								value={ attributes.transitionDuration }
							/>
						</>
					)}
					<SelectControl
						label={__('Slider height', 'skvn-marine-blocks')}
						help={
							attributes.heightPreset ===
							'viewport-below-header'
								? __(
										'Fills the visible viewport below the site header.',
										'skvn-marine-blocks'
								  )
								: attributes.heightPreset === 'content'
								? __(
										'Uses the slide content and padding to determine height.',
										'skvn-marine-blocks'
								  )
								: undefined
						}
						onChange={(heightPreset) =>
							setAttributes({
								heightPreset:
									heightPreset as SliderAttributes['heightPreset'],
							})
						}
						options={[
							{ label: __('Default', 'skvn-marine-blocks'), value: 'default' },
							{ label: __('Content height', 'skvn-marine-blocks'), value: 'content' },
							{ label: __('Medium', 'skvn-marine-blocks'), value: 'medium' },
							{ label: __('Tall', 'skvn-marine-blocks'), value: 'tall' },
							{
								label: __('Viewport below header', 'skvn-marine-blocks'),
								value: 'viewport-below-header',
							},
						]}
						value={attributes.heightPreset}
					/>
					{!hasGovernedSlidesPerView && (
						<RangeControl
							label={__('Slides per view', 'skvn-marine-blocks')}
							max={4}
							min={1}
							onChange={(slidesPerView) =>
								setAttributes({
									slidesPerView: slidesPerView || 1,
									responsiveSlides: 'uniform',
								})
							}
							value={attributes.slidesPerView}
						/>
					)}
				</PanelBody>
			</InspectorControls>
			<div className="skvn-slider__editor-stack">
				<InnerBlocks
					allowedBlocks={ allowedBlocks }
					renderAppender={ () => null }
					template={ TEMPLATE }
				/>
			</div>
			<div
				aria-label={__(
					'Static controls preview',
					'skvn-marine-blocks'
				)}
				className={staticControlsClass}
			>
						{attributes.showArrows && (
							<div
								className={`skvn-slider__arrows skvn-slider__arrows--${ attributes.arrowStyle } skvn-slider__arrows--${ attributes.arrowPosition }`}
							>
								<button
									aria-label={__(
										'Previous slide',
										'skvn-marine-blocks'
									)}
									className="skvn-slider__arrow skvn-slider__arrow--prev"
									type="button"
								/>
								<button
									aria-label={__(
										'Next slide',
										'skvn-marine-blocks'
									)}
									className="skvn-slider__arrow skvn-slider__arrow--next"
									type="button"
								/>
							</div>
						)}
						<Button
							className="skvn-slider__add-slide"
							disabled={reachedSlideLimit}
							icon="plus-alt2"
							onClick={addSlide}
							title={
								reachedSlideLimit
									? __(
											'This Slider preset supports up to five slides.',
											'skvn-marine-blocks'
									  )
									: __('Add slide', 'skvn-marine-blocks')
							}
							variant="secondary"
						>
							{reachedSlideLimit
								? __('Slide limit reached', 'skvn-marine-blocks')
								: __('Add slide', 'skvn-marine-blocks')}
						</Button>
						{controlsCluster && (
							<span
								aria-hidden="true"
								className="skvn-slider__controls-separator"
							/>
						)}
						{attributes.showPagination && (
							<div
								className={`skvn-slider__pagination skvn-slider__pagination--${ attributes.paginationStyle } skvn-slider__pagination--${ attributes.paginationPosition }`}
							>
								{attributes.paginationStyle.includes(
									'fraction'
								) ? (
									<>
										<span>01</span>
										{attributes.paginationStyle ===
											'timed-fraction' && (
											<span
												aria-hidden="true"
												className="skvn-slider__timer"
											/>
										)}
										<span>
											{String( slideCount ).padStart(
												2,
												'0'
											)}
										</span>
									</>
								) : (
									Array.from(
										{ length: slideCount },
										( _, index ) => (
											<span
												aria-current={
													index === 0
														? 'true'
														: undefined
												}
												className="skvn-slider__static-bullet"
												key={index}
											/>
										)
									)
								)}
							</div>
						)}
			</div>
		</div>
	);
}
