import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

export function registerPostCollectionVariations() {
	registerBlockVariation( 'skvn-marine/post-collection', {
		name: 'skvn-post-grid',
		title: __( 'SKVN Post Grid', 'skvn-marine-blocks' ),
		description: __( 'Dynamic grid of live posts.', 'skvn-marine-blocks' ),
		attributes: {
			layout: 'grid',
			responsivePreset: '3-2-1',
		},
		isActive: [ 'layout' ],
		scope: [ 'inserter' ],
	} );

	registerBlockVariation( 'skvn-marine/post-collection', {
		name: 'skvn-post-carousel',
		title: __( 'SKVN Post Carousel', 'skvn-marine-blocks' ),
		description: __( 'Dynamic carousel of live posts.', 'skvn-marine-blocks' ),
		attributes: {
			layout: 'carousel',
			responsivePreset: '3-2-1',
		},
		isActive: [ 'layout' ],
		scope: [ 'inserter' ],
	} );
}
