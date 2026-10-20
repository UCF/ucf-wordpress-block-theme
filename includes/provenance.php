<?php
/**
 * Provenance: who wrote a page, who checked it, when, and which unit stands behind it.
 *
 * CONTEXT: the design system's trust layer. The author and both dates are real post fields;
 * the reviewer is a post meta field, `ucf_reviewer`, which nothing edits yet — until it is
 * filled, the block falls back to a sample reviewer and says so. The owning unit is the
 * site's name.
 *
 * WHY real fields where they exist. The brief's rule is that the visible date and the
 * structured `dateModified` cannot drift apart; reading both from the post is how.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the Provenance block and the reviewer field it reads.
 *
 * @return void
 */
function ucf_theme_register_provenance_block() {
	foreach ( array( 'post', 'page' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'ucf_reviewer',
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => true,
				'auth_callback' => static function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}

	register_block_type(
		'ucf/provenance',
		array(
			'api_version'     => 3,
			'title'           => __( 'Provenance', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'Who wrote this, who reviewed it, when, and the unit responsible. Read from the post.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'yes-alt',
			'keywords'        => array( 'byline', 'reviewed', 'author', 'updated' ),
			'uses_context'    => array( 'postId' ),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'ucf_theme_render_provenance',
		)
	);
}
add_action( 'init', 'ucf_theme_register_provenance_block' );

/**
 * Render the Provenance block for the current post.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Inner content (none).
 * @param WP_Block             $block      The block instance.
 * @return string Markup.
 */
function ucf_theme_render_provenance( $attributes, $content, $block ) {
	$post_id = $block->context['postId'] ?? get_the_ID();
	$post    = get_post( $post_id );

	if ( ! $post ) {
		return '';
	}

	$reviewer = (string) get_post_meta( $post->ID, 'ucf_reviewer', true );

	$record = array(
		'author'    => get_the_author_meta( 'display_name', $post->post_author ),
		'reviewer'  => '' !== $reviewer ? $reviewer : __( 'Sample Name, Ph.D.', 'ucf-wordpress-block-theme' ),
		'sample'    => '' === $reviewer,
		'published' => get_the_date( 'Y-m-d', $post ),
		'modified'  => get_the_modified_date( 'Y-m-d', $post ),
		'owner'     => get_bloginfo( 'name' ),
	);

	if ( 'post' === $post->post_type ) {
		ucf_theme_structured_data( ucf_theme_article_node( $record, get_the_title( $post ) ) );
	}

	return sprintf(
		'<div %s>%s</div>',
		get_block_wrapper_attributes(),
		ucf_theme_provenance_markup( $record )
	);
}

/**
 * The provenance list.
 *
 * @param array<string, mixed> $record author, reviewer, sample, published, modified, owner.
 * @return string Markup.
 */
function ucf_theme_provenance_markup( array $record ) {
	$rows = array(
		__( 'Written by', 'ucf-wordpress-block-theme' )    => esc_html( $record['author'] ),
		__( 'Reviewed by', 'ucf-wordpress-block-theme' )   => esc_html( $record['reviewer'] ),
		__( 'Published', 'ucf-wordpress-block-theme' )     => sprintf( '<time datetime="%s">%s</time>', esc_attr( $record['published'] ), esc_html( ucf_theme_format_date( $record['published'] ) ) ),
		__( 'Last reviewed', 'ucf-wordpress-block-theme' ) => sprintf( '<time datetime="%s">%s</time>', esc_attr( $record['modified'] ), esc_html( ucf_theme_format_date( $record['modified'] ) ) ),
		__( 'Page owner', 'ucf-wordpress-block-theme' )    => esc_html( $record['owner'] ),
	);

	$html = '';

	// FIX: a row with nothing to say is left out. Imported posts — the degrees — have no author,
	// and an empty "Written by" reads as a page nobody stands behind.
	foreach ( $rows as $label => $value ) {
		if ( '' === trim( wp_strip_all_tags( $value ) ) ) {
			continue;
		}

		$html .= '<div><dt>' . esc_html( $label ) . '</dt><dd>' . $value . '</dd></div>';
	}

	$html = '<dl class="ucf-provenance">' . $html . '</dl>';

	return empty( $record['sample'] ) ? $html : $html . ucf_theme_sample_note();
}

/**
 * The `Article` node for a post.
 *
 * WHY no reviewer in the node while it is sample data. Structured data is a claim made to
 * search engines; a placeholder name does not belong in one.
 *
 * @param array<string, mixed> $record See `ucf_theme_provenance_markup()`.
 * @param string               $title  The post title.
 * @return array<string, mixed> Node.
 */
function ucf_theme_article_node( array $record, $title ) {
	$node = array(
		'@type'         => 'Article',
		'headline'      => $title,
		'author'        => array(
			'@type' => 'Person',
			'name'  => $record['author'],
		),
		'datePublished' => $record['published'],
		'dateModified'  => $record['modified'],
		'publisher'     => array(
			'@type' => 'CollegeOrUniversity',
			'name'  => $record['owner'],
		),
	);

	if ( empty( $record['sample'] ) ) {
		$node['reviewedBy'] = array(
			'@type' => 'Person',
			'name'  => $record['reviewer'],
		);
	}

	return $node;
}
