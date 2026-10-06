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
	ucf_theme_register_element_styles();
}
add_action( 'init', 'ucf_theme_register_block_styles' );

/**
 * Register the color compositions as core/group block styles.
 *
 * One primitive, four color pairs, one registered style each — `is-style-alt` and friends,
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
		'light' => __( 'Light', 'ucf-wordpress-block-theme' ),
		'alt'   => __( 'Light Gray', 'ucf-wordpress-block-theme' ),
		'dark'  => __( 'Dark', 'ucf-wordpress-block-theme' ),
		'gold'  => __( 'Bold Gold', 'ucf-wordpress-block-theme' ),
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

/**
 * The non-composition block styles: a short, reviewed list.
 *
 * WHY a list at all. Compositions are the only styles that change what a *field* is, and
 * they are only ever registered on core/group. These are different in kind: each is a look
 * for one element that no core control can express — a source line in the mono data voice, a
 * checklist, a heading in the sans family at a smaller level's look. Every one comes from the
 * UCF Web Design System's component briefs (Text, Lists, Buttons, Headings).
 *
 * WHY not more. Anything a preset can say is a preset: Lead and Display are font sizes, not
 * styles, so the Styles panel stays free for the decisions that need it.
 *
 * Each style's CSS is the `.is-style-{name}` rule in the partial named beside it.
 * `BlockStylesTest` checks every name here has that rule somewhere in src/scss/.
 *
 * @return array<string, array<string, string>> Block name to [ style name => label ].
 */
function ucf_theme_element_styles() {
	return array(
		// src/scss/_text.scss.
		'core/paragraph' => array(
			'data' => __( 'Data', 'ucf-wordpress-block-theme' ),
		),
		// src/scss/_text.scss.
		'core/heading'   => array(
			'sans' => __( 'Sans', 'ucf-wordpress-block-theme' ),
		),
		// src/scss/_lists.scss.
		'core/list'      => array(
			'check'   => __( 'Checklist', 'ucf-wordpress-block-theme' ),
			'divided' => __( 'Divided', 'ucf-wordpress-block-theme' ),
		),
		// src/scss/_buttons.scss.
		'core/button'    => array(
			'text' => __( 'Text', 'ucf-wordpress-block-theme' ),
		),
	);
}

/**
 * Register the element styles listed in `ucf_theme_element_styles()`.
 *
 * @return void
 */
function ucf_theme_register_element_styles() {
	foreach ( ucf_theme_element_styles() as $block => $styles ) {
		foreach ( $styles as $name => $label ) {
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
