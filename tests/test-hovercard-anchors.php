<?php
/**
 * Tests for the revision anchor text that feeds the hovercards.
 *
 * @package PHPUnit_Test_Reporter
 */

/**
 * The make.wordpress.org hovercard script sends the anchor's text as the
 * lookup slug. The endpoint returns an empty card when the slug carries
 * whitespace around the reference, and only the bracket form carries the
 * changeset number into the card, so the anchor text must be exactly
 * [NNNNN] with nothing around it.
 */
class Test_Hovercard_Anchor_Text extends WP_UnitTestCase {

	/**
	 * Creates a result post named after the revision.
	 *
	 * @param string $revision Revision name, for example r63601.
	 * @return WP_Post
	 */
	private function create_revision( $revision ) {
		$post_id = self::factory()->post->create(
			array(
				'post_type'  => 'result',
				'post_name'  => $revision,
				'post_title' => 'Test revision ' . $revision,
			)
		);
		return get_post( $post_id );
	}

	/**
	 * Extracts the inner text of every Trac changeset anchor in the markup.
	 *
	 * @param string $html Rendered template output.
	 * @return array Anchor texts, untrimmed.
	 */
	private function changeset_anchor_texts( $html ) {
		preg_match_all(
			// phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledInText -- lowercase is part of the URL pattern.
			'#<a[^>]*core\.trac\.wordpress\.org/changeset[^>]*>(.*?)</a>#s',
			$html,
			$matches
		);
		return $matches[1];
	}

	public function test_result_set_all_anchor_text_is_exactly_the_changeset_reference() {
		$revision = $this->create_revision( 'r63601' );

		$html  = ptr_get_template_part( 'result-set-all', array( 'revisions' => array( $revision ) ) );
		$texts = $this->changeset_anchor_texts( $html );

		$this->assertCount( 1, $texts );
		$this->assertSame( '[63601]', $texts[0] );
	}

	public function test_result_set_single_anchor_text_is_exactly_the_changeset_reference() {
		$revision = $this->create_revision( 'r63601' );

		$html  = ptr_get_template_part(
			'result-set-single',
			array(
				'revisions'      => array( $revision ),
				'posts_per_page' => 500,
			)
		);
		$texts = $this->changeset_anchor_texts( $html );

		$this->assertCount( 1, $texts );
		$this->assertSame( '[63601]', $texts[0] );
	}
}
