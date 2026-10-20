<?php
/**
 * The icon set: its names, its files, and what it registers.
 *
 * `ucf_theme_icons()` and assets/icons/ hold the same list in two forms. Drift is silent
 * either way: a name with no file registers an icon whose content is empty, which core
 * renders as nothing, and a file with no name is a glyph no editor can pick.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_icons
 * @covers ::ucf_theme_register_icons
 */
final class IconsTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'icons' );
	}

	/**
	 * The icon names, read from assets/icons/.
	 *
	 * @return string[]
	 */
	private function iconFiles() {
		$names = array();

		foreach ( (array) glob( UCF_THEME_DIR . '/assets/icons/*.svg' ) as $file ) {
			$names[] = basename( $file, '.svg' );
		}

		sort( $names );

		return $names;
	}

	/**
	 * Every listed icon has a file, and every file is listed.
	 *
	 * @return void
	 */
	public function test_listed_icons_match_the_files() {
		$listed = array_keys( ucf_theme_icons() );
		sort( $listed );

		$this->assertNotEmpty( $this->iconFiles(), 'assets/icons/ holds no SVGs — the glob is wrong, not the set.' );
		$this->assertSame( $this->iconFiles(), $listed, 'ucf_theme_icons() and assets/icons/ disagree.' );
	}

	/**
	 * Every file is a single-color SVG, so it takes its color from the text around it.
	 *
	 * WHY: a hard-coded fill is the one way an icon stops working on every field at once — it
	 * is right on white and invisible on black.
	 *
	 * @return void
	 */
	public function test_every_icon_inherits_its_color() {
		foreach ( (array) glob( UCF_THEME_DIR . '/assets/icons/*.svg' ) as $file ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own files; no WordPress in this suite.
			$svg = (string) file_get_contents( $file );

			$this->assertStringStartsWith( '<svg', $svg, basename( $file ) . ' is not an SVG.' );
			$this->assertStringContainsString( 'fill="currentColor"', $svg, basename( $file ) . ' does not inherit its color.' );
			$this->assertDoesNotMatchRegularExpression( '/fill="#/', $svg, basename( $file ) . ' hard-codes a fill.' );
		}
	}

	/**
	 * One collection, and every icon namespaced into it with its own file.
	 *
	 * @return void
	 */
	public function test_registers_each_icon_under_the_collection() {
		$collections = array();
		$icons       = array();

		Functions\when( 'get_theme_file_path' )->alias(
			static function ( $path ) {
				return UCF_THEME_DIR . '/' . $path;
			}
		);
		Functions\when( 'wp_register_icon_collection' )->alias(
			static function ( $slug ) use ( &$collections ) {
				$collections[] = $slug;
				return true;
			}
		);
		Functions\when( 'wp_register_icon' )->alias(
			static function ( $name, $args ) use ( &$icons ) {
				$icons[ $name ] = $args;
				return true;
			}
		);

		ucf_theme_register_icons();

		$this->assertSame( array( 'ucf' ), $collections );
		$this->assertCount( count( ucf_theme_icons() ), $icons );

		foreach ( $icons as $name => $args ) {
			$this->assertStringStartsWith( 'ucf/', $name );
			$this->assertFileExists( $args['file_path'], "{$name} points at a file that does not exist." );
			$this->assertNotEmpty( $args['label'] );
		}
	}
}
