import { defineGovernedTimeConfig } from '../shared/governed-time';

export const SLIDER_AUTOPLAY_TIME = defineGovernedTimeConfig( {
	defaultValue: 7000,
	options: [
		{ label: '5s', value: 5000 },
		{ label: '7s', value: 7000 },
		{ label: '9s', value: 9000 },
		{ label: '12s', value: 12000 },
	],
} as const );

export const SLIDER_TRANSITION_TIME = defineGovernedTimeConfig( {
	defaultValue: 800,
	options: [
		{ label: '600ms', value: 600 },
		{ label: '700ms', value: 700 },
		{ label: '800ms', value: 800 },
		{ label: '900ms', value: 900 },
		{ label: '1000ms', value: 1000 },
	],
} as const );
