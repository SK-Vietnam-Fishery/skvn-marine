import { defineGovernedTimeConfig } from '../shared/governed-time';

export const FEATURE_SHOWCASE_AUTOPLAY_TIME = defineGovernedTimeConfig( {
	defaultValue: 5000,
	options: [
		{ label: '3s', value: 3000 },
		{ label: '5s', value: 5000 },
		{ label: '7s', value: 7000 },
		{ label: '9s', value: 9000 },
	],
} as const );
