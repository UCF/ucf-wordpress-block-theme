/**
 * Editor glue.
 *
 * The single webpack entry for everything that customizes the block editor — block
 * variations, block filters, sidebar panels. Built to build/editor.js and enqueued by
 * `ucf_theme_enqueue_editor_assets()` in includes/enqueue.php, which reads the generated
 * build/editor.asset.php for its dependency list.
 *
 * Add a module here by importing it for its side effects; each one registers itself.
 */
import './section-variation';
import './badge-format';
