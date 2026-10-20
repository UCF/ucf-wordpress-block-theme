/**
 * Video facade: load the player only when asked.
 *
 * SPEC: a video costs nothing on page load — a poster and a play link — and YouTube's scripts
 * and cookies arrive only after a click, through the privacy-enhanced domain. Without this
 * script the play link simply goes to the video. Markup: patterns/video.php.
 */
import { sprintf, __ } from '@wordpress/i18n';

/**
 * The embed URL for a YouTube watch or share link.
 *
 * @param {string} href A link to a video.
 * @return {string|null} An autoplaying, privacy-enhanced embed URL, or null.
 */
export function embedUrl( href ) {
	const match = String( href ).match(
		/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([\w-]{11})/
	);

	return match
		? `https://www.youtube-nocookie.com/embed/${ match[ 1 ] }?autoplay=1&rel=0`
		: null;
}

document.querySelectorAll( '.ucf-video' ).forEach( ( root ) => {
	const link = root.querySelector( '.ucf-video__play a' );
	const poster = root.querySelector( 'figure' );
	const src = link && embedUrl( link.href );

	if ( ! src || ! poster ) {
		return;
	}

	link.addEventListener( 'click', ( event ) => {
		event.preventDefault();

		const frame = document.createElement( 'iframe' );
		frame.src = src;
		frame.title = sprintf(
			/* translators: %s: video title. */
			__( 'Video: %s', 'ucf-wordpress-block-theme' ),
			link.textContent.trim()
		);
		frame.allow = 'autoplay; encrypted-media; picture-in-picture';
		frame.allowFullscreen = true;

		poster.replaceWith( frame );
		frame.focus();
	} );
} );
