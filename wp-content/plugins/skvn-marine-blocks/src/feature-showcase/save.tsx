import { RichText, useBlockProps } from '@wordpress/block-editor';

type FeatureItem = {
	kicker: string;
	heading: string;
	copy: string;
	imageUrl: string;
	imageAlt: string;
};

type FeatureShowcaseAttributes = {
	eyebrow: string;
	headingBefore: string;
	headingAccent: string;
	headingAfter: string;
	intro: string;
	metaLabel: string;
	metaText: string;
	items: FeatureItem[];
};

type FeatureShowcaseSaveProps = {
	attributes: FeatureShowcaseAttributes;
};

export function save( { attributes }: FeatureShowcaseSaveProps ) {
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
