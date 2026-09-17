# Blocks

Static custom blocks only — a block here has a `save()` that emits real markup and no
`render.php`. `includes/blocks.php` registers every folder in `build/` that has a `block.json`,
discovered from disk, so adding a block needs no registration edit.

A **server-rendered** block does not belong here. It goes in the `includes/` file that owns the
data it renders.

Block sources must not reference the theme — that is what keeps moving this directory into a
distribution plugin later a copy plus a registration loop. Block CSS belongs in `src/scss/`.

See [docs/architecture.md § Blocks](../../docs/architecture.md#blocks).
