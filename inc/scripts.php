<?php
/**
 * Scripts.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise\Inc;

use MdWpCliExercise\Inc\Traits\Get_Instance;
use MdWpCliExercise\Inc\Exercise;

/**
 * Scripts
 *
 * @since x.x.x
 */
class Scripts extends Exercise {

	use Get_Instance;

	/**
	 * Plugin version.
	 *
	 * @var string $version Current plugin version.
	 */
	public $version;

	/**
	 * Folder suffix.
	 *
	 * @var string $folder_suffix Select script folder.
	 */
	public $folder_suffix;

	/**
	 * File suffix.
	 *
	 * @var string $file_suffix Select script file.
	 */
	public $file_suffix;

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		$this->version       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : MD_WP_CLI_EXERCISE_VER;
		$this->folder_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : 'min-';
		$this->file_suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'dynamic_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_styles' ] );
	}

	/**
	 * Dynamic styles
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function dynamic_styles() {
		if ( ! $this->is_global_enabled() ) {
			return;
		}

		$dynamic_css  = ':root {';
		$dynamic_css .= "
		--md-wp-cli-exercise-primary-background-color: {$this->get_option( 'primary_bg_color', MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS )};
		--md-wp-cli-exercise-primary-font-color: {$this->get_option( 'primary_font_color', MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS )};
		";

		$dynamic_css .= '}';

		wp_add_inline_style( 'md-wp-cli-exercise-css', $dynamic_css );
	}

	/**
	 * Admin enqueue scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function admin_styles() {
		global $post;
		global $md_feedback_list_page;

		$current_screen = get_current_screen();

		// Check the currect post.
		if ( $post && $this->post_type === $post->post_type ) {
			wp_register_style( 'md-wp-cli-exercise-admin-css', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'css/admin-styles' . $this->file_suffix . '.css', [], $this->version );
			wp_enqueue_style( 'md-wp-cli-exercise-admin-css' );
		}
		
		if ( method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			wp_register_style( 'md-wp-cli-exercise-css', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'css/styles' . $this->file_suffix . '.css', [], $this->version );
			wp_enqueue_style( 'md-wp-cli-exercise-css' );
		}

		if ( is_object( $current_screen ) && $current_screen->id === $md_feedback_list_page ) {
			wp_register_style( 'md-wp-cli-exercise-css', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'css/styles' . $this->file_suffix . '.css', [], $this->version );
			wp_enqueue_style( 'md-wp-cli-exercise-css' );

			wp_register_script( 'md-wp-cli-exercise-js', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'js/scripts' . $this->file_suffix . '.js', [ 'jquery' ], $this->version, true );
			wp_enqueue_script( 'md-wp-cli-exercise-js' );

			$this->localize_script();
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_global_enabled() ) {
			return;
		}

		wp_register_style( 'md-wp-cli-exercise-css', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'css/styles' . $this->file_suffix . '.css', [], $this->version );
		wp_enqueue_style( 'md-wp-cli-exercise-css' );
		
		wp_register_script( 'md-wp-cli-exercise-js', MD_WP_CLI_EXERCISE_URL . 'assets/' . $this->folder_suffix . 'js/scripts' . $this->file_suffix . '.js', [ 'jquery' ], $this->version, true );
		wp_enqueue_script( 'md-wp-cli-exercise-js' );

		$this->localize_script();
	}

	/**
	 * Localize scripts
	 *
	 * @since x.x.x
	 *
	 * @return void
	 */
	public function localize_script() {
		wp_localize_script(
			'md-wp-cli-exercise-js',
			'md_wp_cli_exercise_ajax_object',
			apply_filters(
				'md_wp_cli_exercise_localize_script_args',
				[
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'ajax_nonce'     => wp_create_nonce( 'md_wp_cli_exercise_ajax_nonce' ),
					'general_error'  => __( 'Sometings wrong! try again later', 'md-wp-cli-exercise' ),
					'required_error' => __( 'Required fields must be filled in', 'md-wp-cli-exercise' ),
				]
			)
		);
	}
}
