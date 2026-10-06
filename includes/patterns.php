<?php
/**
 * The UCF pattern category.
 *
 * WHY one category for every pattern the theme ships, rather than core's Featured, Text,
 * Banner and the rest: an author looking for "the UCF ones" finds them in one place, the same
 * reason every theme block is filed under the UCF block category (includes/blocks.php).
 *
 * SYNC: every file in patterns/ declares `Categories: ucf`. `PatternsTest` checks it, since a
 * pattern naming any other category lands outside the group with nothing to say so.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/** The pattern category slug every theme pattern declares. */
const UCF_THEME_PATTERN_CATEGORY = 'ucf';

/**
 * Register the category.
 *
 * @return void
 */
function ucf_theme_register_pattern_category() {
	register_block_pattern_category(
		UCF_THEME_PATTERN_CATEGORY,
		array(
			'label'       => __( 'UCF', 'ucf-wordpress-block-theme' ),
			'description' => __( 'Sections, components and page layouts from the UCF Web Design System.', 'ucf-wordpress-block-theme' ),
		)
	);
}
add_action( 'init', 'ucf_theme_register_pattern_category' );
