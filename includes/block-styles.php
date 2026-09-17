<?php
/**
 * Block style registration.
 *
 * Every `register_block_style()` in the theme is here; each one's CSS lives in a partial
 * under src/scss/ named in the comment above it. A style registered here and not defined
 * there is an editor offering that paints nothing, so the two move together — add the rule
 * in the same commit as the registration.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the theme's block styles.
 *
 * @return void
 */
function ucf_theme_register_block_styles() {
	ucf_theme_register_composition_styles();

	// `.is-style-on-dark` supplies the dark treatment only — no background and no base
	// color, both of which stay with the block's own controls. It is the escape hatch for a
	// block that already has its background set some other way (a cover image, a featured
	// image) and needs the text roles to follow. Definition: src/scss/_compositions.scss.
	register_block_style(
		'core/group',
		array(
			'name'  => 'on-dark',
			'label' => __( 'On Dark', 'ucf-wordpress-block-theme' ),
		)
	);
}
add_action( 'init', 'ucf_theme_register_block_styles' );

/**
 * Register the color compositions as core/group block styles.
 *
 * One primitive, four color pairs, two flavors each — a plain background/text pair and the
 * same pair with a 3px accent rule on the leading edge:
 *
 *     is-style-paper           background + text only
 *     is-style-paper-accent    the same pair plus the edge rule
 *
 * Definitions live in src/scss/_compositions.scss, which also declares the `--ucf-*` roles
 * each pair supplies — including `--ucf-accent`, set by both flavors so a component can bind
 * it whether or not the edge rule is showing.
 *
 * SYNC: adding a pair means a row here plus a row in the `$compositions` map in
 * _compositions.scss. PHP cannot read a Sass map, so the two lists are maintained by hand;
 * a name in one and not the other is either a style that paints nothing or CSS no editor can
 * reach.
 *
 * @return void
 */
function ucf_theme_register_composition_styles() {
	$compositions = array(
		'bold-gold' => __( 'Bold Gold', 'ucf-wordpress-block-theme' ),
		'paper'     => __( 'Paper', 'ucf-wordpress-block-theme' ),
		'light'     => __( 'Light', 'ucf-wordpress-block-theme' ),
		'dark'      => __( 'Dark', 'ucf-wordpress-block-theme' ),
	);

	foreach ( $compositions as $name => $label ) {
		register_block_style(
			'core/group',
			array(
				'name'  => $name,
				'label' => $label,
			)
		);

		register_block_style(
			'core/group',
			array(
				'name'  => $name . '-accent',
				/* translators: %s: composition color pair name, e.g. "Paper". */
				'label' => sprintf( __( '%s + Accent', 'ucf-wordpress-block-theme' ), $label ),
			)
		);
	}
}
