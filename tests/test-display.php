<?php
/**
 * Tests for the Display class image library helpers.
 *
 * @package PHPUnit_Test_Reporter
 */

use PTR\Display;

/**
 * Tests for Display::get_display_gd_support() and
 * Display::get_display_imagick_support().
 */
class Test_Display_Image_Libraries extends WP_UnitTestCase {

	/**
	 * Creates a report post with the given env meta.
	 *
	 * @param mixed $env Env meta value, or null to store no env at all.
	 * @return integer Post ID.
	 */
	private function create_report( $env = null ) {
		$post_id = self::factory()->post->create();
		if ( null !== $env ) {
			update_post_meta( $post_id, 'env', $env );
		}
		return $post_id;
	}

	public function test_gd_missing_key_returns_not_reported() {
		$post_id = $this->create_report( array( 'php_version' => '8.2' ) );
		$this->assertSame( 'Not reported', Display::get_display_gd_support( $post_id ) );
	}

	public function test_gd_empty_array_returns_not_reported() {
		// The runner submits an empty array when the extension is unavailable.
		$post_id = $this->create_report( array( 'gd_info' => array() ) );
		$this->assertSame( 'Not reported', Display::get_display_gd_support( $post_id ) );
	}

	public function test_gd_real_shape_lists_version_and_supported_formats() {
		$post_id = $this->create_report(
			array(
				'gd_info' => array(
					'GD Version'                       => 'bundled (2.1.0 compatible)',
					'FreeType Support'                 => true,
					'GIF Read Support'                 => true,
					'GIF Create Support'               => true,
					'JPEG Support'                     => true,
					'PNG Support'                      => true,
					'WBMP Support'                     => true,
					'XPM Support'                      => false,
					'XBM Support'                      => true,
					'WebP Support'                     => true,
					'BMP Support'                      => true,
					'AVIF Support'                     => false,
					'TGA Read Support'                 => true,
					'JIS-mapped Japanese Font Support' => false,
				),
			)
		);
		$this->assertSame(
			'bundled (2.1.0 compatible) (GIF, JPEG, PNG, WebP, BMP)',
			Display::get_display_gd_support( $post_id )
		);
	}

	public function test_gd_without_version_key_still_reports() {
		$post_id = $this->create_report(
			array( 'gd_info' => array( 'JPEG Support' => true ) )
		);
		$this->assertSame( 'Available (JPEG)', Display::get_display_gd_support( $post_id ) );
	}

	public function test_imagick_missing_key_returns_not_reported() {
		$post_id = $this->create_report( array( 'php_version' => '8.2' ) );
		$this->assertSame( 'Not reported', Display::get_display_imagick_support( $post_id ) );
	}

	public function test_imagick_empty_array_returns_not_reported() {
		$post_id = $this->create_report( array( 'imagick_info' => array() ) );
		$this->assertSame( 'Not reported', Display::get_display_imagick_support( $post_id ) );
	}

	public function test_imagick_counts_and_finds_common_formats_case_insensitively() {
		$post_id = $this->create_report(
			array(
				'imagick_info' => array( 'jpeg', 'Png', 'GIF', 'WEBP', 'TIFF', 'BMP', 'PDF', 'heic', 'jxl' ),
			)
		);
		$this->assertSame(
			'9 formats (JPEG, PNG, GIF, WEBP, HEIC, JXL)',
			Display::get_display_imagick_support( $post_id )
		);
	}

	public function test_imagick_single_format_uses_singular_wording() {
		$post_id = $this->create_report(
			array( 'imagick_info' => array( 'JPEG', 42, null ) )
		);
		$this->assertSame(
			'1 format (JPEG)',
			Display::get_display_imagick_support( $post_id )
		);
	}

	public function test_env_meta_stored_as_string_returns_not_reported() {
		$post_id = $this->create_report( 'corrupted' );
		$this->assertSame( 'Not reported', Display::get_display_gd_support( $post_id ) );
		$this->assertSame( 'Not reported', Display::get_display_imagick_support( $post_id ) );
	}

	public function test_no_env_meta_at_all_returns_not_reported() {
		$post_id = $this->create_report();
		$this->assertSame( 'Not reported', Display::get_display_gd_support( $post_id ) );
		$this->assertSame( 'Not reported', Display::get_display_imagick_support( $post_id ) );
	}

	public function test_output_is_plain_text_safe_under_esc_html() {
		$post_id = $this->create_report(
			array(
				'gd_info'      => array(
					'GD Version'   => '2.3.3',
					'JPEG Support' => true,
				),
				'imagick_info' => array( 'JPEG', 'PNG' ),
			)
		);
		foreach ( array(
			Display::get_display_gd_support( $post_id ),
			Display::get_display_imagick_support( $post_id ),
		) as $output ) {
			$this->assertSame( $output, esc_html( $output ) );
			$this->assertStringNotContainsString( '<', $output );
		}
	}
}
