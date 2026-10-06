/**
 * Editor policy — what the design system takes out of the editor.
 *
 * SPEC: the toolbar is trimmed to bold, italic, link, highlight and footnotes ("Editors can
 * apply bold, italic, links and the lead style; nothing else" — the Text brief, plus core's
 * footnotes, which are the system's in-text source markers). And Tabs are removed: "They hide
 * content behind a second interaction and read poorly on phones. Use an accordion."
 *
 * WHY on `domReady`: core registers its formats and blocks during editor boot, after this
 * script is parsed. Unregistering earlier would remove nothing and warn.
 */
import { getBlockType, unregisterBlockType } from '@wordpress/blocks';
import domReady from '@wordpress/dom-ready';
import { select } from '@wordpress/data';
import {
	unregisterFormatType,
	store as richTextStore,
} from '@wordpress/rich-text';

const FORMATS = [
	'core/code',
	'core/image',
	'core/keyboard',
	'core/language',
	'core/strikethrough',
	'core/subscript',
	'core/superscript',
];

domReady( () => {
	FORMATS.forEach( ( name ) => {
		if ( select( richTextStore ).getFormatType( name ) ) {
			unregisterFormatType( name );
		}
	} );

	if ( getBlockType( 'core/tabs' ) ) {
		unregisterBlockType( 'core/tabs' );
	}
} );
