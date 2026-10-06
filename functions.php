<?php
/**
 * Theme bootstrap.
 *
 * This file loads includes/ and does nothing else. Every behavior the theme adds lives in
 * one topic file there, so "where does X happen" is answered by the list below rather than
 * by scrolling. Anything new belongs in the file that owns its topic — or in a new one —
 * not here.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/*
 * Load order is the reading order, coarse to fine: what the theme *is*, then what it
 * loads, then what it registers, then the features built on top. No file here depends on
 * another at include time — every one only defines functions and adds hooks — so nothing
 * runs before WordPress calls it. Keep it that way: a require-time dependency between two
 * of these turns the order below from documentation into a constraint.
 */
$ucf_theme_files = array(
	'setup',              // Theme supports and editor rendering modes.
	'enqueue',            // Every way CSS and JS reach the front end or editor canvas.
	'blocks',             // Static custom blocks compiled from src/blocks/.
	'block-styles',       // register_block_style() for core blocks.
	'patterns',           // The UCF pattern category.
	'icons',              // The UCF icon set, registered with core's icon registry.
	'format',             // How dates are written, for every block that prints one.
	'mock-data',          // The stand-in for the systems the data blocks will read.
	'structured-data',    // One JSON-LD graph per page, from what the page renders.
	'programs',           // Facts and Program finder blocks.
	'people',             // Profile block.
	'events',             // Events block.
	'provenance',         // Provenance block and its reviewer field.
	'alerts',             // Alert banner block.
	'tables',             // Keyboard access to a table that scrolls sideways.
	'university-header',  // The UCF University Header: its script tag and placeholder.
	'paste-artifacts',    // Word-processor characters normalized on display.
);

foreach ( $ucf_theme_files as $ucf_theme_file ) {
	require_once get_theme_file_path( "includes/{$ucf_theme_file}.php" );
}

unset( $ucf_theme_files, $ucf_theme_file );
