import {
	InspectorControls,
	RichText,
	useBlockProps,
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import {
	LAYOUT_OPTIONS,
	ORDER_MODE_OPTIONS,
	RESPONSIVE_PRESET_OPTIONS,
} from './constants';
import type {
	CollectionAttributes,
	CollectionLayout,
	CollectionOrderMode,
	ResponsivePreset,
} from './types';

type CollectionEditProps = {
	attributes: CollectionAttributes;
	contentType: 'post' | 'product';
	setAttributes: ( attributes: Partial< CollectionAttributes > ) => void;
};

export function CollectionEdit( {
	attributes,
	contentType,
	setAttributes,
}: CollectionEditProps ) {
	const blockProps = useBlockProps( {
		className: [
			'skvn-collection',
			`skvn-collection--${ contentType }`,
			`skvn-collection--${ attributes.layout || 'grid' }`,
			'skvn-collection--editor-preview',
		]
			.filter( Boolean )
			.join( ' ' ),
	} );

	return (
		<section { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Content', 'skvn-marine-blocks' ) }>
					<TextControl
						label={ __( 'Accessible label', 'skvn-marine-blocks' ) }
						onChange={ ( accessibleLabel ) =>
							setAttributes( { accessibleLabel } )
						}
						value={ attributes.accessibleLabel || '' }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Query', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Order', 'skvn-marine-blocks' ) }
						onChange={ ( orderMode ) =>
							setAttributes( {
								orderMode: orderMode as CollectionOrderMode,
							} )
						}
						options={ ORDER_MODE_OPTIONS }
						value={ attributes.orderMode || 'newest' }
					/>
					<RangeControl
						label={ __( 'Items to show', 'skvn-marine-blocks' ) }
						max={ attributes.layout === 'carousel' ? 10 : 9 }
						min={ 1 }
						onChange={ ( itemsToShow ) =>
							setAttributes( { itemsToShow: itemsToShow || 3 } )
						}
						value={ attributes.itemsToShow || 3 }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Layout', 'skvn-marine-blocks' ) }>
					<SelectControl
						label={ __( 'Layout', 'skvn-marine-blocks' ) }
						onChange={ ( layout ) =>
							setAttributes( {
								layout: layout as CollectionLayout,
							} )
						}
						options={ LAYOUT_OPTIONS }
						value={ attributes.layout || 'grid' }
					/>
					<SelectControl
						label={ __( 'Responsive preset', 'skvn-marine-blocks' ) }
						onChange={ ( responsivePreset ) =>
							setAttributes( {
								responsivePreset:
									responsivePreset as ResponsivePreset,
							} )
						}
						options={ RESPONSIVE_PRESET_OPTIONS }
						value={ attributes.responsivePreset || '3-2-1' }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Card', 'skvn-marine-blocks' ) } />
				<PanelBody title={ __( 'Actions', 'skvn-marine-blocks' ) } />
				<PanelBody title={ __( 'Advanced', 'skvn-marine-blocks' ) } />
			</InspectorControls>
			<RichText
				className="skvn-collection__heading"
				onChange={ ( heading ) => setAttributes( { heading } ) }
				placeholder={ __( 'Collection heading...', 'skvn-marine-blocks' ) }
				tagName="h2"
				value={ attributes.heading }
			/>
			<RichText
				className="skvn-collection__intro"
				onChange={ ( intro ) => setAttributes( { intro } ) }
				placeholder={ __( 'Optional intro...', 'skvn-marine-blocks' ) }
				tagName="p"
				value={ attributes.intro }
			/>
			<div className="skvn-collection__notice">
				{ contentType === 'post'
					? __(
							'Live post grid renders on the frontend.',
							'skvn-marine-blocks'
					  )
					: __(
							'Product collection is guarded for WooCommerce availability.',
							'skvn-marine-blocks'
					  ) }
			</div>
		</section>
	);
}
