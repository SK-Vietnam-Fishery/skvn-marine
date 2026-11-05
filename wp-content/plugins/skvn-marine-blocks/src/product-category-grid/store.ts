import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { saveCategoryDetails } from './api';
import { invalidateCategoryData } from './data-version';
import { notifySaveResults } from './notices';
import type {
	CategoryRecord,
	SaveDetailsRequestItem,
	SaveDetailsResult,
} from './types';

/**
 * Shared Category details drafts.
 *
 * One module-level store keeps drafts for every category term used in the
 * current editor session, so two blocks using the same term share one draft
 * and one pending save. It deliberately does not live in the Theme Design
 * store: entity drafts and page/theme settings have different save policies.
 *
 * Invariants:
 * - editing only changes the draft; nothing is written until an explicit save;
 * - a response only cleans the revision it was sent with, so typing during a
 *   pending save keeps the newer draft dirty;
 * - failed items stay dirty so a retry sends only what still needs saving.
 */

export type DraftStatus = 'clean' | 'dirty' | 'saving' | 'error' | 'conflict';

export type CategoryDraft = {
	termId: number;
	name: string;
	canEdit: boolean;
	description: string;
	thumbnailId: number;
	persistedDescription: string;
	persistedThumbnailId: number;
	dirty: { description: boolean; thumbnail: boolean };
	revision: number;
	pendingRevision: number | null;
	status: DraftStatus;
	error: string;
	conflict: { description: string; thumbnailId: number } | null;
};

type Listener = () => void;

const drafts = new Map< number, CategoryDraft >();
const listeners = new Set< Listener >();
let revisionCounter = 0;

function emit() {
	listeners.forEach( ( listener ) => listener() );
}

export function subscribeDrafts( listener: Listener ): () => void {
	listeners.add( listener );
	return () => listeners.delete( listener );
}

export function getDraft( termId: number ): CategoryDraft | undefined {
	return drafts.get( termId );
}

export function getDirtyTermIds(): number[] {
	return Array.from( drafts.values() )
		.filter(
			( draft ) =>
				( draft.dirty.description || draft.dirty.thumbnail ) &&
				draft.canEdit &&
				draft.pendingRevision === null
		)
		.map( ( draft ) => draft.termId );
}

export function hasUnsavedDrafts(): boolean {
	return Array.from( drafts.values() ).some(
		( draft ) => draft.dirty.description || draft.dirty.thumbnail
	);
}

function nextRevision(): number {
	revisionCounter += 1;
	return revisionCounter;
}

/** Create or refresh a draft from server data without losing local edits. */
export function ensureDraft( record: CategoryRecord ): CategoryDraft {
	const existing = drafts.get( record.id );

	const persistedDescription = record.description ?? existing?.persistedDescription ?? '';
	const persistedThumbnailId = record.thumbnail?.storedId ?? existing?.persistedThumbnailId ?? 0;

	if ( ! existing ) {
		const draft: CategoryDraft = {
			termId: record.id,
			name: record.name,
			canEdit: record.canEdit,
			description: persistedDescription,
			thumbnailId: persistedThumbnailId,
			persistedDescription,
			persistedThumbnailId,
			dirty: { description: false, thumbnail: false },
			revision: 0,
			pendingRevision: null,
			status: 'clean',
			error: '',
			conflict: null,
		};

		drafts.set( record.id, draft );
		return draft;
	}

	existing.name = record.name;
	existing.canEdit = record.canEdit;

	// Server data only replaces values that are not dirty; a dirty field keeps
	// the editor text (and its base) until it is saved or discarded.
	if ( ! existing.dirty.description && existing.pendingRevision === null ) {
		existing.persistedDescription = persistedDescription;
		existing.description = persistedDescription;
	}

	if ( ! existing.dirty.thumbnail && existing.pendingRevision === null ) {
		existing.persistedThumbnailId = persistedThumbnailId;
		existing.thumbnailId = persistedThumbnailId;
	}

	return existing;
}

function patch( termId: number, change: Partial< CategoryDraft > ) {
	const draft = drafts.get( termId );
	if ( ! draft ) {
		return;
	}
	Object.assign( draft, change );
	emit();
}

export function setDraftDescription( termId: number, value: string ) {
	const draft = drafts.get( termId );
	if ( ! draft || ! draft.canEdit ) {
		return;
	}

	draft.description = value;
	draft.dirty.description = value !== draft.persistedDescription;
	draft.revision = nextRevision();
	draft.conflict = null;

	if ( draft.status !== 'saving' ) {
		draft.status = draft.dirty.description || draft.dirty.thumbnail ? 'dirty' : 'clean';
		draft.error = '';
	}

	emit();
}

export function setDraftThumbnail( termId: number, thumbnailId: number ) {
	const draft = drafts.get( termId );
	if ( ! draft || ! draft.canEdit ) {
		return;
	}

	draft.thumbnailId = Math.max( 0, Math.trunc( thumbnailId ) );
	draft.dirty.thumbnail = draft.thumbnailId !== draft.persistedThumbnailId;
	draft.revision = nextRevision();

	if ( draft.status !== 'saving' ) {
		draft.status = draft.dirty.description || draft.dirty.thumbnail ? 'dirty' : 'clean';
		draft.error = '';
	}

	emit();
}

/** Drop local edits and return to the last known persisted values. */
export function discardDraft( termId: number ) {
	const draft = drafts.get( termId );
	if ( ! draft ) {
		return;
	}

	patch( termId, {
		description: draft.persistedDescription,
		thumbnailId: draft.persistedThumbnailId,
		dirty: { description: false, thumbnail: false },
		status: 'clean',
		error: '',
		conflict: null,
	} );
}

/**
 * Resolve a conflict by adopting the server value.
 *
 * The "current" value is the one returned with the conflict, not the local
 * base the editor loaded earlier, so both the draft and its base move forward.
 * Category data is invalidated so the card and the panel refresh together.
 */
export function reloadConflictValue( termId: number ) {
	const draft = drafts.get( termId );
	if ( ! draft || ! draft.conflict ) {
		return;
	}

	draft.description = draft.conflict.description;
	draft.thumbnailId = draft.conflict.thumbnailId;
	draft.persistedDescription = draft.conflict.description;
	draft.persistedThumbnailId = draft.conflict.thumbnailId;
	draft.dirty = { description: false, thumbnail: false };
	draft.conflict = null;
	draft.status = 'clean';
	draft.error = '';
	emit();
	invalidateCategoryData();
}

/**
 * Resolve a conflict by keeping the local draft on top of the current server
 * value. This is an explicit user action, never an automatic last-write-wins.
 */
export function keepDraftOverConflict( termId: number ) {
	const draft = drafts.get( termId );
	if ( ! draft || ! draft.conflict ) {
		return;
	}

	const dirty = {
		description: draft.description !== draft.conflict.description,
		thumbnail: draft.thumbnailId !== draft.conflict.thumbnailId,
	};

	patch( termId, {
		persistedDescription: draft.conflict.description,
		persistedThumbnailId: draft.conflict.thumbnailId,
		dirty,
		conflict: null,
		// The draft may already match the server value (nothing left to save),
		// so the status follows the real dirty flags instead of assuming dirty.
		status: dirty.description || dirty.thumbnail ? 'dirty' : 'clean',
		error: '',
	} );
}

function buildRequest( draft: CategoryDraft ): SaveDetailsRequestItem {
	const item: SaveDetailsRequestItem = {
		termId: draft.termId,
		revision: draft.revision,
	};

	if ( draft.dirty.description ) {
		item.descriptionDirty = true;
		item.description = draft.description;
		item.descriptionBase = draft.persistedDescription;
	}

	if ( draft.dirty.thumbnail ) {
		item.thumbnailDirty = true;
		item.thumbnailId = draft.thumbnailId;
		item.thumbnailBase = draft.persistedThumbnailId;
	}

	return item;
}

function applyResult( result: SaveDetailsResult ) {
	const draft = drafts.get( result.termId );
	if ( ! draft ) {
		return;
	}

	// The pending revision identifies this request. Clearing it matters even
	// when the draft has newer local edits, otherwise the draft would stay
	// "saving" forever and never be saved again.
	const isPendingResult = result.revision === draft.pendingRevision;
	const isCurrentRevision = result.revision === draft.revision;

	if ( isPendingResult ) {
		draft.pendingRevision = null;
	}

	if ( 'conflict' === result.status ) {
		draft.conflict = {
			description: result.description ?? draft.persistedDescription,
			thumbnailId: result.thumbnailId ?? draft.persistedThumbnailId,
		};
		draft.status = 'conflict';
		draft.error = result.message || '';
		return;
	}

	if ( 'error' === result.status ) {
		draft.status = 'error';
		draft.error = result.message || '';
		return;
	}

	if ( result.description !== undefined ) {
		draft.persistedDescription = result.description;
		if ( isCurrentRevision ) {
			draft.description = result.description;
			draft.dirty.description = false;
		}
	}

	if ( result.thumbnail && typeof result.thumbnail.storedId === 'number' ) {
		draft.persistedThumbnailId = result.thumbnail.storedId;
		if ( isCurrentRevision ) {
			draft.thumbnailId = result.thumbnail.storedId;
			draft.dirty.thumbnail = false;
		}
	}

	if ( isCurrentRevision ) {
		draft.error = '';
	}

	draft.status =
		draft.dirty.description || draft.dirty.thumbnail ? 'dirty' : 'clean';
}

export type SaveOptions = {
	/** Show a native notice with the per-item outcome. Default true. */
	notify?: boolean;
};

export type SaveOutcome = {
	saved: number;
	failed: number;
	results: SaveDetailsResult[];
};

/**
 * Save the given dirty drafts (or every dirty draft) once.
 *
 * Saves already pending are skipped so a retry cannot send the same revision
 * twice. Returns per-item outcomes for callers that need to branch.
 */
export async function saveDrafts(
	termIds?: number[],
	options: SaveOptions = {}
): Promise< SaveOutcome > {
	const targets = ( termIds ?? getDirtyTermIds() )
		.map( ( termId ) => drafts.get( termId ) )
		.filter( ( draft ): draft is CategoryDraft => Boolean( draft ) )
		.filter(
			( draft ) =>
				draft.canEdit &&
				draft.pendingRevision === null &&
				( draft.dirty.description || draft.dirty.thumbnail )
		);

	if ( targets.length === 0 ) {
		return { saved: 0, failed: 0, results: [] };
	}

	const requests: SaveDetailsRequestItem[] = [];

	targets.forEach( ( draft ) => {
		draft.pendingRevision = draft.revision;
		draft.status = 'saving';
		draft.error = '';
		requests.push( buildRequest( draft ) );
	} );
	emit();

	try {
		const response = await saveCategoryDetails( requests );
		const results = Array.isArray( response.results ) ? response.results : [];

		results.forEach( applyResult );

		// Defensive: a response that omits an item must not leave that draft
		// permanently stuck in the "saving" state.
		const answered = new Set( results.map( ( result ) => result.termId ) );

		targets.forEach( ( draft ) => {
			if ( answered.has( draft.termId ) ) {
				return;
			}

			if ( draft.pendingRevision !== null ) {
				draft.pendingRevision = null;
			}

			draft.status = 'error';
			draft.error = __(
				'The server did not confirm this category. Try saving again.',
				'skvn-marine-blocks'
			);
		} );

		emit();

		const storedSomething = results.some(
			( result ) => result.status === 'saved' || result.status === 'unchanged'
		);

		if ( storedSomething ) {
			// Counts, excerpts and thumbnails are server-derived, so the canvas
			// refetches instead of guessing the new value locally.
			invalidateCategoryData();
		}

		if ( options.notify !== false ) {
			notifySaveResults( results );
		}

		return {
			saved: response.saved ?? 0,
			failed: response.failed ?? 0,
			results,
		};
	} catch ( error ) {
		const message =
			error instanceof Error
				? error.message
				: String( error || 'Unknown error' );

		targets.forEach( ( draft ) => {
			draft.pendingRevision = null;
			draft.status = 'error';
			draft.error = message;
		} );
		emit();

		if ( options.notify !== false ) {
			notifySaveResults( [], message );
		}

		return { saved: 0, failed: requests.length, results: [] };
	}
}

/** Reset session state (used by tests and by editor teardown guards). */
export function resetDrafts() {
	drafts.clear();
	emit();
}

/** Subscribe a component to the shared draft store. */
export function useCategoryDraft( termId: number ): CategoryDraft | undefined {
	const [ , forceRender ] = useState( 0 );

	useEffect( () => subscribeDrafts( () => forceRender( ( value ) => value + 1 ) ), [] );

	return getDraft( termId );
}
