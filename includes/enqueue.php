<?php
/**
 * Asset delivery — front end and block editor.
 *
 * Every way CSS or JS reaches a browser is in this file, so "what does this theme load, and
 * on which screen" is one place rather than a hunt. What each script *does* is documented at
 * the top of the script itself, not here.
 *
 * One case is worth knowing before adding to this file: the editor canvas is an iframe with
 * its own document, so `wp_enqueue_style()` never reaches inside it. Editor styles get there
 * through `add_editor_style()` (includes/setup.php) for a stylesheet, or through the
 * `styles` key of `block_editor_settings_all` for CSS computed per post.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Enqueue a script built by `npm run build`, using its generated asset manifest.
 *
 * Every entry in webpack.config.js emits a `<name>.asset.php` next to its `<name>.js`,
 * holding the WordPress script handles that entry imported and a content hash. Reading
 * those means a dependency list is never restated here — adding an `@wordpress/*` import to
 * the source is the whole change.
 *
 * Silently does nothing when the manifest is missing, which is the pre-build state and also
 * the state of an entry that has not been written yet.
 *
 * @param string $handle    Script handle to register.
 * @param string $name      Entry name, matching build/<name>.js.
 * @param bool   $in_footer Whether to print the tag in the footer.
 * @return void
 */
function ucf_theme_enqueue_build_script( $handle, $name, $in_footer = true ) {
	$asset_path = get_theme_file_path( "build/{$name}.asset.php" );

	if ( ! file_exists( $asset_path ) ) {
		return;
	}

	$asset = require $asset_path;

	wp_enqueue_script(
		$handle,
		get_theme_file_uri( "build/{$name}.js" ),
		$asset['dependencies'],
		$asset['version'],
		$in_footer
	);
}

/**
 * Enqueue front-end assets.
 *
 * Webfonts are deliberately absent here — they are declared as `fontFace` entries in
 * theme.json and served from assets/fonts/, so nothing is requested from a third-party font
 * CDN at render time.
 *
 * WHY: `filemtime()` as the version rather than the theme's own version string. The
 * stylesheet is a build artifact, so it changes on a schedule of its own; stamping it with
 * the theme version means a rebuilt stylesheet ships behind a cache key that did not move.
 *
 * @return void
 */
function ucf_theme_enqueue_assets() {
	$css_path = get_theme_file_path( 'build/css/main.css' );

	wp_enqueue_style(
		'ucf-theme',
		get_theme_file_uri( 'build/css/main.css' ),
		array(),
		file_exists( $css_path ) ? filemtime( $css_path ) : false
	);
}
add_action( 'wp_enqueue_scripts', 'ucf_theme_enqueue_assets' );

/**
 * Enqueue the block-editor scripts.
 *
 * `editor` is the theme's editor glue — block filters, sidebar panels, editor stand-ins for
 * anything rendered on the server. Its entry is declared in webpack.config.js; until
 * src/js/editor/index.js exists the helper above finds no manifest and this is a no-op.
 *
 * @return void
 */
function ucf_theme_enqueue_editor_assets() {
	ucf_theme_enqueue_build_script( 'ucf-theme-editor', 'editor' );
}
add_action( 'enqueue_block_editor_assets', 'ucf_theme_enqueue_editor_assets' );
