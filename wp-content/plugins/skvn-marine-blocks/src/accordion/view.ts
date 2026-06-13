import { prefersReducedMotion } from '../shared/motion';

let accordionId = 0;

function afterHeightTransition( element: HTMLElement, callback: () => void ) {
	let completed = false;

	const complete = () => {
		if ( completed ) {
			return;
		}

		completed = true;
		element.removeEventListener( 'transitionend', onTransitionEnd );
		window.clearTimeout( timeout );
		callback();
	};

	const onTransitionEnd = ( event: TransitionEvent ) => {
		if ( event.target === element && event.propertyName === 'height' ) {
			complete();
		}
	};

	const timeout = window.setTimeout( complete, 450 );
	element.addEventListener( 'transitionend', onTransitionEnd );
}

function setExpanded(
	button: HTMLButtonElement,
	panel: HTMLElement,
	expanded: boolean
) {
	button.setAttribute( 'aria-expanded', String( expanded ) );

	if ( prefersReducedMotion() ) {
		panel.hidden = ! expanded;
		panel.style.height = expanded ? 'auto' : '0px';
		return;
	}

	if ( expanded ) {
		panel.hidden = false;
		panel.style.height = '0px';

		window.requestAnimationFrame( () => {
			panel.style.height = `${ panel.scrollHeight }px`;
			afterHeightTransition( panel, () => {
				if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
					panel.style.height = 'auto';
				}
			} );
		} );
		return;
	}

	panel.style.height = `${ panel.scrollHeight }px`;
	window.requestAnimationFrame( () => {
		panel.style.height = '0px';
		afterHeightTransition( panel, () => {
			if ( button.getAttribute( 'aria-expanded' ) === 'false' ) {
				panel.hidden = true;
			}
		} );
	} );
}

function moveFocus(
	button: HTMLButtonElement,
	direction: 'first' | 'last' | 'next' | 'previous'
) {
	const buttons = Array.from(
		document.querySelectorAll< HTMLButtonElement >(
			'.skvn-accordion__trigger'
		)
	);
	const currentIndex = buttons.indexOf( button );

	if ( currentIndex < 0 || buttons.length < 2 ) {
		return;
	}

	const targetIndex = {
		first: 0,
		last: buttons.length - 1,
		next: ( currentIndex + 1 ) % buttons.length,
		previous: ( currentIndex - 1 + buttons.length ) % buttons.length,
	}[ direction ];

	buttons[ targetIndex ]?.focus();
}

document
	.querySelectorAll< HTMLElement >( '.skvn-accordion' )
	.forEach( ( accordion ) => {
		const heading = accordion.querySelector< HTMLElement >(
			'.skvn-accordion__heading'
		);
		const panel = accordion.querySelector< HTMLElement >(
			'.skvn-accordion__panel'
		);

		if (
			! heading ||
			! panel ||
			heading.querySelector( '.skvn-accordion__trigger' )
		) {
			return;
		}

		accordionId += 1;
		const panelId = panel.id || `skvn-accordion-panel-${ accordionId }`;
		const button = document.createElement( 'button' );

		button.className = 'skvn-accordion__trigger';
		button.type = 'button';
		button.setAttribute( 'aria-controls', panelId );
		button.setAttribute( 'aria-expanded', 'false' );

		while ( heading.firstChild ) {
			button.appendChild( heading.firstChild );
		}

		heading.appendChild( button );
		panel.id = panelId;
		panel.hidden = true;
		panel.style.height = '0px';
		accordion.classList.add( 'skvn-accordion--enhanced' );

		button.addEventListener( 'click', () => {
			setExpanded(
				button,
				panel,
				button.getAttribute( 'aria-expanded' ) !== 'true'
			);
		} );

		button.addEventListener( 'keydown', ( event ) => {
			const direction = {
				ArrowDown: 'next',
				ArrowUp: 'previous',
				Home: 'first',
				End: 'last',
			}[ event.key ] as
				| 'first'
				| 'last'
				| 'next'
				| 'previous'
				| undefined;

			if ( ! direction ) {
				return;
			}

			event.preventDefault();
			moveFocus( button, direction );
		} );
	} );
