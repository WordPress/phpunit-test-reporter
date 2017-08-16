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

require_once dirname( __FILE__ ) . '/src/class-content-model.php';
require_once dirname( __FILE__ ) . '/src/class-restapi.php';
require_once dirname( __FILE__ ) . '/src/class-display.php';

add_action( 'init', array( 'PTR\Content_Model', 'action_init_register_post_type' ) );
add_action( 'init', array( 'PTR\Content_Model', 'action_init_register_role' ) );
add_action( 'init', array( 'PTR\Display', 'action_init_register_shortcode' ) );
add_action( 'post_class', array( 'PTR\Display', 'filter_post_class' ) );
add_action( 'the_content', array( 'PTR\Display', 'filter_the_content' ) );
add_action( 'rest_api_init', array( 'PTR\RestAPI', 'register_routes' ) );

/**
 * Get a rendered template part
 *
 * @param string $template
 * @param array $vars
 * @return string
 */
function ptr_get_template_part( $template, $vars = array() ) {
	$full_path = dirname( __FILE__ ) . '/parts/' . $template . '.php';

	if ( ! file_exists( $full_path ) ) {
		return '';
	}

	ob_start();
	// @codingStandardsIgnoreStart
	if ( ! empty( $vars ) ) {
		extract( $vars );
	}
	// @codingStandardsIgnoreEnd
	include $full_path;
	return ob_get_clean();
}
