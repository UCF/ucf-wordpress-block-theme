# Patterns

Flat. One file per pattern, no subdirectories.

Core discovers patterns by reading the **header comment** in each `.php` file here — the path
is never consulted. Grouping them into folders only implies a taxonomy that nothing enforces
and nothing reads, and it drifts from the `Categories:` the files actually declare.

## Writing one

```php
<?php
/**
 * Title: Call to Action
 * Slug: ucf-wordpress-block-theme/call-to-action
 * Categories: ucf
 * Description: Shown in the inserter preview; write it for an author, not a developer.
 * Keywords: cta, button, banner
 *
 * @package ucf-wordpress-block-theme
 */
?>
<!-- wp:group … -->
```

`Title` and `Slug` are required; the rest are optional. The slug should be namespaced to the
theme so it cannot collide with a plugin's.

**`Categories:` is `ucf`, for every pattern.** It is the theme's one pattern category,
registered in `includes/patterns.php`, so an author finds every UCF pattern in one place in
the inserter. `PatternsTest` fails if a pattern names any other category.

## Rules

**A pattern declares structure and composition, never color.** A `textColor` or
`backgroundColor` attribute here is a bug — it freezes the pattern to one field. Name a
composition (`is-style-dark`) and let the roles resolve, so the same pattern works wherever it
is dropped. See
[docs/architecture.md § Patterns declare structure and composition, never color](../docs/architecture.md#patterns-declare-structure-and-composition-never-color).

**This directory is in `.prettierignore`, deliberately.** Block markup's canonical
serialization is defined by each block's `save()`, not by Prettier. Reformatting the
style or void-element syntax diverges from what the editor emits and produces invalid-block
warnings. Do not remove that entry.

**Never verify a pattern through the front end.** Invalid blocks still render there, so a page
that looks right proves nothing. Paste the markup into the editor and check the console for
"Updated Block", or ask the store directly:

```js
wp.data.select( 'core/block-editor' ).getBlocks();
```

## Component classes

Many patterns here are a design-system component made of core blocks: the pattern prefills a
class on a core Group, List or Table, and that class — styled in `src/scss/` — is what makes it
the component. Removing the class in the editor turns it back into a plain block. They are
deliberately not block styles: the Styles panel slot on a Group is its composition.

| Class                                        | Component                     | Styled in                                     |
| -------------------------------------------- | ----------------------------- | --------------------------------------------- |
| `ucf-card` (`--boxed`, `--ruled`, `--horizontal`) | Card                     | `_components.scss`                            |
| `ucf-stat`                                   | Stat statement                | `_components.scss`                            |
| `ucf-notice` (`--success`, `--danger`)       | Inline notice                 | `_components.scss`                            |
| `ucf-quick-links`, `ucf-quick-link`          | Quick links                   | `_components.scss`                            |
| `ucf-steps`                                  | Steps (on an ordered List)    | `_components.scss`                            |
| `ucf-answer`                                 | Answer summary                | `_components.scss`                            |
| `ucf-toc`                                    | On this page                  | `_components.scss`, `src/js/frontend/spy.js`  |
| `ucf-sources`, `ucf-ref`                     | Sources and in-text markers   | `_components.scss`                            |
| `ucf-video`, `ucf-video__play`               | Video facade                  | `_components.scss`, `src/js/frontend/video.js`|
| `ucf-faq` (on core Accordion)                | FAQ, "Expand all", `FAQPage`  | `src/js/frontend/expand.js`, `structured-data.php` |
| `ucf-compare` (on core Table)                | Comparison table              | `_table.scss`                                 |
| `ucf-hero-image`, `ucf-keep-dark`            | Hero image                    | `_media.scss`, `_compositions.scss`           |
| `ucf-media-pair`, `ucf-split-media`, `ucf-photo-strip`, `ucf-plate`, `ucf-bleed` | Photo placements | `_media.scss` |
| `ucf-schematic`, `ucf-marker`                | Schematic figure, section marker | `_media.scss`                              |
| `ucf-section--tight`                         | Tight Section band            | `_section.scss`                               |

Page-level patterns (`page-*`, `post-*`) declare `Block Types: core/post-content`, so the
editor offers them when a new page or post is created. The two page patterns expect the
Landing template, which leaves the H1 to the content.
