<?php
/**
 * Plugin Loader.
 *
 * @package md-wp-cli-exercise
 * @since x.x.x
 */

namespace MdWpCliExercise;

use MdWpCliExercise\Admin_Core\Admin_Menu;
use MdWpCliExercise\Inc\Scripts;
use MdWpCliExercise\Inc\Wp_Custom_Cli;

/**
 * Plugin_Loader
 *
 * @since x.x.x
 */
class Plugin_Loader {

	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class Instance.
	 * @since x.x.x
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @since x.x.x
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Autoload classes.
	 *
	 * @param string $class class name.
	 */
	public function autoload( $class ) {
		if ( 0 !== strpos( $class, __NAMESPACE__ ) ) {
			return;
		}

		$class_to_load = $class;

		$filename = strtolower(
			preg_replace(
				[ '/^' . __NAMESPACE__ . '\\\/', '/([a-z])([A-Z])/', '/_/', '/\\\/' ],
				[ '', '$1-$2', '-', DIRECTORY_SEPARATOR ],
				$class_to_load
			)
		);

		$file = MD_WP_CLI_EXERCISE_DIR . $filename . '.php';

		// if the file redable, include it.
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Constructor
	 *
	 * @since x.x.x
	 */
	public function __construct() {
		spl_autoload_register( [ $this, 'autoload' ] );

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', [ $this, 'load_classes' ] );
		//add_filter( 'plugin_action_links_' . MD_WP_CLI_EXERCISE_BASE, [ $this, 'action_links' ] );
		register_activation_hook( MD_WP_CLI_EXERCISE_FILE, [ $this, 'activate' ] );
	}

	/**
	 * Create roles on plugin activation.
	 *
	 * @return void
	 */
	public function activate() {
		flush_rewrite_rules();
	}

	/**
	 * Load Plugin Text Domain.
	 * This will load the translation textdomain depending on the file priorities.
	 *      1. Global Languages /wp-content/languages/md-wp-cli-exercise/ folder
	 *      2. Local dorectory /wp-content/plugins/md-wp-cli-exercise/languages/ folder
	 *
	 * @since x.x.x
	 * @return void
	 */
	public function load_textdomain() {
		// Default languages directory.
		$lang_dir = MD_WP_CLI_EXERCISE_DIR . 'languages/';

		/**
		 * Filters the languages directory path to use for plugin.
		 *
		 * @param string $lang_dir The languages directory path.
		 */
		$lang_dir = apply_filters( 'wpb_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter.
		global $wp_version;

		$get_locale = get_locale();

		if ( $wp_version >= 4.7 ) {
			$get_locale = get_user_locale();
		}

		/**
		 * Language Locale for plugin
		 *
		 * @var $get_locale The locale to use.
		 * Uses get_user_locale()` in WordPress 4.7 or greater,
		 * otherwise uses `get_locale()`.
		 */
		$locale = apply_filters( 'plugin_locale', $get_locale, 'md-wp-cli-exercise' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'md-wp-cli-exercise', $locale );

		// Setup paths to current locale file.
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;
		$mofile_local  = $lang_dir . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/md-wp-cli-exercise/ folder.
			load_textdomain( 'md-wp-cli-exercise', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/md-wp-cli-exercise/languages/ folder.
			load_textdomain( 'md-wp-cli-exercise', $mofile_local );
		} else {
			// Load the default language files.
			load_plugin_textdomain( 'md-wp-cli-exercise', false, $lang_dir );
		}
	}

	/**
	 * Loads plugin classes as per requirement.
	 *
	 * @return void
	 * @since X.X.X
	 */
	public function load_classes() {
		if ( is_admin() ) {
			Admin_Menu::get_instance();
		}

		Scripts::get_instance();

		if ( class_exists( 'WP_CLI' ) ) {
			// Register the instance for the callable parameter.
			$instance = Wp_Custom_Cli::get_instance();

			\WP_CLI::add_command( 'md-csv-import', $instance );
		}
	}

	/**
	 * Adds links in Plugins page
	 *
	 * @param array $links existing links.
	 * @return array
	 * @since x.x.x
	 */
	public function action_links( $links ) {
		$plugin_links = apply_filters(
			'md_wp_cli_exercise_action_links',
			[
				'md_wp_cli_exercise_settings' => '<a href="' . admin_url( 'edit.php?post_type=md_locations&page=md_wp_cli_exercise_settings' ) . '">' . __( 'Settings', 'md-wp-cli-exercise' ) . '</a>',
			]
		);

		return array_merge( $plugin_links, $links );
	}
}

/**
 * Kicking this off by calling 'get_instance()' method
 */
Plugin_Loader::get_instance();
