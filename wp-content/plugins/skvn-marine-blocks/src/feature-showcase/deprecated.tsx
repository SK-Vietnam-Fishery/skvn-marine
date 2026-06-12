import { RichText, useBlockProps } from '@wordpress/block-editor';
import type { FeatureItem, FeatureShowcaseAttributes } from './types';

type LegacyAttributes = {
	eyebrow: string;
	headingBefore: string;
	headingAccent: string;
	headingAfter: string;
	intro: string;
	metaLabel: string;
	metaText: string;
	items: FeatureItem[];
};

type PanelOnlyAttributes = Omit<
	FeatureShowcaseAttributes,
	'interactionMode' | 'autoplayDelay' | 'outerRadius'
>;

const legacyItems: FeatureItem[] = [
	{
		kicker: '01 . OCEAN GROUPER',
		heading: 'Ocean Grouper Fillet',
		copy: 'Wild-caught grouper prepared for premium restaurant and export programs.',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		kicker: '02 . IQF TUNNEL FREEZING',
		heading: 'IQF Freezing Technology',
		copy: 'Fast freezing preserves texture, freshness, and product consistency.',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		kicker: '03 . BARRAMUNDI',
		heading: 'Premium Barramundi',
		copy: 'Sustainably prepared seafood for retail and foodservice buyers.',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
	{
		kicker: '04 . EXPORT PROCESSING PLANT',
		heading: 'Global Standard Processing',
		copy: 'Controlled processing environments support demanding export requirements.',
		imageId: 0,
		imageUrl: '',
		imageAlt: '',
	},
];

const legacyAttributes = {
	eyebrow: {
		type: 'string',
		default: 'Premium Ocean Catch',
	},
	headingBefore: {
		type: 'string',
		default: 'Global',
	},
	headingAccent: {
		type: 'string',
		default: 'Seafood',
	},
	headingAfter: {
		type: 'string',
		default: 'Exporter',
	},
	intro: {
		type: 'string',
		default:
			'Premium wild-caught seafood prepared for demanding buyers in the United States, Japan, and the European Union.',
	},
	metaLabel: {
		type: 'string',
		default: 'Compliance Standards',
	},
	metaText: {
		type: 'string',
		default: 'HACCP - BRC GLOBAL - IFS',
	},
	items: {
		type: 'array',
		default: legacyItems,
	},
};

function stripMarkup( value: string ) {
	return value.replace( /<[^>]*>/g, '' ).trim();
}

const panelOnlyAttributes = {
	desktopLayout: {
		type: 'string',
		default: 'horizontal',
	},
	mobileBehavior: {
		type: 'string',
		default: 'accordion',
	},
	defaultOpen: {
		type: 'string',
		default: 'last',
	},
	gradientPreset: {
		type: 'string',
		default: '',
	},
	labelRotation: {
		type: 'string',
		default: 'default',
	},
	items: {
		type: 'array',
		default: legacyItems,
	},
};

function getPanelOnlyClassName( attributes: PanelOnlyAttributes ) {
	return [
		'skvn-feature-showcase',
		`skvn-feature-showcase--${ attributes.desktopLayout }`,
		`skvn-feature-showcase--mobile-${ attributes.mobileBehavior }`,
		attributes.gradientPreset
			? `skvn-feature-showcase--gradient-${ attributes.gradientPreset }`
			: '',
		attributes.labelRotation === '180'
			? 'skvn-feature-showcase--label-rotate-180'
			: '',
	]
		.filter( Boolean )
		.join( ' ' );
}

function isPanelInitiallyOpen(
	index: number,
	itemCount: number,
	defaultOpen: PanelOnlyAttributes[ 'defaultOpen' ]
) {
	return (
		( defaultOpen === 'first' && index === 0 ) ||
		( defaultOpen === 'last' && index === itemCount - 1 )
	);
}

function savePanelOnly( {
	attributes,
}: {
	attributes: PanelOnlyAttributes;
} ) {
	const items = attributes.items || [];
	const blockProps = useBlockProps.save( {
		className: getPanelOnlyClassName( attributes ),
	} );

	return (
		<section { ...blockProps }>
			<div className="skvn-feature-showcase__items">
				{ items.map( ( item, index ) => (
					<details
						className="skvn-feature-showcase__item"
						key={ index }
						open={ isPanelInitiallyOpen(
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

function saveLegacy( { attributes }: { attributes: LegacyAttributes } ) {
	const blockProps = useBlockProps.save( {
		className: 'skvn-feature-showcase',
	} );
	const items = ( attributes.items || [] ).slice( 0, 4 );

	return (
		<section { ...blockProps }>
			<div className="skvn-feature-showcase__grid">
				<div className="skvn-feature-showcase__intro">
					<RichText.Content
						className="skvn-feature-showcase__eyebrow"
						tagName="p"
						value={ attributes.eyebrow }
					/>
					<h2 className="skvn-feature-showcase__heading">
						<RichText.Content
							tagName="span"
							value={ attributes.headingBefore }
						/>
						<RichText.Content
							className="skvn-feature-showcase__heading-accent"
							tagName="strong"
							value={ attributes.headingAccent }
						/>
						<RichText.Content
							tagName="span"
							value={ attributes.headingAfter }
						/>
					</h2>
					<RichText.Content
						className="skvn-feature-showcase__copy"
						tagName="p"
						value={ attributes.intro }
					/>
					<div className="skvn-feature-showcase__meta">
						<RichText.Content
							className="skvn-feature-showcase__meta-label"
							tagName="p"
							value={ attributes.metaLabel }
						/>
						<RichText.Content
							className="skvn-feature-showcase__meta-text"
							tagName="p"
							value={ attributes.metaText }
						/>
					</div>
				</div>
				<div className="skvn-feature-showcase__panels">
					{ items.map( ( item, index ) => (
						<article
							aria-label={ `Feature ${ index + 1 }: ${ stripMarkup(
								item.heading
							) }` }
							className="skvn-feature-showcase__panel"
							key={ index }
							tabIndex={ 0 }
						>
							{ item.imageUrl && (
								<img
									alt={ item.imageAlt }
									className="skvn-feature-showcase__image"
									src={ item.imageUrl }
								/>
							) }
							<div className="skvn-feature-showcase__panel-shade" />
							<RichText.Content
								className="skvn-feature-showcase__panel-kicker"
								tagName="p"
								value={ item.kicker }
							/>
							<div className="skvn-feature-showcase__panel-body">
								<RichText.Content
									className="skvn-feature-showcase__panel-title"
									tagName="h3"
									value={ item.heading }
								/>
								<RichText.Content
									className="skvn-feature-showcase__panel-copy"
									tagName="p"
									value={ item.copy }
								/>
							</div>
						</article>
					) ) }
				</div>
			</div>
		</section>
	);
}

const deprecated = [
	{
		attributes: panelOnlyAttributes,
		migrate( attributes: PanelOnlyAttributes ): FeatureShowcaseAttributes {
			return {
				...attributes,
				autoplayDelay: 5000,
				interactionMode: 'hover',
				outerRadius: 0,
				items: ( attributes.items || [] ).map( ( item ) => ( {
					...item,
					linkTarget: item.linkTarget || '_self',
					linkText: item.linkText || '',
					linkUrl: item.linkUrl || '',
				} ) ),
			};
		},
		save: savePanelOnly,
	},
	{
		attributes: legacyAttributes,
		migrate( attributes: LegacyAttributes ): FeatureShowcaseAttributes {
			return {
				desktopLayout: 'horizontal',
				mobileBehavior: 'accordion',
				defaultOpen: 'last',
				gradientPreset: '',
				labelRotation: 'default',
				autoplayDelay: 5000,
				interactionMode: 'hover',
				outerRadius: 0,
				items: attributes.items || [],
			};
		},
		save: saveLegacy,
	},
];

export default deprecated;
