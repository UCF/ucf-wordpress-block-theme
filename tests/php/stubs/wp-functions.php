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

/*
 * The escaping and formatting functions the data blocks' markup builders call
 * (includes/programs.php, people.php, events.php, provenance.php, alerts.php). Real
 * definitions rather than per-test stubs, because no test varies them: the builders' tests
 * assert on the markup, and these must simply behave like core's for plain values.
 */

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * Escape for HTML text. Same entity set as core for the characters a test uses.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	function esc_html( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * Escape for an HTML attribute.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	function esc_attr( $text ) {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	/**
	 * Escape a URL. Passes http(s) and fragment URLs through; drops anything else, as core does
	 * for a disallowed protocol.
	 *
	 * @param string $url URL.
	 * @return string
	 */
	function esc_url( $url ) {
		return preg_match( '#^(https?://|\#|/)#', (string) $url ) ? htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' ) : '';
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * Translate and escape.
	 *
	 * @param string $text   Text.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function esc_html__( $text, $domain = 'default' ) {
		return esc_html( $text );
	}
}

if ( ! function_exists( '_n' ) ) {
	/**
	 * Plural lookup.
	 *
	 * @param string $single Singular.
	 * @param string $plural Plural.
	 * @param int    $number Count.
	 * @param string $domain Text domain.
	 * @return string
	 */
	function _n( $single, $plural, $number, $domain = 'default' ) {
		return 1 === (int) $number ? $single : $plural;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * JSON encode.
	 *
	 * @param mixed $data  Data.
	 * @param int   $flags Flags.
	 * @return string|false
	 */
	function wp_json_encode( $data, $flags = 0 ) {
		return json_encode( $data, $flags ); // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode -- This is the stub for wp_json_encode().
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * Strip tags.
	 *
	 * @param string $text Text.
	 * @return string
	 */
	function wp_strip_all_tags( $text ) {
		return trim( strip_tags( (string) $text ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.strip_tags_strip_tags -- This is the stub for wp_strip_all_tags().
	}
}

if ( ! defined( 'DAY_IN_SECONDS' ) ) {
	define( 'DAY_IN_SECONDS', 86400 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound -- Core's own constant, reproduced for the suite.
}
