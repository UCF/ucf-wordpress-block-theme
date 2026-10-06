<?php
/**
 * Custom block registration.
 *
 * This file owns the *static* blocks — the ones compiled from src/blocks/ whose `save()`
 * emits real markup — and the inserter category every theme block is filed under.
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

/**
 * The inserter category every block the theme adds is filed under.
 *
 * WHY one category for all of them — the data blocks, and the Section variation of core/group:
 * an author looking for "the UCF ones" finds them in one place, rather than scattered across
 * Widgets and Design among core's.
 *
 * SYNC: each block's `category` — in includes/programs.php, people.php, events.php,
 * provenance.php, alerts.php and src/js/editor/section-variation.js — names this slug.
 * `BlocksTest` checks the PHP registrations; the JS one is by hand.
 */
const UCF_THEME_BLOCK_CATEGORY = 'ucf';

/**
 * Add the UCF category to the inserter, first in the list.
 *
 * @param array<int, array<string, mixed>> $categories Registered categories.
 * @return array<int, array<string, mixed>> Categories, with UCF first.
 */
function ucf_theme_block_category( $categories ) {
	foreach ( $categories as $category ) {
		if ( UCF_THEME_BLOCK_CATEGORY === $category['slug'] ) {
			return $categories;
		}
	}

	array_unshift(
		$categories,
		array(
			'slug'  => UCF_THEME_BLOCK_CATEGORY,
			'title' => __( 'UCF', 'ucf-wordpress-block-theme' ),
			'icon'  => null,
		)
	);

	return $categories;
}
add_filter( 'block_categories_all', 'ucf_theme_block_category' );
