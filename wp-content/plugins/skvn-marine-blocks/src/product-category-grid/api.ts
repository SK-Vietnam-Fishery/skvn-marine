import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';
import type {
	CategoryListResponse,
	SaveDetailsRequestItem,
	SaveDetailsResponse,
} from './types';

export type CategoryQuery = {
	search?: string;
	include?: number[];
	onlyWithProducts?: boolean;
	withDetails?: boolean;
	page?: number;
	perPage?: number;
};

const BASE = '/skvn-marine-blocks/v1/product-categories';

/** Read product categories from the native taxonomy. */
export async function fetchCategories(
	query: CategoryQuery = {}
): Promise< CategoryListResponse > {
	const params: Record< string, unknown > = {
		page: query.page ?? 1,
		perPage: query.perPage ?? 50,
	};

	if ( query.search ) {
		params.search = query.search;
	}

	if ( query.include && query.include.length > 0 ) {
		params.include = query.include;
	}

	if ( query.onlyWithProducts ) {
		params.onlyWithProducts = true;
	}

	if ( query.withDetails ) {
		params.withDetails = true;
	}

	return apiFetch( { path: addQueryArgs( BASE, params ) } );
}

/** Write dirty category description/thumbnail fields in one batch. */
export async function saveCategoryDetails(
	items: SaveDetailsRequestItem[]
): Promise< SaveDetailsResponse > {
	return apiFetch( {
		path: `${ BASE }/details`,
		method: 'POST',
		data: { items },
	} );
}
