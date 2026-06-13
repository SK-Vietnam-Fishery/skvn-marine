import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

export function registerProductCollectionVariations() {
	registerBlockVariation( 'skvn-marine/product-collection', {
		name: 'skvn-product-grid',
		title: __( 'SKVN Product Grid', 'skvn-marine-blocks' ),
		description: __( 'Dynamic grid of WooCommerce products.', 'skvn-marine-blocks' ),
		attributes: {
			layout: 'grid',
			responsivePreset: '3-2-1',
		},
		isActive: [ 'layout' ],
		scope: [ 'inserter' ],
	} );

	registerBlockVariation( 'skvn-marine/product-collection', {
		name: 'skvn-product-carousel',
		title: __( 'SKVN Product Carousel', 'skvn-marine-blocks' ),
		description: __( 'Dynamic carousel of WooCommerce products.', 'skvn-marine-blocks' ),
		attributes: {
			layout: 'carousel',
			responsivePreset: '4-2-1',
		},
		isActive: [ 'layout' ],
		scope: [ 'inserter' ],
	} );
}
