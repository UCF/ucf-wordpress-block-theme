/**
 * Pre-publish checks — the design system's editor checks, as a checklist rather than a lock.
 *
 * SPEC: the Figure brief requires alt text, the Table brief a caption "by an editor check",
 * and the Headings brief one H1 and levels in order.
 * WHY a checklist and not a lock on saving. An image can be decorative, and the right alt
 * text for one is empty — a hard rule would force authors to invent alt text, which is worse
 * than none. So the panel lists what to look at, and the author decides.
 */
import { useSelect } from '@wordpress/data';
import { PluginPrePublishPanel } from '@wordpress/editor';
import { __, sprintf } from '@wordpress/i18n';
import { registerPlugin } from '@wordpress/plugins';

/**
 * Every block in a tree, depth first.
 *
 * @param {Object[]} blocks Blocks.
 * @return {Object[]} Flattened.
 */
function flatten( blocks ) {
	return blocks.flatMap( ( block ) => [
		block,
		...flatten( block.innerBlocks || [] ),
	] );
}

/**
 * What the checks found.
 *
 * @param {Object[]} blocks Every block in the post.
 * @return {string[]} One sentence per problem.
 */
export function findProblems( blocks ) {
	const problems = [];
	const noAlt = blocks.filter(
		( b ) =>
			'core/image' === b.name && b.attributes.url && ! b.attributes.alt
	).length;
	const noCaption = blocks.filter(
		( b ) =>
			'core/table' === b.name && ! String( b.attributes.caption || '' )
	).length;
	const levels = blocks
		.filter( ( b ) => 'core/heading' === b.name )
		.map( ( b ) => b.attributes.level || 2 );

	if ( noAlt ) {
		problems.push(
			sprintf(
				/* translators: %d: number of images. */
				__(
					'%d image(s) have no alt text. Add it, or confirm each one is decorative.',
					'ucf-wordpress-block-theme'
				),
				noAlt
			)
		);
	}

	if ( noCaption ) {
		problems.push(
			sprintf(
				/* translators: %d: number of tables. */
				__(
					'%d table(s) have no caption saying what they show.',
					'ucf-wordpress-block-theme'
				),
				noCaption
			)
		);
	}

	if ( levels.filter( ( level ) => 1 === level ).length > 1 ) {
		problems.push(
			__( 'There is more than one H1.', 'ucf-wordpress-block-theme' )
		);
	}

	if ( levels.some( ( level, i ) => i > 0 && level > levels[ i - 1 ] + 1 ) ) {
		problems.push(
			__(
				'A heading skips a level. Choose the level for structure and the style for size.',
				'ucf-wordpress-block-theme'
			)
		);
	}

	return problems;
}

registerPlugin( 'ucf-prepublish-checks', {
	render: function Checks() {
		const problems = useSelect(
			( select ) =>
				findProblems(
					flatten( select( 'core/block-editor' ).getBlocks() )
				),
			[]
		);

		return (
			<PluginPrePublishPanel
				title={ __( 'UCF checks', 'ucf-wordpress-block-theme' ) }
				initialOpen={ problems.length > 0 }
			>
				{ problems.length ? (
					<ul>
						{ problems.map( ( problem ) => (
							<li key={ problem }>{ problem }</li>
						) ) }
					</ul>
				) : (
					<p>
						{ __(
							'Alt text, table captions and heading order look right.',
							'ucf-wordpress-block-theme'
						) }
					</p>
				) }
			</PluginPrePublishPanel>
		);
	},
} );
