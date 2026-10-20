<?php
/**
 * The style variation's body class.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_variation_classes
 */
final class SetupTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'setup' );
	}

	/**
	 * Only the Brighter variation's mode adds a class, and the class is the one
	 * _compositions.scss keys on.
	 *
	 * @return void
	 */
	public function test_bright_mode_adds_the_class_the_stylesheet_reads() {
		$this->assertSame( array( 'is-bright' ), ucf_theme_variation_classes( 'bright' ) );
		$this->assertSame( array(), ucf_theme_variation_classes( null ) );
		$this->assertSame( array(), ucf_theme_variation_classes( array() ) );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own source; no WordPress here.
		$this->assertStringContainsString( '.is-bright .is-style-dark', (string) file_get_contents( UCF_THEME_DIR . '/src/scss/_compositions.scss' ) );

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- As above.
		$variation = json_decode( (string) file_get_contents( UCF_THEME_DIR . '/styles/bright.json' ), true );
		$this->assertSame( 'bright', $variation['settings']['custom']['mode'] );
	}
}
