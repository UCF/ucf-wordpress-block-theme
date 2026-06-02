/**
 * Editor registration for the server-rendered ucf/page-header block.
 *
 * No build step: written against the global `wp` packages. The block has no
 * editable attributes — its content comes from the page meta at render
 * time — so the editor simply shows a ServerSideRender preview of the masthead.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var ServerSideRender = wp.serverSideRender;
	var __ = wp.i18n.__;

	wp.blocks.registerBlockType( 'ucf/page-header', {
		apiVersion: 3,
		title: __( 'Page Header', 'ucf-wordpress-block-theme' ),
		description: __( 'Full-width masthead: background image, gradient overlay, primary nav, and the page title/subtitle.', 'ucf-wordpress-block-theme' ),
		category: 'theme',
		icon: 'cover-image',
		supports: {
			html: false,
			reusable: false,
			inserter: true,
		},
		edit: function () {
			return el(
				'div',
				{ className: 'ucf-page-header-editor-preview' },
				el( ServerSideRender, { block: 'ucf/page-header' } )
			);
		},
		save: function () {
			return null; // Dynamic block; rendered in PHP.
		},
	} );
}( window.wp ) );