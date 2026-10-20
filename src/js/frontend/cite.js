/**
 * "Cite this": reveal the copy button and copy the citation text.
 *
 * The citation is already in the page as text (includes/programs.php); the button is `hidden`
 * in the markup, because without this script it would do nothing.
 */
import { __ } from '@wordpress/i18n';

document.querySelectorAll( '[data-ucf-copy]' ).forEach( ( button ) => {
	const source = document.getElementById( button.dataset.ucfCopy );

	// WHY: the Clipboard API is only offered on secure origins; on http the button stays hidden
	// and the text is there to select by hand.
	if ( ! source || ! window.navigator.clipboard ) {
		return;
	}

	const label = button.textContent;
	button.hidden = false;

	button.addEventListener( 'click', async () => {
		try {
			await window.navigator.clipboard.writeText(
				source.textContent.trim()
			);
			button.textContent = __( 'Copied', 'ucf-wordpress-block-theme' );
		} catch {
			button.textContent = __(
				'Copy failed — select the text instead',
				'ucf-wordpress-block-theme'
			);
		}

		setTimeout( () => ( button.textContent = label ), 2500 );
	} );
} );
