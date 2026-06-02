/**
 * Page Header sidebar panel.
 *
 * Adds a "Page Header" panel to the Page editor's document sidebar with the
 * hero title/subtitle text fields and the three breakpoint background-image
 * pickers. Values are stored in post meta (see includes/page-fields.php) and
 * read by the ucf/page-header block at render time.
 *
 * No build step: written against the global `wp` packages.
 */
( function ( wp ) {
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var __ = wp.i18n.__;
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel =
		( wp.editPost && wp.editPost.PluginDocumentSettingPanel ) ||
		( wp.editor && wp.editor.PluginDocumentSettingPanel );
	var TextControl = wp.components.TextControl;
	var Button = wp.components.Button;
	var BaseControl = wp.components.BaseControl;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var useEntityProp = wp.coreData.useEntityProp;
	var useSelect = wp.data.useSelect;

	if ( ! PluginDocumentSettingPanel ) {
		return;
	}

	/**
	 * A single breakpoint image picker.
	 */
	function ImageField( props ) {
		var id = props.value ? parseInt( props.value, 10 ) : 0;

		var media = useSelect(
			function ( select ) {
				return id ? select( 'core' ).getMedia( id ) : null;
			},
			[ id ]
		);

		var thumb =
			media &&
			media.media_details &&
			media.media_details.sizes &&
			media.media_details.sizes.thumbnail
				? media.media_details.sizes.thumbnail.source_url
				: media
				? media.source_url
				: null;

		return el(
			BaseControl,
			{ label: props.label, help: props.help },
			el(
				MediaUploadCheck,
				null,
				el( MediaUpload, {
					allowedTypes: [ 'image' ],
					value: id,
					onSelect: function ( selected ) {
						props.onChange( selected.id );
					},
					render: function ( open ) {
						return el(
							'div',
							{ className: 'ucf-image-field' },
							thumb
								? el( 'img', {
										src: thumb,
										alt: '',
										style: {
											display: 'block',
											maxWidth: '100%',
											height: 'auto',
											marginBottom: '8px',
										},
								  } )
								: null,
							el(
								Button,
								{ variant: 'secondary', onClick: open.open },
								id
									? __( 'Change image', 'ucf-wordpress-block-theme' )
									: __( 'Select image', 'ucf-wordpress-block-theme' )
							),
							id
								? el(
										Button,
										{
											variant: 'tertiary',
											isDestructive: true,
											onClick: function () {
												props.onChange( 0 );
											},
											style: { marginLeft: '8px' },
										},
										__( 'Remove', 'ucf-wordpress-block-theme' )
								  )
								: null
						);
					},
				} )
			)
		);
	}

	function PageHeaderPanel() {
		var postType = useSelect( function ( select ) {
			return select( 'core/editor' ).getCurrentPostType();
		}, [] );

		var entity = useEntityProp( 'postType', postType, 'meta' );
		var meta = entity[ 0 ];
		var setMeta = entity[ 1 ];

		// Only Pages have these fields.
		if ( 'page' !== postType ) {
			return null;
		}

		function update( key, value ) {
			var next = {};
			next[ key ] = value;
			setMeta( Object.assign( {}, meta, next ) );
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'ucf-page-header',
				title: __( 'Page Header', 'ucf-wordpress-block-theme' ),
				className: 'ucf-page-header-panel',
			},
			el( TextControl, {
				label: __( 'Header title', 'ucf-wordpress-block-theme' ),
				help: __( 'Leave blank to use the page title.', 'ucf-wordpress-block-theme' ),
				value: meta.hero_title || '',
				onChange: function ( value ) {
					update( 'hero_title', value );
				},
			} ),
			el( TextControl, {
				label: __( 'Header subtitle', 'ucf-wordpress-block-theme' ),
				value: meta.hero_subtitle || '',
				onChange: function ( value ) {
					update( 'hero_subtitle', value );
				},
			} ),
			el( ImageField, {
				label: __( 'Header image — desktop', 'ucf-wordpress-block-theme' ),
				help: __( 'Used by default if smaller sizes are blank.', 'ucf-wordpress-block-theme' ),
				value: meta.hero_image_lg,
				onChange: function ( value ) {
					update( 'hero_image_lg', value );
				},
			} ),
			el( ImageField, {
				label: __( 'Header image — tablet (≤780px)', 'ucf-wordpress-block-theme' ),
				value: meta.hero_image_md,
				onChange: function ( value ) {
					update( 'hero_image_md', value );
				},
			} ),
			el( ImageField, {
				label: __( 'Header image — mobile (≤360px)', 'ucf-wordpress-block-theme' ),
				value: meta.hero_image_sm,
				onChange: function ( value ) {
					update( 'hero_image_sm', value );
				},
			} )
		);
	}

	registerPlugin( 'ucf-page-header-panel', {
		render: PageHeaderPanel,
		icon: null,
	} );
}( window.wp ) );