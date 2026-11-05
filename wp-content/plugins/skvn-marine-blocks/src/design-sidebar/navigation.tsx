import { Button } from '@wordpress/components';
import { createReduxStore, dispatch, register } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import type { DesignGroup } from '../collection/controls';

// Session UI only. Block attributes remain in core/block-editor.
export const designStore = createReduxStore( 'skvn-marine/design-sidebar', {
	reducer( state = { tab: 'selected', group: 'title', request: 0 }, action ) {
		if ( action.type === 'OPEN' ) return { tab: 'selected', group: action.group, request: state.request + 1 };
		if ( action.type === 'TAB' ) return { ...state, tab: action.tab };
		if ( action.type === 'GROUP' ) return { ...state, group: action.group };
		return state;
	},
	actions: {
		open: ( group: DesignGroup ) => ( { type: 'OPEN', group } ),
		tab: ( tab: string ) => ( { type: 'TAB', tab } ),
		group: ( group: DesignGroup ) => ( { type: 'GROUP', group } ),
	},
	selectors: { getState: ( state ) => state },
} );
register( designStore );

export function openDesign( clientId: string, group: DesignGroup = 'title' ) {
	dispatch( 'core/block-editor' ).selectBlock( clientId );
	dispatch( designStore ).open( group );
	dispatch( 'core/edit-post' ).openGeneralSidebar( 'skvn-marine-design/design' );
}

export function DesignShortcut( { clientId }: { clientId: string } ) {
	return <>
		<Button variant="secondary" onClick={ () => openDesign( clientId ) }>{ __( 'Open design settings', 'skvn-marine-blocks' ) }</Button>
		<Button variant="tertiary" onClick={ () => openDesign( clientId, 'action' ) }>{ __( 'Edit button style', 'skvn-marine-blocks' ) }</Button>
		<p>{ __( 'Image, Title, Details and Action styles apply to this block. Save them with the page.', 'skvn-marine-blocks' ) }</p>
	</>;
}
