import { RangeControl } from '@wordpress/components';
import {
	getGovernedTimeIndex,
	type GovernedTimeConfig,
} from './governed-time';

type GovernedTimeControlProps< T extends number > = {
	config: GovernedTimeConfig< T >;
	help?: string;
	label: string;
	onChange: ( value: T ) => void;
	value: unknown;
};

export function GovernedTimeControl< T extends number >( {
	config,
	help,
	label,
	onChange,
	value,
}: GovernedTimeControlProps< T > ) {
	const selectedIndex = getGovernedTimeIndex( value, config );
	const selectedOption =
		config.options[ selectedIndex ] || config.options[ 0 ];

	return (
		<RangeControl
			aria-valuetext={ selectedOption?.label }
			help={ help }
			label={ label }
			marks={ config.options.map( ( option, index ) => ( {
				label: option.label,
				value: index,
			} ) ) }
			max={ Math.max( config.options.length - 1, 0 ) }
			min={ 0 }
			onChange={ ( nextIndex ) => {
				const nextOption =
					config.options[ nextIndex ?? selectedIndex ];

				if ( nextOption ) {
					onChange( nextOption.value );
				}
			} }
			step={ 1 }
			value={ selectedIndex }
			withInputField={ false }
		/>
	);
}
