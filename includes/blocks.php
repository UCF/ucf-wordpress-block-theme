<?php
/**
 * Custom block registration.
 *
 * This file owns the *static* blocks only — the ones compiled from src/blocks/ whose
 * `save()` emits real markup.
 *
 * A server-rendered block does not belong here. A dynamic block lives in the file that owns
 * the data it renders, next to the queries and meta it reads, so that its registration and
 * its behavior are one thing rather than two files that have to be kept in step.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the theme's custom blocks.
 *
 * Every block here is static — its `save()` emits real markup and there is no
 * `render.php`, so nothing is rendered on the server. Sources live in `src/blocks/`,
 * compiled to `build/` by `npm run build:blocks`.
 *
 * Discovered from disk rather than listed: a compiled block folder is one that has a
 * `block.json` in it, which is the same test `register_block_type()` applies. Listing them
 * by hand makes adding a block a two-place edit, and the list falling out of step shows up
 * only as the block silently missing from the inserter.
 *
 * Nothing in the block sources should reference the theme. Keeping that true is what makes
 * moving these into a distribution plugin later a copy of `src/blocks/` plus this loop.
 *
 * @return void
 */
function ucf_theme_register_blocks() {
	// glob() returns false rather than an empty array when the directory is missing —
	// before a first build, say — and foreach over false is a PHP 8 warning.
	$manifests = (array) glob( get_theme_file_path( 'build/*/block.json' ) );

	foreach ( $manifests as $manifest ) {
		register_block_type( dirname( $manifest ) );
	}
}
add_action( 'init', 'ucf_theme_register_blocks' );
