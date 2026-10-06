<?php
/**
 * Tables: keyboard access to a table that scrolls sideways.
 *
 * FIX: core's Table scrolls inside its own `<figure>` when it is wider than the screen, which on
 * a phone the comparison table always is (src/scss/_table.scss gives it a 720px minimum). A
 * scrolling region a keyboard cannot reach fails WCAG 2.1.1 — the accessibility suite reported
 * it against the Comparison pattern the first time it ran (axe `scrollable-region-focusable`).
 * Core's markup has no tab stop, so one is added here, on render.
 *
 * A11Y: the figure becomes a focusable `region` named by the table's caption, so a screen reader
 * announces what it is entering. Applied to every table, not only comparisons — any table can
 * outgrow a phone.
 *
 * WHY this file has no unit test: it is a `WP_HTML_Tag_Processor` walk, which tests/README.md
 * keeps out of the unit tier. Its test is the pattern tier of the accessibility suite, which
 * audits the Comparison pattern at phone width.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Make a rendered table's scrolling figure keyboard-reachable and named.
 *
 * @param string $content Rendered `core/table` block.
 * @return string The block, with the figure focusable.
 */
function ucf_theme_table_region( $content ) {
	$caption = '';

	if ( preg_match( '#<figcaption[^>]*>(.*?)</figcaption>#s', $content, $match ) ) {
		$caption = trim( wp_strip_all_tags( $match[1] ) );
	}

	$figure = new WP_HTML_Tag_Processor( $content );

	if ( ! $figure->next_tag( 'figure' ) ) {
		return $content;
	}

	$figure->set_attribute( 'tabindex', '0' );
	$figure->set_attribute( 'role', 'region' );
	$figure->set_attribute( 'aria-label', '' !== $caption ? $caption : __( 'Table', 'ucf-wordpress-block-theme' ) );

	return $figure->get_updated_html();
}
add_filter( 'render_block_core/table', 'ucf_theme_table_region' );
