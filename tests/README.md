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
npm install                 # installs wp-env + Playwright into node_modules
npm run build               # compile CSS first
npm run env:start           # boot wp-env (needs Docker running)
npm run env:seed            # create deterministic content (tests/seed.sh)
npx playwright install chromium

npm run test:a11y
npm run test:visual              # diffs against baselines
npm run test:visual:update       # regenerate baselines after a reviewed change
```

`TEST_BASE_URL` defaults to `http://localhost:8888` (wp-env).

### Requirements / gotchas

- **Docker must be running.** wp-env runs WordPress in Docker.
- **The checkout must live in a Docker-shared path.** Docker Desktop shares your
  home directory by default but *not* arbitrary locations like
  `/Applications/...`. Clone the theme somewhere under your home folder (or add
  the path under Docker → Settings → Resources → File Sharing). Otherwise
  `wp-env start` fails with a "mounts denied" error.
- **No global installs / PATH edits needed.** The `npm run *` scripts put
  `./node_modules/.bin` on PATH, so the local `wp-env`/`playwright` binaries are
  used. Run `env:seed` via `npm run env:seed` (or from the repo root after
  `npm install`) so `seed.sh` finds the local `wp-env`.

### Visual baselines

Baselines live in `tests/visual.spec.js-snapshots/` and are committed. Playwright
suffixes them per-OS, so each platform needs its own set:

- **`*-darwin.png`** — committed, for local runs on macOS.
- **`*-linux.png`** — needed by CI (Ubuntu). Easiest path: run the
  **"Update visual baselines"** workflow (`.github/workflows/update-visual-baselines.yml`,
  `workflow_dispatch`) from the branch — it seeds a wp-env on the Linux runner,
  regenerates the baselines, and commits the updated `*-linux.png` back to that
  branch. Or generate them locally on a Linux machine with
  `npm run test:visual:update`.

A missing baseline is written on first run and fails that run by design (forces a
human to review the new screenshot before it becomes the reference). Regenerate
intentionally after a reviewed layout change.

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