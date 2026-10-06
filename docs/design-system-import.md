# UCF Web Design System import

What came into this theme from the UCF Web Design System prototype (the design system
artifact and its page studies), where each piece landed, and what was deliberately left out.
Read [architecture.md](architecture.md) first; this file only records the import.

The import followed the escalation ladder: tokens first, then core blocks styled, then
patterns over core blocks. **No custom block type was needed.** WordPress 7.1 core already
ships Breadcrumbs, Accordion, Details, Icon (with an icon registry), Page List and grid
layout, which between them covered every component the design system builds as a block of
its own.

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
| Section        | The existing `ucf-section` variation; `ucf-section--tight` added                |
| Headings, Text | theme.json element styles; Lead/Display presets; `data` and `sans` block styles |
| Lists          | Core List; `check` and `divided` styles                                         |
| Buttons        | Core Button; Outline is the secondary; `text` style added                       |
| Quote          | Core Quote and Pullquote                                                        |
| Table          | Core Table; right-aligned columns become mono, tabular                          |
| Figure         | Core Image with the aspect-ratio presets; caption in the data voice             |
| Accordion      | Core Details                                                                    |
| FAQ            | Core Accordion (its toggle is a real heading) — `patterns/faq.php`              |
| Breadcrumbs    | Core Breadcrumbs, styled                                                        |
| SectionNav     | Core Page List, styled with the `indicator` bar                                 |
| Icon           | Core Icon; the set registered as the `ucf` collection (`includes/icons.php`)    |
| Card           | Pattern + `.ucf-card` (`--boxed`, `--ruled`)                                    |
| StatStatement  | Pattern + `.ucf-stat`; three in a core grid is the stats band                   |
| Alert (inline) | Pattern + `.ucf-notice`; Important is the `gold` composition plus the class     |
| QuickLinks     | Pattern + `.ucf-quick-links` / `.ucf-quick-link`                                |
| Steps          | Pattern: core ordered List + `.ucf-steps`                                       |
| CallToAction   | Patterns, gold and dark                                                         |
| Section header | Patterns, plain and split                                                       |

**Prototype page.** `https://www.ucf.edu/academics/` was rebuilt on the local `ucf` site as
the "Academics" page, using the Landing template (`templates/page-landing.html`, which leaves
the H1 to the page's own hero). It uses only the pieces above. Its images point at
ucf.edu, because the local PHP has no GD or Imagick to import them.

**Removed after review.** The anchor chip ("UCF is" / "At UCF" on gold before a headline)
was built as a rich-text format and then taken out: it is described in the design system as a
brand device, but it is not confirmed against UCF's published brand guidelines.

## Log: represented, not integrated

Each of these needs a system larger than a theme. Where a plugin already on this install
covers the data, it is named.

| Item                                                                                        | Needs                                     | Represented as                      | Existing plugin                                |
| ------------------------------------------------------------------------------------------- | ----------------------------------------- | ----------------------------------- | ---------------------------------------------- |
| Site header (unit name, nav, one action)                                                    | A decision: build or drop                 | Nothing yet                         | —                                              |
| University Header                                                                           | Retiring `includes/university-header.php` | The theme's own include, unchanged  | UCF-Header-Plugin                              |
| Site footer, owner and review date, external-link notice                                    | Footer plugin changes                     | The plugin's footer                 | UCF-Footer-Plugin                              |
| Structured data (FAQPage, Program, Person, Event…)                                          | A schema layer fed from fields            | Semantic markup only                | —                                              |
| AnswerSummary, FactList, Provenance, ComparisonTable                                        | Program data, post meta                   | Not built in this branch            | UCF-Degree-CPT-Plugin, UCF-Tuition-Fees-Plugin |
| ProfileCard                                                                                 | People directory                          | Not built                           | UCF-People-CPT                                 |
| EventList                                                                                   | Events feed                               | Not built                           | UCF-Events-Plugin                              |
| FilterList / degree search                                                                  | Program source and client filtering       | A "Search all degrees" button       | UCF-Degree-Search-Plugin                       |
| "Recently searched degrees"                                                                 | Search analytics                          | A static list on the academics page | —                                              |
| Emergency banner                                                                            | UCF Alert feed                            | Not built; inline notices only      | UCF-Alert-Plugin                               |
| Forms                                                                                       | Forms plugin, CRM field lock              | Not built                           | gravityforms, Athena-GravityForms-Plugin       |
| OnThisPage auto-generation, scroll-spy                                                      | Content parse, front-end JS               | Not built                           | —                                              |
| Video facade                                                                                | Front-end JS                              | Core Embed                          | —                                              |
| FAQ "Expand all", FactList "cite this"                                                      | A first front-end script entry            | Not built                           | —                                              |
| Font Awesome Sharp Solid                                                                    | Pro license token in CI                   | Free Solid, same names              | —                                              |
| Editor policy (trimmed toolbar, required alt/caption, locked patterns, Tabs block disabled) | Editor configuration                      | Not built                           | —                                              |
| Image budgets (300/150/60 KB)                                                               | Media pipeline                            | Documented in design-principles.md  | —                                              |
| Page-study layouts (media pair, split media, bleed, plate, schematic, drafting grid)        | A page-composition branch                 | Not built                           | —                                              |

## Verification still owed

Block markup in `patterns/` and on the academics page was written against core's `save()`
source rather than generated in the editor. Before relying on it, paste each pattern into the
editor (or open the page) and check the store, per [CLAUDE.md](../CLAUDE.md):

```js
wp.data.select( 'core/block-editor' ).getBlocks(); // walk innerBlocks; watch for "Updated Block"
```

The axe tier (`npm run test:a11y`) has also not been run against these changes.
