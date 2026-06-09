/**
 * UCF Badge — rich-text inline formats.
 *
 * Registers a "Badge" formatting option per color tone. Each wraps the selected
 * text in <span class="badge…">…</span>, so a badge can be applied inline to any
 * RichText (Paragraph, Heading, List item, etc.) from the formatting toolbar.
 * The tones are mutually exclusive: applying one clears the others on the
 * selection.
 *
 * No build step: this uses the global `wp.*` packages enqueued as script
 * dependencies in functions.php. Styling lives in src/scss/_badge.scss (the
 * .badge / .badge-* classes), so the look is token-driven and identical in the
 * editor and on the front end.
 */
( function ( wp ) {
	var registerFormatType = wp.richText.registerFormatType;
	var toggleFormat = wp.richText.toggleFormat;
	var removeFormat = wp.richText.removeFormat;
	var RichTextToolbarButton = wp.blockEditor.RichTextToolbarButton;
	var el = wp.element.createElement;
	var __ = wp.i18n.__;

	// name = format id; className = the class the span carries (and how the
	// format is recognized on load); title = toolbar label.
	// Mirrors the Athena contextual badge variations. name = format id;
	// className = the class the span carries (and how the format is recognized
	// on load); title = toolbar label.
	var tones = [
		{ name: 'ucf/badge', className: 'badge', title: __( 'Badge: Default', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-primary', className: 'badge-primary', title: __( 'Badge: Primary', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-secondary', className: 'badge-secondary', title: __( 'Badge: Secondary', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-complementary', className: 'badge-complementary', title: __( 'Badge: Complementary', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-success', className: 'badge-success', title: __( 'Badge: Success', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-info', className: 'badge-info', title: __( 'Badge: Info', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-warning', className: 'badge-warning', title: __( 'Badge: Warning', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-danger', className: 'badge-danger', title: __( 'Badge: Danger', 'ucf-wordpress-block-theme' ) },
		{ name: 'ucf/badge-inverse', className: 'badge-inverse', title: __( 'Badge: Inverse', 'ucf-wordpress-block-theme' ) },
	];

	tones.forEach( function ( tone ) {
		registerFormatType( tone.name, {
			title: tone.title,
			tagName: 'span',
			className: tone.className,
			edit: function ( props ) {
				return el( RichTextToolbarButton, {
					icon: 'tag',
					title: tone.title,
					isActive: props.isActive,
					onClick: function () {
						// Clear the other badge tones so they stay mutually
						// exclusive, then toggle the chosen one.
						var value = props.value;
						tones.forEach( function ( other ) {
							if ( other.name !== tone.name ) {
								value = removeFormat( value, other.name );
							}
						} );
						props.onChange( toggleFormat( value, { type: tone.name } ) );
					},
				} );
			},
		} );
	} );
} )( window.wp );