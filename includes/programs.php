<?php
/**
 * Academic programs: the Facts block and the Program finder.
 *
 * CONTEXT: both render from program data, which today is mock data (data/mock/programs.json,
 * read by `ucf_theme_mock_data()`) and will be UCF-Degree-CPT-Plugin. Both are server-rendered
 * blocks, so per docs/architecture.md they live here, beside the data they read, not in
 * src/blocks/. The editor previews them with core's server-side render
 * (src/js/editor/data-blocks.js).
 *
 * The shape `ucf_theme_mock_data( 'programs' )` returns, which a real source must match:
 *
 *     programs  list of { slug, name, url, college, level }
 *     facts     map of slug => { name, url, college, sample, source: { label, url, asOf },
 *                                facts: list of { label, value, data? } }
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register both blocks.
 *
 * WHY the program list is an `enum` on the attribute. The editor reads it back out of the
 * server's block definition to build its picker, so the list of programs has one home.
 *
 * @return void
 */
function ucf_theme_register_program_blocks() {
	$data = ucf_theme_mock_data( 'programs' );

	register_block_type(
		'ucf/facts',
		array(
			'api_version'     => 3,
			'title'           => __( 'Facts', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'The "at a glance" facts for a program, with their source and a ready-made citation.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'editor-table',
			'keywords'        => array( 'at a glance', 'program', 'degree' ),
			'attributes'      => array(
				// WHY '' first and the default: empty means "the degree this page is", which is how
				// templates/single-degree.html uses the block for every degree at once. A named
				// program is for placing one degree's facts on some other page.
				'program' => array(
					'type'    => 'string',
					'enum'    => array_merge( array( '' ), array_keys( $data['facts'] ?? array() ) ),
					'default' => '',
				),
			),
			'uses_context'    => array( 'postId', 'postType' ),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'ucf_theme_render_facts',
		)
	);

	register_block_type(
		'ucf/program-finder',
		array(
			'api_version'     => 3,
			'title'           => __( 'Program finder', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'Every program, with search and level filters. The full list is in the page; filtering is an enhancement.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'search',
			'keywords'        => array( 'degree search', 'filter', 'majors' ),
			'supports'        => array( 'html' => false ),
			'render_callback' => 'ucf_theme_render_program_finder',
		)
	);
}
add_action( 'init', 'ucf_theme_register_program_blocks' );

/**
 * Render the Facts block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @param string               $content    Inner content (none).
 * @param WP_Block|null        $block      The block instance, for the post it sits in.
 * @return string Markup.
 */
function ucf_theme_render_facts( $attributes, $content = '', $block = null ) {
	$data = ucf_theme_mock_data( 'programs' );
	$slug = $attributes['program'] ?? '';

	if ( '' !== $slug ) {
		$program = $data['facts'][ $slug ] ?? null;
	} else {
		$program = ucf_theme_current_degree_facts( $data['facts'] ?? array(), $block );
	}

	if ( ! $program ) {
		return '';
	}

	ucf_theme_structured_data( ucf_theme_program_node( $program ) );

	return sprintf(
		'<section %s>%s</section>',
		get_block_wrapper_attributes( array( 'class' => 'ucf-facts' ) ),
		ucf_theme_facts_markup( $program, wp_unique_id( 'ucf-cite-' ) )
	);
}

/**
 * The inside of a Facts block.
 *
 * @param array<string, mixed> $program One entry from `facts`.
 * @param string               $cite_id Unique id for the citation text.
 * @return string Markup.
 */
function ucf_theme_facts_markup( array $program, $cite_id ) {
	$items = '';

	foreach ( $program['facts'] as $fact ) {
		$items .= sprintf(
			'<div class="ucf-facts__item"><dt>%s</dt><dd%s>%s</dd></div>',
			esc_html( $fact['label'] ),
			empty( $fact['data'] ) ? '' : ' class="is-data"',
			esc_html( $fact['value'] )
		);
	}

	$source = $program['source'];
	$as_of  = ucf_theme_format_date( $source['asOf'] );

	// WHY a fixed id: "On this page" in templates/single-degree.html links to it.
	$html  = '<h2 class="wp-block-heading" id="at-a-glance">' . esc_html__( 'At a glance', 'ucf-wordpress-block-theme' ) . '</h2>';
	$html .= '<dl class="ucf-facts__list">' . $items . '</dl>';
	$html .= sprintf(
		'<p class="ucf-facts__source">%s <a href="%s">%s</a>, %s <time datetime="%s">%s</time></p>',
		esc_html__( 'Source:', 'ucf-wordpress-block-theme' ),
		esc_url( $source['url'] ),
		esc_html( $source['label'] ),
		esc_html__( 'as of', 'ucf-wordpress-block-theme' ),
		esc_attr( $source['asOf'] ),
		esc_html( $as_of )
	);

	if ( ! empty( $program['sample'] ) ) {
		$html .= ucf_theme_sample_note();
	}

	// SPEC: "Cite this" — the citation is plain text in the page; the copy button is added by
	// src/js/frontend/cite.js, which looks for `data-ucf-copy`.
	$citation = sprintf(
		/* translators: 1: program name, 2: URL, 3: as-of date. */
		__( '%1$s. University of Central Florida. %2$s. Facts as of %3$s.', 'ucf-wordpress-block-theme' ),
		$program['name'],
		$program['url'],
		$as_of
	);

	$html .= sprintf(
		'<details class="ucf-cite"><summary>%s</summary><div class="ucf-cite__box"><p class="ucf-cite__text" id="%s">%s</p><button type="button" class="wp-element-button ucf-cite__copy" data-ucf-copy="%2$s" hidden>%s</button></div></details>',
		esc_html__( 'Cite this', 'ucf-wordpress-block-theme' ),
		esc_attr( $cite_id ),
		esc_html( $citation ),
		esc_html__( 'Copy citation', 'ucf-wordpress-block-theme' )
	);

	return $html;
}

/**
 * The `EducationalOccupationalProgram` node for a program's facts.
 *
 * @param array<string, mixed> $program One entry from `facts`.
 * @return array<string, mixed> Node.
 */
function ucf_theme_program_node( array $program ) {
	$node = array(
		'@type'    => 'EducationalOccupationalProgram',
		'name'     => $program['name'],
		'url'      => $program['url'],
		'provider' => array(
			'@type' => 'CollegeOrUniversity',
			'name'  => 'University of Central Florida',
		),
	);

	foreach ( $program['facts'] as $fact ) {
		if ( 'Credit hours' === $fact['label'] ) {
			$node['numberOfCredits'] = (int) $fact['value'];
		}
	}

	return $node;
}

/**
 * Render the Program finder.
 *
 * @return string Markup.
 */
function ucf_theme_render_program_finder() {
	$data = ucf_theme_mock_data( 'programs' );

	return sprintf(
		'<div %s>%s</div>',
		get_block_wrapper_attributes(
			array(
				'class'           => 'ucf-finder',
				'data-ucf-filter' => '',
			)
		),
		ucf_theme_finder_markup( $data['programs'] ?? array(), wp_unique_id( 'ucf-finder-' ) )
	);
}

/**
 * The inside of a Program finder: controls, a status line and the full list.
 *
 * SPEC: the full list is in the HTML and every entry links to its own page; the controls are
 * `hidden` until src/js/frontend/finder.js runs, so without JavaScript the block is a plain,
 * crawlable list.
 *
 * @param array<int, array<string, string>> $programs Programs.
 * @param string                            $id       Unique id prefix.
 * @return string Markup.
 */
function ucf_theme_finder_markup( array $programs, $id ) {
	$levels = array();

	foreach ( $programs as $program ) {
		$levels[ $program['level'] ] = true;
	}

	$chips = '';

	foreach ( array_keys( $levels ) as $level ) {
		$chips .= sprintf(
			'<button type="button" class="ucf-chip" aria-pressed="false" data-value="%s">%s</button>',
			esc_attr( $level ),
			esc_html( $level )
		);
	}

	$rows = '';

	foreach ( $programs as $program ) {
		$rows .= sprintf(
			'<li class="ucf-result" data-level="%s" data-search="%s"><p class="ucf-result__name"><a href="%s">%s</a></p><p class="ucf-result__college">%s</p><p class="ucf-result__level">%s</p></li>',
			esc_attr( $program['level'] ),
			esc_attr( strtolower( $program['name'] . ' ' . $program['college'] ) ),
			esc_url( $program['url'] ),
			esc_html( $program['name'] ),
			esc_html( $program['college'] ),
			esc_html( $program['level'] )
		);
	}

	$total = count( $programs );

	return sprintf(
		'<div class="ucf-finder__controls" hidden><div class="ucf-finder__search"><label for="%1$s-q">%2$s</label><input type="search" id="%1$s-q" data-ucf-filter-search autocomplete="off"></div><fieldset class="ucf-finder__group"><legend>%3$s</legend>%4$s</fieldset></div><p class="ucf-finder__status" role="status" aria-live="polite" data-ucf-filter-status>%5$s</p><ul class="ucf-results">%6$s</ul>',
		esc_attr( $id ),
		esc_html__( 'Search programs', 'ucf-wordpress-block-theme' ),
		esc_html__( 'Level', 'ucf-wordpress-block-theme' ),
		$chips,
		esc_html(
			sprintf(
				/* translators: %d: number of programs. */
				_n( '%d program', '%d programs', $total, 'ucf-wordpress-block-theme' ),
				$total
			)
		),
		$rows
	);
}

/**
 * Facts for the degree post a Facts block sits in.
 *
 * @param array<string, array<string, mixed>> $mock  The mock `facts` map, keyed by path slug.
 * @param WP_Block|null                       $block The block instance.
 * @return array<string, mixed>|null Facts, or null outside a degree.
 */
function ucf_theme_current_degree_facts( array $mock, $block ) {
	$post = get_post( $block->context['postId'] ?? get_the_ID() );

	if ( ! $post || 'degree' !== $post->post_type ) {
		return null;
	}

	$parent = $post->post_parent ? get_post( $post->post_parent ) : null;
	$key    = str_replace( '/', '-', get_page_uri( $post ) );

	return ucf_theme_degree_record(
		// FIX: the raw title, not get_the_title(). That one runs wptexturize, which turns the
		// " - " between program and track into an en dash, and the split below then never matches.
		array(
			'title'    => $post->post_title,
			'url'      => get_permalink( $post ),
			'parent'   => $parent ? $parent->post_title : '',
			'modified' => get_the_modified_date( 'Y-m-d', $post ),
		),
		$mock[ $key ] ?? null
	);
}

/**
 * A Facts record for one degree, from what its post knows.
 *
 * CONTEXT: the degree posts on a site hold a title, a URL, a parent program for a track, and
 * a date — the catalog fields arrive with UCF-Degree-CPT-Plugin's importer, which has not run
 * here. So this builds the facts that are real (level, program, track) and marks the record as
 * sample, and a mock record for the same degree, when one exists, stands in until then.
 *
 * @param array{title: string, url: string, parent: string, modified: string} $degree The post's fields.
 * @param array<string, mixed>|null                                           $mock   Mock facts for this degree, if any.
 * @return array<string, mixed> A record in the `facts` shape.
 */
function ucf_theme_degree_record( array $degree, $mock ) {
	if ( $mock ) {
		return $mock;
	}

	// "Chemistry (BS) - Biochemistry": the program, its abbreviation, and the track.
	$parts = array_map( 'trim', explode( ' - ', $degree['title'], 2 ) );
	$facts = array(
		array(
			'label' => __( 'Level', 'ucf-wordpress-block-theme' ),
			'value' => ucf_theme_degree_level( $degree['title'] ),
		),
	);

	if ( '' !== $degree['parent'] || isset( $parts[1] ) ) {
		$facts[] = array(
			'label' => __( 'Program', 'ucf-wordpress-block-theme' ),
			'value' => '' !== $degree['parent'] ? $degree['parent'] : $parts[0],
		);
		$facts[] = array(
			'label' => __( 'Track', 'ucf-wordpress-block-theme' ),
			'value' => $parts[1] ?? $degree['title'],
		);
	}

	return array(
		'name'   => $degree['title'],
		'url'    => $degree['url'],
		'sample' => true,
		'source' => array(
			'label' => __( 'UCF degree records', 'ucf-wordpress-block-theme' ),
			'url'   => $degree['url'],
			'asOf'  => $degree['modified'],
		),
		'facts'  => $facts,
	);
}

/**
 * A degree's level, read from the abbreviation in its title — "(BS)", "MBA", "(PhD)".
 *
 * @param string $title The degree's title.
 * @return string Level, or "Degree" when the title names none.
 */
function ucf_theme_degree_level( $title ) {
	if ( preg_match( '/\bcertificate\b/i', $title ) ) {
		return __( 'Certificate', 'ucf-wordpress-block-theme' );
	}

	if ( preg_match( '/\bminor\b/i', $title ) ) {
		return __( 'Minor', 'ucf-wordpress-block-theme' );
	}

	if ( preg_match( '/\b(PhD|EdD|DNP|DPT|AuD|MD|MFA-PhD)\b/', $title ) ) {
		return __( 'Doctoral', 'ucf-wordpress-block-theme' );
	}

	if ( preg_match( '/\b(M[A-Z]{1,4}|MBA|MSW|MPA|MSN|MAT)\b/', $title ) ) {
		return __( 'Master’s', 'ucf-wordpress-block-theme' );
	}

	if ( preg_match( '/\b(B[A-Z]{1,4}|BDes)\b/', $title ) ) {
		return __( 'Bachelor’s', 'ucf-wordpress-block-theme' );
	}

	return __( 'Degree', 'ucf-wordpress-block-theme' );
}
