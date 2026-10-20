/**
 * "On this page": mark the link for the section in view.
 *
 * Sets `aria-current="true"` on the jump link whose heading was most recently scrolled past,
 * which src/scss/_components.scss renders in bold. Markup: patterns/on-this-page.php.
 */
document.querySelectorAll( '.ucf-toc' ).forEach( ( toc ) => {
	const links = [ ...toc.querySelectorAll( 'a[href^="#"]' ) ];
	const targets = links
		.map( ( link ) =>
			document.getElementById(
				decodeURIComponent( link.hash.slice( 1 ) )
			)
		)
		.filter( Boolean );

	if ( ! targets.length || ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	const mark = ( id ) =>
		links.forEach( ( link ) =>
			link.hash.slice( 1 ) === id
				? link.setAttribute( 'aria-current', 'true' )
				: link.removeAttribute( 'aria-current' )
		);

	const observer = new window.IntersectionObserver(
		( entries ) => {
			const visible = entries
				.filter( ( entry ) => entry.isIntersecting )
				.sort(
					( a, b ) =>
						a.boundingClientRect.top - b.boundingClientRect.top
				);

			if ( visible.length ) {
				mark( visible[ 0 ].target.id );
			}
		},
		{ rootMargin: '0px 0px -70% 0px' }
	);

	targets.forEach( ( target ) => observer.observe( target ) );
} );
