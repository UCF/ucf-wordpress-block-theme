/**
 * Accessibility: one audit per registered block style.
 *
 * This is the tier the theme actually needs, and the one a per-route suite cannot stand in
 * for. The compositions — `is-style-light`, `-paper`, `-dark` and `-bold-gold` — set the
 * `--ucf-*` roles their contents read, so the same link resolves to a different color in each.
 * Link blue passes on paper and would be 3.20:1 on gold — which is why the gold composition
 * sets its link role to black, and only a page per composition shows that it still does.
 * **A variant can fail contrast where the default passes**, which means auditing the default
 * composition proves nothing about the other three.
 *
 * So every `core/group` variant page carries the full probe from `seed.php` — a heading, body
 * copy, a link, a list, a button and every text role utility — and axe measures each of them
 * against whatever that composition resolved to. Badges are not in it: each tone paints its
 * own fill, so its contrast does not depend on the field, and `features.spec.js` covers them.
 *
 * Every style in `WP_Block_Styles_Registry` gets a page. Core registers its own styles on the
 * client, so the server registry is exactly this theme's — nothing to filter, and a style
 * added to `includes/block-styles.php` is covered on the next seed.
 *
 * WHY the unregistered `-accent` flavors get no page: all they add is a 3px leading-edge
 * border, and axe does not measure non-text contrast, so a page for each would audit the
 * same text pairs as its plain twin.
 */
const { test } = require( '@playwright/test' );
const { auditPage } = require( './axe' );
const { loadManifest } = require( './manifest' );

const { variants } = loadManifest();

for ( const variant of variants ) {
	test( `variant: ${ variant.name }`, async ( { page }, testInfo ) => {
		// `variant.class` is asserted in the DOM before axe runs — see tests/a11y/axe.js.
		// Without it, a stale sample renders in default colors and passes as the variant.
		await auditPage( page, testInfo, variant );
	} );
}
