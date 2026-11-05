import { select, subscribe } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { notify } from './notices';
import { getDirtyTermIds, hasUnsavedDrafts, saveDrafts } from './store';

/**
 * Explicit page-save coordination for Category details.
 *
 * Scope is narrow on purpose:
 * - an explicit Save/Update/Publish flushes the dirty category drafts;
 * - periodic/background autosave never flushes them;
 * - page attributes and category entities stay separate writes, so a failure
 *   in one never reports the other as saved.
 *
 * This uses public `core/editor` selectors only (no DOM patching of the Save
 * button). It is best-effort: the runtime behaviour still needs the CG-UX-03
 * onsite evidence described in `docs/testing/product-category-grid-1.3.20.md`.
 */

type EditorSelectors = {
	isSavingPost?: () => boolean;
	isAutosavingPost?: () => boolean;
};

let started = false;
let wasSaving = false;
let flushInFlight = false;
let consumers = 0;
let teardown: ( () => void ) | null = null;

/** Save every dirty draft once. Safe to call while a flush is running. */
export async function flushDirtyDrafts( source: 'page-save' | 'manual' = 'manual' ) {
	if ( flushInFlight ) {
		return;
	}

	if ( getDirtyTermIds().length === 0 ) {
		return;
	}

	flushInFlight = true;

	try {
		await saveDrafts( undefined, { notify: true } );
	} finally {
		flushInFlight = false;
	}

	if ( source === 'page-save' && hasUnsavedDrafts() ) {
		// A newer draft was typed while saving, or an item failed. The page may
		// already be saved, so say what is still pending instead of implying a
		// single atomic save.
		notify(
			__(
				'The page was saved, but some category details are still unsaved. Review the Category details panel.',
				'skvn-marine-blocks'
			),
			'warning'
		);
	}
}

function handleBeforeUnload( event: BeforeUnloadEvent ) {
	if ( ! hasUnsavedDrafts() ) {
		return;
	}

	event.preventDefault();
	event.returnValue = '';
}

/**
 * Start the coordinator once per editor session.
 *
 * Reference counted: the first consumer installs the subscriptions, the last
 * one removes them, so deleting one block cannot silently stop flushing for
 * other category grids on the page.
 *
 * @return Cleanup function.
 */
export function startCategorySaveCoordinator(): () => void {
	consumers += 1;

	if ( ! started ) {
		started = true;

		const unsubscribe = subscribe( () => {
			const editor = select( 'core/editor' ) as EditorSelectors | undefined;

			if ( ! editor || typeof editor.isSavingPost !== 'function' ) {
				return;
			}

			const saving = Boolean( editor.isSavingPost() );
			const autosaving =
				typeof editor.isAutosavingPost === 'function'
					? Boolean( editor.isAutosavingPost() )
					: false;

			if ( saving && ! autosaving && ! wasSaving ) {
				void flushDirtyDrafts( 'page-save' );
			}

			wasSaving = saving;
		} );

		if ( typeof window !== 'undefined' ) {
			window.addEventListener( 'beforeunload', handleBeforeUnload );
		}

		teardown = () => {
			unsubscribe();
			started = false;
			wasSaving = false;

			if ( typeof window !== 'undefined' ) {
				window.removeEventListener( 'beforeunload', handleBeforeUnload );
			}
		};
	}

	return () => {
		consumers = Math.max( 0, consumers - 1 );

		if ( consumers === 0 && teardown ) {
			teardown();
			teardown = null;
		}
	};
}
