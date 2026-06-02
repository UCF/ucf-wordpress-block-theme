<?php
/**
 * ACF field definitions for page headers (hero title / subtitle).
 *
 * Registered in PHP via ACF's local field groups so the fields live in version
 * control with the theme rather than the database. The whole file no-ops cleanly
 * when ACF is not active; an admin notice flags the missing dependency.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Whether Advanced Custom Fields is installed and active.
 *
 * `acf_add_local_field_group()` exists in both the free and Pro plugins once
 * active, so it's the most reliable capability check.
 *
 * @return bool
 */
function ucf_block_theme_acf_active() {
	return function_exists( 'acf_add_local_field_group' );
}

/**
 * Register the "Page Header" field group (hero title + subtitle) on Pages.
 *
 * These let an editor set header text per page independently of the page title,
 * which the page template overlays on the masthead image. The H1/subtitle the
 * template renders fall back to the page title when these are left blank
 * (fallback handled at render time, not here).
 */
function ucf_block_theme_register_page_header_fields() {
	if ( ! ucf_block_theme_acf_active() ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_ucf_page_header',
			'title'                 => __( 'Page Header', 'ucf-wordpress-block-theme' ),
			'fields'                => array(
				array(
					'key'          => 'field_ucf_hero_title',
					'label'        => __( 'Header title', 'ucf-wordpress-block-theme' ),
					'name'         => 'hero_title',
					'type'         => 'text',
					'instructions' => __( 'Overlaid on the header image. Leave blank to use the page title.', 'ucf-wordpress-block-theme' ),
				),
				array(
					'key'          => 'field_ucf_hero_subtitle',
					'label'        => __( 'Header subtitle', 'ucf-wordpress-block-theme' ),
					'name'         => 'hero_subtitle',
					'type'         => 'text',
					'instructions' => __( 'Optional supporting line shown beneath the title.', 'ucf-wordpress-block-theme' ),
				),
				array(
					'key'           => 'field_ucf_hero_image_lg',
					'label'         => __( 'Header image — desktop', 'ucf-wordpress-block-theme' ),
					'name'          => 'hero_image_lg',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'medium',
					'library'       => 'all',
					'instructions'  => __( 'Background image for the Desktop preview (wider than 780px). Used as the default if smaller breakpoints are left blank.', 'ucf-wordpress-block-theme' ),
				),
				array(
					'key'               => 'field_ucf_hero_image_md',
					'label'             => __( 'Header image — tablet', 'ucf-wordpress-block-theme' ),
					'name'              => 'hero_image_md',
					'type'              => 'image',
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'instructions'      => __( 'Background image for the Tablet preview (up to 780px wide). Falls back to the desktop image when blank.', 'ucf-wordpress-block-theme' ),
				),
				array(
					'key'               => 'field_ucf_hero_image_sm',
					'label'             => __( 'Header image — mobile', 'ucf-wordpress-block-theme' ),
					'name'              => 'hero_image_sm',
					'type'              => 'image',
					'return_format'     => 'id',
					'preview_size'      => 'medium',
					'library'           => 'all',
					'instructions'      => __( 'Background image for the Mobile preview (up to 360px wide). Falls back to the tablet, then desktop image when blank.', 'ucf-wordpress-block-theme' ),
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'page',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'active'                => true,
			'description'           => __( 'Title and subtitle overlaid on the page header image.', 'ucf-wordpress-block-theme' ),
		)
	);
}
add_action( 'acf/init', 'ucf_block_theme_register_page_header_fields' );

/**
 * Show an admin notice when ACF is missing, since the page-header fields
 * (and any template features that read them) depend on it.
 */
function ucf_block_theme_acf_admin_notice() {
	if ( ucf_block_theme_acf_active() ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html__( 'UCF Block Theme: Advanced Custom Fields (ACF) is not active. Page header title/subtitle fields are unavailable until it is installed and activated.', 'ucf-wordpress-block-theme' )
	);
}
add_action( 'admin_notices', 'ucf_block_theme_acf_admin_notice' );