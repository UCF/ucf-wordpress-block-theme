/**
 * Editor previews for the theme's server-rendered data blocks.
 *
 * Facts, Program finder, Profile, Events, Provenance and Alert banner are registered in PHP,
 * in the includes/ file that owns each one's data, and render on the server. This gives each
 * an `edit` that previews that render and exposes its attributes as controls.
 *
 * WHY the controls are built from the attribute schema. WordPress hands the server-side
 * definition to the editor, so `getBlockType()` already knows each attribute's type and, for
 * a picker, its `enum` — the list of programs or people comes from the data file, through PHP,
 * with no second list here.
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { getBlockType, registerBlockType } from '@wordpress/blocks';
import {
	PanelBody,
	RangeControl,
	SelectControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import ServerSideRender from '@wordpress/server-side-render';

const BLOCKS = [
	'ucf/facts',
	'ucf/program-finder',
	'ucf/profile',
	'ucf/events',
	'ucf/provenance',
	'ucf/alert-banner',
];

/**
 * One control per attribute, chosen by its schema.
 *
 * @param {Object}   props          Props.
 * @param {string}   props.name     Attribute name.
 * @param {Object}   props.schema   Its schema from the block type.
 * @param {*}        props.value    Current value.
 * @param {Function} props.onChange Setter.
 * @return {Element|null} A control, or nothing for an attribute with no editor.
 */
function AttributeControl( { name, schema, value, onChange } ) {
	if ( Array.isArray( schema.enum ) ) {
		return (
			<SelectControl
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				label={ name }
				value={ value }
				// WHY: '' is a real choice — Facts uses it for "the degree this page is".
				options={ schema.enum.map( ( option ) => ( {
					label:
						'' === option
							? __( '(this degree)', 'ucf-wordpress-block-theme' )
							: option,
					value: option,
				} ) ) }
				onChange={ onChange }
			/>
		);
	}

	if ( 'boolean' === schema.type ) {
		return (
			<ToggleControl
				__nextHasNoMarginBottom
				label={ name }
				checked={ !! value }
				onChange={ onChange }
			/>
		);
	}

	if ( 'number' === schema.type ) {
		return (
			<RangeControl
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				label={ name }
				value={ value }
				min={ 1 }
				max={ 10 }
				onChange={ onChange }
			/>
		);
	}

	return null;
}

BLOCKS.forEach( ( name ) => {
	registerBlockType( name, {
		edit: function Edit( { attributes, setAttributes } ) {
			const blockProps = useBlockProps();
			const schema = getBlockType( name )?.attributes || {};
			// WHY: only the attributes this block declared — core adds its own (className,
			// lock, metadata), which have their own controls already.
			const own = Object.keys( schema ).filter(
				( key ) =>
					! [ 'className', 'lock', 'metadata', 'align' ].includes(
						key
					)
			);

			return (
				<div { ...blockProps }>
					{ own.length > 0 && (
						<InspectorControls>
							<PanelBody
								title={ __(
									'Data',
									'ucf-wordpress-block-theme'
								) }
							>
								{ own.map( ( key ) => (
									<AttributeControl
										key={ key }
										name={ key }
										schema={ schema[ key ] }
										value={ attributes[ key ] }
										onChange={ ( value ) =>
											setAttributes( { [ key ]: value } )
										}
									/>
								) ) }
							</PanelBody>
						</InspectorControls>
					) }
					<ServerSideRender
						block={ name }
						attributes={ attributes }
					/>
				</div>
			);
		},
		save: () => null,
	} );
} );
