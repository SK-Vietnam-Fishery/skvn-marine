import { canUseDesignSidebar, DesignFallback, DesignShortcut } from '../design-sidebar/navigation';
import { CategoryDesignControls } from './design';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { useEffect, useMemo, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { GAP_OPTIONS } from './constants';
import { CategoryDetailsPanel } from './details';
import { useCategoryData, invalidateCategoryData } from './data';
import { CategoryPicker } from './picker';
import { CategoryGridPreview } from './preview';
import { startCategorySaveCoordinator } from './save-coordinator';
import { SelectedCategoryList } from './selected-list';
import type {
	CategoryGridAttributes,
	CategorySelection,
} from './types';

type EditProps = {
	clientId: string;
	attributes: CategoryGridAttributes;
	setAttributes: ( attributes: Partial< CategoryGridAttributes > ) => void;
};

function normalizeSelection( raw: unknown ): CategorySelection[] {
	if ( ! Array.isArray( raw ) ) {
		return [];
	}

	const seen = new Set< number >();
	const items: CategorySelection[] = [];

	raw.forEach( ( entry ) => {
		const candidate =
			typeof entry === 'number' || typeof entry === 'string'
				? { termId: Number( entry ) }
				: ( entry as Partial< CategorySelection > );

		const termId = Number( candidate?.termId );

		if ( ! Number.isFinite( termId ) || termId <= 0 || seen.has( termId ) ) {
			return;
		}

		seen.add( termId );

		items.push( {
			termId,
			destination: candidate?.destination === 'custom' ? 'custom' : 'archive',
			customUrl:
				typeof candidate?.customUrl === 'string' ? candidate.customUrl : '',
		} );
	} );

	return items;
}

export function Edit( { attributes, setAttributes, clientId }: EditProps ) {
	const selection = useMemo(
		() => normalizeSelection( attributes.categories ),
		[ attributes.categories ]
	);
	const termIds = useMemo(
		() => selection.map( ( item ) => item.termId ),
		[ selection ]
	);

	const data = useCategoryData( termIds );
	const records = useMemo(
		() => new Map( data.ordered.map( ( record ) => [ record.id, record ] ) ),
		[ data.ordered ]
	);

	const [ pickerOpen, setPickerOpen ] = useState( false );
	const [ detailsTermId, setDetailsTermId ] = useState< number | null >( null );

	useEffect( () => startCategorySaveCoordinator(), [] );

	function commitSelection( orderedIds: number[] ) {
		const existing = new Map( selection.map( ( item ) => [ item.termId, item ] ) );
		const seen = new Set< number >();

		const next: CategorySelection[] = [];

		orderedIds.forEach( ( termId ) => {
			const id = Number( termId );

			if ( ! Number.isFinite( id ) || id <= 0 || seen.has( id ) ) {
				return;
			}

			seen.add( id );
			next.push(
				existing.get( id ) || {
					termId: id,
					destination: 'archive',
					customUrl: '',
				}
			);
		} );

		setAttributes( { categories: next } );
		setPickerOpen( false );
		setDetailsTermId( null );
		invalidateCategoryData();
	}

	const blockProps = useBlockProps( {
		className: 'skvn-category-grid-block',
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody
					initialOpen
					title={ __( 'Categories', 'skvn-marine-blocks' ) }
				>
					{ detailsTermId !== null ? (
						<CategoryDetailsPanel
							onClose={ () => setDetailsTermId( null ) }
							record={ records.get( detailsTermId ) }
							termId={ detailsTermId }
						/>
					) : (
						<SelectedCategoryList
							items={ selection }
							onChange={ ( items ) =>
								setAttributes( { categories: items } )
							}
							onOpenDetails={ setDetailsTermId }
							onPickerOpen={ () => setPickerOpen( true ) }
							records={ records }
						/>
					) }
				</PanelBody>

				<PanelBody
					initialOpen={ false }
					title={ __( 'Layout', 'skvn-marine-blocks' ) }
				>
					<RangeControl
						help={ __(
							'Desired columns in a wide container. The grid also drops columns when the block container itself is narrow.',
							'skvn-marine-blocks'
						) }
						label={ __( 'Columns — wide container', 'skvn-marine-blocks' ) }
						max={ 4 }
						min={ 1 }
						onChange={ ( columnsDesktop ) =>
							setAttributes( { columnsDesktop: ( columnsDesktop || 3 ) as 1 | 2 | 3 | 4 } )
						}
						value={ attributes.columnsDesktop ?? 3 }
					/>
					<RangeControl
						label={ __( 'Columns — medium container', 'skvn-marine-blocks' ) }
						max={ 4 }
						min={ 1 }
						onChange={ ( columnsTablet ) =>
							setAttributes( { columnsTablet: ( columnsTablet || 2 ) as 1 | 2 | 3 | 4 } )
						}
						value={ attributes.columnsTablet ?? 2 }
					/>
					<RangeControl
						label={ __( 'Columns — narrow container', 'skvn-marine-blocks' ) }
						max={ 2 }
						min={ 1 }
						onChange={ ( columnsMobile ) =>
							setAttributes( { columnsMobile: ( columnsMobile || 1 ) as 1 | 2 | 3 | 4 } )
						}
						value={ attributes.columnsMobile ?? 1 }
					/>
					<SelectControl
						label={ __( 'Gap', 'skvn-marine-blocks' ) }
						onChange={ ( gap ) =>
							setAttributes( {
								gap: gap as CategoryGridAttributes['gap'],
							} )
						}
						options={ GAP_OPTIONS }
						value={ attributes.gap || 'medium' }
					/>
					<p className="skvn-category-grid__columns-legend">
						{ __(
							'Rows are generated automatically; the grid never limits how many categories it shows.',
							'skvn-marine-blocks'
						) }
					</p>
				</PanelBody>

				<PanelBody
					initialOpen={ false }
					title={ __( 'Display', 'skvn-marine-blocks' ) }
				>
					<ToggleControl
						checked={ attributes.showImage !== false }
						label={ __( 'Show image', 'skvn-marine-blocks' ) }
						onChange={ ( showImage ) => setAttributes( { showImage } ) }
					/>
					<ToggleControl
						checked={ attributes.showName !== false }
						label={ __( 'Show name', 'skvn-marine-blocks' ) }
						onChange={ ( showName ) => setAttributes( { showName } ) }
					/>
					<ToggleControl
						checked={ attributes.showDescription === true }
						label={ __( 'Show description excerpt', 'skvn-marine-blocks' ) }
						onChange={ ( showDescription ) => setAttributes( { showDescription } ) }
					/>
					<ToggleControl
						checked={ attributes.showCount === true }
						label={ __( 'Show product count', 'skvn-marine-blocks' ) }
						onChange={ ( showCount ) => setAttributes( { showCount } ) }
					/>
					<ToggleControl
						checked={ attributes.showCta === true }
						label={ __( 'Show action label', 'skvn-marine-blocks' ) }
						onChange={ ( showCta ) => setAttributes( { showCta } ) }
					/>
					{ attributes.showCta === true && (
						<TextControl
							label={ __( 'Action label', 'skvn-marine-blocks' ) }
							onChange={ ( ctaLabel ) => setAttributes( { ctaLabel } ) }
							value={ attributes.ctaLabel || '' }
						/>
					) }
				</PanelBody>
				<PanelBody title={ __( 'Design', 'skvn-marine-blocks' ) }>
					{ canUseDesignSidebar() ? <DesignShortcut clientId={ clientId } /> : <DesignFallback name="skvn-marine/product-category-grid" update={ setAttributes }>{ ( group ) => <CategoryDesignControls attributes={ attributes } setAttributes={ setAttributes } group={ group } /> }</DesignFallback> }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<CategoryGridPreview attributes={ attributes } state={ data } />
				{ attributes.showImage === false && (
					<p className="skvn-category-grid__editor-note">
						{ __(
							'Images are turned off for this block. Cards show text only.',
							'skvn-marine-blocks'
						) }
					</p>
				) }
			</div>

			{ pickerOpen && (
				<CategoryPicker
					onApply={ commitSelection }
					onClose={ () => setPickerOpen( false ) }
					selectedIds={ termIds }
				/>
			) }

			{ data.missingIds.length > 0 && (
				<p className="skvn-category-grid__editor-note" role="status">
					{ __(
						'Some selected categories no longer exist. They are skipped on the front end and can be removed from the list.',
						'skvn-marine-blocks'
					) }
				</p>
			) }
		</>
	);
}
