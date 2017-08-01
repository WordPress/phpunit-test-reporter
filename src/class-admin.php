<?php
namespace WPUTR;
class Admin {

	/**
	 * Returns an instance of this class.
	 *
	 * @return self An instance of this class.
	 */
	private static $instance = false;
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	public static function init() {
		// Create custom post type.
		add_action( 'init', array( self::instance(), 'create_custom_post_type' ) );
	}

	/**
	 * Create custom post type to store the directories we need to process.
	 *
	 * @since 1.0.0
	 * @return  null
	 */
	public function create_custom_post_type() {
		 register_post_type(
			 'result',
			 array(
				 'labels' => array(
					 'name' => __( 'Test Results' ),
					 'singular_name' => __( 'Test Result' ),
				 ),
				 'public' => true,
				 'has_archive' => true,
				 'show_in_rest' => true,
				 'hierarchical' => true,
				 'supports' => array( 'title', 'editor', 'author', 'custom-fields', 'page-attributes' ),
			 )
		 );
	}
}

Admin::instance();
