# UCF WordPress Block Theme

A WordPress block theme for the University of Central Florida that brings the
look, feel, and conventions of the [UCF Athena Framework](https://ucf.github.io/Athena-Framework/) to the WordPress block editor (Full Site Editing).

- **Contributors:** UCF Web Communications
- **Requires at least:** WordPress 7.0
- **Tested up to:** 7.0
- **Requires PHP:** 5.7
- **License:** GPLv2 or later ([details](http://www.gnu.org/licenses/gpl-2.0.html))

## Purpose

This theme adapts the UCF Athena Framework's brand layer — typography, color
palette, and presentational styles — into a native block theme. Rather than
porting Athena's Bootstrap-based grid and utility system (which the block editor
already provides through layout, spacing, and alignment controls), it focuses on:

- The **brand foundation** as `theme.json` design tokens (colors, font sizes,
  spacing scale).
- **CSS-only block style variations** that recreate Athena components on top of
  core blocks.

Interactive, JavaScript-dependent components (carousels, modals, accordions,
etc.) are intentionally **left out of the theme** and belong in a companion
plugin so they survive a theme switch. See [roadmap.md](roadmap.md) for the full
incorporation plan and the theme-vs-plugin split.

## Features

### Typography
- Three UCF web fonts loaded via `theme.json`: **UCF Sans Serif Alt** (default
  body font), **UCF Slab Serif Alt**, and **UCF Condensed Alt**.
- Athena heading scale (responsive, with the base → `md` breakpoint jump) for
  `h1`–`h6`.
- **Heading size variations** (`.h1`–`.h6` / "Heading N size" block styles): apply
  a different visual size to a heading while keeping its semantic tag — e.g. an
  `<h1>` rendered at h3 size.
- **Font-family utilities** (`.font-slab-serif`, `.font-condensed`,
  `.font-sans-serif-alt`), including Athena's per-font size ratios.
- **`.lead`** paragraph style for larger, lighter intro text.
- **Heading "Underline"** variation: uppercase text with a short UCF Gold accent
  bar.

### Color
- Full UCF brand palette in `theme.json`, including the gray ramp, semantic
  colors, the gold shade ramp, and the accessible (AA-contrast) `-aw` variants.

### Components
- **Blockquote** styled to the UCF/Athena look, with **border-color variations**
  (gold, black, success, info, warning, danger) tied to the palette.
- Standardized vertical spacing (default bottom margin) across headings,
  paragraphs, and blockquotes.

## Development

Styles are authored in SCSS under `src/scss/` and compiled to
`assets/css/main.css`, which is enqueued on the front end and inside the block
editor via `functions.php`.

### Setup

```bash
npm install
```

### Commands

| Command | Description |
| --- | --- |
| `npm run build` | Compile SCSS to compressed `assets/css/main.css`. |
| `npm run watch` | Recompile (expanded) on save during development. |

### Project structure

```
.
├── assets/css/main.css   # Compiled stylesheet (enqueued)
├── parts/                # Template parts (header, footer)
├── templates/            # Block templates
├── src/scss/             # SCSS source
│   ├── _variables.scss   # Type scale, font ratios, breakpoint
│   ├── _fonts.scss       # Font-family utilities
│   ├── _headings.scss    # Heading sizes + variations
│   ├── _text.scss        # .lead and body-copy styles
│   ├── _blockquote.scss  # Blockquote + border variations
│   ├── _spacing.scss     # Default block spacing
│   └── main.scss         # Entry point
├── functions.php         # Block style registrations + asset enqueue
├── theme.json            # Settings, design tokens, base element styles
└── style.css             # Theme header metadata
```

## Changelog

### 1.0.0
- Initial release.

## Copyright

UCF WordPress Block Theme, &copy; 2026 UCF Web Communications.

This theme is distributed under the terms of the GNU GPL. This program is free
software: you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation, either
version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.
