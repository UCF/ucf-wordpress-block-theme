# UCF WordPress Block Theme — working notes

**Read [`docs/architecture.md`](docs/architecture.md) before changing anything.** It is the
single source for this theme's conventions and for the reasoning behind them. This file does
not restate those rules — keeping two copies is exactly the drift they are meant to prevent.
Read `README.md` first for what the theme is and where each file lives.

## What is in the architecture doc

Skim this index; go read the section before you touch the area it covers.

| Section                                                 | Covers                                                         |
| ------------------------------------------------------- | -------------------------------------------------------------- |
| The escalation ladder                                   | Tokens → existing classes → core controls → something new      |
| Tokens are placeholders                                 | Which parts of `theme.json` are a contract and which are free  |
| Roles, not tokens                                       | `--ucf-*` roles, and which file may set one                    |
| Patterns declare structure and composition, never color | Why a `textColor` attribute in a pattern is a bug              |
| Blocks                                                  | Static-only rule, where dynamic blocks go instead              |
| PHP lives in `includes/`                                | One topic per file; `functions.php` is a loader only           |
| A registered block style ships with its CSS             | The two hand-maintained lists that must agree                  |
| JavaScript is one pipeline                              | `src/` → `build/`, generated `.asset.php` dependency lists     |
| Build and content                                       | What `npm run build` does; why `build/` is committed           |
| Formatting and linting                                  | Which formatter owns which file type, and why stylelint defers |
| Gotchas that have already bitten                        | The list not to "clean up"                                     |
| Comments                                                | The tag vocabulary every comment here uses                     |
| What is not here yet                                    | Tests, patterns, blocks, Athena values                         |

## State of the theme

This is a **scaffold**, seeded from the structural half of the UCF Brand Block Theme. What is
here works and is linted clean; what is absent is absent on purpose and listed in the
architecture doc's last section. When adding to it:

-   **There are no tests yet.** Do not write a change that assumes a suite will catch it. If you
    add the first suite, follow the brand theme's four-tier split (fast PHPUnit + Jest with no
    Docker; integration and a11y behind separate commands) and add the blocking CI job at the
    `TODO` in `.github/workflows/ci.yml`.
-   **`theme.json`'s values are placeholders; its slugs are a contract.** Changing a hex is a
    one-file change. Changing a slug is a three-file change — `theme.json`,
    `src/scss/_variables.scss`, and any `is-style-*` name in `includes/block-styles.php` built on
    it. Re-check the contrast notes in `_compositions.scss` whenever the values move.

## Working practices

These are about how to make a change here, not about what the theme is.

-   **Never append to `functions.php`.** It is a loader. New behavior goes in the `includes/`
    file that owns its topic, or in a new file added to the array there.
-   **Rebuild and commit `build/` with any change under `src/`.** Both halves: `npm run build`.
    Never hand-edit anything in `build/`. CI fails if the committed output is stale.
-   **Bump both version numbers together.** `style.css` is the one WordPress reads; `package.json`
    is the one npm reads. `npm run lint:version` checks they agree, and it gates CI.
-   **Never verify markup through the front end.** Invalid blocks still render there, so a page
    that looks right proves nothing. Ask the editor's store directly:
    ```js
    wp.data.select( 'core/block-editor' ).getBlocks(); // walk innerBlocks
    ```
    Read that result carefully: a block reporting `isValid: true` may still have been migrated
    through a deprecation rather than matched outright. Watch the console for "Updated Block".
-   **Watch for opcache when testing PHP changes on a running site.** The usual local stack caches
    with `revalidate_freq=2`; a before/after page capture taken faster than that compares stale
    code against stale code and proves nothing.
-   **Run the linters before committing.** `npm run lint:js`, `npm run lint:css`,
    `npm run lint:php`, `npm run format:check`. All four are clean right now — keep them that way,
    and do not silence a sniff without writing down why.
-   **This codebase documents _why_, not _what_.** Comments explain reasoning and record bugs that
    already shipped. Match that when adding code, and when moving code keep its comment with it —
    including the file paths it references. Every comment is tagged; the vocabulary is in the
    architecture doc's Comments section.
