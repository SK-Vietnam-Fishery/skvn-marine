import { InspectorControls } from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
import { PanelBody, ColorPicker, BaseControl } from '@wordpress/components';
import type { BlockConfiguration } from '@wordpress/blocks';

import { isCoreControlEnabled } from '../config';
import { BUTTON_HOVER_ATTRIBUTES } from './attributes';
import './style.css';

if ( ! isCoreControlEnabled( 'button_hover' ) ) {
	// Nothing to register — preserve attributes without rendering controls.
	// We still need to add attributes so saved values are not lost.
}

/**
 * Add namespaced hover attributes to core/button regardless of toggle state.
 * This ensures that saved attribute values survive disable/re-enable cycles.
 */
addFilter(
	'blocks.registerBlockType',
	'skvn-marine/button-hover-attributes',
	( settings: BlockConfiguration, name: string ) => {
		if ( name !== 'core/button' ) {
			return settings;
		}

		return {
			...settings,
			attributes: {
				...( settings.attributes ?? {} ),
				...BUTTON_HOVER_ATTRIBUTES,
			},
		};
	}
);

/**
 * Add hover color Inspector panel to core/button edit when feature is enabled.
 */
addFilter(
	'editor.BlockEdit',
	'skvn-marine/button-hover-controls',
	( BlockEdit: React.ComponentType< any > ) => {
		return function ButtonHoverControls( props: any ) {
			if ( props.name !== 'core/button' ) {
				return <BlockEdit { ...props } />;
			}

			const { attributes, setAttributes } = props;
			const { skvnHoverTextColor, skvnHoverBgColor } = attributes;

			const wrapperStyle: React.CSSProperties = {};
			if ( skvnHoverBgColor ) {
				wrapperStyle[ '--skvn-btn-hover-bg' as any ] = skvnHoverBgColor;
			}
			if ( skvnHoverTextColor ) {
				wrapperStyle[ '--skvn-btn-hover-text' as any ] = skvnHoverTextColor;
			}

			return (
				<>
					<BlockEdit { ...props } />
					{ isCoreControlEnabled( 'button_hover' ) && (
						<InspectorControls>
							<PanelBody
								title={ __(
									'SKVN Hover Colors',
									'skvn-marine-blocks'
								) }
								initialOpen={ false }
							>
								<BaseControl
									id="skvn-btn-hover-text"
									label={ __(
										'Hover text color',
										'skvn-marine-blocks'
									) }
								>
									<ColorPicker
										color={ skvnHoverTextColor || '' }
										onChange={ ( value: string ) =>
											setAttributes( {
												skvnHoverTextColor: value,
											} )
										}
										enableAlpha
									/>
									{ skvnHoverTextColor && (
										<button
											type="button"
											className="button button-small"
											style={ { marginTop: 4 } }
											onClick={ () =>
												setAttributes( {
													skvnHoverTextColor: '',
												} )
											}
										>
											{ __(
												'Clear',
												'skvn-marine-blocks'
											) }
										</button>
									) }
								</BaseControl>
								<BaseControl
									id="skvn-btn-hover-bg"
									label={ __(
										'Hover background color',
										'skvn-marine-blocks'
									) }
								>
									<ColorPicker
										color={ skvnHoverBgColor || '' }
										onChange={ ( value: string ) =>
											setAttributes( {
												skvnHoverBgColor: value,
											} )
										}
										enableAlpha
									/>
									{ skvnHoverBgColor && (
										<button
											type="button"
											className="button button-small"
											style={ { marginTop: 4 } }
											onClick={ () =>
												setAttributes( {
													skvnHoverBgColor: '',
												} )
											}
										>
											{ __(
												'Clear',
												'skvn-marine-blocks'
											) }
										</button>
									) }
								</BaseControl>
							</PanelBody>
						</InspectorControls>
					) }
				</>
			);
		};
	}
);
