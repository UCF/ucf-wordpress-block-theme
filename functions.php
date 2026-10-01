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
	'university-header',  // The UCF University Header: its script tag and placeholder.
	'paste-artifacts',    // Word-processor characters normalized on display.
	'search-service',     // Cached, read-only GETs against the UCF Search Service API.
	'degree',             // Degree pages: bindings source and blocks fed by the Search Service.
);

foreach ( $ucf_theme_files as $ucf_theme_file ) {
	require_once get_theme_file_path( "includes/{$ucf_theme_file}.php" );
}

unset( $ucf_theme_files, $ucf_theme_file );
