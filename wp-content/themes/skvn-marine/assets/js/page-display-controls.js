( function ( wp ) {
	var __ = wp.i18n.__;
	var createElement = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var ToggleControl = wp.components.ToggleControl;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;

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

	function PageDisplayControls() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var meta = useSelect( function ( select ) {
			return select( 'core/editor' ).getEditedPostAttribute( 'meta' ) || {};
		}, [] );

		var editPost = useDispatch( 'core/editor' ).editPost;

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
