<?php
namespace PTR;
/**
 * Plugin Name: PHPUnit Test Reporter
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

require_once dirname( __FILE__ ) . '/src/class-admin.php';
require_once dirname( __FILE__ ) . '/src/class-restapi.php';
require_once dirname( __FILE__ ) . '/src/class-shortcode.php';

add_action( 'init', array( 'PTR\Admin', 'create_custom_post_type' ) );
add_action( 'init', array( 'PTR\Shortcode', 'action_init_register' ) );
add_action( 'rest_api_init', array( 'PTR\RestAPI', 'register_routes' ) );
