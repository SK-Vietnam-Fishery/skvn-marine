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
	spaceBetween?: number;
};

const READY_CLASS = 'skvn-is-ready';
const INITIALIZING_CLASS = 'skvn-is-initializing';
const FALLBACK_CLASS = 'skvn-is-fallback';

const DEFAULT_SPACE_BETWEEN = 16;

/**
 * Upper bound for the "initializing" window.
 *
 * Purpose: a skeleton must never stay on screen forever if the runtime stalls
 * between adding .skvn-is-initializing and the readiness check. It does NOT
 * replace the ready signal — readiness still decides success, this only
 * guarantees the fallback path runs. Bounded by design; no polling loop.
 */
const INITIALIZING_TIMEOUT_MS = 5000;

/**
 * Runtime state owner.
 *
 * The state classes live on the collection SECTION, not on
 * `.skvn-collection__carousel-outer`, because:
 * - every CSS state rule is written against `.skvn-collection--carousel`, which
 *   is a section class (see modules/collection-render/product-collection.php);
 * - arrows and the pause button live in the section header, OUTSIDE
 *   carousel-outer, so their visibility rules can only read a section state;
 * - the skeleton lives inside `.skvn-collection__carousel`, which is a
 *   descendant of the section, so section-scoped rules still reach it.
 *
 * Rule: the element whose class the JS mutates MUST be the same element the
 * CSS state selectors are scoped to. Verified against the real DOM in
 * docs/testing/carousel-skeleton-loading-1.3.17.md.
 */
function resolveStateElement( container: CollectionCarouselElement ): HTMLElement {
	return container.closest< HTMLElement >( '.skvn-collection' ) ?? container;
}

/**
 * Leave the carousel in its static, manually-scrollable state.
 *
 * Used for every failure path (bad config, missing markup, thrown init,
 * unusable geometry) so no instance can stay half-initialized or wait forever.
 */
function markFallback( container: CollectionCarouselElement ): void {
	const stateEl = resolveStateElement( container );
	stateEl.classList.add( FALLBACK_CLASS );
	stateEl.classList.remove( INITIALIZING_CLASS, READY_CLASS );
}

/**
 * Measure whether Swiper actually produced a usable layout.
 *
 * "swiper-initialized" is not proof: a hidden or zero-width container can still
 * get the class. Requires a positive container width, positive slide width, and
 * - when more than one column is expected - slides that actually sit side by
 * side. Deliberately does not wait for images or window.load.
 */
function isCarouselReady( swiperEl: HTMLElement, expectedColumns: number ): boolean {
	const containerRect = swiperEl.getBoundingClientRect();
	if ( containerRect.width <= 0 ) {
		return false;
	}

	const slides = Array.from(
		swiperEl.querySelectorAll< HTMLElement >( '.swiper-slide' )
	);
	if ( slides.length === 0 ) {
		return false;
	}

	const first = slides[ 0 ].getBoundingClientRect();
	if ( first.width <= 0 ) {
		return false;
	}

	if ( expectedColumns > 1 && slides.length > 1 ) {
		return slides[ 1 ].getBoundingClientRect().left > first.left;
	}

	return true;
}

function resolveColumns( config: CarouselConfig ): number {
	const width = window.innerWidth;
	const columns =
		width >= 1024
			? config.slidesPerViewDesktop
			: width >= 640
				? config.slidesPerViewTablet
				: config.slidesPerViewMobile;
	return Math.max( 1, columns || 1 );
}

function initCollectionCarousel( container: CollectionCarouselElement ): void {
	// Idempotent — Rule 1: never init twice on the same element
	if ( container.skvnCollectionCleanup ) {
		return;
	}

	// State goes on the section (see resolveStateElement), never on
	// carousel-outer, so the CSS state selectors actually match the DOM.
	const stateEl = resolveStateElement( container );
	stateEl.classList.add( INITIALIZING_CLASS );

	const raw = container.dataset.skvnCollectionCarousel;
	if ( ! raw ) {
		markFallback( container );
		return;
	}

	let config: CarouselConfig;
	try {
		config = JSON.parse( raw ) as CarouselConfig;
	} catch {
		markFallback( container );
		return;
	}

	const swiperEl = container.querySelector<HTMLElement>( '.skvn-collection__carousel' );
	if ( ! swiperEl ) {
		markFallback( container );
		return;
	}

	const slideCount = swiperEl.querySelectorAll( '.swiper-slide' ).length;
	const maxVisible = Math.max( 1, config.slidesPerViewDesktop );
	const canLoop    = slideCount > maxVisible;
	// Also guard legacy/cached Product markup that still contains autoplay=true.
	const isProduct = stateEl.classList.contains( 'skvn-collection--product' );
	const useAutoplay = ! isProduct && config.autoplay && canLoop && ! prefersReducedMotion();

	// Arrows live in the section header (outside carousel-outer), so walk up to
	// the nearest .skvn-collection ancestor to find them.
	const arrowScope  = container.closest< HTMLElement >( '.skvn-collection' ) ?? container;
	const arrowPrevEl = arrowScope.querySelector< HTMLButtonElement >( '.skvn-collection__arrow--prev' );
	const arrowNextEl = arrowScope.querySelector< HTMLButtonElement >( '.skvn-collection__arrow--next' );
	const paginationEl   = container.querySelector< HTMLElement >( '.skvn-collection__pagination' );
	const pauseBtn       = container.querySelector< HTMLButtonElement >( '.skvn-collection__pause-btn' );
	if ( isProduct ) pauseBtn?.remove();

	// Single source for the gap: PHP renders the same number into the config and
	// into --skvn-carousel-gap, so the ready and pre-init paths cannot drift.
	const spaceBetween =
		typeof config.spaceBetween === 'number' && config.spaceBetween >= 0
			? config.spaceBetween
			: DEFAULT_SPACE_BETWEEN;

	const activeModules = [ A11y ];
	if ( config.showArrows && arrowPrevEl && arrowNextEl ) activeModules.push( Navigation );
	if ( config.showPagination && paginationEl ) activeModules.push( Pagination );
	if ( useAutoplay ) activeModules.push( Autoplay );

	const swiperOptions: SwiperOptions = {
		modules:        activeModules,
		slidesPerView:  Math.max( 1, config.slidesPerViewMobile ),
		spaceBetween,
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

	// One instance must never take the page down: any thrown error here falls
	// back to the static, manually-scrollable carousel and lets sibling
	// carousels on the same page initialize normally.
	let swiper: Swiper;
	try {
		swiper = new Swiper( swiperEl, swiperOptions );
	} catch {
		markFallback( container );
		return;
	}

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
	let watchdog = 0;
	const cleanup = () => {
		window.clearTimeout( watchdog );
		coordinator?.cleanup();
		pauseBtn?.removeEventListener( 'click', handlePauseBtnClick );
		if ( ! swiper.destroyed ) {
			swiper.destroy( true, true );
		}
		// Destroy returns the DOM to a usable state; never leave a ready flag
		// behind, because that flag is what disables the static fallback.
		markFallback( container );
		delete container.skvnCollectionCleanup;
	};

	// Store handle for idempotent guard and manual teardown
	container.skvnCollectionCleanup = cleanup;
	// Also hook Swiper's own destroy so we don't double-fire
	swiper.on( 'destroy', () => {
		coordinator?.cleanup();
		pauseBtn?.removeEventListener( 'click', handlePauseBtnClick );
		markFallback( container );
		delete container.skvnCollectionCleanup;
	} );

	// Bounded safety net: if initialization stalls after the class was added,
	// release the skeleton and fall back to the static carousel. It never
	// replaces the readiness check — it only guarantees the fallback runs.
	watchdog = window.setTimeout( () => {
		if ( stateEl.classList.contains( INITIALIZING_CLASS ) ) {
			cleanup();
		}
	}, INITIALIZING_TIMEOUT_MS );

	// Promote to ready only when real geometry exists. Swiper lays slides out
	// synchronously here, so one frame is enough and we avoid waiting on images
	// or window.load.
	window.requestAnimationFrame( () => {
		if ( container.skvnCollectionCleanup !== cleanup ) {
			return;
		}
		if ( isCarouselReady( swiperEl, resolveColumns( config ) ) ) {
			window.clearTimeout( watchdog );
			stateEl.classList.add( READY_CLASS );
			stateEl.classList.remove( INITIALIZING_CLASS, FALLBACK_CLASS );
		} else {
			// Zero-width/hidden container or unusable layout: keep the static
			// carousel instead of half-initialized geometry. No polling loop.
			cleanup();
		}
	} );
}

function initAll(): void {
	document
		.querySelectorAll< CollectionCarouselElement >( '[data-skvn-collection-carousel]' )
		.forEach( ( container ) => {
			// Isolation: one broken carousel must not stop its siblings.
			try {
				initCollectionCarousel( container );
			} catch {
				markFallback( container );
			}
		} );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', initAll );
} else {
	initAll();
}
