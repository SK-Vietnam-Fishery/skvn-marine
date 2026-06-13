import {
	isMotionEnabledForCurrentDevice,
	observeOnce,
	parseMotionDevices,
	prefersReducedMotion,
} from '../shared/motion';

document
	.querySelectorAll< HTMLElement >( '[data-skvn-motion]' )
	.forEach( ( element ) => {
		const trigger = element.dataset.skvnMotionTrigger || 'on-scroll';
		const devices = parseMotionDevices(
			element.dataset.skvnMotionDevices || ''
		);
		let stopObserving = () => undefined;
		let hasEntered = false;

		const activate = () => {
			stopObserving();
			stopObserving = () => undefined;
			element.classList.remove(
				'skvn-motion-ready',
				'skvn-motion-active'
			);

			if (
				prefersReducedMotion() ||
				! isMotionEnabledForCurrentDevice( devices )
			) {
				return;
			}

			if ( trigger === 'on-scroll' ) {
				if ( hasEntered ) {
					element.classList.add( 'skvn-motion-active' );
					return;
				}

				element.classList.add( 'skvn-motion-ready' );
				stopObserving = observeOnce( element, () => {
					hasEntered = true;
					element.classList.add( 'skvn-motion-active' );
				} );
				return;
			}

			element.classList.add( 'skvn-motion-active' );
		};

		activate();
		window.addEventListener( 'resize', activate, { passive: true } );
	} );
