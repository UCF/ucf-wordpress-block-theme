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
 * Load additional theme PHP from the includes/ directory.
 */
require_once get_theme_file_path( 'includes/patterns.php' );

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
 * Register Button block style variations (Athena button modifiers).
 *
 * Solid color, outline, inverse, and a large-size modifier. Each applies an
 * `is-style-{name}` class; the matching styles live in src/scss/_buttons.scss.
 * Block styles are single-select, so to pair a color with the large size, add
 * the utility classes (.btn-primary .btn-lg) via Advanced > Additional CSS class.
 */
function ucf_block_theme_register_button_styles() {
	$button_styles = array(
		'btn-primary'           => __( 'Gold', 'ucf-wordpress-block-theme' ),
		'btn-secondary'         => __( 'Black', 'ucf-wordpress-block-theme' ),
		'btn-inverse'           => __( 'Inverse', 'ucf-wordpress-block-theme' ),
		'btn-outline-primary'   => __( 'Outline gold', 'ucf-wordpress-block-theme' ),
		'btn-outline-secondary' => __( 'Outline black', 'ucf-wordpress-block-theme' ),
		'btn-outline-inverse'   => __( 'Outline inverse', 'ucf-wordpress-block-theme' ),
		'btn-lg'                => __( 'Large', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $button_styles as $name => $label ) {
		register_block_style(
			'core/button',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_button_styles' );

/**
 * Register Pullquote block style variations that recolor the top/bottom rules
 * with a palette color. Each applies an `is-style-pullquote-{slug}` class; the
 * matching styles live in src/scss/_blockquote.scss.
 */
function ucf_block_theme_register_pullquote_styles() {
	$border_colors = array(
		'pullquote-primary'   => __( 'Gold border', 'ucf-wordpress-block-theme' ),
		'pullquote-secondary' => __( 'Black border', 'ucf-wordpress-block-theme' ),
		'pullquote-success'   => __( 'Success border', 'ucf-wordpress-block-theme' ),
		'pullquote-info'      => __( 'Info border', 'ucf-wordpress-block-theme' ),
		'pullquote-warning'   => __( 'Warning border', 'ucf-wordpress-block-theme' ),
		'pullquote-danger'    => __( 'Danger border', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $border_colors as $name => $label ) {
		register_block_style(
			'core/pullquote',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_pullquote_styles' );

/**
 * Register Group block style variations: container components from Athena.
 *
 * "Card" (bordered panel), "Jumbotron" (padded hero band), "Well" (inset box),
 * and the static "Alert" callouts. Each applies an `is-style-{name}` class; the
 * matching styles live in src/scss/_group.scss and src/scss/_alerts.scss.
 */
function ucf_block_theme_register_group_styles() {
	$group_styles = array(
		'card'          => __( 'Card', 'ucf-wordpress-block-theme' ),
		'jumbotron'     => __( 'Jumbotron', 'ucf-wordpress-block-theme' ),
		'well'          => __( 'Well', 'ucf-wordpress-block-theme' ),
		'alert-primary' => __( 'Alert: Gold', 'ucf-wordpress-block-theme' ),
		'alert-success' => __( 'Alert: Success', 'ucf-wordpress-block-theme' ),
		'alert-info'    => __( 'Alert: Info', 'ucf-wordpress-block-theme' ),
		'alert-warning' => __( 'Alert: Warning', 'ucf-wordpress-block-theme' ),
		'alert-danger'  => __( 'Alert: Danger', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $group_styles as $name => $label ) {
		register_block_style(
			'core/group',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_group_styles' );

/**
 * Register Table block style variations (Athena table modifiers).
 *
 * Striped, bordered, hover, compact, and dark treatments. Each applies an
 * `is-style-{name}` class; the matching styles live in src/scss/_tables.scss.
 * Block styles are single-select per block — to combine more than one (e.g.
 * striped + bordered), add the equivalent utility classes (.table-striped,
 * .table-bordered, ...) via the block's Advanced > Additional CSS class field.
 */
function ucf_block_theme_register_table_styles() {
	$table_styles = array(
		'table-striped'  => __( 'Striped', 'ucf-wordpress-block-theme' ),
		'table-bordered' => __( 'Bordered', 'ucf-wordpress-block-theme' ),
		'table-hover'    => __( 'Row hover', 'ucf-wordpress-block-theme' ),
		'table-sm'       => __( 'Compact', 'ucf-wordpress-block-theme' ),
		'table-dark'     => __( 'Dark', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $table_styles as $name => $label ) {
		register_block_style(
			'core/table',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);
	}
}
add_action( 'init', 'ucf_block_theme_register_table_styles' );

/**
 * Register Tier 4 utility block style variations.
 *
 * The few Athena utilities worth exposing in the editor: visually-hidden
 * (.sr-only) and the text helpers (uppercase / nowrap / truncate). Each applies
 * an `is-style-{name}` class; the matching rules live in src/scss/_utilities.scss
 * (which also defines the equivalent plain utility classes for hand-application).
 */
function ucf_block_theme_register_utility_styles() {
	$text_styles = array(
		'sr-only'        => __( 'Screen-reader only', 'ucf-wordpress-block-theme' ),
		'text-uppercase' => __( 'Uppercase', 'ucf-wordpress-block-theme' ),
		'text-nowrap'    => __( 'No wrap', 'ucf-wordpress-block-theme' ),
		'text-truncate'  => __( 'Truncate', 'ucf-wordpress-block-theme' ),
	);

	foreach ( array( 'core/paragraph', 'core/heading' ) as $block ) {
		foreach ( $text_styles as $name => $label ) {
			register_block_style(
				$block,
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
	}
}
add_action( 'init', 'ucf_block_theme_register_utility_styles' );

/**
 * Disable the core Navigation block's "Page List" fallback.
 *
 * When no menu is assigned, the Navigation block falls back to a Page List,
 * which renders a <ul> directly inside the navigation's own <ul> — invalid,
 * inaccessible markup (axe "list" violation). Returning an empty fallback means
 * an unconfigured nav renders nothing rather than broken markup.
 */
add_filter( 'block_core_navigation_render_fallback', '__return_empty_string' );

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
