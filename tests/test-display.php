<?php
/**
 * Class TestDisplay
 *
 * @package PHPUnit_Test_Reporter
 */

/**
 * Tests for rendering a single result view.
 */
class TestDisplay extends WP_UnitTestCase {

	public function set_up() {
		parent::set_up();

		// Author reports as an administrator so post titles are stored verbatim
		// (no kses on save), letting each test control the exact rendered input.
		$admin = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin );
	}

	/**
	 * Create a changeset parent and a single child report carrying the given
	 * failure text, and return the child report ID.
	 */
	private function create_report( $failure_text, $parent_title = 'Example changeset' ) {
		$parent = self::factory()->post->create(
			array(
				'post_type'  => 'result',
				'post_name'  => 'r' . wp_rand( 100000, 999999 ),
				'post_title' => $parent_title,
			)
		);

		$child = self::factory()->post->create(
			array(
				'post_type'    => 'result',
				'post_parent'  => $parent,
				'post_content' => '',
			)
		);

		update_post_meta(
			$child,
			'results',
			array(
				'tests'      => 1,
				'failures'   => 1,
				'errors'     => 0,
				'testsuites' => array(
					'ExampleTest' => array(
						'failures'  => 1,
						'errors'    => 0,
						'testcases' => array(
							'test_example' => array( 'failure' => $failure_text ),
						),
					),
				),
			)
		);

		return $child;
	}

	/**
	 * Render a single result exactly as a theme would: run the main loop (which
	 * fires loop_start and sets the global post) and apply the_content, so
	 * do_shortcode (priority 11) and Display::filter_the_content (99) run in order.
	 */
	private function render_report( $child ) {
		$this->go_to( get_permalink( $child ) );

		$html = '';
		while ( have_posts() ) {
			the_post();
			$html = apply_filters( 'the_content', get_the_content() );
		}
		wp_reset_postdata();

		return $html;
	}

	/**
	 * A shortcode embedded in submitted report data must not be executed when the
	 * result view is rendered. Regression test for the report template being fed
	 * to the shortcode parser.
	 */
	public function test_shortcodes_in_report_content_are_not_executed() {
		add_shortcode(
			'ptr_probe',
			static function () {
				return 'SHORTCODE_EXECUTED';
			}
		);

		$child = $this->create_report( 'BEGIN [ptr_probe] END' );
		$html  = $this->render_report( $child );

		$this->assertStringContainsString( 'Errors/Failures', $html, 'The report template should render.' );
		$this->assertStringNotContainsString( 'SHORTCODE_EXECUTED', $html, 'Shortcodes in report data must not execute.' );
		$this->assertStringContainsString( '[ptr_probe]', $html, 'The shortcode should survive as literal text.' );
	}

	/**
	 * Markup in the submitter-supplied commit message must be escaped, not
	 * rendered as live HTML, in the parent link.
	 */
	public function test_parent_commit_message_markup_is_escaped() {
		$child = $this->create_report(
			'ok',
			'INJECT <a href="https://example.com/pwn">x</a>'
		);
		$html = $this->render_report( $child );

		$this->assertStringNotContainsString( '<a href="https://example.com/pwn">', $html, 'Commit message markup must not become a live tag.' );
		$this->assertStringContainsString( '&lt;a href=&quot;https://example.com/pwn&quot;&gt;', $html, 'Commit message markup should be escaped.' );
	}

	/**
	 * The commit message is plain text, so special characters must be escaped
	 * exactly once. Guards against re-running the_title (wptexturize) and then
	 * escaping the resulting entity, which double-encodes the output.
	 */
	public function test_parent_commit_message_is_not_double_encoded() {
		$child = $this->create_report( 'ok', 'Fish & Chips' );
		$html  = $this->render_report( $child );

		$this->assertStringContainsString( 'Fish &amp; Chips', $html, 'Ampersand should be escaped once.' );
		$this->assertStringNotContainsString( '&amp;#', $html, 'Output must not be double-encoded.' );
	}
}
