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
