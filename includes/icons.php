<?php
/**
 * The UCF icon set, registered with core's icon registry.
 *
 * WordPress 7.1 ships an icon registry and a core Icon block that reads it, so the design
 * system's icon set needs no block of its own: registering the glyphs as a `ucf` collection
 * puts them in the Icon block's picker beside core's, and `wp_get_icon( 'ucf/download' )`
 * returns the same markup on the server.
 *
 * SPEC: the set is the UCF Web Design System's — Font Awesome 6 Free Solid (6.7.2), the glyphs
 * its component briefs name, extracted from its sprite into assets/icons/. Licensed CC BY 4.0;
 * see assets/icons/README.md. The system's own choice is Sharp Solid under UCF's Pro license,
 * which cannot be fetched without the license token; the names match across families, so
 * swapping the files is the whole change.
 *
 * WHY: icons clarify an action or a kind of thing — download, external link, date, location.
 * They never decorate, and they take their color from the text around them.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/** The collection slug, and the namespace every icon name is registered under. */
const UCF_THEME_ICON_COLLECTION = 'ucf';

/**
 * Icon names and their labels.
 *
 * SYNC: one entry per file in assets/icons/, named for the file. `IconsTest` checks the two
 * agree in both directions — a name with no file registers nothing, and a file with no name
 * is a glyph no editor can reach.
 *
 * @return array<string, string> Icon name to label.
 */
function ucf_theme_icons() {
	return array(
		'alert'        => __( 'Alert', 'ucf-wordpress-block-theme' ),
		'alert-bell'   => __( 'Alert bell', 'ucf-wordpress-block-theme' ),
		'arrow'        => __( 'Arrow', 'ucf-wordpress-block-theme' ),
		'calendar'     => __( 'Calendar', 'ucf-wordpress-block-theme' ),
		'campus'       => __( 'Campus', 'ucf-wordpress-block-theme' ),
		'career'       => __( 'Career', 'ucf-wordpress-block-theme' ),
		'check'        => __( 'Check', 'ucf-wordpress-block-theme' ),
		'chevron'      => __( 'Chevron', 'ucf-wordpress-block-theme' ),
		'chevron-down' => __( 'Chevron down', 'ucf-wordpress-block-theme' ),
		'clock'        => __( 'Clock', 'ucf-wordpress-block-theme' ),
		'close'        => __( 'Close', 'ucf-wordpress-block-theme' ),
		'code'         => __( 'Code', 'ucf-wordpress-block-theme' ),
		'cost'         => __( 'Cost', 'ucf-wordpress-block-theme' ),
		'course'       => __( 'Course', 'ucf-wordpress-block-theme' ),
		'data'         => __( 'Data', 'ucf-wordpress-block-theme' ),
		'degree'       => __( 'Degree', 'ucf-wordpress-block-theme' ),
		'download'     => __( 'Download', 'ucf-wordpress-block-theme' ),
		'error'        => __( 'Error', 'ucf-wordpress-block-theme' ),
		'external'     => __( 'External link', 'ucf-wordpress-block-theme' ),
		'filter'       => __( 'Filter', 'ucf-wordpress-block-theme' ),
		'health'       => __( 'Health', 'ucf-wordpress-block-theme' ),
		'info'         => __( 'Information', 'ucf-wordpress-block-theme' ),
		'lab'          => __( 'Lab', 'ucf-wordpress-block-theme' ),
		'link'         => __( 'Link', 'ucf-wordpress-block-theme' ),
		'mail'         => __( 'Email', 'ucf-wordpress-block-theme' ),
		'map'          => __( 'Map', 'ucf-wordpress-block-theme' ),
		'menu'         => __( 'Menu', 'ucf-wordpress-block-theme' ),
		'minus'        => __( 'Minus', 'ucf-wordpress-block-theme' ),
		'pdf'          => __( 'PDF', 'ucf-wordpress-block-theme' ),
		'people'       => __( 'People', 'ucf-wordpress-block-theme' ),
		'phone'        => __( 'Phone', 'ucf-wordpress-block-theme' ),
		'pin'          => __( 'Location', 'ucf-wordpress-block-theme' ),
		'play'         => __( 'Play', 'ucf-wordpress-block-theme' ),
		'plus'         => __( 'Plus', 'ucf-wordpress-block-theme' ),
		'print'        => __( 'Print', 'ucf-wordpress-block-theme' ),
		'research'     => __( 'Research', 'ucf-wordpress-block-theme' ),
		'search'       => __( 'Search', 'ucf-wordpress-block-theme' ),
		'share'        => __( 'Share', 'ucf-wordpress-block-theme' ),
		'signal'       => __( 'Signal', 'ucf-wordpress-block-theme' ),
		'space'        => __( 'Space', 'ucf-wordpress-block-theme' ),
		'student'      => __( 'Student', 'ucf-wordpress-block-theme' ),
		'success'      => __( 'Success', 'ucf-wordpress-block-theme' ),
	);
}

/**
 * Register the collection and every icon in it.
 *
 * UPSTREAM: the registry is new in WordPress 7.1. On an older install the functions do not
 * exist and the theme simply has no icons — the Icon block does not exist there either.
 *
 * @return void
 */
function ucf_theme_register_icons() {
	if ( ! function_exists( 'wp_register_icon_collection' ) ) {
		return;
	}

	wp_register_icon_collection(
		UCF_THEME_ICON_COLLECTION,
		array(
			'label'       => __( 'UCF', 'ucf-wordpress-block-theme' ),
			'description' => __( 'The UCF Web Design System icon set.', 'ucf-wordpress-block-theme' ),
		)
	);

	foreach ( ucf_theme_icons() as $name => $label ) {
		wp_register_icon(
			UCF_THEME_ICON_COLLECTION . '/' . $name,
			array(
				'label'     => $label,
				'file_path' => get_theme_file_path( "assets/icons/{$name}.svg" ),
			)
		);
	}
}
add_action( 'init', 'ucf_theme_register_icons' );
