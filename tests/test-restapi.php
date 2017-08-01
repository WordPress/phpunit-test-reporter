<?php
/**
 * Class TestRestAPI
 *
 * @package Wp_Unit_Test_Reporter
 */

/**
 * Tests for the REST API.
 */
class TestRestAPI extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

		global $wp_rest_server;
		$this->server = $wp_rest_server = new WP_REST_Server;

		$this->administrator = $this->factory->user->create(
			array( 'role' => 'administrator' )
		);

		do_action( 'rest_api_init' );
	 }

	public function test_add_result() {
		wp_set_current_user( $this->administrator );
		$request = new WP_REST_Request( 'POST', '/wp-unit-test-api/v1/results' );

		$request->set_body_params( array(
			'results' => 'test',
			'commit' => '1234',
			'message' => 'Docs: Did something',
			'meta' => json_encode( array( 'php_version' => '7.1' ) ),
		) );

		$response = $this->server->dispatch( $request );
		$data = $response->get_data();

		$this->assertTrue( $data['success'] );

		$parent = get_page_by_path( 'r1234', 'OBJECT', 'results' );

		$this->assertEquals( 'Docs: Did something', $parent->post_title );

		$args = array(
			'post_parent' => $parent->ID,
			'post_type'   => 'results',
			'numberposts' => -1,
		);

		$results = get_children( $args );

		$this->assertEquals( 1, count( $results ) );
	 }

	public function tearDown() {
		parent::tearDown();
		global $wp_rest_server;
		$wp_rest_server = null;
	}
}
