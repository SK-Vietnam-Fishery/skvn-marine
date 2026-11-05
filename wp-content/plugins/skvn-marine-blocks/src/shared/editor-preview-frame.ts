import { useEffect, useRef } from '@wordpress/element';
import './editor-preview-frame.css';

/** Editor DOM only: add just the spacing missing between adjacent frames. */
export function useEditorPreviewFrame() {
	const ref = useRef< HTMLElement >( null );
	useEffect( () => {
		const element = ref.current;
		const parent = element?.parentElement;
		const view = element?.ownerDocument.defaultView;
		if ( ! element || ! parent || ! view ) return;
		const original = element.style.marginBlockStart;
		const baseline = parseFloat( view.getComputedStyle( element ).marginBlockStart ) || 0;
		let adjustment = 0;
		let pending = 0;
		const measure = () => {
			pending = 0;
			const previous = element.previousElementSibling;
			const currentRect = element.getBoundingClientRect();
			const previousRect = previous?.getBoundingClientRect();
			// Side-by-side flex/grid children must not be pushed into a new row.
			const adjacent = previous?.classList.contains( 'skvn-editor-preview-frame' ) && previousRect &&
				Math.min( currentRect.right, previousRect.right ) - Math.max( currentRect.left, previousRect.left ) > 1;
			// Compare rendered borders, so existing margins and parent gap count.
			const gap = adjacent ? currentRect.top - previousRect.bottom : 10;
			// Bound the correction: overlapping/custom-positioned layouts need their
			// own layout fix, never an unbounded measurement feedback loop.
			const next = adjacent ? Math.min( 10, Math.max( 0, adjustment + 10 - gap ) ) : 0;
			if ( Math.abs( next - adjustment ) < 0.5 ) return;
			adjustment = next;
			element.style.marginBlockStart = adjustment ? `${ baseline + adjustment }px` : original;
			// A second measure also handles collapsed margins without guessing DOM.
			pending = view.requestAnimationFrame( measure );
		};
		const schedule = () => {
			if ( ! pending ) pending = view.requestAnimationFrame( measure );
		};
		const resize = new view.ResizeObserver( schedule );
		resize.observe( parent );
		resize.observe( element );
		const observePrevious = () => {
			resize.disconnect();
			resize.observe( parent );
			resize.observe( element );
			if ( element.previousElementSibling ) resize.observe( element.previousElementSibling );
			schedule();
		};
		const mutation = new view.MutationObserver( observePrevious );
		mutation.observe( parent, { childList: true, attributes: true } );
		view.addEventListener( 'resize', schedule );
		observePrevious();
		return () => {
			resize.disconnect();
			mutation.disconnect();
			view.removeEventListener( 'resize', schedule );
			if ( pending ) view.cancelAnimationFrame( pending );
			element.style.marginBlockStart = original;
		};
	}, [] );
	return ref;
}
