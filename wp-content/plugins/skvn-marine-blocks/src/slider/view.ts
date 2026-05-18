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

document.querySelectorAll<HTMLElement>('[data-skvn-slider]').forEach((slider) => {
	const rawConfig = slider.getAttribute('data-skvn-slider') || '{}';
	const config = JSON.parse(rawConfig) as SliderConfig;

	new Swiper(slider, {
		modules: [Autoplay, EffectFade, Keyboard, Navigation, Pagination],
		autoplay:
			config.autoplay && !prefersReduced
				? {
						delay: config.delay || 5000,
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
		slidesPerView: config.slidesPerView || 1,
	});
});
