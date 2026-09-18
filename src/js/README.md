# Scripts

Editor glue and front-end scripts — anything that is not a block, since a block is auto-detected
from its `block.json`.

Add an entry for each in `webpack.config.js`. The build emits a `<name>.asset.php` beside each
`<name>.js` holding that entry's WordPress script dependencies, which
`ucf_theme_enqueue_build_script()` reads — so a dependency list is never restated in PHP.

`includes/enqueue.php` enqueues the `editor` entry (`src/js/editor/index.js`), which today
registers the Section band as a variation of core/group.

See [docs/architecture.md § JavaScript is one pipeline](../../docs/architecture.md#javascript-is-one-pipeline).
