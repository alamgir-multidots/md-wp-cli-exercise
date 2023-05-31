<?php
/**
 * Plugin functions.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */
if ( ! function_exists( 'md_wp_cli_exercise_get_template_part' ) ) {
	/**
	 * Get template part implementation for wedocs.
	 *
	 * @since x.x.x
	 *
	 * @param string $slug Template slug.
	 * @param string $name Template name.
	 * @param array  $args Template passing data.
	 * @param bool   $return Flag for retun with ob_start.
	 *
	 * @return html Return html file.
	 */
	function md_wp_cli_exercise_get_template_part( $slug, $name = '', $args = [], $return = false ) {
		$defaults = [
			'pro' => false,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( $args && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}

		$template = '';

		// Look in yourtheme/md-wp-cli-exercise/slug-name.php and yourtheme/md-wp-cli-exercise/slug.php.
		$template_path = ! empty( $name ) ? "{$slug}-{$name}.php" : "{$slug}.php";
		$template      = locate_template( [ 'md-wp-cli-exercise/' . $template_path ] );

		/**
		 * Change template directory path filter.
		 *
		 * @since x.x.x
		 */
		$template_path = apply_filters( 'md_wp_cli_exercise_set_template_path', MD_WP_CLI_EXERCISE_PLUGIN_PATH . '/templates', $template, $args );

		// Get default slug-name.php.
		if ( ! $template && $name && file_exists( $template_path . "/{$slug}-{$name}.php" ) ) {
			$template = $template_path . "/{$slug}-{$name}.php";
		}

		if ( ! $template && ! $name && file_exists( $template_path . "/{$slug}.php" ) ) {
			$template = $template_path . "/{$slug}.php";
		}

		// Allow 3rd party plugin filter template file from their plugin.
		$template = apply_filters( 'md_wp_cli_exercise_get_template_part', $template, $slug, $name );

		if ( $template ) {
			if ( $return ) {
				ob_start();
				require $template;
				return ob_get_clean();
			} else {
				require $template;
				return '';
			}
		}
	}
}

if ( ! function_exists( 'multidots_location_get_option ' ) ) {
	/**
	 * Get setting option data.
	 *
	 * @since x.x.x
	 *
	 * @param string $option Option name.
	 * @param string $section Option section.
	 * @param string $default Default value.
	 */
	function multidots_location_get_option( $option, $section, $default = '' ) {
		$options = get_option( $section );

		if ( isset( $options[ $option ] ) ) {
			return '' === $options[ $option ] ? $default : $options[ $option ];
		}

		return $default;
	}
}
