import Swiper from 'swiper';
import {
	A11y,
	Autoplay,
	EffectFade,
	Keyboard,
	Navigation,
	Pagination,
} from 'swiper/modules';
import { __ } from '@wordpress/i18n';
import { prefersReducedMotion } from '../shared/motion';
import 'swiper/css';
import 'swiper/css/effect-fade';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import './style.css';

type SliderConfig = {
	autoplay?: boolean;
	delay?: number;
	loop?: boolean;
	arrows?: boolean;
	dots?: boolean;
	effect?: string;
	slidesPerView?: number;
	responsiveSlides?: string;
};

type NormalizedSliderConfig = {
	autoplay: boolean;
	delay: number;
	loop: boolean;
	arrows: boolean;
	dots: boolean;
	effect: 'fade' | 'slide';
	slidesPerView: number;
	responsiveSlides: '3-2-1' | 'uniform';
};

type SliderElement = HTMLElement & {
	swiper?: Swiper;
	skvnSliderCleanup?: () => void;
};

function clampInteger(
	value: unknown,
	fallback: number,
	minimum: number,
	maximum: number
) {
	return typeof value === 'number' && Number.isFinite( value )
		? Math.min( maximum, Math.max( minimum, Math.round( value ) ) )
		: fallback;
}

function normalizeSliderDelay( value: unknown ) {
	return clampInteger( value, 5000, 1000, 12000 );
}

function parseSliderConfig( rawConfig: string ): NormalizedSliderConfig {
	let parsed: SliderConfig = {};

	try {
		const candidate = JSON.parse( rawConfig ) as SliderConfig;
		parsed =
			candidate && typeof candidate === 'object' ? candidate : {};
	} catch {
		parsed = {};
	}

	return {
		autoplay:
			typeof parsed.autoplay === 'boolean' ? parsed.autoplay : true,
		delay: normalizeSliderDelay( parsed.delay ),
		loop: typeof parsed.loop === 'boolean' ? parsed.loop : true,
		arrows: typeof parsed.arrows === 'boolean' ? parsed.arrows : true,
		dots: typeof parsed.dots === 'boolean' ? parsed.dots : true,
		effect: parsed.effect === 'fade' ? 'fade' : 'slide',
		slidesPerView: clampInteger( parsed.slidesPerView, 1, 1, 4 ),
		responsiveSlides:
			parsed.responsiveSlides === '3-2-1' ? '3-2-1' : 'uniform',
	};
}

document
	.querySelectorAll< SliderElement >( '[data-skvn-slider]' )
	.forEach( ( slider ) => {
		if (
			slider.swiper ||
			slider.dataset.skvnSliderInitialized === 'true'
		) {
			return;
		}

		const rawConfig = slider.getAttribute( 'data-skvn-slider' ) || '{}';
		const config = parseSliderConfig( rawConfig );
		const usesCardBreakpoints = config.responsiveSlides === '3-2-1';
		const reducedMotion = prefersReducedMotion();

		slider.dataset.skvnSliderInitialized = 'true';
		slider.classList.add( 'skvn-slider--initialized' );

		try {
			const swiper = new Swiper( slider, {
				modules: [
					A11y,
					Autoplay,
					EffectFade,
					Keyboard,
					Navigation,
					Pagination,
				],
				a11y: {
					containerRoleDescriptionMessage: __(
						'Carousel',
						'skvn-marine-blocks'
					),
					nextSlideMessage: __(
						'Next slide',
						'skvn-marine-blocks'
					),
					prevSlideMessage: __(
						'Previous slide',
						'skvn-marine-blocks'
					),
					slideLabelMessage: __(
						'{{index}} of {{slidesLength}}',
						'skvn-marine-blocks'
					),
				},
				autoplay:
					config.autoplay && ! reducedMotion
						? {
								delay: config.delay,
								pauseOnMouseEnter: false,
						  }
						: false,
				effect: config.effect,
				fadeEffect: {
					crossFade: true,
				},
				keyboard: { enabled: true },
				loop: config.loop,
				navigation: config.arrows
					? {
							nextEl: slider.querySelector< HTMLElement >(
								'.swiper-button-next'
							),
							prevEl: slider.querySelector< HTMLElement >(
								'.swiper-button-prev'
							),
					  }
					: false,
				pagination: config.dots
					? {
							clickable: true,
							el: slider.querySelector< HTMLElement >(
								'.swiper-pagination'
							),
					  }
					: false,
				breakpoints: usesCardBreakpoints
					? {
							600: { slidesPerView: 2 },
							1024: { slidesPerView: 3 },
					  }
					: undefined,
				slidesPerView: usesCardBreakpoints
					? 1
					: config.slidesPerView,
			} );

			let pointerInside = false;
			let focusInside = slider.contains( document.activeElement );
			let focusTimeout: number | undefined;

			const pauseAutoplay = () => {
				swiper.autoplay?.pause();
			};
			const resumeAutoplay = () => {
				if (
					config.autoplay &&
					! prefersReducedMotion() &&
					! document.hidden &&
					! pointerInside &&
					! focusInside
				) {
					swiper.autoplay?.resume();
					return;
				}

				pauseAutoplay();
			};

			const handlePointerEnter = ( event: PointerEvent ) => {
				if ( event.pointerType !== 'mouse' ) {
					return;
				}

				pointerInside = true;
				pauseAutoplay();
			};
			const handlePointerLeave = ( event: PointerEvent ) => {
				if ( event.pointerType !== 'mouse' ) {
					return;
				}

				pointerInside = false;
				resumeAutoplay();
			};
			const handleFocusIn = () => {
				focusInside = true;
				pauseAutoplay();
			};
			const handleFocusOut = () => {
				focusTimeout = window.setTimeout( () => {
					focusInside = slider.contains( document.activeElement );
					resumeAutoplay();
				}, 0 );
			};
			const handleVisibilityChange = () => {
				if ( document.hidden ) {
					pauseAutoplay();
					return;
				}

				resumeAutoplay();
			};
			const cleanup = () => {
				slider.removeEventListener(
					'pointerenter',
					handlePointerEnter
				);
				slider.removeEventListener(
					'pointerleave',
					handlePointerLeave
				);
				slider.removeEventListener( 'focusin', handleFocusIn );
				slider.removeEventListener( 'focusout', handleFocusOut );
				document.removeEventListener(
					'visibilitychange',
					handleVisibilityChange
				);

				if ( focusTimeout !== undefined ) {
					window.clearTimeout( focusTimeout );
				}

				delete slider.skvnSliderCleanup;
				delete slider.dataset.skvnSliderInitialized;
			};

			slider.addEventListener( 'pointerenter', handlePointerEnter );
			slider.addEventListener( 'pointerleave', handlePointerLeave );
			slider.addEventListener( 'focusin', handleFocusIn );
			slider.addEventListener( 'focusout', handleFocusOut );
			document.addEventListener(
				'visibilitychange',
				handleVisibilityChange
			);
			slider.skvnSliderCleanup = cleanup;
			swiper.on( 'destroy', cleanup );
			resumeAutoplay();
		} catch {
			slider.skvnSliderCleanup?.();
			delete slider.dataset.skvnSliderInitialized;
			slider.classList.remove( 'skvn-slider--initialized' );
		}
	} );
