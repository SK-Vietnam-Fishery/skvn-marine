import { Button, SelectControl, TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { ACTION_DESTINATION_OPTIONS } from './constants';
import type { CategoryRecord, CategorySelection } from './types';

/**
 * Ordered selection list.
 *
 * Ordering contract from the design lock: desktop reorders by dragging the
 * handle, keyboard reorders with Up/Down, and touch devices use the Move
 * up/Move down buttons. The order stored in the block is the render order.
 */

export function SelectedCategoryList( {
	items,
	records,
	onChange,
	onOpenDetails,
	onPickerOpen,
}: {
	items: CategorySelection[];
	records: Map< number, CategoryRecord >;
	onChange: ( items: CategorySelection[] ) => void;
	onOpenDetails: ( termId: number ) => void;
	onPickerOpen: () => void;
} ) {
	const [ expandedTermId, setExpandedTermId ] = useState< number | null >( null );
	const [ draggingIndex, setDraggingIndex ] = useState< number | null >( null );

	// Items are keyed by term ID, so a reorder moves the same DOM node and the
	// focused control keeps its focus without extra bookkeeping.
	function move( from: number, to: number ) {
		if ( to < 0 || to >= items.length || from === to ) {
			return;
		}

		const next = [ ...items ];
		const [ moved ] = next.splice( from, 1 );
		next.splice( to, 0, moved );
		onChange( next );
	}

	function updateItem( termId: number, patch: Partial< CategorySelection > ) {
		onChange(
			items.map( ( item ) =>
				item.termId === termId ? { ...item, ...patch } : item
			)
		);
	}

	function removeItem( termId: number ) {
		onChange( items.filter( ( item ) => item.termId !== termId ) );
	}

	if ( items.length === 0 ) {
		return (
			<div className="skvn-category-selection">
				<p className="skvn-category-selection__empty">
					{ __( 'No categories selected yet.', 'skvn-marine-blocks' ) }
				</p>
				<Button onClick={ onPickerOpen } variant="primary">
					{ __( 'Add categories', 'skvn-marine-blocks' ) }
				</Button>
			</div>
		);
	}

	return (
		<div className="skvn-category-selection">
			<ol className="skvn-category-selection__list">
				{ items.map( ( item, index ) => {
					const record = records.get( item.termId );
					const label = record
						? record.name
						: sprintf(
								/* translators: %d: category term ID. */
								__( 'Category #%d', 'skvn-marine-blocks' ),
								item.termId
						  );
					const expanded = expandedTermId === item.termId;

					return (
						<li
							className={
								'skvn-category-selection__item' +
								( draggingIndex === index ? ' is-dragging' : '' ) +
								( record && record.count < 1 ? ' is-empty' : '' )
							}
							key={ item.termId }
							onDragOver={ ( event ) => event.preventDefault() }
							onDrop={ ( event ) => {
								event.preventDefault();

								if ( draggingIndex !== null ) {
									move( draggingIndex, index );
								}

								setDraggingIndex( null );
							} }
						>
							<div className="skvn-category-selection__row">
								<span
									aria-label={ __(
										'Reorder category: drag, or press the up and down arrow keys',
										'skvn-marine-blocks'
									) }
									className="skvn-category-selection__handle"
									draggable
									onDragEnd={ () => setDraggingIndex( null ) }
									onDragStart={ ( event ) => {
										setDraggingIndex( index );
										event.dataTransfer.effectAllowed = 'move';
										event.dataTransfer.setData( 'text/plain', String( index ) );
									} }
									onKeyDown={ ( event ) => {
										if ( event.key === 'ArrowUp' ) {
											event.preventDefault();
											move( index, index - 1 );
										}

										if ( event.key === 'ArrowDown' ) {
											event.preventDefault();
											move( index, index + 1 );
										}
									} }
									role="button"
									tabIndex={ 0 }
									title={ __( 'Drag to reorder', 'skvn-marine-blocks' ) }
								>
									⠿
								</span>

								<span className="skvn-category-selection__position">{ index + 1 }</span>

								<span className="skvn-category-selection__name">
									{ label }
									{ record && record.path.length > 1 && (
										<span className="skvn-category-selection__path">
											{ record.path.slice( 0, -1 ).join( ' › ' ) }
										</span>
									) }
									{ record && record.count < 1 && (
										<span className="skvn-category-selection__badge">
											{ __( 'Hidden — no products', 'skvn-marine-blocks' ) }
										</span>
									) }
									{ ! record && (
										<span className="skvn-category-selection__badge">
											{ __( 'Missing category', 'skvn-marine-blocks' ) }
										</span>
									) }
								</span>

								<span className="skvn-category-selection__tools">
									<Button
										aria-label={ __( 'Move up', 'skvn-marine-blocks' ) }
										disabled={ index === 0 }
										onClick={ () => move( index, index - 1 ) }
										size="small"
										variant="tertiary"
									>
										↑
									</Button>
									<Button
										aria-label={ __( 'Move down', 'skvn-marine-blocks' ) }
										disabled={ index === items.length - 1 }
										onClick={ () => move( index, index + 1 ) }
										size="small"
										variant="tertiary"
									>
										↓
									</Button>
								</span>
							</div>

							<div className="skvn-category-selection__actions">
								<Button
									onClick={ () => onOpenDetails( item.termId ) }
									size="small"
									variant="link"
								>
									{ __( 'Category details', 'skvn-marine-blocks' ) }
								</Button>
								<Button
									onClick={ () =>
										setExpandedTermId( expanded ? null : item.termId )
									}
									size="small"
									variant="link"
								>
									{ expanded
										? __( 'Hide link', 'skvn-marine-blocks' )
										: __( 'Link', 'skvn-marine-blocks' ) }
								</Button>
								<Button
									onClick={ () => removeItem( item.termId ) }
									size="small"
									variant="link"
								>
									{ __( 'Remove', 'skvn-marine-blocks' ) }
								</Button>
							</div>

							{ expanded && (
								<div className="skvn-category-selection__link">
									<SelectControl
										label={ __( 'Destination', 'skvn-marine-blocks' ) }
										onChange={ ( destination ) =>
											updateItem( item.termId, {
												destination: destination === 'custom' ? 'custom' : 'archive',
											} )
										}
										options={ ACTION_DESTINATION_OPTIONS }
										value={ item.destination }
									/>
									{ item.destination === 'custom' && (
										<TextControl
											help={ __(
												'Custom links still respect the hide-empty rule: a category without catalog products stays hidden.',
												'skvn-marine-blocks'
											) }
											label={ __( 'Custom URL', 'skvn-marine-blocks' ) }
											onChange={ ( customUrl ) =>
												updateItem( item.termId, { customUrl } )
											}
											type="url"
											value={ item.customUrl }
										/>
									) }
								</div>
							) }
						</li>
					);
				} ) }
			</ol>

			<Button onClick={ onPickerOpen } variant="secondary">
				{ __( 'Add categories', 'skvn-marine-blocks' ) }
			</Button>
		</div>
	);
}
