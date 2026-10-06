# Architecture

This is the single source for the theme's conventions and the reasoning behind them. Read it
before changing anything; `CLAUDE.md` and `README.md` deliberately do not restate these rules,
because keeping two copies is how they drift.

The theme was seeded from the structural half of the UCF Brand Block Theme. The machinery
described here — the token escalation ladder, the role layer, the one-topic-per-file rule —
came over intact and is proven. The _values_ those tokens hold now come from the UCF Web
Design System prototype; see [Tokens are placeholders](#tokens-are-placeholders) and
[design-system-import.md](design-system-import.md) for what came in and what was left out.

---

## The escalation ladder

When something needs to look a particular way, go down this list and stop at the first rung
that works. Each rung down is more code to own, so skipping ahead is the expensive mistake.

1. **A token.** A color, size, spacing step or family already in `theme.json`. Use the preset,
   not the literal.
2. **An existing class or block style.** A composition (`.is-style-dark`,
   `.is-style-alt`), a role utility (`.accent-text`, `.accent-fill`, `.hairline`, `.muted`),
   an element style (`is-style-data`, `is-style-check`…) or a component class a pattern
   applies (`.ucf-card`, `.ucf-stat`…) — the vocabulary in `src/scss/`.
3. **A core block control.** Padding, border width, alignment, background. If the editor can
   already express it, let the editor express it.
4. **Something new.** A new token in `theme.json`, a new block style, a new partial. This rung
   requires a reason that the three above could not cover.

The ladder exists because rungs 1–3 are things an editor can change later without a developer.
Rung 4 is not.

## Tokens are placeholders

`theme.json`'s palette, type scale, spacing scale and layout widths hold the UCF Web Design
System's values — itself a prototype, with open brand decisions marked `SPEC:` where they
land. **They are a test of that system, not a decision.**

What matters is the distinction between a slug and a value:

-   **The slugs are the contract.** `gold`, `gray-600`, `horizon-deep` and the rest are
    referenced by `src/scss/_variables.scss`, assigned to roles in `_compositions.scss`, and
    baked into the `is-style-*` names in `includes/block-styles.php`. Renaming a slug is a
    three-place edit.
-   **The values are free.** Changing a hex in `theme.json` re-paints everything downstream with
    no other edit, because nothing below `_variables.scss` names a token directly.

So: swapping in new colors is a `theme.json` change. Swapping in a new _vocabulary_ —
different role names, a different number of compositions — is a change to the three files
above. The design system import was exactly that second kind.

**A11Y: re-check the contrast pairs when the values change.** `_compositions.scss` carries
per-row comments recording which combinations are at the WCAG AA limit and why a given role
gave up grey. Those notes are about the current hexes. New hexes invalidate them.

## Roles, not tokens

The layer that makes a pattern reusable on any background.

```
theme.json           defines what a token is worth
_variables.scss      gives the token a Sass-level name       $color, $font, $size, $space
_compositions.scss   assigns tokens to roles                 sets --ucf-*
_typography.scss     binds elements to roles                 reads --ucf-body, --ucf-heading …
_utilities.scss      binds roles to parts of a component     reads --ucf-accent, --ucf-accent-text, --ucf-line
component partials   read a role for one component           reads --ucf-*
```

A **composition** is a background plus everything that has to be true of what sits on it: body
copy, headings, links, meta text, an accent, a hairline. `.is-style-dark` does not just paint
a black background — it redeclares every `--ucf-*` role for a dark field — including the focus ring and the buttons.

The payoff: **a pattern holds no color at all.** It names a composition and nothing else. Drop
the same pattern inside a Dark group and every role re-resolves. A pattern that hardcodes
`textColor` breaks the moment it is nested somewhere else — see below.

**A treatment is applied by a composition style and by nothing else.** Not by
`.has-black-background-color`, not by an `on-dark` modifier. A background set through the
block's own color control is just a color — it does not change what the field _is_, and a
block with one keeps the roles of whatever composition encloses it. Both alternatives were
built and removed: they made one visual result reachable by several routes with different
consequences, and left every palette color lacking such a rule behaving differently again.
To change the field, change the style. `BlockStylesTest` holds the line by asserting the
compositions are the only registered block styles.

**Only `_compositions.scss` may set a `--ucf-*` property.** Everything else reads one. A
component that sets its own role is a component that cannot be recomposed.

### Why custom properties and not descendant selectors

A rule like `.is-style-dark p { color: white }` keeps applying to a nested card that has its
own white background, because inheritance never beats a matching selector. The custom-property
form scopes correctly: the nested card redeclares `--ucf-body` for itself, and the paragraph
inside it resolves to the card's value.

## The Section band

A Section is a full-bleed strip of page with its own padding and its own field. It is
registered as the `ucf-section` **variation of `core/group`**, in
`src/js/editor/section-variation.js`, and styled as `.ucf-section` in `src/scss/_section.scss`.

**Why a variation and not a block type.** A band is a group whose defaults are already chosen.
As a variation it _is_ a group, so every composition style, every core group control and every
future core improvement applies with nothing to keep in step — and there is no `save()` to
version with deprecations for markup core already emits. A separate block type would have
meant re-registering all eight compositions onto it to arrive at the same markup.

**It ships with a composition applied** (`is-style-light`). That follows from the rule above:
a background set through the color control does not bring the roles with it, so a band with no
style is a band with no field. Switching the style in the Styles panel is the one documented
way to change what the field is.

**Two details worth keeping:**

-   `scope: [ 'inserter' ]`. Without it the variation is also offered as a transform on every
    existing group, which turns an ordinary content group into a full-bleed band by accident.
-   `isActive` is a function, not the `[ 'className' ]` shorthand. That shorthand compares the
    attribute for equality, and `className` changes the moment an author switches composition —
    `ucf-section is-style-dark` would stop matching and the block would revert to reading as a
    plain Group in the list view. Only the marker class is load-bearing.

## Patterns declare structure and composition, never color

A `textColor` or `backgroundColor` attribute in a pattern is a bug, not a style choice. It
freezes that pattern to one field.

What a pattern may carry:

-   structure — groups, columns, block nesting
-   a composition class — `is-style-dark`, `is-style-alt`
-   a role utility — `accent-text`, `accent-fill`, `hairline`
-   layout the block's own controls own — padding, width, gap, border _width_

## Blocks

**Static blocks only in `src/blocks/`.** A block there has a `save()` that emits real markup and
no `render.php`. `includes/blocks.php` registers every folder in `build/` that has a
`block.json`, discovered from disk rather than listed, so adding a block is a one-place change.

**A server-rendered block does not go in `includes/blocks.php`.** It lives in the file that owns
the data it renders, next to the queries and meta it reads, so its registration and its behavior
are one thing. It is registered in PHP with its attributes, and `src/js/editor/data-blocks.js`
gives it an editor preview through core's server-side render — the attribute schema, including
an `enum` for a picker, reaches the editor from the server definition, so it is written once.
Keep each one's markup in a pure builder function beside the render callback; that is the part
the unit suite can test.

**Block sources must not reference the theme.** Keeping that true is what makes moving
`src/blocks/` into a distribution plugin later a copy plus a registration loop.

**Block CSS belongs in `src/scss/`, not beside the block.** One stylesheet, one cascade, one
place where a role is read.

## PHP lives in `includes/`

One topic per file. `functions.php` is a loader and nothing else — **never append to it.** New
behavior goes in the `includes/` file that owns its topic, or in a new file added to the array
there.

The current topics:

| File                    | Owns                                                                               |
| ----------------------- | ---------------------------------------------------------------------------------- |
| `setup.php`             | Theme supports; the page editor's rendering mode; the style variation's body class |
| `enqueue.php`           | Every way CSS or JS reaches a browser                                              |
| `blocks.php`            | Static custom block registration                                                   |
| `block-styles.php`      | Every `register_block_style()`                                                     |
| `patterns.php`          | The UCF pattern category every theme pattern is filed under                        |
| `icons.php`             | The UCF icon set, registered with core's icon registry                             |
| `format.php`            | How dates are written, for every block that prints one                             |
| `mock-data.php`         | The stand-in for the data sources the data blocks will read                        |
| `structured-data.php`   | One JSON-LD graph per page, from what the page rendered                            |
| `programs.php`          | Facts and Program finder blocks                                                    |
| `people.php`            | Profile block                                                                      |
| `events.php`            | Events block                                                                       |
| `provenance.php`        | Provenance block and the `ucf_reviewer` post field                                 |
| `alerts.php`            | Alert banner block                                                                 |
| `university-header.php` | The UCF University Header script tag and placeholder                               |
| `paste-artifacts.php`   | Word-processor characters normalized on save and on display                        |

**No file may depend on another at include time.** Every one only defines functions and adds
hooks, so the load order in `functions.php` is documentation rather than a constraint. A
require-time dependency between two of them silently makes it load-bearing.

**Prefix is `ucf_theme_` / `UCF_THEME_`.** Not the bare `ucf_`: WPCS rejects a three-character
prefix outright, and it would be wrong anyway — this WordPress install already carries 200+
`ucf_*` functions across the other UCF themes, every one of which uses a second segment
(`ucf_bct_`, `ucf_bot_`, `ucf_brand_`). CSS classes and custom properties keep the shorter
`ucf-` / `--ucf-`; they have no collision sniff, and a differing PHP and CSS prefix is the
existing house pattern.

## A registered block style ships with its CSS

`includes/block-styles.php` registers the style; a partial under `src/scss/` defines it. The
comment above each registration names the partial. **A style registered and not defined is an
editor offering that paints nothing** — add both in the same commit.

The reverse also matters: a composition defined in `_compositions.scss` and not registered in
`ucf_theme_register_composition_styles()` is CSS no editor can reach. PHP cannot read a Sass
map, so those two lists are maintained by hand.

**Element styles are a short, reviewed list.** Compositions are the only styles on
`core/group`. Styles on other blocks — `data` on Paragraph, `sans` on Heading, `check` and
`divided` on List, `text` on Button — live in `ucf_theme_element_styles()`, and
`BlockStylesTest` checks each has an `.is-style-{name}` rule in `src/scss/` and that nothing
is registered outside the two lists. Anything a preset can say is a preset instead: Lead and
Display are font sizes, not styles.

**One deliberate exception: the `-accent` flavors.** Every composition has a second rule in the
stylesheet — `.is-style-alt-accent` and friends — adding a rule on the leading edge. None of
them are registered. The accent is _established_ by whatever wants the edge, a component or a
pattern applying the class, rather than picked by an author from the Styles panel; registering
them would double that panel to offer eight variants of one decision.

Because that looks exactly like the bug this section warns about, it is pinned from both sides
by `BlockStylesTest::test_accent_flavors_are_defined_but_not_registered()`. Deleting the rules
as "unreachable" fails, and so does registering them.

## JavaScript is one pipeline

`src/` → `build/`, through `@wordpress/scripts`.

Every entry emits a `<name>.asset.php` next to its `<name>.js`, holding the WordPress script
handles that entry imported plus a content hash. `ucf_theme_enqueue_build_script()` reads it,
so **a dependency list is never restated in PHP** — adding an `@wordpress/*` import to the
source is the whole change.

A folder with a `block.json` is auto-detected. Anything else — editor glue, a front-end script,
a rich-text format — is named in `webpack.config.js`. There are two named entries: `editor`
(variations, formats, the data blocks' previews, editor policy) and `frontend` (every
progressive enhancement — finder filtering, citation copy, the video facade, "Expand all",
scroll-spy, external-link marking). Each front-end module finds its own markup and does
nothing on a page without it; nothing on a page may depend on one to appear.

## Build and content

`npm run build` is both halves: the stylesheet (`sass`) and the blocks (`wp-scripts`).

**`build/` is committed.** The theme deploys without a build step, which is why CI fails if a
rebuild changes anything — stale output means the deployed theme does not match its source.
**Never hand-edit anything in `build/`.** Rebuild and commit it with any change under `src/`.

`npm run build:blocks` goes through `tools/build-blocks.js` rather than calling `wp-scripts`
directly, because `wp-scripts build` exits non-zero when there are no entries at all — the
state of a theme that has not written its first block. Delete that guard once the theme ships
a block and the empty case can no longer happen.

## Formatting and linting

One formatter owns whitespace; each linter keeps its rules about what the code _means_.

| Files                  | Formatter | Linter              |
| ---------------------- | --------- | ------------------- |
| PHP                    | PHPCBF    | PHPCS (WordPress)   |
| JS                     | Prettier  | ESLint (wp-scripts) |
| SCSS                   | Prettier  | Stylelint           |
| `templates/`, `parts/` | _nothing_ | —                   |

**Block markup is never reformatted.** Its canonical serialization is defined by each block's
`save()`, not by Prettier. Reformatting the style or void-element syntax diverges from what the
editor emits and produces invalid-block warnings — hence those three directories in
`.prettierignore`.

**Stylelint uses the `scss` preset, not `scss-stylistic`.** The stylistic rules contradict
Prettier on any declaration long enough to wrap, and stylelint's own `--fix` is then rejected by
Prettier. `.stylelintrc.js` records the specific failure modes, including one where the fixer
corrupts the Sass maps in `_compositions.scss`.

**`no-console` is off in `tools/`.** Those are command-line scripts whose job is to print, and
nothing in `tools/` ships.

## Gotchas that have already bitten

Do not "clean these up".

-   **`overflow-x: clip`, never `hidden`.** An `overflow: hidden` ancestor silently disables
    `position: sticky` on every descendant, and the bug surfaces far from the rule that caused it.
-   **`:not(:first-child)` / `:not(:last-child)` in the heading rhythm rules.** Core pairs its
    layout rule with first/last-child exceptions at the same specificity, so outranking one
    outranks those too. Without the exclusions the first heading in a band takes the band's
    padding _plus_ a full step.
-   **`styles.elements.link` reads `--ucf-link`, not a token.** WordPress emits element styles at
    (0,1,0), which outranks the fallback rule in `_typography.scss`. Naming a token there instead
    freezes every link to the light field's blue — 3.58:1 on black.
-   **Heading color is a role, not `styles.elements.heading.color`.** Core emits that as
    `:root :where(h1, …, h6)`, which matches the heading itself and always beats a color inherited
    from an ancestor — every heading in a Dark section would go black on black.
-   **The University Header's `?use-full-width=1` cannot move into a setting.** The host serves a
    _different build_ of the script for that query string, and that build reads the option back out
    of its own `src` at runtime.
-   **The University Header's script tag id is matched on the handle, never on the `src`.** UCF's
    published snippet tests the src, which both mis-stamps other scripts from that host and stops
    working behind an asset proxy.
-   **The editor canvas is an iframe.** `wp_enqueue_style()` never reaches inside it. Editor CSS
    goes through `add_editor_style()`, or the `styles` key of `block_editor_settings_all` when it
    has to be computed per post.
-   **Never verify markup through the front end.** Invalid blocks still render there, so a page
    that looks right proves nothing. Ask the editor's store instead:
    `wp.data.select( 'core/block-editor' ).getBlocks()`, and watch the console for
    "Updated Block" — a block reporting `isValid: true` may have been migrated through a
    deprecation rather than matched outright.
-   **Watch for opcache when testing PHP changes on a running site.** The usual local stack caches
    with `revalidate_freq=2`; a before/after capture taken faster than that compares stale code
    against stale code.

## Comments

This codebase documents **why**, not **what**. Comments explain reasoning and record bugs that
already shipped. Match that when adding code, and when moving code keep its comment with it —
including the file paths it references.

Unbudgeted, that principle produces twenty-line essays above four-line rules. So each comment is
tagged with the kind of reason it gives:

| Tag         | Means                                                 |
| ----------- | ----------------------------------------------------- |
| `WHY:`      | A decision that has a defensible alternative          |
| `FIX:`      | A bug this line prevents, which has actually occurred |
| `A11Y:`     | An accessibility requirement, with the measurement    |
| `UPSTREAM:` | Behavior imposed by WordPress core or a third party   |
| `SYNC:`     | This value must match another place, named            |
| `SPEC:`     | A value that comes from a design, not from reasoning  |
| `PERF:`     | A measured cost, not a guess                          |
| `CONTEXT:`  | A file- or section-level orientation banner           |

An untagged comment is usually one restating the code beneath it — delete it.

## What is not here yet

Ported from the brand theme's structural half only. Deliberately absent, with no stub:

-   **The slower test tiers, except accessibility.** The PHP unit suite gates CI — see
    [tests/README.md](../tests/README.md) — and an axe tier runs under wp-env
    (`npm run test:a11y`, `tests/a11y/`), seeding one page per template, pattern and block
    style. Missing are the Jest markup sweep over `templates/`, `parts/` and `patterns/`, and
    an integration tier. Keep them out of `npm test`: that command must never need Docker.
-   **A full pattern library.** `patterns/` holds the design system's basic components as
    patterns (cards, stats, notices, quick links, steps, FAQ, calls to action, section
    headers); page-level patterns and templates are next. Every pattern is filed under one custom
    category, UCF (`includes/patterns.php`). The directory is **flat on purpose** — core reads each
    file's header comment and never the path, so subdirectories imply a taxonomy nothing
    enforces. The brand theme's units → groups → sections → pages ladder was its own editorial
    structure; units and sections are intended to ship here as blocks instead, and Section
    already does, as the `ucf-section` variation of core/group (see
    [The Section band](#the-section-band)). Core's built-in categories cover almost everything;
    register a custom one only for a pattern that fits none of them. See
    [patterns/README.md](../patterns/README.md).
-   **Static custom blocks.** `src/blocks/` is empty: every design-system component is a core
    block, a block style, a pattern over core blocks — or, where it renders data, a
    server-rendered block in the `includes/` file that owns the data.
-   **Real data sources.** The six data blocks (Facts, Program finder, Profile, Events,
    Provenance, Alert banner) read mock data from `data/mock/` through
    `ucf_theme_mock_data()`. Each set has a filter, `ucf_theme_mock_data_{set}`, which is where a
    plugin's real data replaces it; the shape each block expects is documented at the top of its
    file, and `MockDataTest` checks the mock files still have it. Everything rendered from mock
    data carries the sample-data note. See [design-system-import.md](design-system-import.md).
