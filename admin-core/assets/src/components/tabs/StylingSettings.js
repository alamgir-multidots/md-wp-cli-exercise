import { __ } from '@wordpress/i18n';
import SectionWrapper from '@Admin/components/wrappers/SectionWrapper';
import ColorField from '@Admin/components/fields/ColorField';
import { useStateValue } from '@Admin/components/Data';

function ShopSettings() {
	const [ data ] = useStateValue();

	return (
		<>
			<SectionWrapper heading={ __( 'Colors', 'md-wp-cli-exercise' ) }>
				<ColorField
					title={ __( 'Primary color', 'md-wp-cli-exercise' ) }
					description={ __(
						'Choose color for primary color.',
						'md-wp-cli-exercise'
					) }
					name={
						'md_wp_cli_exercise_appearance[primary_bg_color]'
					}
					value={
						data.md_wp_cli_exercise_appearance.primary_bg_color
					}
					default={ '#ECECEE' }
				/>
				<ColorField
					title={ __(
						'Primary text color',
						'md-wp-cli-exercise'
					) }
					description={ __(
						'Choose color for primary text color.',
						'md-wp-cli-exercise'
					) }
					name={
						'md_wp_cli_exercise_appearance[primary_font_color]'
					}
					value={
						data.md_wp_cli_exercise_appearance.primary_font_color
					}
					default={ '#000000' }
				/>
			</SectionWrapper>
		</>
	);
}

export default ShopSettings;
