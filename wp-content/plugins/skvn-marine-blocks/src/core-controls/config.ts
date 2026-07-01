import type { CoreControlsConfig } from './shared/types';

declare global {
	interface Window {
		skvnCoreControls?: Partial< CoreControlsConfig >;
	}
}

const DEFAULTS: CoreControlsConfig = {
	block_clipboard: false,
	button_hover: false,
	post_heading_numbers: false,
};

export function getCoreControlsConfig(): CoreControlsConfig {
	return { ...DEFAULTS, ...( window.skvnCoreControls ?? {} ) };
}

export function isCoreControlEnabled( feature: keyof CoreControlsConfig ): boolean {
	return getCoreControlsConfig()[ feature ] === true;
}
