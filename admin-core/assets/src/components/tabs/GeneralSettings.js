import React from 'react';
import { __ } from '@wordpress/i18n';
import SectionWrapper from '@Admin/components/wrappers/SectionWrapper';
import DropdownField from '@Admin/components/fields/DropdownField';
import { useStateValue } from '@Admin/components/Data';
import NumberField from '@Admin/components/fields/NumberField';

function GeneralSettings() {
	const [ data ] = useStateValue();

	return (
		<>
			<SectionWrapper
				heading={ __( 'General', 'md-wp-cli-exercise' ) }
			>
				<NumberField
					title={ __( 'Page per limit', 'md-wp-cli-exercise' ) }
					description={ __(
						'Set locations listing page per limit.',
						'md-wp-cli-exercise'
					) }
					badge={ __( 'Default: 10', 'md-wp-cli-exercise' ) }
					name={ 'md_wp_cli_exercise_general[page_per_limit]' }
					max={ 100 }
					value={ data.md_wp_cli_exercise_general.page_per_limit }
					type={ 'Limit' }
				/>
				<DropdownField
					title={ __( 'Locations ordering (By Entry Date)', 'md-wp-cli-exercise' ) }
					description={ __(
						'Choose a locations ordering.',
						'md-wp-cli-exercise'
					) }
					name={ 'md_wp_cli_exercise_general[ordering]' }
					value={ data.md_wp_cli_exercise_general.ordering }
					optionsArray={ [
						{
							id: 'ASC',
							name: __( 'ASC', 'md-wp-cli-exercise' ),
						},
						{
							id: 'DESC',
							name: __( 'DESC', 'md-wp-cli-exercise' ),
						},
					] }
				/>
			</SectionWrapper>
		</>
	);
}

export default GeneralSettings;
