<?php
class Rest {

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
		$meta = null;

		if ( isset( $parameters['meta'] ) ) {
			$meta = json_decode( $parameters['meta'], true );
		}

		$results = array(
			'post_title'    => $parameters['commit'],
			'post_content'  => $parameters['results'],
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'post_type'     => 'results',
			'meta_input'    => $meta,
		);

		// Store the results.
		wp_insert_post( $results );

		// Create the response object.
		$response = new WP_REST_Response(
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

Rest::instance();
