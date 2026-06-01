( function ( wp ) {
	var __ = wp.i18n.__;
	var createElement = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var SelectControl = wp.components.SelectControl;
	var ToggleControl = wp.components.ToggleControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;

	var PRESET_KEY = '_skvn_page_display_preset';

	var CONTROLS = [
		{
			key: '_skvn_hide_header',
			label: __( 'Hide site header', 'skvn-marine' ),
			help: __( 'Hide the GeneratePress site header on this page.', 'skvn-marine' ),
		},
		{
			key: '_skvn_hide_footer',
			label: __( 'Hide site footer', 'skvn-marine' ),
			help: __( 'Hide the site footer on this page.', 'skvn-marine' ),
		},
		{
			key: '_skvn_hide_title',
			label: __( 'Hide page title', 'skvn-marine' ),
			help: __( 'Hide the default page title rendered by the theme.', 'skvn-marine' ),
		},
		{
			key: '_skvn_full_width_canvas',
			label: __( 'Full width canvas', 'skvn-marine' ),
			help: __( 'Remove the default content container and sidebar for full-bleed page content.', 'skvn-marine' ),
		},
	];

	var PRESET_OPTIONS = [
		{
			label: __( 'Default page', 'skvn-marine' ),
			value: 'default',
		},
		{
			label: __( 'SKVN Landing Canvas', 'skvn-marine' ),
			value: 'skvn_landing_canvas',
		},
		{
			label: __( 'Custom settings', 'skvn-marine' ),
			value: 'custom',
		},
	];

	function inferPreset( meta ) {
		var hideHeader = !! meta._skvn_hide_header;
		var hideFooter = !! meta._skvn_hide_footer;
		var hideTitle = !! meta._skvn_hide_title;
		var fullWidthCanvas = !! meta._skvn_full_width_canvas;

		if ( ! hideHeader && ! hideFooter && ! hideTitle && ! fullWidthCanvas ) {
			return 'default';
		}

		if ( ! hideHeader && ! hideFooter && hideTitle && fullWidthCanvas ) {
			return 'skvn_landing_canvas';
		}

		return 'custom';
	}

	function applyPreset( meta, preset ) {
		var nextMeta = Object.assign( {}, meta );
		nextMeta[ PRESET_KEY ] = preset;

		if ( preset === 'default' ) {
			CONTROLS.forEach( function ( control ) {
				nextMeta[ control.key ] = false;
			} );
		}

		if ( preset === 'skvn_landing_canvas' ) {
			nextMeta._skvn_hide_header = false;
			nextMeta._skvn_hide_footer = false;
			nextMeta._skvn_hide_title = true;
			nextMeta._skvn_full_width_canvas = true;
		}

		return nextMeta;
	}

	function PageDisplayControls() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;
		var preset = meta[ PRESET_KEY ] || inferPreset( meta );

		if ( postType !== 'page' ) {
			return null;
		}

		return createElement(
			PluginDocumentSettingPanel,
			{
				name: 'skvn-page-display',
				title: __( 'SKVN Page Display', 'skvn-marine' ),
				className: 'skvn-page-display-panel',
			},
			createElement(
				Fragment,
				null,
				createElement( SelectControl, {
					className: 'skvn-page-display-preset',
					label: __( 'Page preset', 'skvn-marine' ),
					help: __( 'Apply the standard page setup in one step. SKVN Landing Canvas hides the page title and enables the full-width, no-sidebar canvas.', 'skvn-marine' ),
					value: preset,
					options: PRESET_OPTIONS,
					onChange: function ( value ) {
						editPost( { meta: applyPreset( meta, value ) } );
					},
				} ),
				CONTROLS.map( function ( control ) {
					return createElement( ToggleControl, {
						key: control.key,
						className: 'skvn-page-display-control',
						label: control.label,
						help: control.help,
						checked: !! meta[ control.key ],
						onChange: function ( value ) {
							var nextMeta = Object.assign( {}, meta );
							nextMeta[ control.key ] = !! value;
							nextMeta[ PRESET_KEY ] = inferPreset( nextMeta );
							editPost( { meta: nextMeta } );
						},
					} );
				} )
			)
		);
	}

	if ( PluginDocumentSettingPanel ) {
		registerPlugin( 'skvn-page-display-controls', {
			render: PageDisplayControls,
		} );
	}
} )( window.wp );
