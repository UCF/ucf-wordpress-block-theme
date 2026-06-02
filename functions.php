<?php
/**
 * UCF WordPress Block Theme functions.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register heading-size block style variations.
 *
 * These let an editor render a heading at a different visual size
 * (e.g. an <h1> that looks like an <h3>) while keeping the semantic
 * tag intact — mirroring the Athena Framework's .h1–.h6 utilities.
 *
 * Each style applies an `is-style-{name}` class; the matching font
 * sizes live in theme.json under styles.css.
 */
function ucf_block_theme_register_heading_styles() {
	$heading_sizes = array(
		'h1' => __( 'Heading 1 size', 'ucf-wordpress-block-theme' ),
		'h2' => __( 'Heading 2 size', 'ucf-wordpress-block-theme' ),
		'h3' => __( 'Heading 3 size', 'ucf-wordpress-block-theme' ),
		'h4' => __( 'Heading 4 size', 'ucf-wordpress-block-theme' ),
		'h5' => __( 'Heading 5 size', 'ucf-wordpress-block-theme' ),
		'h6' => __( 'Heading 6 size', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $heading_sizes as $name => $label ) {
		register_block_style(
			'core/heading',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}

	// Underline variation: uppercase text with a short UCF Gold accent bar.
	register_block_style(
		'core/heading',
		array(
			'name'  => 'heading-underline',
			'label' => __( 'Underline', 'ucf-wordpress-block-theme' ),
		)
	);

	// Display headings: oversized, light hero type (Athena .display-1–.display-4).
	$display_sizes = array(
		'display-1' => __( 'Display 1', 'ucf-wordpress-block-theme' ),
		'display-2' => __( 'Display 2', 'ucf-wordpress-block-theme' ),
		'display-3' => __( 'Display 3', 'ucf-wordpress-block-theme' ),
		'display-4' => __( 'Display 4', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $display_sizes as $name => $label ) {
		register_block_style(
			'core/heading',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_heading_styles' );

/**
 * Register body-copy block style variations.
 *
 * "Lead" renders larger, lighter intro text (Athena's .lead). It applies an
 * `is-style-lead` class; the matching styles live in src/scss/_text.scss.
 */
function ucf_block_theme_register_text_styles() {
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'lead',
			'label' => __( 'Lead', 'ucf-wordpress-block-theme' ),
		)
	);
}
add_action( 'init', 'ucf_block_theme_register_text_styles' );

/**
 * Register List block style variations.
 *
 * "Unstyled" removes bullets/indent; "Inline" lays items out horizontally
 * (Athena's .list-unstyled / .list-inline). Each applies an
 * `is-style-{name}` class; the matching styles live in src/scss/_lists.scss.
 */
function ucf_block_theme_register_list_styles() {
	$list_styles = array(
		'list-unstyled' => __( 'Unstyled', 'ucf-wordpress-block-theme' ),
		'list-inline'   => __( 'Inline', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $list_styles as $name => $label ) {
		register_block_style(
			'core/list',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_list_styles' );

/**
 * Register Quote block style variations that recolor the left accent border
 * with a color from the theme palette. Each applies an
 * `is-style-quote-{slug}` class; the matching styles live in
 * src/scss/_blockquote.scss.
 */
function ucf_block_theme_register_quote_styles() {
	$border_colors = array(
		'quote-primary'   => __( 'Gold border', 'ucf-wordpress-block-theme' ),
		'quote-secondary' => __( 'Black border', 'ucf-wordpress-block-theme' ),
		'quote-success'   => __( 'Success border', 'ucf-wordpress-block-theme' ),
		'quote-info'      => __( 'Info border', 'ucf-wordpress-block-theme' ),
		'quote-warning'   => __( 'Warning border', 'ucf-wordpress-block-theme' ),
		'quote-danger'    => __( 'Danger border', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $border_colors as $name => $label ) {
		register_block_style(
			'core/quote',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_quote_styles' );

/**
 * Enqueue the compiled theme stylesheet on the front end.
 *
 * Built from src/scss via `npm run build` -> assets/css/main.css.
 */
function ucf_block_theme_enqueue_styles() {
	$relative_path = 'assets/css/main.css';
	$file_path     = get_theme_file_path( $relative_path );
	$version       = file_exists( $file_path ) ? filemtime( $file_path ) : false;

	wp_enqueue_style(
		'ucf-block-theme',
		get_theme_file_uri( $relative_path ),
		array(),
		$version
	);
}
add_action( 'wp_enqueue_scripts', 'ucf_block_theme_enqueue_styles' );

/**
 * Load the same compiled stylesheet inside the block editor so heading
 * sizes and font utilities render accurately while editing.
 */
function ucf_block_theme_editor_styles() {
	add_editor_style( 'assets/css/main.css' );
}
add_action( 'after_setup_theme', 'ucf_block_theme_editor_styles' );
