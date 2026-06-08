<?php
/**
 * Block pattern support: categories and new-page starter content.
 *
 * Patterns themselves live in the /patterns/ directory and are auto-registered
 * by WordPress. This file registers their category and seeds new Pages with the
 * Hero pattern so authors get an editable hero at the top of every new page.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the pattern category used to group the theme's header patterns.
 */
function ucf_block_theme_register_pattern_categories() {
	register_block_pattern_category(
		'ucf-headers',
		array( 'label' => __( 'UCF Headers', 'ucf-wordpress-block-theme' ) )
	);
	register_block_pattern_category(
		'ucf-blocks',
		array( 'label' => __( 'UCF Blocks', 'ucf-wordpress-block-theme' ) )
	);
}
add_action( 'init', 'ucf_block_theme_register_pattern_categories' );

/**
 * Seed new Pages with the Hero pattern as editable starter content.
 *
 * Pulls the registered pattern's block markup so the source of truth stays in
 * /patterns/hero.php. Only applies to new Pages; existing pages are untouched.
 *
 * @param string  $content Default editor content.
 * @param WP_Post $post    Post being created.
 * @return string
 */
function ucf_block_theme_default_page_content( $content, $post ) {
	if ( ! ( $post instanceof WP_Post ) || 'page' !== $post->post_type ) {
		return $content;
	}

	if ( ! class_exists( 'WP_Block_Patterns_Registry' ) ) {
		return $content;
	}

	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( 'ucf/hero' );

	return ( $pattern && ! empty( $pattern['content'] ) ) ? $pattern['content'] : $content;
}
add_filter( 'default_content', 'ucf_block_theme_default_page_content', 10, 2 );