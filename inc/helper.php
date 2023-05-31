<?php
/**
 * Helper.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise\Inc;

use MdWpCliExercise\Inc\Traits\Get_Instance;

/**
 * Helper
 *
 * @since x.x.x
 */
class Helper {

	use Get_Instance;

	/**
	 * Keep default values of all settings.
	 *
	 * @var array
	 * @since x.x.x
	 */
	public function get_defaults() {
		return [
			MD_WP_CLI_EXERCISE_SETTINGS            => [
				'ordering'       => 'ASC',
				'selection'      => 'multiple',
				'page_per_limit' => 10,
			],
			MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS => [
				'primary_bg_color'   => '#ECECEE',
				'primary_font_color' => '#000',
			],
		];
	}

	/**
	 * Get option value from database and retruns value merged with default values
	 *
	 * @param string $option option name to get value from.
	 * @return array
	 * @since x.x.x
	 */
	public function get_option( $option ) {
		$db_values = get_option( $option, [] );
		return wp_parse_args( $db_values, $this->get_defaults()[ $option ] );
	}
}
