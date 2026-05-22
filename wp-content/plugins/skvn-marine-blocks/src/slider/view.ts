import Swiper from 'swiper';
import { Autoplay, EffectFade, Keyboard, Navigation, Pagination } from 'swiper/modules';

type SliderConfig = {
	autoplay?: boolean;
	delay?: number;
	loop?: boolean;
	arrows?: boolean;
	dots?: boolean;
	effect?: string;
	slidesPerView?: number;
};

const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function parseSliderConfig(rawConfig: string): SliderConfig {
	try {
		const parsed = JSON.parse(rawConfig) as SliderConfig;

		return parsed && typeof parsed === 'object' ? parsed : {};
	} catch {
		return {};
	}
}

document.querySelectorAll<HTMLElement>('[data-skvn-slider]').forEach((slider) => {
	const rawConfig = slider.getAttribute('data-skvn-slider') || '{}';
	const config = parseSliderConfig(rawConfig);
	const slidesPerView = Number.isFinite(config.slidesPerView) ? config.slidesPerView : 1;
	const delay = Number.isFinite(config.delay) ? config.delay : 5000;

	new Swiper(slider, {
		modules: [Autoplay, EffectFade, Keyboard, Navigation, Pagination],
		autoplay:
			config.autoplay && !prefersReduced
				? {
						delay,
						pauseOnMouseEnter: true,
					}
				: false,
		effect: config.effect === 'fade' ? 'fade' : 'slide',
		keyboard: { enabled: true },
		loop: Boolean(config.loop),
		navigation: config.arrows
			? {
					nextEl: slider.querySelector<HTMLElement>('.swiper-button-next'),
					prevEl: slider.querySelector<HTMLElement>('.swiper-button-prev'),
				}
			: false,
		pagination: config.dots
			? {
					clickable: true,
					el: slider.querySelector<HTMLElement>('.swiper-pagination'),
				}
			: false,
		slidesPerView,
	});
});
