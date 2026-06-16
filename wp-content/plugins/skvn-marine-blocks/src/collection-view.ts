import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination, A11y } from 'swiper/modules';
import type { SwiperOptions } from 'swiper/types';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import { createAutoplayPauseCoordinator } from './shared/autoplay';
import { prefersReducedMotion } from './shared/motion';

type CollectionCarouselElement = HTMLElement & {
	skvnCollectionCleanup?: () => void;
};

type CarouselConfig = {
	slidesPerViewDesktop: number;
	slidesPerViewTablet: number;
	slidesPerViewMobile: number;
	showArrows: boolean;
	showPagination: boolean;
	autoplay: boolean;
	autoplayDelay: number;
};

function initCollectionCarousel( container: CollectionCarouselElement ): void {
	// Idempotent — Rule 1: never init twice on the same element
	if ( container.skvnCollectionCleanup ) {
		return;
	}

	const raw = container.dataset.skvnCollectionCarousel;
	if ( ! raw ) {
		return;
	}

	let config: CarouselConfig;
	try {
		config = JSON.parse( raw ) as CarouselConfig;
	} catch {
		return;
	}

	const slideCount = container.querySelectorAll( '.swiper-slide' ).length;
	const maxVisible = Math.max( 1, config.slidesPerViewDesktop );
	const canLoop    = slideCount > maxVisible;
	const useAutoplay = config.autoplay && canLoop && ! prefersReducedMotion();

	const arrowPrevEl    = container.querySelector< HTMLButtonElement >( '.skvn-collection__arrow--prev' );
	const arrowNextEl    = container.querySelector< HTMLButtonElement >( '.skvn-collection__arrow--next' );
	const paginationEl   = container.querySelector< HTMLElement >( '.skvn-collection__pagination' );
	const pauseBtn       = container.querySelector< HTMLButtonElement >( '.skvn-collection__pause-btn' );

	const activeModules = [ A11y ];
	if ( config.showArrows && arrowPrevEl && arrowNextEl ) activeModules.push( Navigation );
	if ( config.showPagination && paginationEl ) activeModules.push( Pagination );
	if ( useAutoplay ) activeModules.push( Autoplay );

	const swiperOptions: SwiperOptions = {
		modules:        activeModules,
		slidesPerView:  Math.max( 1, config.slidesPerViewMobile ),
		spaceBetween:   16,
		loop:           canLoop,
		breakpoints: {
			640:  { slidesPerView: Math.max( 1, config.slidesPerViewTablet ) },
			1024: { slidesPerView: Math.max( 1, config.slidesPerViewDesktop ) },
		},
		a11y: { enabled: true },
	};

	if ( config.showArrows && arrowPrevEl && arrowNextEl ) {
		swiperOptions.navigation = { prevEl: arrowPrevEl, nextEl: arrowNextEl };
	}

	if ( config.showPagination && paginationEl ) {
		swiperOptions.pagination = {
			el:            paginationEl,
			clickable:     true,
			bulletElement: 'button',
			renderBullet:  ( _index: number, className: string ) =>
				`<button class="${ className }" aria-label="Go to slide ${ _index + 1 }"></button>`,
		};
	}

	if ( useAutoplay ) {
		swiperOptions.autoplay = {
			delay:                 config.autoplayDelay,
			disableOnInteraction:  false,
			pauseOnMouseEnter:     false,
		};
	}

	const swiper = new Swiper( container, swiperOptions );

	// Rule 0: write teardown first
	// Coordinator manages: pointer hover, focus, tab visibility, explicit user pause
	const coordinator = useAutoplay
		? createAutoplayPauseCoordinator( container, {
				onPause: () => {
					swiper.autoplay.stop();
					if ( pauseBtn ) {
						pauseBtn.textContent = 'Play';
						pauseBtn.setAttribute( 'aria-label', 'Play slideshow' );
						pauseBtn.setAttribute( 'aria-pressed', 'false' );
					}
				},
				onResume: () => {
					swiper.autoplay.start();
					if ( pauseBtn ) {
						pauseBtn.textContent = 'Pause';
						pauseBtn.setAttribute( 'aria-label', 'Pause slideshow' );
						pauseBtn.setAttribute( 'aria-pressed', 'true' );
					}
				},
		  } )
		: null;

	// Track whether user explicitly paused so Play clears both user + any other transient reasons
	let userExplicitlyPaused = false;

	const handlePauseBtnClick = () => {
		if ( ! coordinator ) {
			return;
		}
		userExplicitlyPaused = ! userExplicitlyPaused;
		coordinator.setPauseReason( 'user', userExplicitlyPaused );
		// When explicitly playing, also clear any stale interaction reason
		if ( ! userExplicitlyPaused ) {
			coordinator.setPauseReason( 'interaction', false );
		}
	};

	pauseBtn?.addEventListener( 'click', handlePauseBtnClick );

	// Rule 0: cleanup pairs every acquisition above
	const cleanup = () => {
		coordinator?.cleanup();
		pauseBtn?.removeEventListener( 'click', handlePauseBtnClick );
		if ( ! swiper.destroyed ) {
			swiper.destroy( true, true );
		}
		delete container.skvnCollectionCleanup;
	};

	// Store handle for idempotent guard and manual teardown
	container.skvnCollectionCleanup = cleanup;
	// Also hook Swiper's own destroy so we don't double-fire
	swiper.on( 'destroy', () => {
		coordinator?.cleanup();
		pauseBtn?.removeEventListener( 'click', handlePauseBtnClick );
		delete container.skvnCollectionCleanup;
	} );
}

function initAll(): void {
	document
		.querySelectorAll< CollectionCarouselElement >( '[data-skvn-collection-carousel]' )
		.forEach( initCollectionCarousel );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initAll );
} else {
	initAll();
}
