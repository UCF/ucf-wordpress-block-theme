<?php
/**
 * The UCF Search Service, read at render time.
 *
 * CONTEXT: the Search Service (search.cm.ucf.edu) is the system of record for degree
 * programs. The UCF-Degree-CPT-Plugin importer copies it into post meta on a Jenkins
 * schedule; this theme instead reads it live, so a degree post only has to carry its program
 * id and everything shown on the page is as current as the service. This file is the whole
 * client: one URL, one GET, one cache. What is done with a response is the caller's topic.
 *
 * WHY: GET only, and there is deliberately no function here that can send anything else.
 * The service permits anonymous reads and requires a key to write, and the degree plugin's
 * own save hook already writes back to it — a second write path is not something a theme
 * should own.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Base URL of the Search Service API, with a trailing slash.
 *
 * WHY: falls back to the degree plugin's own setting before the hardcoded default. Both
 * read the same service, and a site pointed somewhere else for importing (a staging
 * instance, a local stack) should render from that same place without a second setting to
 * keep in step.
 *
 * @return string
 */
function ucf_theme_search_service_base_url() {
	$configured = get_option( 'ucf_degree_api_base_url' );
	$base_url   = $configured ? $configured : 'https://search.cm.ucf.edu/api/v1/';

	/**
	 * Filters the Search Service API base URL the theme reads from.
	 *
	 * @param string $base_url Absolute URL of the API root.
	 */
	$base_url = apply_filters( 'ucf_theme_search_service_base_url', $base_url );

	return rtrim( $base_url, '/' ) . '/';
}

/**
 * Absolute URL for one API path.
 *
 * @param string $path  Path under the API root, e.g. `programs/1004/`.
 * @param array  $query Query arguments.
 * @return string
 */
function ucf_theme_search_service_url( $path, $query = array() ) {
	$url = ucf_theme_search_service_base_url() . ltrim( $path, '/' );

	if ( $query ) {
		ksort( $query );
		$url .= '?' . http_build_query( $query, '', '&' );
	}

	return $url;
}

/**
 * GET one Search Service resource, decoded, through the cache.
 *
 * Two copies are kept per URL. The fresh one expires on the cache lifetime and is what a
 * normal request reads. The stale one outlives it by a week and is only read when a refetch
 * fails, so an outage of the service degrades a degree page to slightly old data rather than
 * to an empty one.
 *
 * PERF: an uncached program read measured 150–230 ms from this stack, and a full degree page
 * makes five of them (program, outcomes, projections, careers, deadlines). Uncached, that is
 * the better part of a second on every view.
 *
 * WHY: a failure is remembered for five minutes. Without that, every page view during an
 * outage waits out the full timeout before falling back.
 *
 * @param string $path  Path under the API root, e.g. `programs/1004/`.
 * @param array  $query Query arguments.
 * @return array|null Decoded JSON, or null when there is neither a fresh nor a stale copy.
 */
function ucf_theme_search_service_get( $path, $query = array() ) {
	$url       = ucf_theme_search_service_url( $path, $query );
	$key       = 'ucf_theme_ss_' . md5( $url );
	$stale_key = $key . '_stale';

	$cached = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	if ( 'failed' === $cached ) {
		$stale = get_transient( $stale_key );
		return is_array( $stale ) ? $stale : null;
	}

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 5,
			'headers' => array( 'Accept' => 'application/json' ),
		)
	);

	$data = null;
	if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
	}

	if ( ! is_array( $data ) ) {
		set_transient( $key, 'failed', 5 * MINUTE_IN_SECONDS );
		$stale = get_transient( $stale_key );
		return is_array( $stale ) ? $stale : null;
	}

	/**
	 * Filters how long a Search Service response is served before it is refetched.
	 *
	 * @param int    $lifetime Seconds.
	 * @param string $url      The URL that was fetched.
	 */
	$lifetime = (int) apply_filters( 'ucf_theme_search_service_cache_lifetime', 6 * HOUR_IN_SECONDS, $url );

	set_transient( $key, $data, $lifetime );
	set_transient( $stale_key, $data, $lifetime + WEEK_IN_SECONDS );

	return $data;
}
