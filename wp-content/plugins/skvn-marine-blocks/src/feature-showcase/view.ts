import {
	createAutoplayPauseCoordinator,
	type AutoplayPauseCoordinator,
} from '../shared/autoplay';
import { normalizeGovernedTime } from '../shared/governed-time';
import { prefersReducedMotion } from '../shared/motion';
import { FEATURE_SHOWCASE_AUTOPLAY_TIME } from './time';

const showcaseSelector = '.skvn-feature-showcase';
const itemSelector = '.skvn-feature-showcase__item';
const summarySelector = '.skvn-feature-showcase__summary';
const desktopHoverQuery = window.matchMedia(
	'(hover: hover) and (pointer: fine)'
);
type ShowcaseElement = HTMLElement & {
	skvnFeatureShowcaseCleanup?: () => void;
};

document
	.querySelectorAll< ShowcaseElement >( showcaseSelector )
	.forEach( ( showcase ) => {
		showcase.skvnFeatureShowcaseCleanup?.();

		const items = Array.from(
			showcase.querySelectorAll< HTMLDetailsElement >( itemSelector )
		);

		if ( items.length === 0 ) {
			return;
		}

		let activeItem =
			items.find( ( item ) => item.open ) || items[ items.length - 1 ];
		const interactionMode =
			showcase.dataset.skvnInteraction === 'autoplay'
				? 'autoplay'
				: 'hover';
		const autoplayDelay = normalizeGovernedTime(
			Number( showcase.dataset.skvnAutoplayDelay ),
			FEATURE_SHOWCASE_AUTOPLAY_TIME
		);
		const autoplayEnabled =
			interactionMode === 'autoplay' &&
			items.length > 1 &&
			desktopHoverQuery.matches &&
			! prefersReducedMotion();
		const cleanupCallbacks: Array< () => void > = [];
		let autoplayTimer: number | undefined;
		let pauseCoordinator: AutoplayPauseCoordinator | undefined;
		let isSynchronizing = false;

		const activate = ( nextItem: HTMLDetailsElement ) => {
			if ( isSynchronizing ) {
				return;
			}

			isSynchronizing = true;
			items.forEach( ( item ) => {
				item.open = item === nextItem;
			} );
			activeItem = nextItem;
			isSynchronizing = false;
		};
		const clearAutoplayTimer = () => {
			if ( autoplayTimer === undefined ) {
				return;
			}

			window.clearTimeout( autoplayTimer );
			autoplayTimer = undefined;
		};
		const scheduleNextPanel = () => {
			clearAutoplayTimer();

			if (
				! autoplayEnabled ||
				pauseCoordinator?.isPaused()
			) {
				return;
			}

			autoplayTimer = window.setTimeout( () => {
				const activeIndex = items.indexOf( activeItem );
				const nextItem =
					items[ ( activeIndex + 1 ) % items.length ];

				activate( nextItem );
				scheduleNextPanel();
			}, autoplayDelay );
		};

		activate( activeItem );
		showcase.classList.add( 'skvn-feature-showcase--enhanced' );

		items.forEach( ( item ) => {
			const summary =
				item.querySelector< HTMLElement >( summarySelector );
			const handleSummaryClick = ( event: MouseEvent ) => {
				event.preventDefault();
				activate( item );
				scheduleNextPanel();
			};
			const handlePointerEnter = () => {
				if (
					interactionMode === 'hover' &&
					desktopHoverQuery.matches
				) {
					activate( item );
				}
			};

			summary?.addEventListener( 'click', handleSummaryClick );
			item.addEventListener( 'pointerenter', handlePointerEnter );
			cleanupCallbacks.push( () => {
				summary?.removeEventListener( 'click', handleSummaryClick );
				item.removeEventListener(
					'pointerenter',
					handlePointerEnter
				);
			} );
		} );

		pauseCoordinator = createAutoplayPauseCoordinator( showcase, {
			onPause: clearAutoplayTimer,
			onResume: scheduleNextPanel,
		} );
		scheduleNextPanel();

		showcase.skvnFeatureShowcaseCleanup = () => {
			clearAutoplayTimer();
			pauseCoordinator?.cleanup();
			cleanupCallbacks.forEach( ( cleanup ) => cleanup() );
			showcase.classList.remove(
				'skvn-feature-showcase--enhanced'
			);
			delete showcase.skvnFeatureShowcaseCleanup;
		};
	} );
