# Icons

The UCF Web Design System's icon set, one SVG per glyph, registered with core's icon
registry by `includes/icons.php` as the `ucf` collection. They appear in the core Icon
block's picker; nothing in the theme loads them as a sprite or a webfont.

**Font Awesome Free 6.7.2, Solid.** Copyright Fonticons, Inc. — https://fontawesome.com.
Icons licensed [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/).

The design system specifies **Sharp Solid** from Font Awesome Pro, under UCF's license. Pro
glyphs cannot be fetched without the license token, so these are the Free Solid glyphs the
system's own build shipped. The file names are the system's names, not Font Awesome's, and
are identical across families: replacing the files with Sharp Solid exports of the same
glyphs is the whole swap. The name-to-glyph map is in the design system's Icon component.

Adding a glyph is a two-place change — the file here, and its name in `ucf_theme_icons()`.
`tests/php/IconsTest.php` fails if the two disagree.
