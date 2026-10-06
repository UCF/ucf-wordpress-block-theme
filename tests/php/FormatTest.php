<?php
/**
 * Dates as the design system writes them.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_format_date
 */
final class FormatTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'format' );
	}

	/**
	 * AP style: some months abbreviate, five never do, and September is "Sept.".
	 *
	 * @dataProvider dates
	 * @param string $ymd      Input.
	 * @param string $expected Output.
	 * @return void
	 */
	public function test_writes_dates_in_ap_style( $ymd, $expected ) {
		$this->assertSame( $expected, ucf_theme_format_date( $ymd ) );
	}

	/**
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function dates() {
		return array(
			'abbreviated'       => array( '2026-01-05', 'Jan. 5, 2026' ),
			'September is Sept' => array( '2026-09-15', 'Sept. 15, 2026' ),
			'March in full'     => array( '2026-03-02', 'March 2, 2026' ),
			'July in full'      => array( '2026-07-31', 'July 31, 2026' ),
			'not a date'        => array( '2026-02-30', '2026-02-30' ),
			'garbage'           => array( 'soon', 'soon' ),
		);
	}
}
