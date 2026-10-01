<?php
/**
 * The WordPress functions the theme's code calls, reproduced for the unit suite.
 *
 * The suite runs without WordPress, so anything the code under test calls has to exist here
 * or be stubbed per test. This file holds the first kind: functions with no behavior worth
 * varying between tests, defined once so every test reads the same implementation.
 *
 * The second kind — functions whose return value a test needs to *control*, like
 * `register_block_style()`, `wp_enqueue_script()` or `get_post_meta()` — are stubbed with
 * Brain Monkey inside the test that cares, not here.
 *
 * **Add a function when a test needs it, not before.** This file previously carried eight,
 * seven of which nothing called: `esc_html()`/`esc_attr()` and `esc_html__()`/`esc_attr__()`
 * are pairs with byte-identical bodies in core — they differ only in which filter they apply,
 * and a stub has no filters — so they were two names for one function that nothing invoked.
 * A stub that no test exercises is not a safety net; it is untested code asserting it works.
 *
 * Every definition is guarded by `function_exists()`, so this file stays safe to load in any
 * order and harmless if real WordPress is ever present in the same process.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! function_exists( '__' ) ) {
	/**
	 * Translation lookup.
	 *
	 * Identity, because the suite asserts on English output. `includes/block-styles.php` is
	 * the current caller — every block style label goes through it.
	 *
	 * @param string $text   Text to translate.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function __( $text, $domain = 'default' ) { // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.textFound
		return $text;
	}
}

if ( ! function_exists( 'wp_list_pluck' ) ) {
	/**
	 * One field from each item of a list.
	 *
	 * Reduced to what `includes/degree.php` calls it with: a list of arrays, one key, no
	 * index key. Items missing the key are skipped, as core does.
	 *
	 * @param array      $input List of arrays.
	 * @param int|string $field Key to pluck.
	 * @return array
	 */
	function wp_list_pluck( $input, $field ) {
		$plucked = array();
		foreach ( $input as $item ) {
			if ( is_array( $item ) && array_key_exists( $field, $item ) ) {
				$plucked[] = $item[ $field ];
			}
		}
		return $plucked;
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	/**
	 * Ensure exactly one trailing slash. Core's implementation, verbatim.
	 *
	 * @param string $value Path or URL.
	 * @return string
	 */
	function trailingslashit( $value ) {
		return rtrim( $value, '/\\' ) . '/';
	}
}

// Core's time constants, which `includes/search-service.php` computes cache lifetimes from.
// The prefix sniff is silenced because these are core's names, not the theme's: a prefixed
// copy would be a different constant, and the code under test would not see it.
defined( 'MINUTE_IN_SECONDS' ) || define( 'MINUTE_IN_SECONDS', 60 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
defined( 'HOUR_IN_SECONDS' ) || define( 'HOUR_IN_SECONDS', 3600 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
defined( 'WEEK_IN_SECONDS' ) || define( 'WEEK_IN_SECONDS', 604800 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound
