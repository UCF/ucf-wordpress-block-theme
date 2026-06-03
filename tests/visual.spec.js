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

for (const route of routes) {
	test(`visual: ${route.name}`, async ({ page }) => {
		const response = await page.goto(route.path, { waitUntil: 'networkidle' });
		// 404 route is expected to return 404; everything else should be 200.
		if (route.name !== 'notfound-404') {
			expect(response.status(), `${route.path} should not error`).toBeLessThan(400);
		}
		await expect(page).toHaveScreenshot(`${route.name}.png`, {
			fullPage: true,
			mask: [page.locator('time, .wp-block-post-date')],
		});
	});
}