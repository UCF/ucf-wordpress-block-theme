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

## Tier 1 — `theme.json` design tokens  ✅ DONE

Foundational brand tokens exposed in every block's editor controls (color pickers,
font-size menus, spacing menus).

- [x] **Color palette** → `settings.color.palette`
  - UCF Gold `#fc0` (primary), Black `#000` (secondary), White (inverse)
  - Gray ramp: `#292b2c`, `#464a4c`, `#636c72`, `#767676`, `#ccc`, `#eceeef`, `#f7f7f9`
  - Semantic: success `#2ecc71`, info `#81cfe0`, warning `#f4b350`, danger `#ff6445`
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

## Tier 4 — Select pure-CSS utility classes

Most utilities are redundant in a block theme; only port those with no block-editor
equivalent. (Font-family utilities are already done.)

- [ ] **`.sr-only` / `.sr-only-focusable`** — accessibility
- [ ] **`.stretched-link`** — makes a whole card/group clickable
- [ ] **Text helpers**: `.text-uppercase`, `.text-truncate`, `.text-nowrap`
- [ ] **`.embed-responsive`** (note: `core/embed` largely handles this)
- [x] **Font-family utilities** — `.font-slab-serif`, `.font-condensed`, `.font-sans-serif-alt`

**Intentionally skipped** (block editor / `theme.json` already provides): spacing
(`.m-*`/`.p-*`), flex, float, display, sizing (`.w-*`), borders/rounded, alignment,
the grid (`.container`/`.row`/`.col-*`), background/text color utilities (palette
presets replace these).

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
