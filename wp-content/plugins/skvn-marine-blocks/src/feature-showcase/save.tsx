import { RichText, useBlockProps } from '@wordpress/block-editor';
import type { FeatureShowcaseAttributes } from './types';

type FeatureShowcaseSaveProps = {
	attributes: FeatureShowcaseAttributes;
};

function getClassName( attributes: FeatureShowcaseAttributes ) {
	return [
		'skvn-feature-showcase',
		`skvn-feature-showcase--${ attributes.desktopLayout }`,
		`skvn-feature-showcase--mobile-${ attributes.mobileBehavior }`,
		attributes.gradientPreset
			? `skvn-feature-showcase--gradient-${ attributes.gradientPreset }`
			: '',
	]
		.filter( Boolean )
		.join( ' ' );
}

function isInitiallyOpen(
	index: number,
	itemCount: number,
	defaultOpen: FeatureShowcaseAttributes[ 'defaultOpen' ]
) {
	return (
		( defaultOpen === 'first' && index === 0 ) ||
		( defaultOpen === 'last' && index === itemCount - 1 )
	);
}
export function save( { attributes }: FeatureShowcaseSaveProps ) {
	const items = attributes.items || [];
	const blockProps = useBlockProps.save( {
		className: getClassName( attributes ),
	} );

	return (
		<section { ...blockProps }>
			<div className="skvn-feature-showcase__items">
				{ items.map( ( item, index ) => (
					<details
						className="skvn-feature-showcase__item"
						key={ index }
						open={ isInitiallyOpen(
							index,
							items.length,
							attributes.defaultOpen
						) }
					>
						<summary className="skvn-feature-showcase__summary">
							<span className="skvn-feature-showcase__index">
								{ String( index + 1 ).padStart( 2, '0' ) }
							</span>
							<RichText.Content
								className="skvn-feature-showcase__label"
								tagName="span"
								value={ item.kicker }
							/>
						</summary>
						<div className="skvn-feature-showcase__body">
							{ item.imageUrl && (
								<img
									alt={ item.imageAlt }
									className="skvn-feature-showcase__image"
									src={ item.imageUrl }
								/>
							) }
							<span
								aria-hidden="true"
								className="skvn-feature-showcase__shade"
							/>
							<div className="skvn-feature-showcase__content">
								<RichText.Content
									className="skvn-feature-showcase__title"
									tagName="h3"
									value={ item.heading }
								/>
								<RichText.Content
									className="skvn-feature-showcase__copy"
									tagName="p"
									value={ item.copy }
								/>
							</div>
						</div>
					</details>
				) ) }
			</div>
		</section>
	);
}
