<?php
/**
 * Plugin Name: WP Unit Test Reporter
 * Plugin URI: http://domain.com
 * Description: Description
 * Version: 1.0.0
 * Requires at least: 4.7.0
 * Tested up to: 4.7.0
 * Text Domain: wp-unit-test-api
 *
 * @package WP_Unit_Test_API
 * @category Core
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main wp-unit-test-reporter Class
 *
 * @class WP_Unit_Test_Reporter
 * @version 1.0.0
 * @since 1.0.0
 * @package WP_Unit_Test_Reporter
 */
final class WP_Unit_Test_Reporter {
	// Define and register singleton
	private static $instance = false;
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor function.
	 *
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct() {
		// RestAPI.
		require_once( 'src/class-restapi.php' );
		// Admin.
		require_once( 'src/class-admin.php' );
		require_once( 'src/class-shortcode.php' );

	} // End __construct()

} // End Class

WP_Unit_Test_Reporter::instance();
