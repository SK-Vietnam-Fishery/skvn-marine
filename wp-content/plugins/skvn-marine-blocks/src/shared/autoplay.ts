export type AutoplayPauseCoordinator = {
	cleanup: () => void;
	isPaused: () => boolean;
	setPauseReason: ( reason: string, active: boolean ) => void;
};

type AutoplayPauseCoordinatorOptions = {
	onPause: () => void;
	onResume: () => void;
};

export function createAutoplayPauseCoordinator(
	element: HTMLElement,
	{ onPause, onResume }: AutoplayPauseCoordinatorOptions
): AutoplayPauseCoordinator {
	const pauseReasons = new Set< string >();
	let cleaned = false;
	let focusTimeout: number | undefined;
	let wasPaused = false;

	const notify = () => {
		const isPaused = pauseReasons.size > 0;

		if ( isPaused === wasPaused ) {
			return;
		}

		wasPaused = isPaused;

		if ( isPaused ) {
			onPause();
		} else {
			onResume();
		}
	};
	const setPauseReason = ( reason: string, active: boolean ) => {
		if ( cleaned ) {
			return;
		}

		if ( active ) {
			pauseReasons.add( reason );
		} else {
			pauseReasons.delete( reason );
		}

		notify();
	};
	const handlePointerEnter = ( event: PointerEvent ) => {
		if ( event.pointerType === 'mouse' ) {
			setPauseReason( 'pointer', true );
		}
	};
	const handlePointerLeave = ( event: PointerEvent ) => {
		if ( event.pointerType === 'mouse' ) {
			setPauseReason( 'pointer', false );
		}
	};
	const handleFocusIn = () => {
		setPauseReason( 'focus', true );
	};
	const handleFocusOut = () => {
		if ( focusTimeout !== undefined ) {
			window.clearTimeout( focusTimeout );
		}

		focusTimeout = window.setTimeout( () => {
			setPauseReason(
				'focus',
				element.contains( document.activeElement )
			);
		}, 0 );
	};
	const handleVisibilityChange = () => {
		setPauseReason( 'hidden', document.hidden );
	};
	const cleanup = () => {
		if ( cleaned ) {
			return;
		}

		cleaned = true;
		element.removeEventListener( 'pointerenter', handlePointerEnter );
		element.removeEventListener( 'pointerleave', handlePointerLeave );
		element.removeEventListener( 'focusin', handleFocusIn );
		element.removeEventListener( 'focusout', handleFocusOut );
		document.removeEventListener(
			'visibilitychange',
			handleVisibilityChange
		);

		if ( focusTimeout !== undefined ) {
			window.clearTimeout( focusTimeout );
		}

		pauseReasons.clear();
	};

	element.addEventListener( 'pointerenter', handlePointerEnter );
	element.addEventListener( 'pointerleave', handlePointerLeave );
	element.addEventListener( 'focusin', handleFocusIn );
	element.addEventListener( 'focusout', handleFocusOut );
	document.addEventListener( 'visibilitychange', handleVisibilityChange );

	if ( document.hidden ) {
		pauseReasons.add( 'hidden' );
	}

	if ( element.contains( document.activeElement ) ) {
		pauseReasons.add( 'focus' );
	}

	wasPaused = pauseReasons.size > 0;

	return {
		cleanup,
		isPaused: () => pauseReasons.size > 0,
		setPauseReason,
	};
}
