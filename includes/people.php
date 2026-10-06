<?php
/**
 * People: the Profile block.
 *
 * CONTEXT: a person rendered from the people directory — today mock data
 * (data/mock/people.json), eventually UCF-People-CPT. The design system's rule is that one
 * person is described in one place and every other page links to it; a block that reads the
 * directory, rather than a card an editor types, is how that rule holds.
 *
 * The shape `ucf_theme_mock_data( 'people' )` returns:
 *
 *     people  map of slug => { name, role, credentials, expertise: list of string,
 *                              links: list of { label, url }, contact: { label, url } }
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the Profile block.
 *
 * @return void
 */
function ucf_theme_register_people_blocks() {
	$people = ucf_theme_mock_data( 'people' )['people'] ?? array();

	register_block_type(
		'ucf/profile',
		array(
			'api_version'     => 3,
			'title'           => __( 'Profile', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'A person from the directory: portrait, role, credentials, expertise and a way to reach them.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'id',
			'keywords'        => array( 'person', 'faculty', 'expert', 'staff' ),
			'attributes'      => array(
				'person'  => array(
					'type'    => 'string',
					'enum'    => array_keys( $people ),
					'default' => (string) array_key_first( $people ? $people : array( '' => '' ) ),
				),
				'feature' => array(
					'type'    => 'boolean',
					'default' => false,
				),
			),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'ucf_theme_render_profile',
		)
	);
}
add_action( 'init', 'ucf_theme_register_people_blocks' );

/**
 * Render the Profile block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string Markup.
 */
function ucf_theme_render_profile( $attributes ) {
	$person = ucf_theme_mock_data( 'people' )['people'][ $attributes['person'] ?? '' ] ?? null;

	if ( ! $person ) {
		return '';
	}

	ucf_theme_structured_data( ucf_theme_person_node( $person ) );

	$classes = 'ucf-profile' . ( empty( $attributes['feature'] ) ? '' : ' ucf-profile--feature' );

	return sprintf(
		'<div %s><article class="%s">%s</article>%s</div>',
		get_block_wrapper_attributes(),
		esc_attr( $classes ),
		ucf_theme_profile_markup( $person ),
		ucf_theme_sample_note()
	);
}

/**
 * The inside of a Profile.
 *
 * @param array<string, mixed> $person One person.
 * @return string Markup.
 */
function ucf_theme_profile_markup( array $person ) {
	$tags = '';

	foreach ( $person['expertise'] as $tag ) {
		$tags .= '<li>' . esc_html( $tag ) . '</li>';
	}

	$links = '';

	foreach ( array_merge( $person['links'], array( $person['contact'] ) ) as $link ) {
		$links .= sprintf( '<li><a href="%s">%s</a></li>', esc_url( $link['url'] ), esc_html( $link['label'] ) );
	}

	// WHY a labeled placeholder, not an image: the directory is mock data, and a stand-in face
	// would read as a real person. The label says what photo belongs there.
	return sprintf(
		'<div class="ucf-profile__photo" aria-hidden="true">%s</div><div class="ucf-profile__body"><h3 class="wp-block-heading ucf-profile__name is-style-sans has-heading-4-font-size">%s</h3><p class="ucf-profile__role">%s</p><p class="ucf-profile__credentials">%s</p><ul class="wp-block-list is-style-tags">%s</ul><ul class="ucf-profile__links">%s</ul></div>',
		esc_html__( 'Portrait, 1:1', 'ucf-wordpress-block-theme' ),
		esc_html( $person['name'] ),
		esc_html( $person['role'] ),
		esc_html( $person['credentials'] ),
		$tags,
		$links
	);
}

/**
 * The `Person` node.
 *
 * @param array<string, mixed> $person One person.
 * @return array<string, mixed> Node.
 */
function ucf_theme_person_node( array $person ) {
	return array(
		'@type'      => 'Person',
		'name'       => $person['name'],
		'jobTitle'   => $person['role'],
		'knowsAbout' => $person['expertise'],
		'worksFor'   => array(
			'@type' => 'CollegeOrUniversity',
			'name'  => 'University of Central Florida',
		),
	);
}
