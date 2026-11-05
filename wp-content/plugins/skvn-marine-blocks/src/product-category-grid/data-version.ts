/**
 * Category dataset version bus.
 *
 * Kept in its own module so the draft store and the read hook can both depend
 * on it without importing each other.
 */

let version = 0;
const listeners = new Set< () => void >();

export function invalidateCategoryData() {
	version += 1;
	listeners.forEach( ( listener ) => listener() );
}

export function getCategoryDataVersion(): number {
	return version;
}

export function subscribeCategoryDataVersion( listener: () => void ): () => void {
	listeners.add( listener );
	return () => {
		listeners.delete( listener );
	};
}
