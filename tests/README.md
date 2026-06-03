# Automated tests

Two suites run with Playwright against a live WordPress instance:

- **Accessibility** (`a11y.spec.js`) — axe-core, WCAG 2.0/2.1 A + AA. Fails on
  axe **violations**; reports axe **incomplete** ("needs review", e.g. text over
  a background image) as log/annotations without failing.
- **Visual regression** (`visual.spec.js`) — full-page screenshots of each
  template at desktop / tablet (780px) / mobile (360px), diffed against
  committed baselines.

Routes (one per template) live in `routes.js`.

## Running against wp-env (the supported path)

```bash
npm install
npm run build           # compile CSS first
npm run env:start       # boot wp-env (needs Docker)
npm run env:seed        # create deterministic content (tests/seed.sh)
npx playwright install chromium

npm run test:a11y
npm run test:visual              # diffs against baselines
npm run test:visual:update       # regenerate baselines after a reviewed change
```

`TEST_BASE_URL` defaults to `http://localhost:8888` (wp-env).

### Visual baselines

Baselines are environment- and platform-specific (Playwright suffixes them, e.g.
`-linux.png`). Generate and commit them from the same OS CI uses (Linux) — run
`test:visual:update` inside the Playwright Linux container, or let the CI job
produce them and commit the artifact. The first run with a missing baseline
writes it and fails by design (forces review).

## Running against another install (e.g. local MAMP)

Point the suite anywhere and override the routes to match that site's
slugs/permalinks:

```bash
PW_CHANNEL=chrome \
TEST_BASE_URL='http://localhost/wordpress/site/' \
TEST_ROUTES_JSON='[{"name":"home","path":""},{"name":"page-hero","path":"name-of-page/"}]' \
npx playwright test tests/a11y.spec.js
```

`PW_CHANNEL=chrome` drives a locally-installed Chrome (no Playwright browser
download). Use a trailing slash on `TEST_BASE_URL` and non-leading-slash paths
when the site lives in a subdirectory.

## CI

`.github/workflows/ci.yml` builds CSS, starts wp-env, seeds content, installs the
Playwright Chromium build, then runs both suites and uploads the report on
failure.