<?php
/**
 * The Search Service client.
 *
 * What is worth pinning is the cache, because each of its failures is silent: a page that
 * refetches on every view is merely slow, and a page that renders nothing during an outage
 * looks like a program with no data.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Filters;
use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_search_service_base_url
 * @covers ::ucf_theme_search_service_url
 * @covers ::ucf_theme_search_service_get
 */
final class SearchServiceTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'search-service' );
	}

	/**
	 * @return void
	 */
	public function test_base_url_defaults_to_production_when_nothing_is_configured() {
		Functions\when( 'get_option' )->justReturn( false );

		$this->assertSame( 'https://search.cm.ucf.edu/api/v1/', ucf_theme_search_service_base_url() );
	}

	/**
	 * The plugin's setting wins, so importing and rendering read the same instance. It is
	 * saved with or without a trailing slash depending on who saved it.
	 *
	 * @return void
	 */
	public function test_base_url_follows_the_degree_plugin_setting_and_normalizes_the_slash() {
		Functions\when( 'get_option' )->justReturn( 'http://localhost:8000/api/v1' );

		$this->assertSame( 'http://localhost:8000/api/v1/', ucf_theme_search_service_base_url() );
	}

	/**
	 * @return void
	 */
	public function test_base_url_is_filterable() {
		Functions\when( 'get_option' )->justReturn( false );
		Filters\expectApplied( 'ucf_theme_search_service_base_url' )->once()->andReturn( 'https://staging.example/api/' );

		$this->assertSame( 'https://staging.example/api/', ucf_theme_search_service_base_url() );
	}

	/**
	 * The URL is the cache key, so two orderings of the same query must be one URL.
	 *
	 * @return void
	 */
	public function test_url_sorts_the_query_so_equal_requests_share_a_cache_key() {
		Functions\when( 'get_option' )->justReturn( false );

		$this->assertSame(
			ucf_theme_search_service_url(
				'programs/search/',
				array(
					'b' => 2,
					'a' => 1,
				)
			),
			ucf_theme_search_service_url(
				'/programs/search/',
				array(
					'a' => 1,
					'b' => 2,
				)
			)
		);
		$this->assertSame(
			'https://search.cm.ucf.edu/api/v1/programs/search/?a=1&b=2',
			ucf_theme_search_service_url(
				'programs/search/',
				array(
					'b' => 2,
					'a' => 1,
				)
			)
		);
	}

	/**
	 * @return void
	 */
	public function test_a_fresh_cached_copy_is_served_without_a_request() {
		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_transient' )->justReturn( array( 'id' => 1004 ) );
		Functions\expect( 'wp_remote_get' )->never();

		$this->assertSame( array( 'id' => 1004 ), ucf_theme_search_service_get( 'programs/1004/' ) );
	}

	/**
	 * @return void
	 */
	public function test_a_successful_fetch_stores_a_fresh_and_a_longer_lived_stale_copy() {
		$stored = array();

		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'wp_remote_get' )->justReturn( array() );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( '{"id":1004}' );
		Functions\when( 'set_transient' )->alias(
			function ( $key, $value, $ttl ) use ( &$stored ) {
				$stored[ $key ] = array( $value, $ttl );
				return true;
			}
		);

		$this->assertSame( array( 'id' => 1004 ), ucf_theme_search_service_get( 'programs/1004/' ) );

		$key = 'ucf_theme_ss_' . md5( 'https://search.cm.ucf.edu/api/v1/programs/1004/' );
		$this->assertSame( array( array( 'id' => 1004 ), 6 * HOUR_IN_SECONDS ), $stored[ $key ] );
		$this->assertSame( array( array( 'id' => 1004 ), 6 * HOUR_IN_SECONDS + WEEK_IN_SECONDS ), $stored[ "{$key}_stale" ] );
	}

	/**
	 * An outage degrades to old data, and is remembered so the next view does not wait out
	 * the timeout again.
	 *
	 * @return void
	 */
	public function test_a_failed_fetch_serves_the_stale_copy_and_remembers_the_failure() {
		$stored = array();

		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_transient' )->alias(
			function ( $key ) {
				return '_stale' === substr( $key, -6 ) ? array(
					'id'   => 1004,
					'name' => 'Old',
				) : false;
			}
		);
		Functions\when( 'wp_remote_get' )->justReturn( array() );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 503 );
		Functions\when( 'set_transient' )->alias(
			function ( $key, $value, $ttl ) use ( &$stored ) {
				$stored[ $key ] = array( $value, $ttl );
				return true;
			}
		);

		$this->assertSame(
			array(
				'id'   => 1004,
				'name' => 'Old',
			),
			ucf_theme_search_service_get( 'programs/1004/' )
		);
		$this->assertSame( array( array( 'failed', 5 * MINUTE_IN_SECONDS ) ), array_values( $stored ) );
	}

	/**
	 * @return void
	 */
	public function test_a_remembered_failure_does_not_refetch() {
		Functions\when( 'get_option' )->justReturn( false );
		Functions\when( 'get_transient' )->alias(
			function ( $key ) {
				return '_stale' === substr( $key, -6 ) ? false : 'failed';
			}
		);
		Functions\expect( 'wp_remote_get' )->never();

		$this->assertNull( ucf_theme_search_service_get( 'programs/1004/' ) );
	}
}
