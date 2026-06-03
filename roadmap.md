# UCF Block Theme — Athena Framework Incorporation Roadmap

This roadmap tracks which parts of the [UCF Athena Framework](https://ucf.github.io/Athena-Framework/)
are being incorporated into this block theme, and which are intentionally left
for a separate plugin.

**Guiding principle:** A WordPress block theme already provides layout, spacing,
columns, and alignment via the editor and `theme.json`. So Bootstrap's grid/flex/
spacing utility system is largely redundant and is *not* ported. What we bring in
is the **brand layer** (colors, typography, presentational styling) and **CSS-only
block style variations**. Anything interactive (JS) or requiring structured custom
markup belongs in a plugin so it survives a theme switch.

---

## Phase 1 — Athena foundation & full-bleed layout  ✅ COMPLETE

The brand layer and presentational styling are in place. Everything an editor
needs to build on-brand UCF pages with the core blocks now exists in the theme,
plus the layout plumbing for full-width sections. Summary of what shipped:

- **Design tokens** (Tier 1) — full UCF color palette incl. accessible `-aw`
  text variants, font-size presets, and a Bootstrap-matched spacing scale, all
  exposed in the editor via `theme.json`.
- **Element base styling** (Tier 2) — links, buttons, display headings, lists,
  tables, code/pre/kbd, captions, separators, and print styles applied globally
  via `styles.elements` and the SCSS pipeline.
- **Block style variations** (Tier 3) — selectable styles on core blocks:
  button colors/outlines/sizes, table treatments, quote/pullquote borders, list
  variants, group card/jumbotron/well, and static alert callouts. Every variant
  also has a parallel utility class so multiple effects can be combined.
- **Utility classes** (Tier 4) — the handful with no editor equivalent:
  `.sr-only(-focusable)`, `.stretched-link`, and text helpers (uppercase /
  nowrap / truncate); the editor-useful ones also registered as block styles.
- **Full-bleed layout & templates** — `theme.json` layout tuned
  (`contentSize 1200px` / `wideSize 1400px` + root padding), and flat
  `single.html` / `page.html` templates so Full-width blocks break out to the
  viewport edges on the front end, matching the editor.

The detailed tier-by-tier breakdown follows.

---

## Tier 1 — `theme.json` design tokens  ✅ DONE

Foundational brand tokens exposed in every block's editor controls (color pickers,
font-size menus, spacing menus).

- [x] **Color palette** → `settings.color.palette`
  - UCF Gold `#fc0` (primary), Black `#000` (secondary), White (inverse)
  - Gray ramp: `#292b2c`, `#464a4c`, `#636c72`, `#767676`, `#ccc`, `#eceeef`, `#f7f7f9`
  - Semantic: success `#2ecc71`, info `#81cfe0`, warning `#f4b350`, danger `#ff6445`
  - Text complementary `#0275d8` — link color (black/gold reserved per usage rules)
  - Accessible "-aw" (AA contrast) variants for text use: primary-aw `#786000`,
    success-aw `#477e5e`, info-aw `#298194`, warning-aw `#966e31`, danger-aw `#c44f38`
  - Gold shade ramp: gold-lighter `#ffeb9b`, gold-lightest `#fdf9e8`,
    gold-darker `#b18e00`, gold-darkest `#786000`; metalgold `#bc9a36`
- [x] **Font-size presets** → `settings.typography.fontSizes`: `xs .75rem`,
  `sm .875rem`, `md 1rem`, `lg 1.25rem`
- [x] **Spacing scale** → `settings.spacing.spacingSizes` matching Bootstrap spacers
  (Tiny `.25rem` → Extra Large `3rem`)

---

## Tier 2 — Element base styling  ✅ DONE

Styled globally via `theme.json` `styles.elements` or the SCSS pipeline — applies
automatically, no editor action needed. (Body font, headings, and blockquote are
already done.)

- [x] **Links** (`elements.link`) — accessible gold (`primary-aw`) text, black
  underline on hover/focus
- [x] **Buttons** (`elements.button` / `core/button`) — Athena `.btn` base look:
  square corners, gold/black, uppercase bold. (Color *variants* are Tier 3.)
- [x] **Display headings** (`.display-1`–`.display-4`) — large, light hero sizes
  (`src/scss/_display.scss`). Also promoted to `core/heading` block styles
  ("Display 1–4") so they're selectable in the editor's Styles list.
- [x] **Lists** — `.list-unstyled`, `.list-inline` (`src/scss/_lists.scss`).
  Also registered as `core/list` block styles ("Unstyled", "Inline").
- [x] **Tables** — `.table` base (`src/scss/_tables.scss`), applied automatically
  to every `core/table` block (no block style needed). Striped / bordered /
  hover *variants* are Tier 3.
- [x] **Code / pre / kbd** styling (`src/scss/_code.scss`)
- [x] **Figures & captions** (`elements.caption`) — muted gray, small size
- [x] **Horizontal rule / separator** (`src/scss/_separator.scss`)
- [x] **Print styles** (`src/scss/_print.scss`, ported from Athena)

---

## Tier 3 — Block style variations (CSS-only)  ✅ DONE

Registered via `register_block_style()` + SCSS — the established pattern in this
theme (see heading sizes, `.lead`).

- [x] **Button variants** on `core/button`: solid Gold/Black/Inverse, Outline
  gold/black/inverse, and a Large size — block styles + `.btn-*` utility classes
  (`src/scss/_buttons.scss`).
- [x] **Table variants** on `core/table`: Striped, Bordered, Row hover, Compact,
  Dark — registered as block styles and available as `.table-*` utility classes
  for combining (`src/scss/_tables.scss`).
- [x] **Quote / pullquote** variants — Quote border colors (Tier 1) plus
  matching `core/pullquote` border-color variants (`src/scss/_blockquote.scss`).
- [x] **List variants**: Unstyled, Inline — `core/list` block styles
  (promoted in Tier 2, `src/scss/_lists.scss`).
- [x] **Group "card"** style on `core/group`: Athena `.card` look
  (`src/scss/_group.scss`).
- [x] **Group "jumbotron / well"** style — Jumbotron + Well `core/group` styles
  (`src/scss/_group.scss`).
- [x] **Alert (static)** style — Gold/Success/Info/Warning/Danger callout boxes
  as `core/group` block styles (`src/scss/_alerts.scss`). Dismissible/closeable
  alerts remain a plugin concern (need JS).

---

## Layout & templates — full-width / full-bleed support  ✅ DONE

Cross-cutting work (not part of the Athena port) enabling full-bleed sections —
e.g. a black jumbotron that spans the viewport edges while its content stays at
the standard width.

- [x] **`theme.json` layout** — `contentSize 1200px`, `wideSize 1400px` (was
  `100%`, which made Wide == Full). Full/Wide alignment toolbar now meaningful.
- [x] **Root padding** — `styles.spacing.padding` left/right =
  `var(--wp--preset--spacing--30)` (1rem). With `useRootPaddingAwareAlignments`,
  normal content gets edge breathing room while `alignfull` blocks break out to
  the true viewport edge.
- [x] **`templates/single.html`** — flat structure (flow `<main>` wrapping
  *sibling* constrained containers) so root padding lives on exactly one level.
  Nesting two `has-global-padding` containers triggers WP's breakout-reset rule
  and pins full-width blocks — avoided here. Full-width Group/Cover now break out
  correctly on the front end, matching the editor.
- [x] **`templates/page.html`** — flat structure mirroring `single.html` (flow
  `<main>`, constrained title/featured-image group + constrained `post-content`).
  Static Pages no longer fall back to the deeply-nested `index.html`, so
  full-width blocks break out correctly on Pages too.

---

## Tier 4 — Select pure-CSS utility classes  ✅ DONE

Most utilities are redundant in a block theme; only port those with no block-editor
equivalent. All live in `src/scss/_utilities.scss`.

- [x] **`.sr-only` / `.sr-only-focusable`** — accessibility. `.sr-only` also a
  `core/paragraph` + `core/heading` block style ("Screen-reader only").
- [x] **`.stretched-link`** — makes a whole card/group clickable; pair with
  `.position-relative` (the `.is-style-card` group is already a positioning
  context, so a stretched link inside a Card works with no extra class).
- [x] **Text helpers**: `.text-uppercase`, `.text-truncate`, `.text-nowrap` —
  also `core/paragraph` + `core/heading` block styles ("Uppercase", "Truncate",
  "No wrap").
- [x] **`.embed-responsive`** — *intentionally skipped*; `core/embed` already
  outputs responsive aspect-ratio wrappers, so a utility class would be redundant.
- [x] **Font-family utilities** — `.font-slab-serif`, `.font-condensed`, `.font-sans-serif-alt`

**Intentionally skipped** (block editor / `theme.json` already provides): spacing
(`.m-*`/`.p-*`), flex, float, display, sizing (`.w-*`), borders/rounded, alignment,
the grid (`.container`/`.row`/`.col-*`), background/text color utilities (palette
presets replace these).

---

## Phase 2 — Next  🚧 PLANNING

Phase 1 delivered the brand layer on top of core blocks. Phase 2 is for the work
that builds on that foundation — site structure, reusable content, and anything
that needs more than block styling. Items are candidates; check off / re-scope as
we go.

### Page header / hero  🚧 IN PROGRESS

Authors place / edit a Hero (Cover + H1 + subtitle) at the top of the page
content; it is auto-seeded into every new Page.

- [x] **Hero pattern** (`patterns/hero.php`) — full-width `core/cover` with a
  gradient overlay, an H1 (`display-2` style) and a `lead` subtitle. Categorized
  under "UCF Headers"; registered for `core/post-content` on Pages.
- [x] **Auto-seed new Pages** — `default_content` filter (`includes/patterns.php`)
  inserts the Hero pattern markup into new Pages so the hero is present and
  editable from the start. Existing pages untouched.
- [x] **Header parts** — `parts/header-page.html` (Pages): nav-only, centered,
  space-between, overlays the hero. `parts/header-post.html` (Posts): nav
  (space-between) + post title (H1) + byline (author/date/category) + featured
  image, then post content. `parts/header.html` (everything else): logo + site
  title + nav. All registered in `theme.json`; `page.html`→`header-page`,
  `single.html`→`header-post`, `index.html`→`header`.
- [x] **Navigation over the hero** — `src/scss/_header.scss` overlays the header
  on the hero image (absolute + legibility gradient, light text) *only* when the
  content opens with a Cover hero (scoped via `:has()`), so posts / hero-less
  pages keep the in-flow black bar. Site title is `<p>` (`level:0`) so the hero
  title is the page's sole H1.
- [x] **`page.html`** — global header part on top, then `post-content` (which
  begins with the Hero). No template-injected masthead or post title (the H1
  lives in the hero content).
- [x] **Post header** — `parts/header-post.html` (see above): a fixed,
  templated header for posts (no per-post hero image picker); posts intentionally
  less flexible than pages.

### Templates & site structure
- [x] **`404.html`** — modeled on UCF's 404: centered "Page Not Found" H1,
  guidance text, a search field, and a feedback link. Uses the default header.
- [x] **`home.html`** — simple fallback (recent-posts query loop) for when a
  static Page isn't set as the front page. Site normally uses a Page for home.
- [x] **`archive.html`** — category/tag/date listings: archive title
  (`query-title`) + term description, then the post listing (query loop,
  pagination, no-results).
- [x] **`search.html`** — "Search results for: …" title, a refine search field,
  then the post listing (query loop, pagination, no-results message).
- [ ] **Template parts** — review/expand `header.html` / `footer.html` (nav,
  branding, utility links) to match UCF site chrome.
- [ ] **Block patterns** — ship ready-made sections (full-width jumbotron hero,
  alert callout, card grid, CTA band) so editors insert in one click instead of
  assembling Group + alignment + styles by hand.

### Content & components
- [ ] **Badge** — decide build approach (custom block vs. inline style); from the
  Phase 1 "Undecided" note below.
- [ ] **Custom block(s)** — any Athena component that needs structured markup the
  core blocks can't express.

### Quality & polish
- [~] **Accessibility pass** — audited all templates with pa11y (htmlcs WCAG2AA
  + axe WCAG 2.1 AA). Confirmed clean: skip link, no `outline:none`, single H1
  per page, mobile nav overlay, landmarks/labels.
  - **Hero contrast** — axe-core reports the white hero text as *incomplete /
    needs-review* (`bgOverlap`: text over a background image, contrast not
    auto-determinable), **not a violation** (0 violations). pa11y's CLI surfaces
    axe "incomplete" as errors, which is misleading. Added a dark fallback
    background-color to the hero cover (`src/scss/_hero.scss`, `.ucf-hero` class +
    structural selector) so the *no-image* hero state passes outright and the
    text always has a dark base.
  - **Remaining real violation** — one author-colored paragraph in a post
    (custom text color ~4.21:1); content-level, fixed by the editor.
- [x] **Responsive review (pass 1)** — fixed the mobile hamburger menu
  (navigation overlay had no background/text color → invisible; set
  `overlayBackgroundColor`/`overlayTextColor` on all header nav blocks). Made
  display headings fluid (`clamp()`, was fixed 3.5–6rem → mobile overflow). Wide
  tables now scroll horizontally (`.wp-block-table { overflow-x: auto }`).
  Header logo/hamburger row intentionally `nowrap`. Re-check after patterns/footer.
- [ ] **Editor parity** — confirm every front-end style also renders in the
  editor (`add_editor_style` coverage).
- [x] **Automated testing** — Playwright suite covering both concerns, wired to
  npm scripts + GitHub Actions against a seeded `wp-env` (see `tests/README.md`).
  - **Accessibility** (`tests/a11y.spec.js`) — `@axe-core/playwright`, WCAG
    2.0/2.1 A+AA; fails on **violations**, reports axe **incomplete** separately
    (avoids the hero-image false positives the pa11y CLI produced). **Gates CI**
    (`.github/workflows/ci.yml`: build → wp-env → seed → a11y).
  - **Visual regression** (`tests/visual.spec.js`) — full-page screenshots of
    each template (home, page/hero, basic page, post, archive, search, 404) at
    desktop / tablet 780px / mobile 360px, diffed against committed baselines.
    **Local/manual only** (`npm run test:visual` / `test:visual:update`); not a
    CI gate. Linux baselines are regenerated via the manual
    `update-visual-baselines.yml` (`workflow_dispatch`).
  - **Wiring** — `.wp-env.json` + `tests/seed.sh` (deterministic content incl. a
    nav menu), `playwright.config.js` (3 viewport projects, system-Chrome via
    `PW_CHANNEL`). Verified locally: a11y passes all routes; visual baselines
    generate and re-match.

> Companion interactive/JS components are tracked separately under
> **Keep in a PLUGIN** below — they intentionally live outside this theme.

---

## Keep in a PLUGIN (not this theme)

Interactive / JS-dependent / structured components that should survive a theme switch:

- Carousel / slideshow
- Modal / dialog
- Dropdown menus
- Collapse / Accordion
- Tabs / Pills
- Tooltips & Popovers
- Scrollspy
- Dismissible alerts (closeable variant)
- Toggle buttons (stateful)
- Navbar with responsive toggler (overlaps core Navigation block)
- Media background (JS full-bleed image/video; `core/cover` covers common cases)
- Sticky-top handler & collapse keyboard-accessibility JS
- Forms / custom form controls, breadcrumbs, pagination (plugin or core blocks)

**Undecided:** **Badge** — CSS-only but needs inline `<span>` markup; cleanest as a
small custom block (theme or plugin) rather than a hand-applied class.

---

## Status legend

- `[ ]` planned
- `[x]` done
- ⬅ marks the tier currently in progress
