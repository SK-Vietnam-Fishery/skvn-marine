import {
	Button,
	CheckboxControl,
	Modal,
	SearchControl,
	Spinner,
} from '@wordpress/components';
import { useEffect, useMemo, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { fetchCategories } from './api';
import type { CategoryRecord } from './types';

/**
 * Category picker — design lock 2026-10-28.
 *
 * Kept intentionally close to the approved baseline: dialog, search/hierarchy,
 * manual selection with Apply/Cancel, and an explicit notice for categories
 * without catalog products. Selection is a draft: nothing is written to the
 * block until Apply.
 */

const PER_PAGE = 50;

export type CategoryPickerProps = {
	selectedIds: number[];
	onApply: ( orderedIds: number[] ) => void;
	onClose: () => void;
};

export function CategoryPicker( {
	selectedIds,
	onApply,
	onClose,
}: CategoryPickerProps ) {
	const [ pending, setPending ] = useState< number[] >( selectedIds );
	const [ search, setSearch ] = useState( '' );
	const [ debouncedSearch, setDebouncedSearch ] = useState( '' );
	const [ onlyWithProducts, setOnlyWithProducts ] = useState( false );
	const [ items, setItems ] = useState< CategoryRecord[] >( [] );
	const [ page, setPage ] = useState( 1 );
	const [ hasMore, setHasMore ] = useState( false );
	const [ total, setTotal ] = useState( 0 );
	const [ loading, setLoading ] = useState( false );
	const [ error, setError ] = useState( '' );

	useEffect( () => {
		const timer = setTimeout( () => setDebouncedSearch( search.trim() ), 300 );
		return () => clearTimeout( timer );
	}, [ search ] );

	useEffect( () => {
		let cancelled = false;

		setLoading( true );
		setError( '' );

		fetchCategories( {
			search: debouncedSearch,
			onlyWithProducts,
			page: 1,
			perPage: PER_PAGE,
		} )
			.then( ( response ) => {
				if ( cancelled ) {
					return;
				}

				setItems( response.items || [] );
				setHasMore( Boolean( response.hasMore ) );
				setTotal( response.total || 0 );
				setPage( 1 );
			} )
			.catch( ( caught: unknown ) => {
				if ( cancelled ) {
					return;
				}

				setError(
					caught instanceof Error ? caught.message : String( caught || '' )
				);
				setItems( [] );
				setHasMore( false );
				setTotal( 0 );
			} )
			.finally( () => {
				if ( ! cancelled ) {
					setLoading( false );
				}
			} );

		return () => {
			cancelled = true;
		};
	}, [ debouncedSearch, onlyWithProducts ] );

	function loadMore() {
		const nextPage = page + 1;

		setLoading( true );

		fetchCategories( {
			search: debouncedSearch,
			onlyWithProducts,
			page: nextPage,
			perPage: PER_PAGE,
		} )
			.then( ( response ) => {
				setItems( ( current ) => [ ...current, ...( response.items || [] ) ] );
				setHasMore( Boolean( response.hasMore ) );
				setPage( nextPage );
			} )
			.catch( ( caught: unknown ) => {
				setError(
					caught instanceof Error ? caught.message : String( caught || '' )
				);
			} )
			.finally( () => setLoading( false ) );
	}

	const pendingSet = useMemo( () => new Set( pending ), [ pending ] );

	function toggle( termId: number, checked: boolean ) {
		setPending( ( current ) => {
			if ( checked ) {
				return current.includes( termId ) ? current : [ ...current, termId ];
			}

			return current.filter( ( id ) => id !== termId );
		} );
	}

	/**
	 * Apply keeps the order the list already had.
	 *
	 * Reordering is owned by the selected list (drag / keyboard / touch), so
	 * unchecking and rechecking inside the dialog must not silently move a card.
	 * Only new picks are appended.
	 */
	function applySelection() {
		const kept = selectedIds.filter( ( id ) => pendingSet.has( id ) );
		const added = pending.filter( ( id ) => ! selectedIds.includes( id ) );

		onApply( [ ...kept, ...added ] );
	}

	return (
		<Modal
			className="skvn-category-picker"
			onRequestClose={ onClose }
			size="large"
			title={ __( 'Choose product categories', 'skvn-marine-blocks' ) }
		>
			<p className="skvn-category-picker__hint">
				{ __(
					'Select individually. Choosing a parent does not select its children.',
					'skvn-marine-blocks'
				) }
			</p>

			<SearchControl
				label={ __( 'Search categories', 'skvn-marine-blocks' ) }
				onChange={ setSearch }
				placeholder={ __( 'Search by name or parent…', 'skvn-marine-blocks' ) }
				value={ search }
			/>

			<CheckboxControl
				checked={ onlyWithProducts }
				label={ __( 'Only categories with products', 'skvn-marine-blocks' ) }
				onChange={ setOnlyWithProducts }
			/>

			{ error && (
				<div className="skvn-category-picker__error" role="alert">
					{ error }
				</div>
			) }

			<div
				aria-busy={ loading }
				className="skvn-category-picker__results"
				role="group"
				aria-label={ __( 'Category results', 'skvn-marine-blocks' ) }
			>
				{ items.map( ( item ) => {
					const parents = item.path.slice( 0, -1 );

					return (
						<div className="skvn-category-picker__row" key={ item.id }>
							<CheckboxControl
								checked={ pendingSet.has( item.id ) }
								label={ item.name }
								onChange={ ( checked ) => toggle( item.id, checked ) }
							/>
							<div className="skvn-category-picker__meta">
								{ parents.length > 0 && (
									<span>{ parents.join( ' › ' ) }</span>
								) }
								<span>
									{ item.count > 0
										? sprintf(
												/* translators: %d: number of catalog products. */
												__( '%d products', 'skvn-marine-blocks' ),
												item.count
										  )
										: __( 'No catalog products', 'skvn-marine-blocks' ) }
								</span>
							</div>
						</div>
					);
				} ) }

				{ ! loading && items.length === 0 && ! error && (
					<p className="skvn-category-picker__empty">
						{ __( 'No categories match this search.', 'skvn-marine-blocks' ) }
					</p>
				) }

				{ loading && (
					<p className="skvn-category-picker__loading">
						<Spinner />
						{ __( 'Loading categories…', 'skvn-marine-blocks' ) }
					</p>
				) }
			</div>

			{ hasMore && ! loading && (
				<Button onClick={ loadMore } variant="secondary">
					{ __( 'Load more', 'skvn-marine-blocks' ) }
				</Button>
			) }

			{ total > 0 && (
				<p className="skvn-category-picker__total">
					{ sprintf(
						/* translators: 1: number of categories shown, 2: total matching categories. */
						__( '%1$d of %2$d categories', 'skvn-marine-blocks' ),
						items.length,
						total
					) }
				</p>
			) }

			<div className="skvn-category-picker__actions">
				<span className="skvn-category-picker__count">
					{ sprintf(
						/* translators: %d: number of categories selected in the dialog. */
						__( '%d selected', 'skvn-marine-blocks' ),
						pending.length
					) }
				</span>
				<Button onClick={ onClose } variant="tertiary">
					{ __( 'Cancel', 'skvn-marine-blocks' ) }
				</Button>
				<Button onClick={ applySelection } variant="primary">
					{ __( 'Apply selection', 'skvn-marine-blocks' ) }
				</Button>
			</div>
		</Modal>
	);
}
