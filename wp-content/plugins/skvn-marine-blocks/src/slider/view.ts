import Swiper from 'swiper';
import {
	Autoplay,
	EffectFade,
	Keyboard,
	Navigation,
	Pagination,
} from 'swiper/modules';
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

const prefersReduced = window.matchMedia(
	'(prefers-reduced-motion: reduce)'
).matches;

function parseSliderConfig( rawConfig: string ): SliderConfig {
	try {
		const parsed = JSON.parse( rawConfig ) as SliderConfig;

		return parsed && typeof parsed === 'object' ? parsed : {};
	} catch {
		return {};
	}
}

document
	.querySelectorAll< HTMLElement >( '[data-skvn-slider]' )
	.forEach( ( slider ) => {
		const rawConfig = slider.getAttribute( 'data-skvn-slider' ) || '{}';
		const config = parseSliderConfig( rawConfig );
		const slidesPerView = Number.isFinite( config.slidesPerView )
			? config.slidesPerView
			: 1;
		const delay = Number.isFinite( config.delay ) ? config.delay : 5000;
		const usesCardBreakpoints = config.responsiveSlides === '3-2-1';

		new Swiper( slider, {
			modules: [ Autoplay, EffectFade, Keyboard, Navigation, Pagination ],
			autoplay:
				config.autoplay && ! prefersReduced
					? {
							delay,
							pauseOnMouseEnter: true,
					  }
					: false,
			effect: config.effect === 'fade' ? 'fade' : 'slide',
			fadeEffect: {
				crossFade: true,
			},
			keyboard: { enabled: true },
			loop: Boolean( config.loop ),
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
			slidesPerView: usesCardBreakpoints ? 1 : slidesPerView,
		} );
	} );
