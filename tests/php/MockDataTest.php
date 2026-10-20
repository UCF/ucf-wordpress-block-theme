<?php
/**
 * The mock data: that it loads, that a real source can replace it, and that each set has the
 * shape the block reading it documents.
 *
 * WHY the shape checks. A mock file edited by hand — a missing `level`, a fact with no
 * `value` — renders as a notice on the page and nothing in a build. These are the only check.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_mock_data
 * @covers ::ucf_theme_sample_note
 */
final class MockDataTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'mock-data' );

		Functions\when( 'get_theme_file_path' )->alias(
			static function ( $path ) {
				return UCF_THEME_DIR . '/' . $path;
			}
		);
		Functions\when( 'wp_json_file_decode' )->alias(
			static function ( $path ) {
				// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Stub for wp_json_file_decode(); no WordPress here.
				return json_decode( (string) file_get_contents( $path ), true );
			}
		);
	}

	/**
	 * A real source replaces a set through its filter, with no change to any block.
	 *
	 * @return void
	 */
	public function test_a_filter_replaces_a_set() {
		Filters\expectApplied( 'ucf_theme_mock_data_events' )->once()->andReturn( array( 'events' => array() ) );

		$this->assertSame( array( 'events' => array() ), ucf_theme_mock_data( 'events' ) );
	}

	/**
	 * A missing set is empty, not an error.
	 *
	 * @return void
	 */
	public function test_a_missing_set_is_empty() {
		$this->assertSame( array(), ucf_theme_mock_data( 'no-such-set' ) );
	}

	/**
	 * Programs: every list entry and every facts record has the documented keys.
	 *
	 * @return void
	 */
	public function test_programs_have_the_documented_shape() {
		$data = ucf_theme_mock_data( 'programs' );

		$this->assertNotEmpty( $data['programs'] );

		foreach ( $data['programs'] as $program ) {
			foreach ( array( 'slug', 'name', 'url', 'college', 'level' ) as $key ) {
				$this->assertNotEmpty( $program[ $key ] ?? null, "A program is missing `{$key}`." );
			}
		}

		foreach ( $data['facts'] as $slug => $facts ) {
			foreach ( array( 'name', 'url', 'source', 'facts' ) as $key ) {
				$this->assertArrayHasKey( $key, $facts, "Facts for {$slug} are missing `{$key}`." );
			}

			$this->assertMatchesRegularExpression( '/^\d{4}-\d{2}-\d{2}$/', $facts['source']['asOf'] );

			foreach ( $facts['facts'] as $fact ) {
				$this->assertNotEmpty( $fact['label'] );
				$this->assertNotEmpty( $fact['value'] );
			}
		}
	}

	/**
	 * People, events and alerts: the documented keys.
	 *
	 * @return void
	 */
	public function test_people_events_and_alerts_have_the_documented_shape() {
		foreach ( ucf_theme_mock_data( 'people' )['people'] as $slug => $person ) {
			foreach ( array( 'name', 'role', 'credentials', 'expertise', 'links', 'contact' ) as $key ) {
				$this->assertArrayHasKey( $key, $person, "{$slug} is missing `{$key}`." );
			}
		}

		foreach ( ucf_theme_mock_data( 'events' )['events'] as $event ) {
			foreach ( array( 'title', 'offset', 'time', 'location', 'url' ) as $key ) {
				$this->assertArrayHasKey( $key, $event );
			}
		}

		foreach ( array( 'emergency', 'notice' ) as $type ) {
			$alert = ucf_theme_mock_data( 'alerts' )['alerts'][ $type ];
			$this->assertNotEmpty( $alert['title'] );
			$this->assertNotEmpty( $alert['link']['url'] );
		}
	}

	/**
	 * The sample note is the design system's wording, in the sample style.
	 *
	 * @return void
	 */
	public function test_sample_note_says_what_it_is() {
		$this->assertStringContainsString( 'class="ucf-sample"', ucf_theme_sample_note() );
		$this->assertStringContainsString( 'Sample data for review', ucf_theme_sample_note() );
	}
}
