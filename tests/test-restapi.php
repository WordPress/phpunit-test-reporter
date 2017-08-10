<?php
/**
 * Class TestRestAPI
 *
 * @package PHPUnit_Test_Reporter
 */

/**
 * Tests for the REST API.
 */
class TestRestAPI extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$this->server = new WP_REST_Server;
		$wp_rest_server = $this->server;

		$this->administrator = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);

		wp_set_current_user( $this->administrator );

		do_action( 'rest_api_init' );
	}

	public function test_add_result() {
		$request = new WP_REST_Request( 'POST', '/wp-unit-test-api/v1/results' );

		$request->set_body_params( array(
			'results' => 'test',
			'commit' => '1234',
			'message' => 'Docs: Did something',
			'env' => json_encode( array(
				'php_version' => '7.1',
			) ),
		) );

		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertTrue( isset( $data['id'] ) );
		$this->assertTrue( isset( $data['link'] ) );

		$parent = get_page_by_path( 'r1234', 'OBJECT', 'result' );

		$this->assertEquals( 'Docs: Did something', $parent->post_title );

		$args = array(
			'post_parent' => $parent->ID,
			'post_type'   => 'result',
			'numberposts' => -1,
		);

		$results = get_children( $args );

		$this->assertEquals( 1, count( $results ) );
	}

	public function test_add_result_saves_post_meta() {
		$request = new WP_REST_Request( 'POST', '/wp-unit-test-api/v1/results' );

		$request->set_body_params( array(
			'results' => '{"failures": "5"}',
			'commit' => '1234',
			'message' => 'Docs: Did something',
			'env' => json_encode( array(
				'php_version' => '7.1',
			) ),
		) );

		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$post_id = $data['id'];

		$env = get_post_meta( $post_id, 'env', true );
		$results = get_post_meta( $post_id, 'results', true );

		$this->assertEquals( '7.1', $env['php_version'] );
		$this->assertEquals( array(
			'failures' => '5',
		), $results );
	}

	public function test_add_result_updates_existing_results() {
		$request = new WP_REST_Request( 'POST', '/wp-unit-test-api/v1/results' );

		$request->set_body_params( array(
			'results' => '{"failures": "1"}',
			'commit' => '1234',
			'message' => 'Docs: Did something',
			'env' => '',
		) );

		$response = $this->server->dispatch( $request );
		$data = $response->get_data();
		$post_id = $data['id'];

		$results = get_post_meta( $post_id, 'results', true );
		$this->assertEquals( array(
			'failures' => '1',
		), $results );

		// Make second request.
		$request->set_body_params( array(
			'results' => '{"failures": "0"}',
			'commit' => '1234',
			'message' => 'Docs: Did something',
			'env' => '',
		) );

		$this->server->dispatch( $request );

		$results = get_post_meta( $post_id, 'results', true );
		$this->assertEquals( array(
			'failures' => '0',
		), $results );
	}

	public function tearDown() {
		parent::tearDown();
		global $wp_rest_server;
		$wp_rest_server = null;
	}
}
