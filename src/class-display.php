<?php

namespace PTR;

use WP_Query;

class Display {

	/**
	 * Register the shortcode.
	 */
	public static function action_init_register_shortcode() {
		add_shortcode( 'ptr-results', array( __CLASS__, 'render_results' ) );
	}

	/**
	 * Filter post classes
	 */
	public static function filter_post_class( $classes ) {
		if ( is_singular( 'result' ) ) {
			$classes[] = 'page';
		}
		return $classes;
	}

	/**
	 * Render the data for an individual result within the main content well
	 */
	public static function filter_the_content( $content ) {
		if ( ! is_singular( 'result' ) ) {
			return $content;
		}

		if ( get_queried_object()->post_parent ) {
			$content = ptr_get_template_part( 'single-result', array( 'report' => get_queried_object() ) );
		}

		return $content;
	}

	/**
	 * Render the test results.
	 */
	public static function render_results( $atts ) {

		// Shortcodes must return, not echo.
		// But, echo'ing is easier than concatenating strings.
		ob_start();

		echo '<h3>PHPUnit Test Results</h3>' . PHP_EOL;

		$query_args = array(
			'posts_per_page'   => 5,
			'post_type'        => 'result',
			'post_parent'      => 0,
			'orderby'          => 'post_name',
			'order'            => 'DESC',
		);
		$paged = get_query_var( 'paged' );
		if ( $paged ) {
			$query_args['paged'] = $paged;
		}
		$rev_query = new WP_Query( $query_args );
		if ( empty( $rev_query->posts ) ) {
			echo '<p>No revisions found</p>';
			return ob_get_clean();
		}
		?>
		<style>
			a.ptr-status-badge {
				color: #FFF;
				display: inline-block;
				padding-left: 8px;
				padding-right: 8px;
				padding-top: 3px;
				padding-bottom: 3px;
				border-radius: 3px;
				font-weight: normal;
			}
			a.ptr-status-badge-passed {
				background-color: #39BC00;
			}
			a.ptr-status-badge-failed {
				background-color: #CD543A;
			}
			a.ptr-status-badge-errored {
				background-color: #909090;
			}
			.pagination-centered {
				text-align: center;
			}
			.pagination-centered ul.pagination {
				list-style-type: none;
			}
			.pagination-centered ul.pagination li {
				display: inline-block;
			}
			.pagination-centered ul.pagination li a {
				cursor: pointer;
			}
		</style>
		<table>
			<thead>
				<tr>
					<th style="width:100px">Revision</th>
					<th style="width:100px">Status</th>
					<th>Host</th>
					<th>PHP Version</th>
					<th>Database Version</th>
					<th>Extensions</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_cols = 6;
				foreach( $rev_query->posts as $revision ) :
					$rev_id = (int) ltrim( $revision->post_name, 'r' );
				?>
					<tr>
						<th><a href="<?php echo esc_url( sprintf( 'https://core.trac.wordpress.org/changeset/%d', $rev_id ) ); ?>"><?php echo (int) $rev_id; ?></a></th>
						<th colspan="<?php echo (int) $total_cols - 1; ?>"><?php echo wp_kses_post( apply_filters( 'the_title', $revision->post_title ) ); ?></th>
					</tr>
					<?php
					$query_args = array(
						'posts_per_page'   => 10,
						'post_type'        => 'result',
						'post_parent'      => $revision->ID,
						'orderby'          => 'post_title',
						'order'            => 'ASC',
					);
					$report_query = new WP_Query( $query_args );
					if ( ! empty( $report_query->posts ) ) :
						foreach( $report_query->posts as $report ) :
							$status = 'Errored';
							$status_title = 'No results found for test.';
							$results = get_post_meta( $report->ID, 'results', true );
							if ( isset( $results['failures'] ) ) {
								$status = 0 === (int) $results['failures'] && 0 === (int) $results['errors'] ? 'Passed' : 'Failed';
								$status_title = (int) $results['tests'] . ' tests, ' . (int) $results['failures'] . ' failed, ' . (int) $results['errors'] . ' errors';
							}
							$host = 'Unknown';
							$user = get_user_by( 'id', $report->post_author );
							if ( $user ) {
								$host = $user->display_name;
							}
							?>
						<tr>
							<td></td>
							<td><a href="<?php echo esc_url( get_permalink( $report->ID ) ); ?>" title="<?php echo esc_attr( $status_title ); ?>" class="<?php echo esc_attr( 'ptr-status-badge ptr-status-badge-' . strtolower( $status ) ); ?>"><?php echo esc_html( $status ); ?></a></td>
							<td><?php echo esc_html( $host ); ?></td>
							<td><?php echo esc_html( self::get_display_php_version( $report->ID ) ); ?></td>
							<td><?php echo esc_html( self::get_display_mysql_version( $report->ID ) ); ?></td>
							<td><?php echo esc_html( self::get_display_extensions( $report->ID ) ); ?></td>
						</tr>
					<?php
						endforeach;
					else : ?>
						<tr>
							<td></td>
							<td colspan="<?php echo (int) $total_cols - 1; ?>">
								No reports for changeset.
							</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		self::pagination( $rev_query );
		return ob_get_clean();
	}

	/**
	 * Get the PHP version for display
	 *
	 * @param integer $report_id Report ID.
	 * @return string
	 */
	public static function get_display_php_version( $report_id ) {
		$php_version = 'Unknown';
		$env = get_post_meta( $report_id, 'env', true );
		if ( ! empty( $env['php_version'] ) ) {
			$php_version = 'PHP ' . $env['php_version'];
		}
		return $php_version;
	}

	/**
	 * Get the database version for display
	 *
	 * @param integer $report_id Report ID.
	 * @return string
	 */
	public static function get_display_mysql_version( $report_id ) {
		$mysql_version = 'Unknown';
		$env = get_post_meta( $report_id, 'env', true );
		if ( ! empty( $env['mysql_version'] ) ) {
			$bits = explode( ',', $env['mysql_version'] );
			$mysql_version = $bits[0];
		}
		return $mysql_version;
	}

	/**
	 * Get the extensions list for display
	 *
	 * @param integer $report_id Report ID.
	 * @return string
	 */
	public static function get_display_extensions( $report_id ) {
		$extensions = array();
		$env = get_post_meta( $report_id, 'env', true );
		if ( ! empty( $env['php_modules'] ) ) {
			foreach( $env['php_modules'] as $module => $version ) {
				if ( ! empty( $version ) ) {
					$extensions[] = $module . ' (' . $version . ')';
				}
			}
		}
		if ( ! empty( $env['system_utils'] ) ) {
			foreach( $env['system_utils'] as $module => $version ) {
				if ( ! empty( $version ) ) {
					$extensions[] = $module . ' (' . $version . ')';
				}
			}
		}
		return implode( ', ', $extensions );
	}

	private static function pagination( $query ) {
		$bignum = 999999999;
		$base_link = str_replace( $bignum, '%#%', esc_url( get_pagenum_link( $bignum ) ) );
		$max_num_pages = $query->max_num_pages;
		$current_page = max( 1, $query->get( 'paged' ) );
		$prev_page_label = '&lsaquo;';
		$next_page_label = '&rsaquo;';
		$args = array(
			'base'          => $base_link,
			'format'        => '',
			'current'       => $current_page,
			'total'         => $max_num_pages,
			'prev_text'     => $prev_page_label,
			'next_text'     => $next_page_label,
			'type'          => 'array',
			'end_size'      => 1,
			'mid_size'      => 2
		);

		if ( $max_num_pages <= 1 ) {
			return;
		}

		$pagination_links = paginate_links( $args );

		if ( ! empty( $pagination_links ) ) {

			if ( 1 === $current_page ) {
				array_unshift( $pagination_links, '<span class="prev page-numbers">' . esc_html( $prev_page_label ) . '</span>' );
			} else if ( $current_page >= $max_num_pages ) {
				array_push( $pagination_links, '<span class="next page-numbers">' . esc_html( $next_page_label ) . '</span>' );
			}

			echo '<nav class="pagination-centered">';

				echo '<ul class="pagination">';
				foreach ( $pagination_links as $paginated_link ) {
					// $paginated_link contains arbitrary HTML
					echo '<li>' . $paginated_link . '</li>';
				}
				echo '</ul>';

			echo '</nav>';
		}

	}

}
