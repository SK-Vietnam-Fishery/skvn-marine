import {
	InspectorControls,
	__experimentalPanelColorGradientSettings as PanelColorGradientSettings,
} from '@wordpress/block-editor';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
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

			const bgIsGradient =
				/^(?:linear|radial|conic)-gradient\(/i.test( skvnHoverBgColor );

			return (
				<>
					{ isCoreControlEnabled( 'button_hover' ) && (
						<InspectorControls group="styles">
							<PanelColorGradientSettings
								title={ __(
									'Hover Colors',
									'skvn-marine-blocks'
								) }
								initialOpen={ false }
								settings={ [
									{
										colorValue:
											skvnHoverTextColor || undefined,
										onColorChange: (
											value: string | undefined
										) =>
											setAttributes( {
												skvnHoverTextColor:
													value ?? '',
											} ),
										label: __(
											'Text',
											'skvn-marine-blocks'
										),
									},
									{
										colorValue: bgIsGradient
											? undefined
											: skvnHoverBgColor || undefined,
										gradientValue: bgIsGradient
											? skvnHoverBgColor
											: undefined,
										onColorChange: (
											value: string | undefined
										) =>
											setAttributes( {
												skvnHoverBgColor: value ?? '',
											} ),
										onGradientChange: (
											value: string | undefined
										) =>
											setAttributes( {
												skvnHoverBgColor: value ?? '',
											} ),
										label: __(
											'Background',
											'skvn-marine-blocks'
										),
									},
								] }
							/>
						</InspectorControls>
					) }
					<BlockEdit { ...props } />
				</>
			);
		};
	}
);
