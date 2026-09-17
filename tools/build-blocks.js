/**
 * Run the block build, but only once there is something to build.
 *
 * WHY: `wp-scripts build` exits non-zero when webpack resolves no entries at all, which is
 * the state of a theme that has not written its first block yet. That turns `npm run build`
 * — and the CI step that runs it — red for a condition that is not an error. Nothing else in
 * the toolchain distinguishes "no blocks" from "the build broke", so this does.
 *
 * An entry is a folder under src/blocks/ with a block.json, which is the same test
 * includes/blocks.php applies when registering from build/. Non-block entries named in
 * webpack.config.js count too; this checks for those rather than assuming there are none.
 *
 * Delete this and inline `wp-scripts build` in package.json once the theme ships a block
 * and the empty case can no longer happen.
 */
const { existsSync, readdirSync } = require( 'fs' );
const { join } = require( 'path' );
const { spawnSync } = require( 'child_process' );

const root = join( __dirname, '..' );
const blocksDir = join( root, 'src', 'blocks' );

const hasBlock =
	existsSync( blocksDir ) &&
	readdirSync( blocksDir, { withFileTypes: true } ).some(
		( entry ) =>
			entry.isDirectory() &&
			existsSync( join( blocksDir, entry.name, 'block.json' ) )
	);

// A named entry in webpack.config.js is just as much a reason to run the build.
//
// Requiring that file evaluates the wp-scripts default config, which warns
// `No entry file discovered in the "src" directory.` on the empty case. That message is both
// confusing here — src/blocks is this theme's entry directory, not src — and immediately
// contradicted by the one printed below. Silencing the two console methods across the require
// is the narrowest way to drop it; the config module is pure, so nothing else is lost.
const entry = ( () => {
	const { log, warn } = console;
	console.log = () => {};
	console.warn = () => {};

	try {
		return require( join( root, 'webpack.config.js' ) ).entry;
	} finally {
		console.log = log;
		console.warn = warn;
	}
} )();

const hasNamedEntry = Object.keys( entry ?? {} ).length > 0;

if ( ! hasBlock && ! hasNamedEntry ) {
	console.log(
		'No blocks in src/blocks/ and no entries in webpack.config.js — skipping the block build.'
	);
	process.exit( 0 );
}

const result = spawnSync(
	process.platform === 'win32' ? 'npx.cmd' : 'npx',
	[
		'wp-scripts',
		'build',
		'--webpack-src-dir=src/blocks',
		'--output-path=build',
	],
	{ cwd: root, stdio: 'inherit' }
);

process.exit( result.status ?? 1 );
