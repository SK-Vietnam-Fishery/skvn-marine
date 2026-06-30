import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

type SlideAttributes = {
	backgroundImageUrl: string;
	backgroundImageAlt: string;
	overlayOpacity: number;
};

type SlideSaveProps = {
	attributes: SlideAttributes;
};

export function save( { attributes }: SlideSaveProps ) {
	const hasImage = Boolean( attributes.backgroundImageUrl );
	const blockProps = useBlockProps.save( {
		className: `skvn-slide swiper-slide${
			hasImage ? ' skvn-slide--has-background' : ''
		}`,
	} );

	return (
		<div { ...blockProps }>
			<div className="skvn-slide__media">
				{ hasImage && (
					<>
						<div className="skvn-slide__bg">
							<img
								alt={ attributes.backgroundImageAlt }
								className="skvn-slide__background-image"
								src={ attributes.backgroundImageUrl }
							/>
						</div>
						<span
							aria-hidden="true"
							className="skvn-slide__overlay"
							style={ { opacity: attributes.overlayOpacity / 100 } }
						/>
					</>
				) }
			</div>
			<div className="skvn-slide__content">
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
