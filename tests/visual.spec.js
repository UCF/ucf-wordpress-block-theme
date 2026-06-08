/**
 * Visual regression: full-page screenshots of every template at each viewport,
 * diffed against committed baselines. Update baselines intentionally with
 * `npm run test:visual:update` after a reviewed layout change.
 *
 * Dynamic, content-dependent bits (dates) are masked so they don't cause
 * spurious diffs.
 */

const { test, expect } = require('@playwright/test');
const { routes } = require('./routes');

// Always-masked selectors (dynamic content). TEST_MASK_SELECTORS (comma-
// separated) appends more — e.g. to exclude the AJAX-injected UCF Header plugin
// bar when running against a plugin-enabled site. The default wp-env test
// environment is theme-only, so that bar isn't present there.
const baseMask = 'time, .wp-block-post-date';
const extraMask = (process.env.TEST_MASK_SELECTORS || '').trim();
const maskSelector = extraMask ? `${baseMask}, ${extraMask}` : baseMask;

for (const route of routes) {
	test(`visual: ${route.name}`, async ({ page }) => {
		const response = await page.goto(route.path, { waitUntil: 'networkidle' });
		// 404 route is expected to return 404; everything else should be 200.
		if (route.name !== 'notfound-404') {
			expect(response.status(), `${route.path} should not error`).toBeLessThan(400);
		}
		await expect(page).toHaveScreenshot(`${route.name}.png`, {
			fullPage: true,
			mask: [page.locator(maskSelector)],
		});
	});
}