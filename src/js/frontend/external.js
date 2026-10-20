/**
 * Mark links that leave UCF.
 *
 * Adds `ucf-external` to links in page content whose host is neither this site nor ucf.edu;
 * src/scss/_text.scss draws the arrow and its alternative text. A script and not a selector,
 * because only the page knows its own host.
 *
 * WHY content only: navigation, buttons and component links style themselves, and an arrow on
 * every one of them is noise.
 */
document
	.querySelectorAll( '.wp-block-post-content a[href^="http"]' )
	.forEach( ( link ) => {
		const host = link.hostname;
		const ucf = 'ucf.edu' === host || host.endsWith( '.ucf.edu' );

		if (
			ucf ||
			host === window.location.hostname ||
			link.classList.contains( 'wp-block-button__link' ) ||
			link.closest( '.ucf-quick-link, .ucf-card, .ucf-result' )
		) {
			return;
		}

		link.classList.add( 'ucf-external' );
	} );
