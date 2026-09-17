/**
 * ESLint configuration.
 *
 * Extends the @wordpress/scripts default — the WordPress coding standards, the React and
 * i18n rules, and the JSDoc checks — and changes one thing.
 *
 * `tools/` holds command-line scripts, not browser or editor code. Printing is what they are
 * for: a build guard that cannot say why it skipped, or a version check that cannot say which
 * two numbers disagree, is useless. `no-console` is a rule about shipped code, and nothing in
 * tools/ ships — it is not enqueued, imported by a block, or reachable from the theme at
 * runtime. Scoping the rule off here rather than dropping tools/ from `npm run lint:js`
 * keeps everything else about those files linted.
 *
 * @package ucf-wordpress-block-theme
 */

module.exports = {
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	overrides: [
		{
			files: [ 'tools/**/*.js', 'webpack.config.js', '*.config.js' ],
			env: {
				node: true,
			},
			rules: {
				'no-console': 'off',
			},
		},
	],
};
