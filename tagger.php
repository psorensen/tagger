<?php
/**
 * Plugin Name:     Tagger
 * Plugin URI:      http://github.com/psorensen/tagger
 * Description:     Scan your Wordpress posts for tags based on terms from a CSV file of tags and terms.
 * Author:          Peter Sorensen
 * Author URI:      http://github.com/psorensen
 * Text Domain:     tagger
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tagger
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Tagger_final' ) ) :

	/**
	 * Final Tagger Class
	 *
	 * @since 0.1.0
	 */
	final class Tagger_final {

		/**
		 * The one true Tagger.
		 *
		 * @var Tagger.
		 * @since 0.1.0
		 */
		private static $instance;

		/**
		 * The version number of Tagger
		 *
		 * @var version number.
		 * @since 0.1.0
		 */
		private $version = '0.1.0';

		/**
		 * Main Tagger Instance
		 *
		 * Insures that only one instance of Tagger exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 0.1.0
		 * @static var array $instance
		 * @return Tagger
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Tagger_final ) ) {
				self::$instance = new Tagger_final;
				self::$instance->constants();
				self::$instance->includes();
				self::$instance = new Tagger;
			}
			return self::$instance;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 0.1.0
		 * @link http://wordpress.stackexchange.com/questions/188448/whats-the-difference-between-get-home-path-and-abspath
		 * @return void
		 */
		private function constants() {
			// Plugin version.
			if ( ! defined( 'TAGGER_VERSION' ) ) {
				define( 'TAGGER_VERSION', $this->version );
			}
			// Plugin Folder Path.
			if ( ! defined( 'TAGGER_PATH' ) ) {
				define( 'TAGGER_PATH', plugin_dir_path( __FILE__ ) );
			}
			// Plugin Folder URL.
			if ( ! defined( 'TAGGER_URL' ) ) {
				define( 'TAGGER_URL', plugin_dir_url( __FILE__ ) );
			}
			// Reports Destination Folder Path.
			if ( ! defined( 'TAGGER_RESULTS_PATH' ) ) {
				// Included to make get_home_path work. See link in docblock. This could very well be bad practice.
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				define( 'TAGGER_RESULTS_PATH', get_home_path() . '/wp-content/uploads/tagger_results/' );
			}
		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			require_once( TAGGER_PATH . 'includes/class-tagger.php' );

			// wp-cli script.
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				require_once( TAGGER_PATH . 'bin/wp-cli.php' );
			}
		}
	}
endif;

/**
 * The main function responsible for returning the one true Tagger
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $Tagger = Tagger(); ?>
 *
 * @since 0.1.0
 * @return Tagger The one true Instance
 */
function tagger() {
	return Tagger_final::instance();
}
tagger();

/**
 * Create a directory for tagging reports on plugin activation
 *
 * @since 0.1.0
 */
function tagger_activate() {
	wp_mkdir_p( TAGGER_RESULTS_PATH );
}
register_activation_hook( __FILE__, 'tagger_activate' );

