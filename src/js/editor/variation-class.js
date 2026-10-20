/**
 * Brighter in the editor: put `is-bright` on the canvas body when the variation is active.
 *
 * includes/setup.php adds the class to the front-end `<body>` through `body_class`, but the
 * editor canvas is an iframe with its own body, which that filter never reaches — without
 * this, an author would edit dark bands that publish bright. The rule that reads the class is
 * the same one in src/scss/_compositions.scss.
 *
 * SYNC: the setting is `settings.custom.mode` in styles/bright.json, the same one
 * `ucf_theme_variation_classes()` reads.
 * UPSTREAM: switching variation in the Site Editor shows up here on the next load, not live —
 * the merged settings this reads are computed when the editor boots.
 */
import { store as blockEditorStore } from '@wordpress/block-editor';
import { select, subscribe } from '@wordpress/data';
import domReady from '@wordpress/dom-ready';

/**
 * Add the class to whichever canvas exists: the iframe's body, or the inline wrapper.
 *
 * @return {boolean} Whether a canvas was found.
 */
function mark() {
	const frame = document.querySelector( 'iframe[name="editor-canvas"]' );
	const body = frame?.contentDocument?.body;
	const inline = document.querySelector( '.editor-styles-wrapper' );
	const target = body || inline;

	if ( target ) {
		target.classList.add( 'is-bright' );
	}

	return !! target;
}

domReady( () => {
	const mode =
		select( blockEditorStore ).getSettings()?.__experimentalFeatures?.custom
			?.mode;

	if ( 'bright' !== mode ) {
		return;
	}

	// WHY: the iframe is created, and re-created on device preview, after this runs; keep
	// marking whatever canvas is current.
	subscribe( mark );
	mark();
} );
