# UCF WordPress Block Theme — working notes

This theme recreates the UCF Athena Framework brand layer on top of WordPress
core blocks. See `roadmap.md` for what's done and planned, and
`docs/athena-feature-mapping.md` for the Athena → block-theme feature map.

## Guiding principle: reuse tokens, add as little as possible

The whole point of the design system is that a change to a **core token** (a font
family, the primary color, a spacing step) propagates everywhere automatically.
So when building anything — especially **patterns** — prefer existing primitives
over new ones, in this order:

1. **`theme.json` tokens first.** Colors via the palette (`secondary`, `inverse`,
   `primary`, the gray ramp, the `-aw` accessible variants…), spacing via the
   presets (`var:preset|spacing|30` etc.), font sizes via `xs/sm/md/lg`, fonts via
   the registered families. Never hard-code a hex, px gap, or font stack that a
   token already expresses.
2. **Existing classes / block styles next.** `.lead`, `.display-1`–`4`, `.h1`–`6`,
   `.btn-*`, `.is-style-*` (card / jumbotron / well / alert-* / table-* / quote /
   pullquote / thick separator), text helpers, `.mt-0/.mb-0/.my-0`, `.mx-auto`.
   Reuse these
   rather than writing pattern-local CSS.
3. **Core block controls next.** Many things that look like they need CSS are now
   native controls: Group **`minHeight`**, flex/constrained layouts, per-block
   color/spacing, `currentColor` inheritance. Express layout in block attributes,
   not bespoke classes.
4. **Only then add something new** — and when you must, make it a **reusable,
   token-driven primitive** (a block style or utility wired to `theme.json`
   variables), not a pattern-specific class. If two patterns would share it, it
   belongs in the theme layer (`functions.php` block style + an `src/scss`
   partial), so a future token change still flows through.

Corollary: **don't ship near-duplicate patterns** for color variants. One pattern
plus editor color overrides is better — and if rules/borders use `currentColor`,
they recolor themselves when the surrounding text color changes (see the Feature
Row pattern).

## Patterns

- Live in `patterns/*.php`, auto-registered by WordPress (the scanner is **not**
  recursive — shared includes can go in a subdir to keep them from registering).
- Categories registered in `includes/patterns.php`: **UCF Headers**
  (`ucf-headers`) and **UCF Blocks** (`ucf-blocks`). Avoid the `ucf-sections`
  slug — reserved for the separate UCF Section plugin.
- Header block comment + matching HTML must reflect what the block would save, or
  the editor flags "invalid content" (e.g. an empty `core/image` is invalid; use a
  real `src`).
- **Set color through the block's color controls, not hard-coded CSS.** Unless
  explicitly told otherwise, a pattern must never hard-code a hex value or add a
  bare `.has-*-color` utility class. Express a default color via the block's
  `textColor`/`backgroundColor` (or `style.color`) attributes — i.e. what the
  editor's Color panel would emit — so the author can change it from the controls.
  Prefer letting a parent Group's color cascade to children over re-declaring it on
  each child.

## Build & test

- SCSS in `src/scss/` → `npm run build` → `assets/css/main.css` (loaded on the
  front end **and** in the editor via `add_editor_style`, so styles stay in
  parity). New partials must be `@use`d in `main.scss`.
- Webfonts come from `theme.json` `fontFace` (not SCSS) and load in both contexts.
- Tests: see `tests/README.md`. Accessibility (axe) gates CI; visual regression is
  local/manual. wp-env provides the test WordPress (`npm run env:start`).