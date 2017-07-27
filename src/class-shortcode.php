<?php

namespace WPUTR;

class Shortcode {

	/**
	 * Register the shortcode.
	 */
	public static function action_init_register() {
		add_shortcode( 'wputr-results', array( __CLASS__, 'render_results' ) );
	}

	/**
	 * Render the test results.
	 */
	public static function render_results( $atts ) {

		ob_start(); ?>
		<h3>PHPUnit Test Results</h3>
		<table>
			<thead>
				<tr>
					<th style="width:100px">Revision</th>
					<th>Host</th>
					<th style="width:100px">Status</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><a href="https://core.trac.wordpress.org/changeset/41176/">41176</a></td>
					<td>REST API: Add some missing assertions to various REST API tests. See …</td>
					<td>Passed</td>
				</tr>
				<tr>
					<td></td>
					<td>DreamHost — PHP 5.6</td>
					<td>Passed</td>
				</tr>
				<tr>
					<td></td>
					<td>Pantheon — PHP 5.6</td>
					<td>Passed</td>
				</tr>
				<tr>
					<td></td>
					<td>WP Engine — PHP 5.6</td>
					<td>Passed</td>
				</tr>
				<tr>
					<td><a href="#">41174</a></td>
					<td>General: Avoid counting uncountable values when reading theme directories, …</td>
					<td>Failed</td>
				</tr>
			</tbody>

		</table>
		<?php
		return ob_get_clean();
	}

}

add_action( 'init', array( 'WPUTR\Shortcode', 'action_init_register' ) );
