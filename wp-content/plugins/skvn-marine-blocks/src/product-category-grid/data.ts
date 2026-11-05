import { useEffect, useMemo, useState } from '@wordpress/element';
import { fetchCategories } from './api';
import {
	getCategoryDataVersion,
	subscribeCategoryDataVersion,
} from './data-version';
import { ensureDraft } from './store';
import type { CategoryRecord } from './types';

/**
 * Small read cache for the editor.
 *
 * The block never stores category snapshots, so the canvas resolves the
 * selected IDs from the REST endpoint. Fetches are batched per ID set and
 * invalidated after a save/apply so counts, thumbnails and excerpts refresh.
 */

type State = {
	status: 'idle' | 'loading' | 'ready' | 'error';
	records: CategoryRecord[];
	missingIds: number[];
	error: string;
	unavailable: boolean;
};

export { invalidateCategoryData } from './data-version';

export function useCategoryData( termIds: number[] ) {
	const key = useMemo( () => termIds.join( ',' ), [ termIds ] );
	const [ localVersion, setLocalVersion ] = useState( getCategoryDataVersion() );
	const [ state, setState ] = useState< State >( {
		status: 'idle',
		records: [],
		missingIds: [],
		error: '',
		unavailable: false,
	} );

	useEffect(
		() => subscribeCategoryDataVersion( () => setLocalVersion( getCategoryDataVersion() ) ),
		[]
	);

	useEffect( () => {
		if ( ! key ) {
			setState( {
				status: 'idle',
				records: [],
				missingIds: [],
				error: '',
				unavailable: false,
			} );
			return;
		}

		let cancelled = false;
		const ids = key
			.split( ',' )
			.map( ( value ) => Number( value ) )
			.filter( ( value ) => Number.isFinite( value ) && value > 0 );

		setState( ( current ) => ( { ...current, status: 'loading', error: '' } ) );

		fetchCategories( {
			include: ids,
			perPage: Math.min( 100, Math.max( 1, ids.length ) ),
			withDetails: true,
		} )
			.then( ( response ) => {
				if ( cancelled ) {
					return;
				}

				const records = response.items || [];
				const found = new Set( records.map( ( record ) => record.id ) );

				records.forEach( ( record ) => ensureDraft( record ) );

				setState( {
					status: 'ready',
					records,
					missingIds: ids.filter( ( id ) => ! found.has( id ) ),
					error: '',
					unavailable: false,
				} );
			} )
			.catch( ( error: unknown ) => {
				if ( cancelled ) {
					return;
				}

				const message =
					error instanceof Error
						? error.message
						: String( error || 'Unknown error' );
				const unavailable = /woocommerce|product categories are not available/i.test(
					message
				);

				setState( {
					status: 'error',
					records: [],
					missingIds: [],
					error: message,
					unavailable,
				} );
			} );

		return () => {
			cancelled = true;
		};
	}, [ key, localVersion ] );

	const ordered = useMemo( () => {
		const byId = new Map( state.records.map( ( record ) => [ record.id, record ] ) );
		return key
			.split( ',' )
			.filter( Boolean )
			.map( ( value ) => byId.get( Number( value ) ) )
			.filter( ( record ): record is CategoryRecord => Boolean( record ) );
	}, [ key, state.records ] );

	return { ...state, ordered };
}
