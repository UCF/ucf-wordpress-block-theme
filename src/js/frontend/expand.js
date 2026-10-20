/**
 * "Expand all" for an FAQ.
 *
 * Adds one button ahead of every FAQ accordion (`.ucf-faq`, from patterns/faq.php) that opens
 * or closes every question at once. Added by script because without the script there is
 * nothing for it to do — the answers are in the page either way.
 */
import { __ } from '@wordpress/i18n';

document.querySelectorAll( '.wp-block-accordion.ucf-faq' ).forEach( ( faq ) => {
	const toggles = () => [
		...faq.querySelectorAll( '.wp-block-accordion-heading__toggle' ),
	];

	if ( toggles().length < 2 ) {
		return;
	}

	const button = document.createElement( 'button' );
	button.type = 'button';
	button.className = 'ucf-expand-all';
	button.setAttribute( 'aria-expanded', 'false' );
	button.textContent = __( 'Expand all', 'ucf-wordpress-block-theme' );

	button.addEventListener( 'click', () => {
		const open = 'true' !== button.getAttribute( 'aria-expanded' );

		// WHY: click each toggle rather than setting state — core's accordion keeps its state
		// in the Interactivity API store, and the toggle is its public way in.
		toggles()
			.filter(
				( toggle ) =>
					( 'true' === toggle.getAttribute( 'aria-expanded' ) ) !==
					open
			)
			.forEach( ( toggle ) => toggle.click() );

		button.setAttribute( 'aria-expanded', String( open ) );
		button.textContent = open
			? __( 'Collapse all', 'ucf-wordpress-block-theme' )
			: __( 'Expand all', 'ucf-wordpress-block-theme' );
	} );

	const row = document.createElement( 'div' );
	row.className = 'ucf-expand-all-row';
	row.append( button );
	faq.before( row );
} );
