<?php
/**
 * Page-header custom fields.
 *
 * Registers post meta for the hero title/subtitle and the three breakpoint
 * background images, exposes them to the REST API, and enqueues the editor
 * sidebar panel (assets/js/page-header-panel.js) that edits them on the Page
 * edit screen. The page-header block (includes/page-header.php) reads this meta
 * at render time.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * The page-header meta keys and their REST types.
 *
 * Images store an attachment ID (integer); text fields store strings.
 *
 * @return array<string,string> Meta key => type.
 */
function ucf_block_theme_page_header_meta() {
	return array(
		'hero_title'    => 'string',
		'hero_subtitle' => 'string',
		'hero_image_lg' => 'integer',
		'hero_image_md' => 'integer',
		'hero_image_sm' => 'integer',
	);
}

/**
 * Register the page-header meta on Pages, exposed to REST so the block editor
 * can read/write it via useEntityProp.
 */
function ucf_block_theme_register_page_header_meta() {
	foreach ( ucf_block_theme_page_header_meta() as $key => $type ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'          => $type,
				'single'        => true,
				'show_in_rest'  => true,
				'default'       => ( 'integer' === $type ) ? 0 : '',
				'auth_callback' => function () {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_page_header_meta' );

/**
 * Enqueue the Page-editor sidebar panel for the page-header fields.
 */
function ucf_block_theme_enqueue_page_header_panel() {
	$rel  = 'assets/js/page-header-panel.js';
	$path = get_theme_file_path( $rel );

	wp_enqueue_script(
		'ucf-page-header-panel',
		get_theme_file_uri( $rel ),
		array(
			'wp-plugins',
			'wp-edit-post',
			'wp-element',
			'wp-components',
			'wp-block-editor',
			'wp-core-data',
			'wp-data',
			'wp-i18n',
		),
		file_exists( $path ) ? filemtime( $path ) : false,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'ucf_block_theme_enqueue_page_header_panel' );