export type GovernedTimeOption< T extends number = number > = {
	label: string;
	value: T;
};

export type GovernedTimeConfig< T extends number = number > = {
	defaultValue: T;
	options: readonly GovernedTimeOption< T >[];
};

export function defineGovernedTimeConfig<
	const TOptions extends readonly GovernedTimeOption[],
	const TDefault extends TOptions[ number ][ 'value' ],
>( config: {
	defaultValue: TDefault;
	options: TOptions;
} ) {
	return config;
}

export function normalizeGovernedTime< T extends number >(
	value: unknown,
	config: GovernedTimeConfig< T >
): T {
	return typeof value === 'number' &&
		config.options.some( ( option ) => option.value === value )
		? ( value as T )
		: config.defaultValue;
}

export function getGovernedTimeIndex< T extends number >(
	value: unknown,
	config: GovernedTimeConfig< T >
): number {
	const normalizedValue = normalizeGovernedTime( value, config );
	const selectedIndex = config.options.findIndex(
		( option ) => option.value === normalizedValue
	);

	return selectedIndex >= 0 ? selectedIndex : 0;
}
