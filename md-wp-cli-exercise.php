<?php
/**
 * Plugin Name: Multidots WP CLI Exercise
 * Description: Multidots WP CLI Exercise is WP plugin just for WordPress Engineer - Practical Exercise.
 * Author: Alamgir Hossain
 * Author URI: https://multidots.com/
 * Version: 1.0.0
 * License: GPL v2
 * Text Domain: md-wp-cli-exercise
 *
 * @package md-wp-cli-exercise
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Set constants
 */
define( 'MD_WP_CLI_EXERCISE_FILE', __FILE__ );
define( 'MD_WP_CLI_EXERCISE_DIR_FILE', dirname(__FILE__) );
define( 'MD_WP_CLI_EXERCISE_BASE', plugin_basename( MD_WP_CLI_EXERCISE_FILE ) );
define( 'MD_WP_CLI_EXERCISE_DIR', plugin_dir_path( MD_WP_CLI_EXERCISE_FILE ) );
define( 'MD_WP_CLI_EXERCISE_URL', plugins_url( '/', MD_WP_CLI_EXERCISE_FILE ) );
define( 'MD_WP_CLI_EXERCISE_PLUGIN_PATH', untrailingslashit( MD_WP_CLI_EXERCISE_DIR ) );
define( 'MD_WP_CLI_EXERCISE_VER', '1.0.0' );
define( 'MD_WP_CLI_EXERCISE_SETTINGS', 'md_wp_cli_exercise_general' );
define( 'MD_WP_CLI_EXERCISE_APPEARANCE_SETTINGS', 'md_wp_cli_exercise_appearance' );

require_once 'inc/functions.php';
require_once 'plugin-loader.php';
