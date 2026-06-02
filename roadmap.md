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

## Tier 2 — Element base styling

Styled globally via `theme.json` `styles.elements` or the SCSS pipeline — applies
automatically, no editor action needed. (Body font, headings, and blockquote are
already done.)

- [ ] **Links** (`elements.link`) — brand color + hover/focus treatment
- [ ] **Buttons** (`elements.button` / `core/button`) — Athena `.btn` base look
- [ ] **Display headings** (`.display-1`–`.display-4`) — large `vw`-based hero sizes
- [ ] **Lists** — `.list-unstyled`, `.list-inline`
- [ ] **Tables** — `.table` base, striped / bordered / hover
- [ ] **Code / pre / kbd** styling
- [ ] **Figures & captions** (`elements.caption`)
- [ ] **Horizontal rule / separator**
- [ ] **Print styles** (port `_print.scss`)

---

## Tier 3 — Block style variations (CSS-only)

Registered via `register_block_style()` + SCSS — the established pattern in this
theme (see heading sizes, `.lead`).

- [ ] **Button variants** on `core/button`: `.btn-primary`, `.btn-secondary`,
  `.btn-outline-*`, `.btn-inverse`, size `lg`
- [ ] **Table variants** on `core/table`: striped / bordered / hover
- [ ] **Quote / pullquote** variants
- [ ] **List variants**: unstyled, inline
- [ ] **Group "card"** style on `core/group`: Athena `.card` look
- [ ] **Group "jumbotron / well"** style (note: `core/cover` overlaps)
- [ ] **Alert (static)** style — colored callout box (`.alert-*`)

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
