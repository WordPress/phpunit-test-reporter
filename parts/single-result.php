<?php
use PTR\Display;

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
} ?>

<?php echo Display::get_display_css(); ?>

<p><a href="<?php echo esc_url( get_permalink( $report->ID ) ); ?>" title="<?php echo esc_attr( $status_title ); ?>" class="<?php echo esc_attr( 'ptr-status-badge ptr-status-badge-' . strtolower( $status ) ); ?>"><?php echo esc_html( $status ); ?></a></p>

<h2>Environment</h2>

<table>
	<tr>
		<td><strong>Host</strong></td>
		<td><?php echo esc_html( $host ); ?></td>
	</tr>
	<tr>
		<td><strong>PHP Version</strong></td>
		<td><?php echo esc_html( Display::get_display_php_version( $report->ID ) ); ?></td>
	</tr>
	<tr>
		<td><strong>Database Version</strong></td>
		<td><?php echo esc_html( Display::get_display_mysql_version( $report->ID ) ); ?></td>
	</tr>
	<tr>
		<td><strong>Extensions</strong></td>
		<td><?php echo esc_html( Display::get_display_extensions( $report->ID ) ) ?></td>
	</tr>
</table>
