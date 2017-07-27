<?php
/**
 * Plugin Name: WP Unit Test API
 * Plugin URI: http://domain.com
 * Description: Description
 * Version: 0.0.1
 * Requires at least: 4.7.0
 * Tested up to: 4.7.0
 * Text Domain: wp-unit-test-api
 *
 * @package WP_Unit_Test_API
 * @category Core
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main WP_Unit_Test_API Class
 *
 * @class WP_Unit_Test_API
 * @version	1.0.0
 * @since 1.0.0
 * @package	WP_Unit_Test_API
 * @author Matty
 */
final class WP_Unit_Test_API {
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
	 * @access  public
	 * @since   1.0.0
	 */
	public function __construct () {
		// RestAPI.
		require_once( 'src/class.restapi.php' );
		// Admin.
		require_once( 'src/class.admin.php' );
		require_once( 'src/class-shortcode.php' );

	} // End __construct()

} // End Class

WP_Unit_Test_API::instance();
