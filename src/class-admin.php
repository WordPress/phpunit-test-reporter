<?php
namespace PTR;
class Admin {

	/**
	 * Create custom post type to store the directories we need to process.
	 *
	 * @since 1.0.0
	 * @return  null
	 */
	public static function create_custom_post_type() {
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
