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
}
add_action( 'init', 'ucf_theme_register_block_styles' );

/**
 * Register the color compositions as core/group block styles.
 *
 * One primitive, four color pairs, one registered style each — `is-style-paper` and friends,
 * a background plus the text roles that go with it.
 *
 * Definitions live in src/scss/_compositions.scss, which also declares the `--ucf-*` roles
 * each pair supplies.
 *
 * WHY only one per pair: each composition also has an `-accent` flavor in the stylesheet,
 * adding a rule on the leading edge. Those are deliberately not registered. The accent is
 * established elsewhere — by whatever component or pattern wants the edge — rather than
 * chosen by an author from the Styles panel, and registering them would double the length of
 * that panel to offer eight variants of one decision.
 *
 * NOTE: this is the theme's one intentional case of CSS with no registration behind it. The
 * general rule still holds everywhere else — a rule defined and not registered is a rule no
 * editor can reach.
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
	}
}
