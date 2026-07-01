import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';

import { isCoreControlEnabled } from '../config';

const NO_NUMBER_CLASS = 'no-number';

/**
 * Toggle the `no-number` token inside a block's className (the Advanced →
 * Additional CSS class(es) field), preserving any other classes.
 */
function toggleNoNumber( className: string | undefined, on: boolean ): string | undefined {
	const tokens = new Set(
		( className ?? '' ).split( /\s+/ ).filter( Boolean )
	);

	if ( on ) {
		tokens.add( NO_NUMBER_CLASS );
	} else {
		tokens.delete( NO_NUMBER_CLASS );
	}

	const next = Array.from( tokens ).join( ' ' );
	return '' === next ? undefined : next;
}

/**
 * Add a "skip numbering" toggle to core/heading inspector when the
 * post_heading_numbers feature is enabled.
 */
addFilter(
	'editor.BlockEdit',
	'skvn-marine/heading-no-number',
	( BlockEdit: React.ComponentType< any > ) => {
		return function HeadingNoNumberControls( props: any ) {
			if (
				props.name !== 'core/heading' ||
				! isCoreControlEnabled( 'post_heading_numbers' )
			) {
				return <BlockEdit { ...props } />;
			}

			const { attributes, setAttributes } = props;
			const checked = ( attributes.className ?? '' )
				.split( /\s+/ )
				.includes( NO_NUMBER_CLASS );

			return (
				<>
					<BlockEdit { ...props } />
					<InspectorControls>
						<PanelBody
							title={ __( 'SKVN', 'skvn-marine-blocks' ) }
							initialOpen={ false }
						>
							<ToggleControl
								label={ __(
									'Bỏ đánh số mục này',
									'skvn-marine-blocks'
								) }
								checked={ checked }
								onChange={ ( on: boolean ) =>
									setAttributes( {
										className: toggleNoNumber(
											attributes.className,
											on
										),
									} )
								}
							/>
						</PanelBody>
					</InspectorControls>
				</>
			);
		};
	}
);
