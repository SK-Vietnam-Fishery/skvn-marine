export type MotionDevices = {
	desktop: boolean;
	tablet: boolean;
	mobile: boolean;
};

const DEVICE_QUERIES = {
	desktop: '(min-width: 1024px)',
	tablet: '(min-width: 768px) and (max-width: 1023px)',
	mobile: '(max-width: 767px)',
} as const;

export function prefersReducedMotion() {
	return window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
}

export function parseMotionDevices( value: string | null ): MotionDevices {
	const enabled = new Set( ( value || '' ).split( /\s+/ ).filter( Boolean ) );

	return {
		desktop: enabled.has( 'desktop' ),
		tablet: enabled.has( 'tablet' ),
		mobile: enabled.has( 'mobile' ),
	};
}

export function isMotionEnabledForCurrentDevice( devices: MotionDevices ) {
	return (
		Object.keys( DEVICE_QUERIES ) as Array< keyof MotionDevices >
	 ).some(
		( device ) =>
			devices[ device ] &&
			window.matchMedia( DEVICE_QUERIES[ device ] ).matches
	);
}

export function observeOnce( element: HTMLElement, onVisible: () => void ) {
	if ( ! ( 'IntersectionObserver' in window ) ) {
		onVisible();
		return () => undefined;
	}

	const observer = new IntersectionObserver(
		( entries ) => {
			if ( ! entries.some( ( entry ) => entry.isIntersecting ) ) {
				return;
			}

			observer.disconnect();
			onVisible();
		},
		{
			rootMargin: '0px 0px -80px',
			threshold: 0.1,
		}
	);

	observer.observe( element );

	return () => observer.disconnect();
}
