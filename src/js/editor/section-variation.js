/**
 * The Section band, as a variation of core/group.
 *
 * A band is a group whose defaults are already chosen: full-bleed, `<section>`, its own
 * padding, a constrained inner measure and a field. Registering it as a variation rather than
 * a block type means it *is* a group — every composition style, every core group control and
 * every future core improvement applies with nothing to keep in step, and there is no `save()`
 * to version with deprecations for markup core already emits.
 *
 * SYNC: `.ucf-section` is styled in src/scss/_section.scss. The class set here is what that
 * file matches on, and `isActive()` below is what recognizes it again on reload.
 */
import { registerBlockVariation } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

/**
 * WHY: a composition is applied at insertion rather than left for the author to choose.
 *
 * Since a background set through the block's color control does not bring the text roles with
 * it (see src/scss/_compositions.scss), a band with no style is a band with no field — it
 * inherits whatever encloses it. Starting on `light` means a Section is a real field from the
 * moment it is inserted, and switching it in the Styles panel is the one documented way to
 * change what that field is.
 */
const STYLE = 'is-style-light';

registerBlockVariation( 'core/group', {
	name: 'ucf-section',
	title: __( 'Section', 'ucf-wordpress-block-theme' ),
	description: __(
		'A full-width band with its own padding and field. Switch its style to change the field.',
		'ucf-wordpress-block-theme'
	),
	category: 'design',
	icon: 'align-full-width',
	keywords: [
		__( 'band', 'ucf-wordpress-block-theme' ),
		__( 'row', 'ucf-wordpress-block-theme' ),
		__( 'container', 'ucf-wordpress-block-theme' ),
	],

	// WHY: inserter only. Without this the variation is also offered as a transform on every
	// existing group, which turns an ordinary content group into a full-bleed band by accident.
	scope: [ 'inserter' ],

	attributes: {
		tagName: 'section',
		align: 'full',
		className: `ucf-section ${ STYLE }`,
		layout: { type: 'constrained' },
	},

	// A heading over an empty drop zone. The inner group is deliberately unstyled: it is where
	// a group pattern lands, and it inherits the band's roles rather than declaring its own.
	//
	// WHY no intro paragraph, though the old section pattern had one. A paragraph seeded from
	// an `innerBlocks` template does not behave like one an author types: it arrives carrying
	// the template's attributes, and wraps differently from an empty paragraph created in
	// place. Authors were having to delete it and add their own to get normal behaviour, which
	// makes it worse than nothing. Anything seeded here has to be something an author would
	// keep as-is; a heading is, placeholder prose is not.
	innerBlocks: [
		[
			'core/heading',
			{
				level: 2,
				content: __( 'Section Heading', 'ucf-wordpress-block-theme' ),
			},
		],
		[ 'core/group', { layout: { type: 'constrained' } } ],
	],

	/**
	 * Recognize a Section on reload, so the editor names it "Section" rather than "Group".
	 *
	 * WHY: a function, not the `[ 'className' ]` shorthand. That form compares the attribute
	 * for equality, and `className` changes the moment an author switches the composition —
	 * `ucf-section is-style-dark` would stop matching and the block would revert to reading as
	 * a plain Group. Only the marker class is load-bearing.
	 *
	 * @param {Object} attributes             The block's current attributes.
	 * @param {string} [attributes.className] Its class list.
	 * @return {boolean} Whether this block is a Section.
	 */
	isActive: ( { className = '' } ) =>
		className.split( ' ' ).includes( 'ucf-section' ),
} );
