/**
 * Accessibility: one audit per template.
 *
 * A page, a single post, the blog index, search with and without results, and 404 — the five
 * templates in `templates/`, plus the empty-results case, which renders a different tree from a
 * populated one and is the version nobody looks at. The front page has no template of its own
 * and renders through `page.html`; it is audited as a route anyway, because it is the page
 * every visitor sees first.
 *
 * Everything the pattern, variant and feature tiers audit sits inside these templates too, so a
 * problem here shows up everywhere. The University Header above them is excluded from the
 * audit — see the exclusion list in `axe.js` for why.
 */
const { test } = require( '@playwright/test' );
const { auditPage } = require( './axe' );
const { loadManifest } = require( './manifest' );

const { routes } = loadManifest();

for ( const route of routes ) {
	test( `route: ${ route.name }`, async ( { page }, testInfo ) => {
		await auditPage( page, testInfo, route );
	} );
}
