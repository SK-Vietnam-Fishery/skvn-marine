import { CollectionEdit } from '../collection/controls';
import type { CollectionAttributes } from '../collection/types';

type PostCollectionEditProps = {
	attributes: CollectionAttributes;
	setAttributes: ( attributes: Partial< CollectionAttributes > ) => void;
};

export function Edit( props: PostCollectionEditProps ) {
	return <CollectionEdit { ...props } contentType="post" />;
}
