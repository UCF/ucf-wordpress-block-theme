# UCF Web Design System import

What came into this theme from the UCF Web Design System prototype (the design system
artifact and its page studies), where each piece landed, and what was deliberately left out.
Read [architecture.md](architecture.md) first; this file only records the import.

The import followed the escalation ladder: tokens first, then core blocks styled, then
patterns over core blocks. **No custom block type was needed in the theme.** WordPress 7.1
core already ships Breadcrumbs, Accordion, Details, Icon (with an icon registry), Page List and
grid layout. The Section, Card, Highlight (StatStatement), Hero and Event List are blocks from
ucf-react-component-library's ucf-content and ucf-events plugins, which extend the theme; the
Alert banner is UCF-Alert-Plugin's.

## Decisions taken by default

The design system leaves five decisions open. The prototype takes these defaults; each one
can be reversed without touching anything outside the files named here.

| Decision                     | Default taken                                                                                                                       | Where                                             |
| ---------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------- |
| Slugs                        | The design system's names (`gray-050`…`gray-900`, `horizon-deep`, `red-*`, `green-*`). `gold-deep` is gone                          | `theme.json`, `_variables.scss`                   |
| Block styles beyond groups   | A short reviewed list, tested; compositions stay the only group styles                                                              | `includes/block-styles.php`, `BlockStylesTest`    |
| Breakpoints                  | One shared, 1024px. Gutters and section padding are fluid `clamp()` tokens instead of steps                                         | `_variables.scss`, `theme.json` `settings.custom` |
| Brighter mode (page studies) | Not built                                                                                                                           | —                                                 |
| Brand questions              | Built as the system specifies, marked `SPEC:`: `horizon-deep` links, H4–H6 in sentence case, sentence-case buttons, inverting hover | `_compositions.scss`, `theme.json`                |

## Where each piece landed

**Foundations.** Palette, type scale (12 sizes), spacing (`space-1`…`space-10` as slugs
`10`…`100`), widths (720 prose, 1240 wide), one shadow, five aspect ratios — all in
`theme.json`. Radius is removed from the editor and forced to zero in `_base.scss`, with
`.ucf-allow-radius` as the per-element way out.

**Surfaces → compositions.** `ucf-surface-light/alt/dark/gold` are the compositions `light`,
`alt`, `dark`, `gold`. The design system's semantic tokens are `--ucf-*` roles, one table per
treatment in `_compositions.scss`. New roles: `muted`, `line-strong`, `indicator`, `raised`,
`danger(-soft)`, `success(-soft)`, and `surface` (per composition).

**Components.**

| Design system  | Built as                                                                        |
| -------------- | ------------------------------------------------------------------------------- |
| Section        | `ucf-content/section` block (ucf-content plugin)                                |
| Headings, Text | theme.json element styles; Lead/Display presets; `data` and `sans` block styles |
| Lists          | Core List; `check` and `divided` styles                                         |
| Buttons        | Core Button; Outline is the secondary; `text` style added                       |
| Quote          | Core Quote and Pullquote                                                        |
| Table          | Core Table; right-aligned columns become mono, tabular                          |
| Figure         | Core Image with the aspect-ratio presets; caption in the data voice             |
| Accordion      | Core Details                                                                    |
| FAQ            | Core Accordion (its toggle is a real heading)                                   |
| Breadcrumbs    | Core Breadcrumbs, styled                                                        |
| SectionNav     | Core Page List, styled with the `indicator` bar                                 |
| Icon           | Core Icon; the set registered as the `ucf` collection (`includes/icons.php`)    |
| Card           | `ucf-content/card` block and its presets                                        |
| StatStatement  | `ucf-content/highlight` block, Statement preset                                 |
| QuickLinks     | Pattern + `.ucf-quick-links` / `.ucf-quick-link`                                |
| Steps          | Pattern: core ordered List + `.ucf-steps`                                       |
| CallToAction   | Patterns, gold and dark                                                         |
| Section header | Patterns, plain and split                                                       |

**Prototype page.** `https://www.ucf.edu/academics/` was rebuilt on the local `ucf` site as
the "Academics" page, using the Landing template (`templates/page-landing.html`, which leaves
the H1 to the page's own hero). Its photos are imported attachments; the local PHP has no GD
or Imagick, so they have no resized variants, only the originals.

**Removed after review.** The anchor chip ("UCF is" / "At UCF" on gold before a headline)
was built as a rich-text format and then taken out: it is described in the design system as a
brand device, but it is not confirmed against UCF's published brand guidelines.

## Built elsewhere or with data

| Component                  | Built as                                                            | Source                                    |
| -------------------------- | ------------------------------------------------------------------- | ----------------------------------------- |
| FilterList / degree search | `ucf-degrees/degree-finder` and `/degree-list` blocks (ucf-degrees) | The UCF Search Service                    |
| EventList                  | `ucf-events/event-list` block (ucf-events plugin)                   | The events.ucf.edu feed                   |
| Structured data            | `includes/structured-data.php`: Organization, BreadcrumbList        | The site                                  |
| Forms                      | `form` pattern, a mock RFI form that does not submit                | gravityforms + Athena-GravityForms-Plugin |

Patterns over core blocks, no data needed: Answer summary, Comparison table, Sources, the
page-study placements, and three page-level
patterns — Homepage, Degree program, Research story — with demo pages on the local `ucf` site.
On this page is the ucf-content plugin's Section Nav.

Editor policy: the toolbar is trimmed and Tabs removed (`src/js/editor/editor-policy.js`), and
a pre-publish panel checks alt text, table captions and heading order without blocking a save
(`prepublish-checks.js`). Brighter is a style variation (`styles/bright.json`).

## Still not built

| Item                                          | Why                                                                                                 |
| --------------------------------------------- | --------------------------------------------------------------------------------------------------- |
| Site header, University Header plugin, footer | Excluded from this round                                                                            |
| Font Awesome Sharp Solid                      | Needs UCF's Pro license token; the Free Solid names match                                           |
| Image budgets (300/150/60 KB)                 | A media pipeline; documented in design-principles.md                                                |
| College "photos" on the academics page        | They are round icon illustrations, not photographs — shown uncropped at 1:1 until real photos exist |

## Verification still owed

Block markup in `patterns/` and on the academics page was written against core's `save()`
source rather than generated in the editor. Before relying on it, paste each pattern into the
editor (or open the page) and check the store, per [CLAUDE.md](../CLAUDE.md):

```js
wp.data.select( 'core/block-editor' ).getBlocks(); // walk innerBlocks; watch for "Updated Block"
```

The axe tier (`npm run test:a11y`) has also not been run against these changes.
