import { Button, Notice, PanelBody, SelectControl, Spinner, TextControl } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

type Group = 'palette' | 'heading' | 'fonts';
type Values = Record< string, string | { size: string; weight: string } >;
type SavedGroup = { value: Values; revision: string; canEdit: boolean };
type Snapshot = {
	available: boolean;
	incompatible: boolean;
	settingsUrl: string;
	groups?: Record< Group, SavedGroup >;
	presets?: { label: string; value: string }[];
	scopes?: { label: string; value: string }[];
};
declare global { interface Window { skvnMarineDesign?: { url: string; nonce: string; woocommerceAvailable: boolean } } }

class DesignRequestError extends Error {
	code: string;
	current?: SavedGroup;
	canEdit?: boolean;
	constructor( message: string, code: string, current?: SavedGroup, canEdit?: boolean ) {
		super( message );
		this.code = code;
		this.current = current;
		this.canEdit = canEdit;
	}
}

async function request< T >( data: Record< string, string >, signal?: AbortSignal ): Promise< T > {
	const config = window.skvnMarineDesign;
	if ( ! config ) throw new Error( __( 'Design settings are unavailable. Reload the editor after updating the plugin.', 'skvn-marine-blocks' ) );
	const response = await fetch( config.url, {
		method: 'POST', credentials: 'same-origin', signal,
		body: new URLSearchParams( { action: 'skvn_marine_design_settings', nonce: config.nonce, ...data } ),
	} );
	let json;
	try { json = await response.json(); } catch {
		throw new DesignRequestError( __( 'The server response could not be read. Your draft is kept. Check your session before trying again.', 'skvn-marine-blocks' ), 'request_error', undefined, response.status === 403 ? false : undefined );
	}
	if ( ! response.ok || ! json.success ) throw new DesignRequestError(
		json.data?.message || __( 'Design settings could not be loaded or saved. Your draft is kept. Check your session and permissions.', 'skvn-marine-blocks' ),
		json.data?.code || 'request_error', json.data?.current || undefined,
		typeof json.data?.canEdit === 'boolean' ? json.data.canEdit : response.status === 403 ? false : undefined
	);
	return json.data;
}

export function useThemeDesign() {
	const [ snapshot, setSnapshot ] = useState< Snapshot | null >( null );
	const [ drafts, setDrafts ] = useState< Partial< Record< Group, Values > > >( {} );
	const [ error, setError ] = useState( '' );
	const [ statuses, setStatuses ] = useState< Partial< Record< Group, string > > >( {} );
	const [ pending, setPending ] = useState< Partial< Record< Group, boolean > > >( {} );
	const [ conflicts, setConflicts ] = useState< Partial< Record< Group, SavedGroup > > >( {} );
	useEffect( () => {
		const controller = new AbortController();
		request< Snapshot >( { operation: 'read' }, controller.signal ).then( ( result ) => {
			setSnapshot( result );
			if ( result.groups ) setDrafts( Object.fromEntries( Object.entries( result.groups ).map( ( [ key, group ] ) => [ key, group.value ] ) ) );
		} ).catch( ( failure ) => { if ( failure.name !== 'AbortError' ) setError( failure.message ); } );
		return () => controller.abort();
	}, [] );
	const dirty = ( group: Group ) => JSON.stringify( drafts[ group ] ) !== JSON.stringify( snapshot?.groups?.[ group ].value );
	const hasDraft = !! snapshot?.groups && ( [ 'palette', 'heading', 'fonts' ] as Group[] ).some( dirty );
	useEffect( () => {
		if ( ! hasDraft ) return;
		const warn = ( event: BeforeUnloadEvent ) => { event.preventDefault(); event.returnValue = ''; };
		window.addEventListener( 'beforeunload', warn );
		return () => window.removeEventListener( 'beforeunload', warn );
	}, [ hasDraft ] );
	const change = ( group: Group, value: Values ) => {
		setDrafts( ( previous ) => ( { ...previous, [ group ]: value } ) );
		setStatuses( ( previous ) => ( { ...previous, [ group ]: '' } ) );
	};
	const save = async ( group: Group ) => {
		if ( ! snapshot?.groups?.[ group ].canEdit || conflicts[ group ] || Object.values( pending ).some( Boolean ) ) return;
		setPending( ( previous ) => ( { ...previous, [ group ]: true } ) );
		try {
			const saved = await request< SavedGroup >( { operation: 'save', group, revision: snapshot.groups[ group ].revision, value: JSON.stringify( drafts[ group ] ) } );
			setSnapshot( ( previous ) => previous && ( { ...previous, groups: { ...previous.groups!, [ group ]: saved } } ) );
			setDrafts( ( previous ) => ( { ...previous, [ group ]: saved.value } ) );
			setStatuses( ( previous ) => ( { ...previous, [ group ]: __( 'Saved. Reload the editor to see the saved theme design in the canvas. The site uses saved settings.', 'skvn-marine-blocks' ) } ) );
		} catch ( failure ) {
			if ( failure instanceof DesignRequestError ) {
				if ( failure.code === 'conflict' && failure.current ) setConflicts( ( previous ) => ( { ...previous, [ group ]: failure.current } ) );
				if ( failure.canEdit === false ) setSnapshot( ( previous ) => previous?.groups ? ( { ...previous, groups: { ...previous.groups, [ group ]: { ...previous.groups[ group ], canEdit: false } } } ) : previous );
			}
			setStatuses( ( previous ) => ( { ...previous, [ group ]: failure instanceof Error ? failure.message : __( 'Save failed. Your draft is kept.', 'skvn-marine-blocks' ) } ) );
		} finally { setPending( ( previous ) => ( { ...previous, [ group ]: false } ) ); }
	};
	const discard = async ( group: Group ) => {
		setPending( ( previous ) => ( { ...previous, [ group ]: true } ) );
		try {
			const current = await request< Snapshot >( { operation: 'read' } );
			if ( ! current.groups?.[ group ] ) throw new Error( __( 'The theme integration is no longer available. Your draft is kept.', 'skvn-marine-blocks' ) );
			setSnapshot( ( previous ) => previous && ( { ...previous, groups: { ...previous.groups!, [ group ]: current.groups![ group ] } } ) );
			setDrafts( ( previous ) => ( { ...previous, [ group ]: current.groups![ group ].value } ) );
			setConflicts( ( previous ) => ( { ...previous, [ group ]: undefined } ) );
			setStatuses( ( previous ) => ( { ...previous, [ group ]: __( 'Draft discarded. Current saved values loaded for this group.', 'skvn-marine-blocks' ) } ) );
		} catch ( failure ) { setStatuses( ( previous ) => ( { ...previous, [ group ]: failure instanceof Error ? failure.message : __( 'Could not reload this group. Your draft is kept.', 'skvn-marine-blocks' ) } ) ); }
		finally { setPending( ( previous ) => ( { ...previous, [ group ]: false } ) ); }
	};
	const resolveConflict = ( group: Group, useCurrent: boolean ) => {
		const current = conflicts[ group ];
		if ( ! current ) return;
		setSnapshot( ( previous ) => previous?.groups ? ( { ...previous, groups: { ...previous.groups, [ group ]: current } } ) : previous );
		if ( useCurrent ) setDrafts( ( previous ) => ( { ...previous, [ group ]: current.value } ) );
		setConflicts( ( previous ) => ( { ...previous, [ group ]: undefined } ) );
		setStatuses( ( previous ) => ( { ...previous, [ group ]: useCurrent
			? __( 'Current saved values loaded for this group. Other drafts are kept.', 'skvn-marine-blocks' )
			: __( 'Your draft is kept against the reviewed saved values. Nothing was saved. Choose Save group when ready.', 'skvn-marine-blocks' ) } ) );
	};
	const refreshAccess = async ( group: Group ) => {
		if ( Object.values( pending ).some( Boolean ) ) return;
		setPending( ( previous ) => ( { ...previous, [ group ]: true } ) );
		try {
			const current = await request< Snapshot >( { operation: 'read' } );
			if ( ! current.available || ! current.groups?.[ group ] ) throw new Error( __( 'The theme integration is unavailable. Your draft is kept.', 'skvn-marine-blocks' ) );
			// Re-enable access only. Keep base revision so a later save still detects conflicts.
			setSnapshot( ( previous ) => previous?.groups ? ( { ...previous, groups: { ...previous.groups, [ group ]: { ...previous.groups[ group ], canEdit: current.groups![ group ].canEdit } } } ) : previous );
			setStatuses( ( previous ) => ( { ...previous, [ group ]: __( 'Permissions refreshed. Your draft and saved baseline are kept.', 'skvn-marine-blocks' ) } ) );
		} catch ( failure ) { setStatuses( ( previous ) => ( { ...previous, [ group ]: failure instanceof Error ? failure.message : __( 'Permissions could not be refreshed. Your draft is kept.', 'skvn-marine-blocks' ) } ) ); }
		finally { setPending( ( previous ) => ( { ...previous, [ group ]: false } ) ); }
	};
	return { snapshot, drafts, error, statuses, pending, conflicts, dirty, change, save, discard, resolveConflict, refreshAccess };
}

const labels: Record< Group, string > = {
	palette: __( 'Brand colors', 'skvn-marine-blocks' ),
	heading: __( 'Typography', 'skvn-marine-blocks' ),
	fonts: __( 'Fonts', 'skvn-marine-blocks' ),
};
const paletteLabels = {
	primary: __( 'Primary', 'skvn-marine-blocks' ), accent: __( 'Accent', 'skvn-marine-blocks' ),
	surface: __( 'Surface', 'skvn-marine-blocks' ), text: __( 'Text', 'skvn-marine-blocks' ),
};
export function ThemeDesign( { model }: { model: ReturnType< typeof useThemeDesign > } ) {
	const { snapshot, drafts, error, statuses, pending, conflicts, dirty, change, save, discard, resolveConflict, refreshAccess } = model;
	const busy = Object.values( pending ).some( Boolean );
	if ( error ) return <PanelBody><Notice status="error" isDismissible={ false }>{ error }</Notice></PanelBody>;
	if ( ! snapshot ) return <PanelBody><Spinner /><p>{ __( 'Loading plugin settings…', 'skvn-marine-blocks' ) }</p></PanelBody>;
	return <>
		<PanelBody>
			<h2>{ __( 'Plugin settings', 'skvn-marine-blocks' ) }</h2>
			<p>{ __( 'Selected block styles are saved with the page. Theme Design is saved separately by group and applies across the site where theme tokens are inherited.', 'skvn-marine-blocks' ) }</p>
			{ snapshot.settingsUrl && <Button href={ snapshot.settingsUrl } target="_blank" rel="noopener noreferrer" variant="link">{ __( 'Open plugin settings', 'skvn-marine-blocks' ) }</Button> }
			{ ! snapshot.available && <p>{ snapshot.incompatible ? __( 'Theme Design needs a compatible SKVN Marine integration. Your saved settings are retained.', 'skvn-marine-blocks' ) : __( 'Theme Design is available with the SKVN Marine child theme. Block design controls work with your current theme.', 'skvn-marine-blocks' ) }</p> }
		</PanelBody>
		{ snapshot.available && snapshot.groups && <>
			<PanelBody><h2>{ __( 'Theme Design — SKVN Marine', 'skvn-marine-blocks' ) }</h2><p>{ __( 'Drafts do not preview in the canvas. Page Save does not save these settings. Local block overrides keep their own styles.', 'skvn-marine-blocks' ) }</p></PanelBody>
			{ ( [ 'palette', 'heading', 'fonts' ] as Group[] ).map( ( group ) => {
				const value = drafts[ group ] || snapshot.groups![ group ].value;
				const canEdit = snapshot.groups![ group ].canEdit;
				return <PanelBody key={ group } title={ labels[ group ] } initialOpen={ group === 'palette' }>
					{ ! canEdit && <><p>{ __( 'This group cannot be saved with the current theme or permissions. Your draft is kept.', 'skvn-marine-blocks' ) }</p><Button variant="secondary" disabled={ busy } onClick={ () => refreshAccess( group ) }>{ __( 'Refresh permissions', 'skvn-marine-blocks' ) }</Button></> }
					{ conflicts[ group ] && <Notice status="warning" isDismissible={ false }>
						<p>{ __( 'Site settings conflict: compare the saved values with your draft. Choosing an option does not save anything.', 'skvn-marine-blocks' ) }</p>
						<ConflictComparison current={ conflicts[ group ]!.value } draft={ value } group={ group } />
						<Button variant="secondary" disabled={ busy } onClick={ () => resolveConflict( group, true ) }>{ __( 'Use current values', 'skvn-marine-blocks' ) }</Button>
						<Button variant="secondary" disabled={ busy || ! canEdit } onClick={ () => resolveConflict( group, false ) }>{ __( 'Keep my draft', 'skvn-marine-blocks' ) }</Button>
					</Notice> }
					<fieldset disabled={ ! canEdit || busy || !! conflicts[ group ] } style={ { border: 0, padding: 0, margin: 0, minWidth: 0 } }>
						<legend>{ labels[ group ] }</legend>
						{ group === 'palette' && Object.entries( paletteLabels ).map( ( [ key, label ] ) => <TextControl key={ key } label={ label } help={ __( 'Hex color, for example #0369a1.', 'skvn-marine-blocks' ) } value={ value[ key ] as string } onChange={ ( color ) => change( group, { ...value, [ key ]: color } ) } /> ) }
						{ group === 'heading' && [ 'h1', 'h2', 'h3', 'h4' ].map( ( level ) => {
							const heading = value[ level ] as { size: string; weight: string };
							return <div key={ level }><TextControl label={ `${ level.toUpperCase() } ${ __( 'size', 'skvn-marine-blocks' ) }` } help={ __( 'Use px or rem, for example 2.25rem.', 'skvn-marine-blocks' ) } value={ heading.size } onChange={ ( size ) => change( group, { ...value, [ level ]: { ...heading, size } } ) } /><SelectControl label={ `${ level.toUpperCase() } ${ __( 'weight', 'skvn-marine-blocks' ) }` } value={ heading.weight } options={ [ 100, 200, 300, 400, 500, 600, 700, 800, 900 ].map( ( weight ) => ( { value: String( weight ), label: String( weight ) } ) ) } onChange={ ( weight ) => change( group, { ...value, [ level ]: { ...heading, weight } } ) } /></div>;
						} ) }
						{ group === 'fonts' && <><SelectControl label={ __( 'Font preset', 'skvn-marine-blocks' ) } options={ snapshot.presets || [] } value={ value.preset as string } onChange={ ( preset ) => change( group, { ...value, preset } ) } /><SelectControl label={ __( 'Heading font scope', 'skvn-marine-blocks' ) } options={ snapshot.scopes || [] } value={ value.scope as string } onChange={ ( scope ) => change( group, { ...value, scope } ) } /><p>{ __( 'Uses the existing theme font registry and delivery. No fonts are loaded for drafts.', 'skvn-marine-blocks' ) }</p></> }
						<Button variant="primary" disabled={ ! dirty( group ) } onClick={ () => save( group ) }>{ __( 'Save group', 'skvn-marine-blocks' ) }</Button>
						<Button variant="tertiary" onClick={ () => discard( group ) }>{ __( 'Discard draft and reload group', 'skvn-marine-blocks' ) }</Button>
					</fieldset>
					<p role="status" aria-live="polite">{ pending[ group ] ? __( 'Saving or reloading…', 'skvn-marine-blocks' ) : statuses[ group ] || ( dirty( group ) ? __( 'Unsaved site design draft', 'skvn-marine-blocks' ) : __( 'Saved values', 'skvn-marine-blocks' ) ) }</p>
				</PanelBody>;
			} ) }
		</> }
	</>;
}

function ConflictComparison( { current, draft, group }: { current: Values; draft: Values; group: Group } ) {
	const rows = Object.entries( current ).flatMap( ( [ key, value ] ) => typeof value === 'object'
		? Object.entries( value ).map( ( [ field, saved ] ) => ( { label: `${ key.toUpperCase() } ${ field === 'size' ? __( 'size', 'skvn-marine-blocks' ) : __( 'weight', 'skvn-marine-blocks' ) }`, saved, draft: typeof draft[ key ] === 'object' ? ( draft[ key ] as Record< string, string > )[ field ] : '' } ) )
		: [ { label: group === 'palette' ? paletteLabels[ key ] : key === 'preset' ? __( 'Font preset', 'skvn-marine-blocks' ) : __( 'Heading scope', 'skvn-marine-blocks' ), saved: value, draft: draft[ key ] as string } ] );
	return <table style={ { width: '100%', tableLayout: 'fixed', overflowWrap: 'anywhere' } }>
		<caption>{ labels[ group ] }</caption>
		<thead><tr><th scope="col">{ __( 'Setting', 'skvn-marine-blocks' ) }</th><th scope="col">{ __( 'Current saved', 'skvn-marine-blocks' ) }</th><th scope="col">{ __( 'Your draft', 'skvn-marine-blocks' ) }</th></tr></thead>
		<tbody>{ rows.map( ( row ) => <tr key={ row.label }><th scope="row">{ row.label }</th><td>{ row.saved }</td><td>{ row.draft }</td></tr> ) }</tbody>
	</table>;
}
