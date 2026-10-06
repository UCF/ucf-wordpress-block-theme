/**
 * Program finder: search and level filters over a list that is already in the page.
 *
 * SPEC: the full list ships in the HTML and every entry links to its own page; filtering only
 * hides rows. The count is announced politely as it changes, and an empty result says what to
 * do next. Markup: includes/programs.php, `ucf_theme_finder_markup()`.
 */
import { __, _n, sprintf } from '@wordpress/i18n';

document.querySelectorAll( '[data-ucf-filter]' ).forEach( ( root ) => {
	const controls = root.querySelector( '.ucf-finder__controls' );
	const search = root.querySelector( '[data-ucf-filter-search]' );
	const status = root.querySelector( '[data-ucf-filter-status]' );
	const chips = [ ...root.querySelectorAll( '.ucf-chip' ) ];
	const rows = [ ...root.querySelectorAll( '.ucf-result' ) ];

	if ( ! controls || ! search || ! status ) {
		return;
	}

	controls.hidden = false;

	const apply = () => {
		const query = search.value.trim().toLowerCase();
		const levels = chips
			.filter(
				( chip ) => 'true' === chip.getAttribute( 'aria-pressed' )
			)
			.map( ( chip ) => chip.dataset.value );
		let shown = 0;

		rows.forEach( ( row ) => {
			const match =
				( ! query || row.dataset.search.includes( query ) ) &&
				( ! levels.length || levels.includes( row.dataset.level ) );

			row.hidden = ! match;
			shown += match ? 1 : 0;
		} );

		status.textContent = shown
			? sprintf(
					/* translators: 1: programs shown, 2: total programs. */
					_n(
						'Showing %1$d of %2$d program',
						'Showing %1$d of %2$d programs',
						rows.length,
						'ucf-wordpress-block-theme'
					),
					shown,
					rows.length
			  )
			: __(
					'No programs match. Try a shorter search or clear the level filters.',
					'ucf-wordpress-block-theme'
			  );
	};

	search.addEventListener( 'input', apply );

	chips.forEach( ( chip ) =>
		chip.addEventListener( 'click', () => {
			chip.setAttribute(
				'aria-pressed',
				'true' === chip.getAttribute( 'aria-pressed' )
					? 'false'
					: 'true'
			);
			apply();
		} )
	);
} );
