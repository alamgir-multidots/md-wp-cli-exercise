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

function fpHandleUpload() {

	if (!wp_verify_nonce($_POST['_wpnonce'], 'media-form')) {
		return new WP_Error('grabfromurl', 'Could not verify request nonce');
	}

	// build up array like PHP file upload
	$file = array();
	$file['name'] = $_POST['grabfrom_saveas'];
	$file['tmp_name'] = download_url($_POST['grabfrom_url']);

	if (is_wp_error($file['tmp_name'])) {
		@unlink($file['tmp_name']);
		return new WP_Error('grabfromurl', 'Could not download image from remote source');
	}

	$attachmentId = media_handle_sideload($file, $_POST['post_id']);

	// create the thumbnails
	$attach_data = wp_generate_attachment_metadata( $attachmentId,  get_attached_file($attachmentId));

	wp_update_attachment_metadata( $attachmentId,  $attach_data );

	return $attachmentId;	
}

function fpGrabFromURLIframe() {
	media_upload_header();

	if (isset($_POST['grabfrom_url'])) {
		// this is an upload request. let's see!
		$attachmentId = fpHandleUpload();
		if (is_wp_error($attachmentId)) {
			fpUploadForm('<div class="error form-invalid">' . $attachmentId->get_error_message(). '</div>');
		}
		else {
			echo "<style>h3, #plupload-upload-ui,.max-upload-size { display: none }</style>";
			media_upload_type_form("image", null, $attachmentId);
		}
	}
	else {
		fpUploadForm();
	}
}