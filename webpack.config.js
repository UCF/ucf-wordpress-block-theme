/**
 * Extends the default @wordpress/scripts webpack config.
 *
 * Two departures from the default:
 *
 * 1. **Named entries.** Only folders with a block.json are auto-detected. Anything that is
 *    not a block — editor glue, a front-end script, a rich-text format — has none, so it is
 *    named here. That is what puts it through the same pipeline as the blocks: one dialect,
 *    one minifier, and a generated `*.asset.php` per entry so includes/enqueue.php never
 *    restates a dependency list by hand.
 *
 * 2. **`clean` keeps css/.** wp-scripts wipes the output directory on every build, preserving
 *    only `fonts/` and `images/`. The stylesheet is compiled into build/css/ by a separate
 *    `sass` run, so without this `npm run build:blocks` on its own would delete a stylesheet
 *    that `npm run build:css` had just produced.
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const blockEntries =
	typeof defaultConfig.entry === 'function'
		? defaultConfig.entry()
		: defaultConfig.entry;

module.exports = {
	...defaultConfig,
	entry: {
		...blockEntries,

		// Everything that customizes the block editor. Imported for side effects; see
		// src/js/editor/index.js.
		editor: './src/js/editor/index.js',

		// Every front-end enhancement. Imported for side effects; see src/js/frontend/index.js.
		frontend: './src/js/frontend/index.js',
	},
	output: {
		...defaultConfig.output,
		clean: {
			keep: /^(css|fonts|images)\//,
		},
	},
};
