<?php
/**
 * How the design system writes things a reader can check — dates, today.
 *
 * SPEC: "Write dates out (Sept. 15, 2026) and mark them up as `time` elements so people and
 * machines read the same date." Every data block that prints a date goes through here.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * A date as the design system writes it: "Oct. 1, 2026".
 *
 * WHY not `date_i18n()`: AP style abbreviates some months and not others (March, April, May,
 * June, July are never shortened; September is "Sept."), which no PHP format string does.
 *
 * @param string $ymd A date as YYYY-MM-DD.
 * @return string The formatted date, or the input if it does not parse.
 */
function ucf_theme_format_date( $ymd ) {
	$parts = array_map( 'intval', explode( '-', (string) $ymd ) );

	if ( 3 !== count( $parts ) || ! checkdate( $parts[1], $parts[2], $parts[0] ) ) {
		return (string) $ymd;
	}

	$months = array( 'Jan.', 'Feb.', 'March', 'April', 'May', 'June', 'July', 'Aug.', 'Sept.', 'Oct.', 'Nov.', 'Dec.' );

	return $months[ $parts[1] - 1 ] . ' ' . $parts[2] . ', ' . $parts[0];
}
