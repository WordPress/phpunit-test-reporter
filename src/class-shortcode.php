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
		<style>
			.wputr-status-badge {
				color: #FFF;
				display: inline-block;
				padding-left: 8px;
				padding-right: 8px;
				padding-top: 3px;
				padding-bottom: 3px;
				border-radius: 3px;
				font-weight: normal;
			}
			.wputr-status-badge-passed {
				background-color: #39BC00;
			}
			.wputr-status-badge-failed {
				background-color: #CD543A;
			}
			.wputr-status-badge-errored {
				background-color: #909090;
			}
		</style>
		<h3>PHPUnit Test Results</h3>
		<table>
			<thead>
				<tr>
					<th style="width:100px">Revision</th>
					<th style="width:100px">Status</th>
					<th>Host</th>
					<th>PHP Version</th>
					<th>PHP Extensions</th>
					<th>Database Version</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><a href="https://core.trac.wordpress.org/changeset/41176/">41176</a></th>
					<th><a href="#" class="wputr-status-badge wputr-status-badge-passed">Passed</a></th>
					<th colspan="4">REST API: Add some missing assertions to various REST API tests. See …</th>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-passed">Passed</a></td>
					<td>DreamHost</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-passed">Passed</a></td>
					<td>Pantheon</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-passed">Passed</a></td>
					<td>WP Engine</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
				<tr>
					<th><a href="#">41174</a></th>
					<th><a href="#" class="wputr-status-badge wputr-status-badge-failed">Failed</a></th>
					<th colspan="4">General: Avoid counting uncountable values when reading theme directories, …</th>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-failed">Failed</a></td>
					<td>DreamHost</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-errored">Errored</a></td>
					<td>Pantheon</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
				<tr>
					<td></td>
					<td><a href="#" class="wputr-status-badge wputr-status-badge-failed">Failed</a></td>
					<td>WP Engine</td>
					<td>PHP 5.6</td>
					<td>Imagick, pcre</td>
					<td>MySQl 1234</td>
				</tr>
			</tbody>

		</table>
		<?php
		return ob_get_clean();
	}

}

add_action( 'init', array( 'WPUTR\Shortcode', 'action_init_register' ) );
