# ucf-wordpress-block-theme

A WordPress block theme for the University of Central Florida that brings the look, feel, and
conventions of the UCF Athena Framework to the WordPress block editor (Full Site Editing).

> **Status: scaffold.** The structure, build pipeline and token architecture are in place and
> working. The design values in `theme.json` are placeholders carried over from the UCF Brand
> Block Theme, pending Athena equivalents — see
> [docs/architecture.md § Tokens are placeholders](docs/architecture.md#tokens-are-placeholders).

## Requirements

-   WordPress 7+
-   PHP 8.1+
-   Node 22+ (see `.nvmrc`)
-   Composer, for the PHP linter

## Quick start

```bash
nvm use
npm install
composer install
npm run build
```

`npm run build` compiles `src/scss/main.scss` to `build/css/main.css` and builds any blocks in
`src/blocks/`. **`build/` is committed** so the theme deploys without a build step — rebuild and
commit it with any change under `src/`.

For a local WordPress to develop against:

```bash
npm run env:start     # wp-env, Docker required
```

## Scripts

| Command                | Does                                                         |
| ---------------------- | ------------------------------------------------------------ |
| `npm test`             | The PHP unit suite. No Docker, under a second                |
| `npm run build`        | Stylesheet + blocks. Run before committing `src/` changes    |
| `npm run watch`        | Recompile the stylesheet on change (expanded, for debugging) |
| `npm run start`        | `wp-scripts` dev server for blocks                           |
| `npm run lint:js`      | ESLint                                                       |
| `npm run lint:css`     | Stylelint                                                    |
| `npm run lint:php`     | PHPCS (WordPress standard)                                   |
| `npm run lint:version` | `style.css` and `package.json` versions agree                |
| `npm run format`       | Prettier, write                                              |
| `npm run format:check` | Prettier, check only                                         |

`npm test` runs the PHP unit suite and needs no Docker. Read [tests/README.md](tests/README.md)
before adding to it — it covers what belongs in this tier, what must not, and the non-obvious
parts of the harness. The integration and accessibility tiers do not exist yet; see
[docs/architecture.md § What is not here yet](docs/architecture.md#what-is-not-here-yet).

## Where things live

```
functions.php          Loader only — never append to it
theme.json             Every design token. The single source for what a token is worth
style.css              WordPress theme header (name, version, requirements)

includes/              One topic per file. See the table in docs/architecture.md
src/scss/              The stylesheet, compiled to build/css/main.css
  _variables.scss        Sass names for theme.json presets
  _compositions.scss     Tokens → roles. The only file that may set a --ucf-* property
  _base.scss             Document-level rules, focus, skip link, block-style definitions
  _typography.scss       Binds elements to color roles
  _utilities.scss        Classes a pattern uses to bind a role to part of a component
  main.scss              Load order — which is output order
src/blocks/            Static custom blocks (empty)
src/js/editor/         Editor glue — Section variation, Badge formats. Builds to build/editor.js

patterns/              Block patterns (empty, flat). See patterns/README.md
templates/             Block templates: index, page, single, search, 404, and the
                       selectable degree-program template for the `degree` post type
                       No header or footer part — the University Header is injected
                       by includes/university-header.php, and the footer is a plugin

assets/fonts/          Self-hosted webfonts, declared as fontFace in theme.json
build/                 Compiled output. Committed; never hand-edited
tests/php/             PHP unit suite. See tests/README.md
tools/                 Build and check scripts
docs/                  architecture.md — read it before changing anything
```

## Before you change anything

Read **[docs/architecture.md](docs/architecture.md)**. It is the single source for this theme's
conventions and the reasoning behind them — the token escalation ladder, the role layer that
lets a pattern carry no color, the one-topic-per-file rule for `includes/`, and the list of
gotchas not to "clean up".

Two rules that catch people immediately:

-   **Never append to `functions.php`.** It is a loader. Behavior goes in the `includes/` file
    that owns its topic.
-   **A pattern never names a color.** It names a composition (`is-style-dark`) and lets the roles
    resolve. A `textColor` attribute in a pattern is a bug.

## Provenance

Seeded from the structural half of the
[UCF Brand Block Theme](../ucf-brand-block-theme): its build pipeline, token and role
architecture, PHP file organization, and its eight generic `includes/` topics. The
brand-documentation features — section numbering, the sidebar drawer, the on-page index,
scoped search, Download Monitor integration, color-swatch blocks — were deliberately left
behind.

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
