/**
 * Credit — a rich-text format for a photo credit inside a caption.
 *
 * Wraps the selection in <span class="ucf-credit">, which src/scss/_text.scss sets on its own
 * line under the caption. Offered on Image captions only, where a credit belongs.
 */
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import { registerFormatType, toggleFormat } from '@wordpress/rich-text';

const NAME = 'ucf/credit';

/**
 * The toolbar toggle, shown only while editing an Image block.
 *
 * @param {Object}   props          Format edit props.
 * @param {boolean}  props.isActive Whether the selection is already a credit.
 * @param {Object}   props.value    The rich-text value.
 * @param {Function} props.onChange Commits a new value.
 * @return {Element|null} The button, or nothing outside an image caption.
 */
function CreditButton( { isActive, value, onChange } ) {
	const inImage = useSelect(
		( select ) =>
			'core/image' ===
			select( 'core/block-editor' ).getSelectedBlock()?.name,
		[]
	);

	if ( ! inImage ) {
		return null;
	}

	return (
		<RichTextToolbarButton
			icon="camera"
			title={ __( 'Photo credit', 'ucf-wordpress-block-theme' ) }
			isActive={ isActive }
			onClick={ () => onChange( toggleFormat( value, { type: NAME } ) ) }
		/>
	);
}

registerFormatType( NAME, {
	title: __( 'Photo credit', 'ucf-wordpress-block-theme' ),
	tagName: 'span',
	className: 'ucf-credit',
	edit: CreditButton,
} );
