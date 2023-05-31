<?php
/**
 * Exercise.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise\Inc;

use MdWpCliExercise\Inc\Traits\Get_Instance;
use MdWpCliExercise\Inc\Helper;

/**
 * Exercise class
 *
 * @since x.x.x
 */
class Exercise {

	use Get_Instance;

	/**
	 * The post type slug.
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * The post per page limit.
	 *
	 * @var string
	 */
	protected $post_per_page = 10;

	/**
	 * Location price currency.
	 *
	 * @var string
	 */
	protected $currency = '';

	/**
	 * Get setting option data.
	 *
	 * @since x.x.x
	 *
	 * @param string $option Option name.
	 * @param string $section Option section.
	 * @param string $default Default value.
	 */
	public function get_option( $option, $section, $default = '' ) {
		$options = get_option( $section );
		$helper  = Helper::get_instance();

		if ( isset( $options[ $option ] ) ) {
			return '' === $options[ $option ] ? $default : $options[ $option ];
		}

		if ( empty( $default ) && isset( $helper->get_option( $section )[ $option ] ) ) {
			return $helper->get_option( $section )[ $option ];
		}

		return $default;
	}

	/**
	 * Check script is globally enable
	 *
	 * @since x.x.x
	 */
	public function is_global_enabled() {
		return true;
	}
}
