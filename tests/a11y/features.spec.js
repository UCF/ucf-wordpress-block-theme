/**
 * Accessibility: theme markup that no template or pattern puts on a page yet.
 *
 * CONTEXT: the brand theme's `blocks.spec.js`, renamed because this theme ships no custom
 * block. What it ships instead is editor-added markup — the Section variation of core/group
 * and the Badge rich-text formats — which is just as invisible to the route tier: no template
 * contains either, so without a seeded page of their own neither would ever be audited.
 *
 * A custom block added to `src/blocks/` belongs here too until a pattern or template carries
 * it. `seed.php` fails the seed outright if a top-level block ends up covered by nothing.
 *
 * Each entry carries a `class`, so `auditPage()` refuses to run axe on a page where the
 * feature failed to render — a badge page whose spans lost their class audits plain text and
 * passes.
 */
const { test } = require( '@playwright/test' );
const { auditPage } = require( './axe' );
const { loadManifest } = require( './manifest' );

const { features } = loadManifest();

for ( const feature of features ) {
	test( `feature: ${ feature.name }`, async ( { page }, testInfo ) => {
		await auditPage( page, testInfo, feature );
	} );
}
