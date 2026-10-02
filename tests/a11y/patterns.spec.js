/**
 * Accessibility: one audit per registered pattern, each on a page of its own.
 *
 * Isolation is the point. A pattern audited only as part of some larger page competes with
 * everything else on it — a contrast failure inside a card is one line in a report about the
 * whole page, and the failing test names the page rather than the pattern. Here the test that
 * goes red is called `pattern: ucf-wordpress-block-theme/<slug>`.
 *
 * The list comes from `WP_Block_Patterns_Registry` at seed time, not from anything checked
 * in, so a new file in `patterns/` is audited on the next run with nothing to remember.
 *
 * Empty today: `patterns/` holds no pattern yet, so this file declares no tests. The seed
 * refuses to write an empty list once a pattern file exists, so that stays honest.
 */
const { test } = require( '@playwright/test' );
const { auditPage } = require( './axe' );
const { loadManifest } = require( './manifest' );

const { patterns } = loadManifest();

for ( const pattern of patterns ) {
	test( `pattern: ${ pattern.name }`, async ( { page }, testInfo ) => {
		await auditPage( page, testInfo, pattern );
	} );
}
