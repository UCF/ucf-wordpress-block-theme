<?php
/**
 * The UCF pattern category, and that every pattern is filed under it.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_register_pattern_category
 */
final class PatternsTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'patterns' );
	}

	/**
	 * The category is registered under the slug the patterns use.
	 *
	 * @return void
	 */
	public function test_registers_the_category() {
		$registered = array();

		Functions\when( 'register_block_pattern_category' )->alias(
			static function ( $slug ) use ( &$registered ) {
				$registered[] = $slug;
				return true;
			}
		);

		ucf_theme_register_pattern_category();

		$this->assertSame( array( UCF_THEME_PATTERN_CATEGORY ), $registered );
	}

	/**
	 * Every pattern file declares exactly the UCF category.
	 *
	 * WHY read the headers: core reads them the same way when it registers the patterns, so
	 * this checks what core will see.
	 *
	 * @return void
	 */
	public function test_every_pattern_is_filed_under_ucf() {
		$files = (array) glob( UCF_THEME_DIR . '/patterns/*.php' );

		$this->assertNotEmpty( $files, 'No pattern files were found — the glob is wrong, not the theme.' );

		foreach ( $files as $file ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own source; no WordPress here.
			$source = (string) file_get_contents( $file );

			$this->assertMatchesRegularExpression(
				'/^ \* Categories: ' . UCF_THEME_PATTERN_CATEGORY . '$/m',
				$source,
				basename( $file ) . ' is not filed under the UCF pattern category.'
			);
		}
	}
}
