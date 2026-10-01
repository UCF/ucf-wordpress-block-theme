<?php
/**
 * Degree pages, rendered from the Search Service.
 *
 * CONTEXT: the `degree` post type is registered by UCF-Degree-CPT-Plugin. The theme needs
 * one thing from a degree post — `degree_api_id`, the Search Service program id the plugin's
 * importer stores — and reads everything else live through includes/search-service.php. A
 * degree post is a slug, a place in the hierarchy, and a pointer.
 *
 * Two ways that data reaches a page, split by shape:
 *
 * -   **Single values** (name, hours, tuition, college, catalog link) are a Block Bindings
 *     source, `ucf-theme/degree`, bound onto core Heading, Paragraph and Button. The editor
 *     keeps every core control over how they look.
 * -   **Collections and HTML** (description, tracks, deadlines, careers, outcomes, job
 *     outlook) are server-rendered `ucf-theme/degree-*` blocks. Each renders nothing at all
 *     when the service has nothing for that program, heading included.
 *
 * The blocks are PHP-only: `supports.autoRegister` has core build their editor view from a
 * ServerSideRender and their inspector from their attributes, so there is no JS to keep in
 * step with this file.
 *
 * SYNC: templates/degree-program.html binds the keys `ucf_theme_degree_field()` knows and
 * uses the blocks registered in `ucf_theme_register_degree_blocks()`.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Expose degrees to the block editor.
 *
 * UPSTREAM: the plugin only sets `show_in_rest` when its own REST option is on, and without
 * it a degree opens in the classic editor, which cannot offer a block template. This does
 * not add the plugin's custom REST fields or routes; it only makes the post type editable.
 *
 * @param array $args Post type arguments.
 * @return array
 */
function ucf_theme_degree_post_type_args( $args ) {
	$args['show_in_rest'] = true;
	return $args;
}
add_filter( 'ucf_degree_post_type_args', 'ucf_theme_degree_post_type_args' );

/**
 * The Search Service program id a degree post points at.
 *
 * @param int $post_id Degree post id.
 * @return int Program id, or 0 when the post carries none.
 */
function ucf_theme_degree_program_id( $post_id ) {
	return (int) get_post_meta( $post_id, 'degree_api_id', true );
}

/**
 * One Search Service resource for a degree post.
 *
 * @param int    $post_id      Degree post id.
 * @param string $sub_resource Empty for the program itself, or a sub-resource path such as
 *                             `outcomes`, `projections`, `careers`, `deadlines`.
 * @return array|null
 */
function ucf_theme_degree_data( $post_id, $sub_resource = '' ) {
	$program_id = ucf_theme_degree_program_id( $post_id );

	if ( ! $program_id ) {
		return null;
	}

	$path = "programs/{$program_id}/";
	if ( $sub_resource ) {
		$path .= trailingslashit( $sub_resource );
	}

	return ucf_theme_search_service_get( $path );
}

/**
 * A program's tuition as a sentence fragment, e.g. "$212.28 per credit hour".
 *
 * SYNC: matches `UCF_Degree_Import::get_tuition()` in
 * UCF-Degree-CPT-Plugin/importers/degree-importer.php, so a page reading live shows the
 * same string a page reading imported meta did.
 *
 * @param string|float|null $amount Amount as the service returns it, e.g. "212.28".
 * @param string|null       $type   Service tuition type: SCH, CRS, TRM or ANN.
 * @return string Empty when there is no amount or the type is unknown.
 */
function ucf_theme_degree_format_tuition( $amount, $type ) {
	$units = array(
		'SCH' => 'per credit hour',
		'CRS' => 'per course',
		'TRM' => 'per term',
		'ANN' => 'per year',
	);

	if ( ! is_numeric( $amount ) || (float) $amount <= 0 || ! is_string( $type ) || ! isset( $units[ $type ] ) ) {
		return '';
	}

	return '$' . number_format( (float) $amount, 2 ) . ' ' . $units[ $type ];
}

/**
 * One named value from a Search Service program, as the bindings source exposes it.
 *
 * SYNC: every key the template binds is a case here. An unknown key returns null, which
 * core treats as "leave the block's own content", so a typo shows the placeholder text
 * rather than an empty element.
 *
 * @param array  $program Decoded program.
 * @param string $key     Field key.
 * @return string|null
 */
function ucf_theme_degree_field( $program, $key ) {
	$names = static function ( $items ) {
		return implode( ', ', array_filter( wp_list_pluck( (array) $items, 'name' ) ) );
	};

	switch ( $key ) {
		case 'name':
		case 'excerpt':
		case 'level':
		case 'career':
		case 'degree':
		case 'catalog_url':
		case 'area_of_interest':
		case 'subarea_of_interest':
			return isset( $program[ $key ] ) ? (string) $program[ $key ] : '';

		case 'profile_url':
			return isset( $program['primary_profile_url'] ) ? (string) $program['primary_profile_url'] : '';

		case 'credit_hours':
			return empty( $program['credit_hours'] ) ? '' : (string) $program['credit_hours'];

		case 'college':
			return $names( isset( $program['colleges'] ) ? $program['colleges'] : array() );

		case 'college_url':
			return isset( $program['colleges'][0]['profile_url'] ) ? (string) $program['colleges'][0]['profile_url'] : '';

		case 'department':
			return $names( isset( $program['departments'] ) ? $program['departments'] : array() );

		case 'tuition_resident':
		case 'tuition_nonresident':
			$field = 'tuition_resident' === $key ? 'resident_tuition' : 'nonresident_tuition';
			return ucf_theme_degree_format_tuition(
				isset( $program[ $field ] ) ? $program[ $field ] : null,
				isset( $program['tuition_type'] ) ? $program['tuition_type'] : null
			);
	}

	return null;
}

/**
 * Register the `ucf-theme/degree` Block Bindings source.
 *
 * @return void
 */
function ucf_theme_register_degree_bindings() {
	register_block_bindings_source(
		'ucf-theme/degree',
		array(
			'label'              => __( 'Degree (Search Service)', 'ucf-wordpress-block-theme' ),
			'get_value_callback' => 'ucf_theme_degree_binding_value',
			'uses_context'       => array( 'postId' ),
		)
	);
}
add_action( 'init', 'ucf_theme_register_degree_bindings' );

/**
 * Value callback for the `ucf-theme/degree` source.
 *
 * WHY: escaped here, by attribute. Core writes a bound `content` into the element as HTML,
 * and a program name containing `&` or `<` is data, not markup.
 *
 * @param array    $source_args    Binding args; `key` names the field.
 * @param WP_Block $block_instance The bound block.
 * @param string   $attribute_name The bound attribute.
 * @return string|null
 */
function ucf_theme_degree_binding_value( $source_args, $block_instance, $attribute_name ) {
	$post_id = isset( $block_instance->context['postId'] ) ? $block_instance->context['postId'] : get_the_ID();
	$program = $post_id ? ucf_theme_degree_data( $post_id ) : null;
	$key     = isset( $source_args['key'] ) ? $source_args['key'] : '';

	if ( ! $program ) {
		return null;
	}

	$value = ucf_theme_degree_field( $program, $key );

	if ( null === $value ) {
		return null;
	}

	return 'url' === $attribute_name ? esc_url( $value ) : esc_html( $value );
}

/**
 * The server-rendered degree blocks, by name.
 *
 * Each is `[ title, description, default heading, markup builder, resource, extra attributes ]`.
 * The builder is handed the decoded resource and the block's attributes and returns inner
 * HTML, or an empty string for "nothing to show".
 *
 * @return array<string, array>
 */
function ucf_theme_degree_blocks() {
	return array(
		'degree-description' => array(
			__( 'Degree Description', 'ucf-wordpress-block-theme' ),
			__( 'The program description from the Search Service.', 'ucf-wordpress-block-theme' ),
			'',
			'ucf_theme_degree_description_markup',
			'',
			array(
				'source' => array(
					'type'    => 'string',
					'enum'    => array( 'custom', 'catalog', 'full' ),
					'default' => 'custom',
				),
			),
		),
		'degree-tracks'      => array(
			__( 'Degree Tracks', 'ucf-wordpress-block-theme' ),
			__( 'The program\'s tracks and options, linked where this site has them.', 'ucf-wordpress-block-theme' ),
			__( 'Tracks and Options', 'ucf-wordpress-block-theme' ),
			'ucf_theme_degree_tracks_markup',
			'',
			array(),
		),
		'degree-deadlines'   => array(
			__( 'Degree Application Deadlines', 'ucf-wordpress-block-theme' ),
			__( 'Application deadlines by applicant type and term.', 'ucf-wordpress-block-theme' ),
			__( 'Application Deadlines', 'ucf-wordpress-block-theme' ),
			'ucf_theme_degree_deadlines_markup',
			'deadlines',
			array(),
		),
		'degree-careers'     => array(
			__( 'Degree Careers', 'ucf-wordpress-block-theme' ),
			__( 'Careers graduates of the program go into.', 'ucf-wordpress-block-theme' ),
			__( 'Career Opportunities', 'ucf-wordpress-block-theme' ),
			'ucf_theme_degree_careers_markup',
			'careers',
			array(
				'limit' => array(
					'type'    => 'integer',
					'default' => 10,
				),
			),
		),
		'degree-outcomes'    => array(
			__( 'Degree Outcomes', 'ucf-wordpress-block-theme' ),
			__( 'Earnings and employment of recent graduates.', 'ucf-wordpress-block-theme' ),
			__( 'Graduate Outcomes', 'ucf-wordpress-block-theme' ),
			'ucf_theme_degree_outcomes_markup',
			'outcomes',
			array(),
		),
		'degree-projections' => array(
			__( 'Degree Job Outlook', 'ucf-wordpress-block-theme' ),
			__( 'Employment projections for the program\'s field.', 'ucf-wordpress-block-theme' ),
			__( 'Job Outlook', 'ucf-wordpress-block-theme' ),
			'ucf_theme_degree_projections_markup',
			'projections',
			array(),
		),
	);
}

/**
 * Register the server-rendered degree blocks.
 *
 * UPSTREAM: `autoRegister` (WordPress 7.0) is what makes these usable without an editor
 * script — core registers each one client-side with a ServerSideRender edit view, always
 * passing `postId`, and generates inspector controls from the attributes.
 *
 * @return void
 */
function ucf_theme_register_degree_blocks() {
	foreach ( ucf_theme_degree_blocks() as $slug => $definition ) {
		list( $title, $description, $heading, , , $attributes ) = $definition;

		register_block_type(
			"ucf-theme/{$slug}",
			array(
				'api_version'     => 3,
				'title'           => $title,
				'description'     => $description,
				'category'        => 'theme',
				'icon'            => 'welcome-learn-more',
				'uses_context'    => array( 'postId', 'postType' ),
				'supports'        => array(
					'autoRegister' => true,
					'html'         => false,
					'align'        => array( 'wide', 'full' ),
				),
				'attributes'      => array_merge(
					array(
						'heading' => array(
							'type'    => 'string',
							'default' => $heading,
						),
					),
					$attributes
				),
				'render_callback' => 'ucf_theme_render_degree_block',
			)
		);
	}
}
add_action( 'init', 'ucf_theme_register_degree_blocks' );

/**
 * Render callback shared by every `ucf-theme/degree-*` block.
 *
 * WHY: nothing at all when the service has nothing — no wrapper, no heading. A heading over
 * an empty section is worse than no section, and which programs have projections or
 * deadlines is the service's call, not the template's.
 *
 * In the editor with no degree to read (the Site Editor, editing the template itself) the
 * block names itself instead, or it would be an invisible, unselectable nothing.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Inner content; unused.
 * @param WP_Block $block      Block instance.
 * @return string
 */
function ucf_theme_render_degree_block( $attributes, $content, $block ) {
	$slug   = substr( $block->name, strlen( 'ucf-theme/' ) );
	$blocks = ucf_theme_degree_blocks();

	if ( ! isset( $blocks[ $slug ] ) ) {
		return '';
	}

	list( $title, , , $builder, $resource ) = $blocks[ $slug ];

	$post_id = isset( $block->context['postId'] ) ? $block->context['postId'] : get_the_ID();
	$data    = $post_id ? ucf_theme_degree_data( $post_id, $resource ) : null;
	$inner   = $data ? call_user_func( $builder, $data, $attributes ) : '';

	if ( '' === $inner ) {
		if ( ! wp_is_serving_rest_request() ) {
			return '';
		}

		$inner = sprintf(
			'<p class="ucf-degree-placeholder">%s</p>',
			/* translators: %s: block title. */
			esc_html( sprintf( __( '%s — filled from the Search Service on a degree page.', 'ucf-wordpress-block-theme' ), $title ) )
		);
	} elseif ( ! empty( $attributes['heading'] ) ) {
		$inner = '<h2 class="wp-block-heading">' . esc_html( $attributes['heading'] ) . '</h2>' . $inner;
	}

	return sprintf(
		'<div %s>%s</div>',
		get_block_wrapper_attributes( array( 'class' => 'ucf-degree-block' ) ),
		$inner
	);
}

/**
 * Description block: one of the program's descriptions, as HTML.
 *
 * `custom` falls back to `catalog`: the custom copy is the curated one, but most programs
 * have only the catalog's.
 *
 * @param array $program    Decoded program.
 * @param array $attributes Block attributes.
 * @return string
 */
function ucf_theme_degree_description_markup( $program, $attributes ) {
	$types = array(
		'custom'  => 'Custom Description',
		'catalog' => 'Catalog Description',
		'full'    => 'Full Catalog Description',
	);

	$wanted = isset( $attributes['source'], $types[ $attributes['source'] ] ) ? $attributes['source'] : 'custom';
	$order  = 'custom' === $wanted ? array( 'custom', 'catalog' ) : array( $wanted );

	$by_type = array();
	foreach ( (array) ( isset( $program['descriptions'] ) ? $program['descriptions'] : array() ) as $description ) {
		if ( isset( $description['description_type']['name'] ) && ! empty( $description['description'] ) ) {
			$by_type[ $description['description_type']['name'] ] = $description['description'];
		}
	}

	foreach ( $order as $type ) {
		if ( isset( $by_type[ $types[ $type ] ] ) ) {
			return wp_kses_post( $by_type[ $types[ $type ] ] );
		}
	}

	return '';
}

/**
 * Tracks block: the program's subplans, each linked to this site's post for it.
 *
 * A subplan with no post here is still listed, unlinked: it is a real option of the program
 * whether or not this site has a page for it.
 *
 * @param array $program Decoded program.
 * @return string
 */
function ucf_theme_degree_tracks_markup( $program ) {
	$subplans = isset( $program['subplans'] ) ? (array) $program['subplans'] : array();

	if ( ! $subplans ) {
		return '';
	}

	$links  = ucf_theme_degree_permalinks_by_program( wp_list_pluck( $subplans, 'id' ) );
	$prefix = isset( $program['name'] ) ? $program['name'] . ' - ' : '';
	$items  = '';

	foreach ( $subplans as $subplan ) {
		$name = isset( $subplan['name'] ) ? (string) $subplan['name'] : '';

		// Tracks are named "Parent (BS) - Track"; under the parent's own heading the prefix is noise.
		if ( $prefix && 0 === strpos( $name, $prefix ) ) {
			$name = substr( $name, strlen( $prefix ) );
		}

		$items .= isset( $links[ $subplan['id'] ] )
			? sprintf( '<li><a href="%s">%s</a></li>', esc_url( $links[ $subplan['id'] ] ), esc_html( $name ) )
			: sprintf( '<li>%s</li>', esc_html( $name ) );
	}

	return '<ul class="ucf-degree-tracks">' . $items . '</ul>';
}

/**
 * Permalinks of this site's degree posts, keyed by the program id they point at.
 *
 * @param int[] $program_ids Search Service program ids.
 * @return array<int, string>
 */
function ucf_theme_degree_permalinks_by_program( $program_ids ) {
	$program_ids = array_filter( array_map( 'intval', (array) $program_ids ) );

	if ( ! $program_ids ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'      => 'degree',
			'post_status'    => 'publish',
			'posts_per_page' => count( $program_ids ),
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- bounded by the program's own subplan count.
				array(
					'key'     => 'degree_api_id',
					'value'   => $program_ids,
					'compare' => 'IN',
				),
			),
		)
	);

	$links = array();
	foreach ( $posts as $post_id ) {
		$links[ ucf_theme_degree_program_id( $post_id ) ] = get_permalink( $post_id );
	}

	return $links;
}

/**
 * Deadlines block: grouped by applicant type, in the order the service returns them.
 *
 * @param array $data Decoded `deadlines` resource.
 * @return string
 */
function ucf_theme_degree_deadlines_markup( $data ) {
	$groups = array();

	foreach ( (array) ( isset( $data['application_deadlines'] ) ? $data['application_deadlines'] : array() ) as $deadline ) {
		if ( empty( $deadline['display'] ) ) {
			continue;
		}

		$type              = ! empty( $deadline['deadline_type'] ) ? $deadline['deadline_type'] : __( 'All Applicants', 'ucf-wordpress-block-theme' );
		$groups[ $type ][] = sprintf(
			'<dt>%s</dt><dd>%s</dd>',
			esc_html( isset( $deadline['admission_term'] ) ? $deadline['admission_term'] : '' ),
			esc_html( $deadline['display'] )
		);
	}

	$html = '';
	foreach ( $groups as $type => $rows ) {
		$html .= sprintf(
			'<div class="ucf-degree-deadlines__group"><h3 class="wp-block-heading">%s</h3><dl>%s</dl></div>',
			esc_html( $type ),
			implode( '', $rows )
		);
	}

	return $html ? '<div class="ucf-degree-deadlines">' . $html . '</div>' : '';
}

/**
 * Careers block: the service's list, in its order, cut to the block's limit.
 *
 * WHY: not shuffled, though Main-Site-Theme shuffles career paths on every view. A list that
 * changes on reload cannot be cached, and the service already ranks them.
 *
 * @param array $careers    Decoded `careers` resource: a list of job titles.
 * @param array $attributes Block attributes; `limit` caps the list.
 * @return string
 */
function ucf_theme_degree_careers_markup( $careers, $attributes ) {
	$limit   = isset( $attributes['limit'] ) ? max( 1, (int) $attributes['limit'] ) : 10;
	$careers = array_slice( array_filter( array_map( 'strval', (array) $careers ) ), 0, $limit );

	if ( ! $careers ) {
		return '';
	}

	return '<ul class="ucf-degree-careers"><li>' . implode( '</li><li>', array_map( 'esc_html', $careers ) ) . '</li></ul>';
}

/**
 * Outcomes block: the latest academic year's figures.
 *
 * @param array $data Decoded `outcomes` resource.
 * @return string
 */
function ucf_theme_degree_outcomes_markup( $data ) {
	$latest = isset( $data['latest'] ) ? (array) $data['latest'] : array();
	$stats  = array();

	if ( ! empty( $latest['avg_annual_earnings'] ) ) {
		$stats[] = array( '$' . number_format( (float) $latest['avg_annual_earnings'] ), __( 'average annual earnings', 'ucf-wordpress-block-theme' ) );
	}
	if ( ! empty( $latest['employed_full_time'] ) ) {
		$stats[] = array( round( (float) $latest['employed_full_time'] ) . '%', __( 'employed full time', 'ucf-wordpress-block-theme' ) );
	}
	if ( ! empty( $latest['continuing_education'] ) ) {
		$stats[] = array( round( (float) $latest['continuing_education'] ) . '%', __( 'continuing their education', 'ucf-wordpress-block-theme' ) );
	}

	if ( ! $stats ) {
		return '';
	}

	$html = ucf_theme_degree_stats_markup( $stats );

	if ( ! empty( $latest['academic_year_display'] ) ) {
		$html .= sprintf(
			'<p class="ucf-degree-stats__source">%s</p>',
			/* translators: %s: academic year, e.g. 2019-20. */
			esc_html( sprintf( __( 'Graduates of the %s academic year.', 'ucf-wordpress-block-theme' ), $latest['academic_year_display'] ) )
		);
	}

	return $html;
}

/**
 * Job outlook block: projected openings and growth for the program's occupations.
 *
 * @param array $data Decoded `projections` resource.
 * @return string
 */
function ucf_theme_degree_projections_markup( $data ) {
	$stats = array();

	if ( ! empty( $data['openings'] ) ) {
		$stats[] = array( number_format( (int) $data['openings'] ), __( 'projected job openings', 'ucf-wordpress-block-theme' ) );
	}
	if ( isset( $data['change_percentage'] ) && is_numeric( $data['change_percentage'] ) && (float) $data['change_percentage'] > 0 ) {
		$stats[] = array( round( (float) $data['change_percentage'], 1 ) . '%', __( 'projected growth', 'ucf-wordpress-block-theme' ) );
	}

	if ( ! $stats ) {
		return '';
	}

	$html = ucf_theme_degree_stats_markup( $stats );

	if ( ! empty( $data['begin_year'] ) && ! empty( $data['end_year'] ) ) {
		$html .= sprintf(
			'<p class="ucf-degree-stats__source">%s</p>',
			/* translators: 1: first year, 2: last year of the projection. */
			esc_html( sprintf( __( 'Projected %1$d–%2$d.', 'ucf-wordpress-block-theme' ), $data['begin_year'], $data['end_year'] ) )
		);
	}

	return $html;
}

/**
 * A row of figure-and-label statistics.
 *
 * @param array $stats List of `[ figure, label ]`.
 * @return string
 */
function ucf_theme_degree_stats_markup( $stats ) {
	$items = '';
	foreach ( $stats as $stat ) {
		$items .= sprintf(
			'<div class="ucf-degree-stats__item"><dt>%2$s</dt><dd>%1$s</dd></div>',
			esc_html( $stat[0] ),
			esc_html( $stat[1] )
		);
	}

	return '<dl class="ucf-degree-stats">' . $items . '</dl>';
}
