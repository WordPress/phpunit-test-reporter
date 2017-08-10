<?php
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
$php_version = 'Unknown';
$extensions = array();
$mysql_version = 'Unknown';
$env = get_post_meta( $report->ID, 'env', true );
if ( ! empty( $env['php_version'] ) ) {
	$php_version = 'PHP ' . $env['php_version'];
}
if ( ! empty( $env['mysql_version'] ) ) {
	$bits = explode( ',', $env['mysql_version'] );
	$mysql_version = $bits[0];
}
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
$extensions = implode( ', ', $extensions ); ?>
<p><a href="<?php echo esc_url( get_permalink( $report->ID ) ); ?>" title="<?php echo esc_attr( $status_title ); ?>" class="<?php echo esc_attr( 'ptr-status-badge ptr-status-badge-' . strtolower( $status ) ); ?>"><?php echo esc_html( $status ); ?></a></p>

<h2>Environment</h2>

<table>
	<tr>
		<td><strong>Host</strong></td>
		<td><?php echo esc_html( $host ); ?></td>
	</tr>
	<tr>
		<td><strong>PHP Version</strong></td>
		<td><?php echo esc_html( $php_version ); ?></td>
	</tr>
	<tr>
		<td><strong>Database Version</strong></td>
		<td><?php echo esc_html( $mysql_version ); ?></td>
	</tr>
	<tr>
		<td><strong>Extensions</strong></td>
		<td><?php echo esc_html( $extensions ); ?></td>
	</tr>
</table>
