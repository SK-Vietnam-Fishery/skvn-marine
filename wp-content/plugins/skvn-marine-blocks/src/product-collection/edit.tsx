import { CollectionEdit } from '../collection/controls';
import type { CollectionAttributes } from '../collection/types';

type ProductCollectionEditProps = {
	attributes: CollectionAttributes;
	setAttributes: ( attributes: Partial< CollectionAttributes > ) => void;
};

export function Edit( props: ProductCollectionEditProps ) {
	return <CollectionEdit { ...props } contentType="product" />;
}
