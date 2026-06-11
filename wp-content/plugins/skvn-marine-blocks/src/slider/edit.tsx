import {
	InnerBlocks,
	InspectorControls,
	store as blockEditorStore,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	Notice,
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';

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
const GOVERNED_DELAYS = [ 5000, 7000, 9000, 12000 ];

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
	const allowedBlocks =
		hasFiveSlideLimit && slideCount >= 5
			? []
			: [ 'skvn-marine/slide' ];
	const blockProps = useBlockProps({
		className: `skvn-slider skvn-slider--editor${ presetClass }`,
	});
	const hasLegacyDelay = ! GOVERNED_DELAYS.includes(
		attributes.autoplayDelay
	);
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
					<SelectControl
						label={__('Autoplay duration', 'skvn-marine-blocks')}
						help={__(
							'One duration applies to every slide.',
							'skvn-marine-blocks'
						)}
						onChange={(autoplayDelay) =>
							setAttributes({
								autoplayDelay: Number( autoplayDelay ),
							})
						}
						options={[
							...(hasLegacyDelay
								? [
										{
											label: sprintf(
												__(
													'Legacy: %s seconds (preserved)',
													'skvn-marine-blocks'
												),
												(
													attributes.autoplayDelay /
													1000
												).toLocaleString()
											),
											value: String(
												attributes.autoplayDelay
											),
										},
								  ]
								: []),
							{ label: __('5 seconds', 'skvn-marine-blocks'), value: '5000' },
							{ label: __('7 seconds', 'skvn-marine-blocks'), value: '7000' },
							{ label: __('9 seconds', 'skvn-marine-blocks'), value: '9000' },
							{ label: __('12 seconds', 'skvn-marine-blocks'), value: '12000' },
						]}
						value={String( attributes.autoplayDelay )}
					/>
					{hasLegacyDelay && (
						<Notice isDismissible={false} status="info">
							{__(
								'This saved Slider uses a legacy duration. It remains unchanged until you choose a governed duration.',
								'skvn-marine-blocks'
							)}
						</Notice>
					)}
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
					title={__('Layout', 'skvn-marine-blocks')}
				>
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
						onChange={(slidesPerView) =>
							setAttributes({
								slidesPerView: slidesPerView || 1,
								responsiveSlides: 'uniform',
							})
						}
						value={attributes.slidesPerView}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="skvn-slider__editor-stack">
				<InnerBlocks
					allowedBlocks={ allowedBlocks }
					template={ TEMPLATE }
				/>
			</div>
			{slideCount > 1 &&
				(attributes.showArrows || attributes.showPagination) && (
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
				)}
		</div>
	);
}
