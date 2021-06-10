<?php
/**
 * Core: Plugin Bootstrap
 *
 * @package     AffiliateWP Force Pending Referrals
 * @subpackage  Core
 * @copyright   Copyright (c) 2021, Sandhills Development, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Force_Pending_Referrals' ) ) {

	/**
	 * Main plugin bootstrap.
	 *
	 * @since 1.1
	 */
	final class AffiliateWP_Force_Pending_Referrals  {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Force_Pending_Referrals exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @since 1.1
		 */
		private static $instance;


		/**
		 * The version number of AffiliateWP
		 *
		 * @since 1.1
		 * @var   string
		 */
		private $version = '1.0';

		/**
		 * Main plugin file.
		 *
		 * @since 1.1
		 * @var   string
		 */
		private $file = '';

		/**
		 * Main AffiliateWP_Force_Pending_Referrals Instance
		 *
		 * Insures that only one instance of AffiliateWP_Force_Pending_Referrals exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.1
		 * @static
		 * @static var array $instance
		 *
		 * @param string $file Main plugin file.
		 * @return The one true AffiliateWP_Force_Pending_Referrals
		 */
		public static function instance( $file = null ) {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Force_Pending_Referrals ) ) {

				self::$instance = new AffiliateWP_Force_Pending_Referrals;
				self::$instance->file = $file;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->hooks();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.1
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-force-pending-referrals' ), '1.1' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.1
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-force-pending-referrals' ), '1.1' );
		}

		/**
		 * Setup plugin constants.
		 *
		 * @access private
		 * @since 1.1
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'AFFWP_FPR_VERSION' ) ) {
				define( 'AFFWP_FPR_VERSION', $this->version );
			}

			// Plugin Folder Path.
			if ( ! defined( 'AFFWP_FPR_PLUGIN_DIR' ) ) {
				define( 'AFFWP_FPR_PLUGIN_DIR', plugin_dir_path( $this->file ) );
			}

			// Plugin Folder URL.
			if ( ! defined( 'AFFWP_FPR_PLUGIN_URL' ) ) {
				define( 'AFFWP_FPR_PLUGIN_URL', plugin_dir_url( $this->file ) );
			}

			// Plugin Root File.
			if ( ! defined( 'AFFWP_FPR_PLUGIN_FILE' ) ) {
				define( 'AFFWP_FPR_PLUGIN_FILE', $this->file );
			}
		}


		/**
		 * Constructor Function
		 *
		 * @since 1.1
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.1
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.1
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory.
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_force_pending_referrals_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'affiliatewp-force-pending-referrals' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'affiliatewp-force-pending-referrals', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-force-pending-referrals/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-force-pending-referrals/ folder.
				load_textdomain( 'affiliatewp-force-pending-referrals', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-force-pending-referrals/languages/ folder.
				load_textdomain( 'affiliatewp-force-pending-referrals', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'affiliatewp-force-pending-referrals', false, $lang_dir );
			}
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.1
		 *
		 * @return void
		 */
		private function hooks() {

			// plugin meta.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// disable auto complete referrals.
			add_filter( 'affwp_auto_complete_referral', '__return_false' );

		}

		/**
		 * Modify plugin metalinks
		 *
		 * @access public
		 * @since  1.1
		 * @param array  $links The current links array.
		 * @param string $file A specific plugin table entry.
		 * @return array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
			if ( plugin_basename( $this->file ) === $file ) {
					$plugins_link = array(
						'<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-force-pending-referrals' ) . '" href="http://affiliatewp.com/addons/" target="_blank">' . __( 'More add-ons', 'affiliatewp-force-pending-referrals' ) . '</a>',
					);

					$links = array_merge( $links, $plugins_link );
			}

			return $links;
		}

	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Force_Pending_Referrals
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_force_pending_referrals = affiliatewp_force_pending_referrals(); ?>
	 *
	 * @since 1.1
	 * @return object The one true AffiliateWP_Force_Pending_Referrals Instance
	 */
	function affiliatewp_force_pending_referrals() {
		return AffiliateWP_Force_Pending_Referrals::instance();
	}
}
