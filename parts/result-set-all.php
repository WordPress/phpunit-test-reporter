<?php
use PTR\Display;

echo Display::get_display_css(); ?>

<table class="ptr-test-reporter-table alignwide">
	<thead>
		<tr>
			<th style="width:100px">Revision</th>
			<th style="width:100px">Environments</th>
			<th style="width:100px">Passed</th>
			<th style="width:100px">Failed</th>
			<th style="width:100px">➡️</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$total_cols = 5;
		foreach ( $revisions as $revision ) :
			$rev_id = (int) ltrim( $revision->post_name, 'r' );
      $query_args   = array(
        'posts_per_page' => $posts_per_page,
        'post_type'      => 'result',
        'post_parent'    => $revision->ID,
        'orderby'        => 'post_title',
        'order'          => 'ASC',
      );
      $report_query = new WP_Query( $query_args );

      $environments = [];
      $num_hosts = 0;
      $num_passed = 0;
      $num_failed = 0;

      foreach ( $report_query->posts as $report ) {
				$env = Display::get_display_environment_name($report->ID);

				$environments[$env] ??= 0;
				++$environments[$env];

				$results = get_post_meta($report->ID, 'results', true);

				if (0 === (int)$results['failures'] && 0 === (int)$results['errors']) {
					++$num_passed;
				} else {
					++$num_failed;
				}
			}
			?>
			<tr>
				<td>
          <a
            href="<?php echo esc_url( sprintf( 'https://core.trac.wordpress.org/changeset/%d', $rev_id ) ); ?>"
            title="<?php echo esc_attr( apply_filters( 'the_title', $revision->post_title ) ); ?>">
            r<?php echo $rev_id; ?>
          </a>
        </td>
        <td>
          <?php echo count( $environments ); ?>
        </td>
        <td>
            <span class="ptr-status-badge ptr-status-badge-passed">
			        <?php echo $num_passed; ?>
            </span>
        </td>
        <td>
            <span class="ptr-status-badge ptr-status-badge-failed">
			        <?php echo $num_failed; ?>
            </span>
        </td>
        <td>
          <a href="<?php the_permalink( $revision->ID ); ?>">
            View
          </a>
        </td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
