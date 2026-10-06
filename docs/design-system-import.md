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
the H1 to the page's own hero). Its photos are imported attachments; the local PHP has no GD
or Imagick, so they have no resized variants, only the originals.

**Removed after review.** The anchor chip ("UCF is" / "At UCF" on gold before a headline)
was built as a rich-text format and then taken out: it is described in the design system as a
brand device, but it is not confirmed against UCF's published brand guidelines.

## The degree program template

`templates/single-degree.html` is the design system's TemplateDegreeProgram: one template for
every post of the `degree` type that UCF-Degree-CPT-Plugin registers — 928 on the local `ucf`
site. It reads what each degree post actually holds (title, URL, parent program for a track,
dates), so the H1, breadcrumbs, level, program and track are real. The catalog facts the
system calls for (credit hours, length, format, outcomes) are not on the local posts, because
the plugin's importer has not populated them; until it does, the Facts block marks each
degree's record as sample, and a mock record for the same degree stands in where one exists.
The `page-degree-program` pattern remains for a hand-built program page.

## Built as mocks

These need a system larger than a theme. Each is built against mock data in `data/mock/`, read
through `ucf_theme_mock_data()`, and marked on the page with the sample-data note. The filter
named is where a real source replaces the mock without touching the block.

| Component                  | Built as                                                                                               | Real source (filter)                                   |
| -------------------------- | ------------------------------------------------------------------------------------------------------ | ------------------------------------------------------ |
| FactList                   | `ucf/facts` block, with "Cite this"                                                                    | UCF-Degree-CPT-Plugin (`ucf_theme_mock_data_programs`) |
| FilterList / degree search | `ucf/program-finder` block                                                                             | UCF-Degree-CPT-Plugin, UCF-Degree-Search-Plugin (same) |
| ProfileCard                | `ucf/profile` block                                                                                    | UCF-People-CPT (`ucf_theme_mock_data_people`)          |
| EventList                  | `ucf/events` block; past events drop off                                                               | UCF-Events-Plugin (`ucf_theme_mock_data_events`)       |
| Alert banner               | `ucf/alert-banner` block                                                                               | UCF-Alert-Plugin (`ucf_theme_mock_data_alerts`)        |
| Provenance                 | `ucf/provenance` block; real author and dates, reviewer from `ucf_reviewer` meta                       | A reviewer field in the editor                         |
| Structured data            | `includes/structured-data.php`: Organization, BreadcrumbList, FAQPage, Program, Person, Event, Article | Real once the data is                                  |
| Forms                      | `form` pattern, a mock RFI form that does not submit                                                   | gravityforms + Athena-GravityForms-Plugin              |

Patterns over core blocks, no data needed: Answer summary, Comparison table, Sources, On this
page, Video (facade in `src/js/frontend/video.js`), Hero statement and Hero image (locked),
the page-study placements, and three page-level patterns — Homepage, Degree program, Research
story — with demo pages on the local `ucf` site.

Editor policy: the toolbar is trimmed and Tabs removed (`src/js/editor/editor-policy.js`), and
a pre-publish panel checks alt text, table captions and heading order without blocking a save
(`prepublish-checks.js`). Brighter is a style variation (`styles/bright.json`).

## Still not built

| Item                                          | Why                                                                                                 |
| --------------------------------------------- | --------------------------------------------------------------------------------------------------- |
| Site header, University Header plugin, footer | Excluded from this round                                                                            |
| Font Awesome Sharp Solid                      | Needs UCF's Pro license token; the Free Solid names match                                           |
| Image budgets (300/150/60 KB)                 | A media pipeline; documented in design-principles.md                                                |
| A reviewer field in the editor                | `ucf_reviewer` is registered and in REST; no panel edits it yet                                     |
| College "photos" on the academics page        | They are round icon illustrations, not photographs — shown uncropped at 1:1 until real photos exist |

## Verification still owed

Block markup in `patterns/` and on the academics page was written against core's `save()`
source rather than generated in the editor. Before relying on it, paste each pattern into the
editor (or open the page) and check the store, per [CLAUDE.md](../CLAUDE.md):

```js
wp.data.select( 'core/block-editor' ).getBlocks(); // walk innerBlocks; watch for "Updated Block"
```

The axe tier (`npm run test:a11y`) has also not been run against these changes.
