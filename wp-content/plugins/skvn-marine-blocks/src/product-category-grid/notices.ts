import { dispatch } from '@wordpress/data';
import { __, _n, sprintf } from '@wordpress/i18n';
import type { SaveDetailsResult } from './types';

const CONTEXT = 'skvn-marine-category-grid';
let noticeCounter = 0;

function nextId( prefix: string ): string {
	noticeCounter += 1;
	return `${ prefix }-${ noticeCounter }`;
}

/** Create a native editor notice. */
export function notify(
	message: string,
	status: 'success' | 'info' | 'warning' | 'error' = 'info',
	id?: string
) {
	dispatch( 'core/notices' ).createNotice( status, message, {
		id: id ?? nextId( 'skvn-category-grid' ),
		type: 'snackbar',
		isDismissible: true,
		context: CONTEXT,
	} );
}

/**
 * Report the outcome of a category details save.
 *
 * The summary is a notice; the durable, per-category error stays on the item
 * in the details panel, so dismissing a notice never hides a failed save.
 */
export function notifySaveResults(
	results: SaveDetailsResult[],
	transportError = ''
) {
	if ( transportError ) {
		notify(
			sprintf(
				/* translators: %s: error message. */
				__( 'Category details could not be saved: %s', 'skvn-marine-blocks' ),
				transportError
			),
			'error'
		);
		return;
	}

	if ( results.length === 0 ) {
		return;
	}

	let saved = 0;
	let conflicts = 0;
	const failures: string[] = [];

	results.forEach( ( result ) => {
		if ( result.status === 'saved' || result.status === 'unchanged' ) {
			saved += 1;
			return;
		}

		if ( result.status === 'conflict' ) {
			conflicts += 1;
		}

		if ( result.message ) {
			failures.push( result.message );
		}

		if ( result.notice ) {
			notify( result.notice, 'warning' );
		}
	} );

	if ( conflicts > 0 ) {
		notify(
			sprintf(
				/* translators: %d: number of categories changed elsewhere. */
				__(
					'%d category was changed elsewhere. Your draft is kept; review or reload it in the details panel.',
					'skvn-marine-blocks'
				),
				conflicts
			),
			'warning'
		);
	}

	if ( failures.length > 0 ) {
		notify(
			sprintf(
				/* translators: %s: list of failure messages. */
				__( 'Some category details were not saved: %s', 'skvn-marine-blocks' ),
				failures.join( ' ' )
			),
			'error'
		);
	}

	if ( saved > 0 && failures.length === 0 && conflicts === 0 ) {
		notify(
			sprintf(
				/* translators: %d: number of saved categories. */
				_n(
					'Category details saved for %d category.',
					'Category details saved for %d categories.',
					saved,
					'skvn-marine-blocks'
				),
				saved
			),
			'success'
		);
	}
}
