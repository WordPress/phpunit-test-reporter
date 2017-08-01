<?php
namespace WPUTR;
class RestAPI {

	// Define and register singleton
	private static $instance = false;
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public static function init() {
		add_action( 'rest_api_init', array( self::instance(), 'register_routes' ) );
	}

	/**
	 * Register REST API routes.
	 *
	 * @action rest_api_init
	 */
	function register_routes() {
		register_rest_route(
			'wp-unit-test-api/v1', 'results', array(
				'methods' => 'POST',
				'callback' => array( $this, 'add_results_callback' ),
				'args' => array(
					'commit' => array(
						'required' => true,
						'description' => 'The SVN Commit SHA.',
						'type' => 'string',
					),
					'results' => array(
						'required' => true,
						'description' => 'phpunit results in JSON format.',
						'type' => 'string',
					),
				),
				'permission_callback' => array( $this, 'permission' ),
			)
		);
	}

	function permission() {
		// TODO: Update this.
		return current_user_can( 'edit_posts' );
	}

	function add_results_callback( $data ) {
		$parameters = $data->get_params();

		$slug = 'r' . $parameters['commit'];
		if ( $post = get_page_by_path( $slug, 'OBJECT', 'results' ) ) {
			$parent_id = $post->ID;
		} else {
			$parent_id = wp_insert_post( array(
				'post_title' => $parameters['message'],
				'post_name' => $slug,
				'post_status' => 'publish',
				'post_type' => 'results',
			) );
		}

		$env = null;

		if ( isset( $parameters['meta'] ) ) {
			$env = json_decode( $parameters['meta'], true );
		}

		$meta = array(
			'results' => $parameters['results'],
			'env' => $env,
		);

		$results = array(
			'post_title' => 'Test results for revision: ' . $parameters['commit'],
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_type' => 'results',
			'meta_input' => $meta,
			'post_parent' => $parent_id,
		);

		// Store the results.
		wp_insert_post( $results );

		// Create the response object.
		$response = new \WP_REST_Response(
			array(
				'success' => true,
			)
		);

		// Add a custom status code.
		$response->set_status( 201 );

		$response->header( 'Content-Type', 'application/json' );

		return $response;
	}
}

RestAPI::instance();
