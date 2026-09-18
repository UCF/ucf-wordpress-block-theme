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
 * Categories: call-to-action
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

**`Categories:` takes core's built-in categories** — `banner`, `call-to-action`, `featured`,
`text`, `gallery`, `about`, `contact`, `services`, `team`, `posts` — and they are almost
always enough. Registering a custom one is a `register_block_pattern_category()` call that
needs its own `includes/` file, so only do it when a pattern genuinely fits none of core's.

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
