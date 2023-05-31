<?php
/**
 * Admin menu.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise\Admin_Core;

use MdWpCliExercise\Inc\Traits\Get_Instance;
use MdWpCliExercise\Inc\Helper;


/**
 * Admin menu
 *
 * @since x.x.x
 */
class Admin_Menu {

	use Get_Instance;

	/**
	 * Tailwind assets base url
	 *
	 * @var string
	 * @since x.x.x
	 */
	private $tailwind_assets = MD_WP_CLI_EXERCISE_URL . 'admin-core/assets/build/';

	/**
	 * Instance of Helper class
	 *
	 * @var Helper
	 * @since x.x.x
	 */
	private $helper;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		$this->helper = new Helper();
		add_action( 'admin_menu', [ $this, 'settings_page' ], 99 );
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_page_scripts' ] );
		add_action( 'wp_ajax_md_wp_cli_exercise_update_settings', [ $this, 'md_wp_cli_exercise_update_settings' ] );
	}

	/**
	 * Adds admin menu for settings page
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function settings_page() {
		add_submenu_page(
			'edit.php?post_type=md_locations',
			__( 'Settings - Multidots WP CLI Exercise', 'md-wp-cli-exercise' ),
			__( 'Settings', 'md-wp-cli-exercise' ),
			'manage_options',
			'md_wp_cli_exercise_settings',
			[ $this, 'render' ],
		);
	}

	/**
	 * Renders main div to implement tailwind UI
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function render() {
		?>
		<div class="md-wp-cli-exercise-settings" id="md-wp-cli-exercise-settings"></div>
		<?php
	}

	/**
	 * Enqueue settings page script and style
	 *
	 * @param string $hook Current page hook name.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function settings_page_scripts( $hook ) {
		if ( 'md_locations_page_md_wp_cli_exercise_settings' !== $hook ) {
			return;
		}

		$version           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : MD_WP_CLI_EXERCISE_VER;
		$script_asset_path = MD_WP_CLI_EXERCISE_DIR . 'admin-core/assets/build/settings.asset.php';
		$script_info       = file_exists( $script_asset_path )
			? include $script_asset_path
			: array(
				'dependencies' => [],
				'version'      => $version,
			);

		$script_dep = array_merge( $script_info['dependencies'], [ 'updates' ] );

		wp_register_script( 'md_wp_cli_exercise_settings', $this->tailwind_assets . 'settings.js', $script_dep, $version, true );
		wp_enqueue_script( 'md_wp_cli_exercise_settings' );
		wp_localize_script(
			'md_wp_cli_exercise_settings',
			'md_wp_cli_exercise_settings',
			[
				'ajax_url'                                 => admin_url( 'admin-ajax.php' ),
				'feedbacks_api_url'                        => site_url( '/wp-json/md/v1/feedbacks' ),
				'update_nonce'                             => wp_create_nonce( 'md_wp_cli_exercise_update_settings' ),
				MD_WP_CLI_EXERCISE_SETTINGS            => $this->helper->get_option( MD_WP_CLI_EXERCISE_SETTINGS ),
				MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS => $this->helper->get_option( MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS ),
			]
		);

		wp_register_style( 'md_wp_cli_exercise_settings', $this->tailwind_assets . 'settings.css', [], $version );
		wp_enqueue_style( 'md_wp_cli_exercise_settings' );
	}

	/**
	 * Ajax handler for submit action on settings page.
	 * Updates settings data in database.
	 *
	 * @return void
	 * @since x.x.x
	 */
	public function md_wp_cli_exercise_update_settings() {
		check_ajax_referer( 'md_wp_cli_exercise_update_settings', 'security' );
		$keys = [];

		if ( ! empty( $_POST[ MD_WP_CLI_EXERCISE_SETTINGS ] ) ) {
			$keys[] = MD_WP_CLI_EXERCISE_SETTINGS;
		}

		if ( ! empty( $_POST[ MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS ] ) ) {
			$keys[] = MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS;
		}

		if ( empty( $keys ) ) {
			wp_send_json_error( [ 'message' => __( 'No valid setting keys found.', 'md-wp-cli-exercise' ) ] );
		}

		$succeded = 0;
		foreach ( $keys as $key ) {
			if ( $this->update_settings( $key, $_POST[ $key ] ) ) {
				$succeded++;
			}
		}

		if ( count( $keys ) === $succeded ) {
			wp_send_json_success( [ 'message' => __( 'Settings saved successfully.', 'md-wp-cli-exercise' ) ] );
		}

		wp_send_json_error( [ 'message' => __( 'Failed to save settings.', 'md-wp-cli-exercise' ) ] );
	}

	/**
	 * Update dettings data in database
	 *
	 * @param string $key options key.
	 * @param string $data user input to be saved in database.
	 * @return boolean
	 * @since x.x.x
	 */
	public function update_settings( $key, $data ) {
		$data 		  = ! empty( $data) ? json_decode( stripslashes( $data ), true ) : array(); // phpcs:ignore
		$data         = $this->sanitize_data( $data );
		$default_data = $this->helper->get_option( $key );
		$data         = wp_parse_args( $data, $default_data );

		return update_option( $key, $data );
	}

	/**
	 * Sanitize data as per data type
	 *
	 * @param array $data raw input received from user.
	 * @return array
	 * @since x.x.x
	 */
	public function sanitize_data( $data ) {
		$temp     = [];
		$booleans = [];
		$numbers  = [];

		foreach ( $data as $key => $value ) {
			if ( in_array( $key, $booleans, true ) ) {
				$temp[ $key ] = rest_sanitize_boolean( $value );
			} elseif ( in_array( $key, $numbers, true ) ) {
				$temp[ $key ] = (int) sanitize_text_field( $value );
			} else {
				$temp[ $key ] = sanitize_text_field( $value );
			}
		}

		return $temp;
	}
}
