<?php
/**
 * The UCF block category, and that every theme block is filed under it.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_block_category
 */
final class BlocksTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'blocks' );
	}

	/**
	 * The category goes first, and only once.
	 *
	 * @return void
	 */
	public function test_adds_the_category_first_and_once() {
		$core = array(
			array(
				'slug'  => 'text',
				'title' => 'Text',
			),
		);

		$once = ucf_theme_block_category( $core );

		$this->assertSame( 'ucf', $once[0]['slug'] );
		$this->assertCount( 2, $once );
		$this->assertCount( 2, ucf_theme_block_category( $once ) );
	}

	/**
	 * Every block the theme registers in PHP names the UCF category.
	 *
	 * WHY read the source: the registrations run on `init` against real WordPress, which this
	 * suite does not have. A block added with core's `widgets` category would otherwise land
	 * outside the UCF group with nothing to say so.
	 *
	 * @return void
	 */
	public function test_every_theme_block_is_filed_under_ucf() {
		$found = 0;

		foreach ( (array) glob( UCF_THEME_DIR . '/includes/*.php' ) as $file ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own source; no WordPress here.
			$source = (string) file_get_contents( $file );

			preg_match_all( "/register_block_type\(\s*'ucf\/[a-z-]+',\s*array\((.*?)'render_callback'/s", $source, $blocks );

			foreach ( $blocks[1] as $args ) {
				++$found;
				$this->assertMatchesRegularExpression( "/'category'\s*=>\s*'ucf'/", $args, basename( $file ) . ' registers a block outside the UCF category.' );
			}
		}

		$this->assertGreaterThan( 0, $found, 'No register_block_type() calls were found — the pattern is wrong, not the theme.' );
		$this->assertSame( 'ucf', UCF_THEME_BLOCK_CATEGORY );
	}
}
