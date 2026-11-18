<?php
/**
 * Structured data — one JSON-LD graph per page, built from what the page renders.
 *
 * CONTEXT: the design system's findability rule is that the visible answer and the
 * machine-readable answer are the same words. So nothing here is typed by an editor: this file
 * adds the site's `Organization` and the page's `BreadcrumbList`, and a server-rendered block
 * can add a node for what it just rendered through `ucf_theme_structured_data()`. The graph
 * prints once, in the footer.
 *
 * UPSTREAM: block themes render the whole template before `wp_head` runs, so by `wp_footer`
 * every block has had its chance to add a node.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Add a node to the page's graph, or read the graph so far.
 *
 * @param array<string, mixed>|null $node A schema.org node, or null to only read.
 * @param bool                      $reset Empty the graph — for tests.
 * @return array<int, array<string, mixed>> Every node added so far.
 */
function ucf_theme_structured_data( $node = null, $reset = false ) {
	static $graph = array();

	if ( $reset ) {
		$graph = array();
	}

	if ( is_array( $node ) && ! empty( $node['@type'] ) ) {
		$graph[] = $node;
	}

	return $graph;
}

/**
 * The `<script>` element for a graph.
 *
 * @param array<int, array<string, mixed>> $graph Nodes.
 * @return string Markup, or empty when there is nothing to say.
 */
function ucf_theme_structured_data_script( array $graph ) {
	if ( ! $graph ) {
		return '';
	}

	$json = wp_json_encode(
		array(
			'@context' => 'https://schema.org',
			'@graph'   => array_values( $graph ),
		),
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
	);

	// WHY: JSON_HEX_TAG escapes `<` and `>`, so a value holding `</script>` cannot close the
	// element early.
	return '<script type="application/ld+json">' . $json . '</script>' . "\n";
}

/**
 * The site's own node: the unit, as part of UCF.
 *
 * @param string $name Site name.
 * @param string $url  Site URL.
 * @return array<string, mixed> Node.
 */
function ucf_theme_organization_node( $name, $url ) {
	return array(
		'@type'              => 'CollegeOrUniversity',
		'name'               => $name,
		'url'                => $url,
		'parentOrganization' => array(
			'@type' => 'CollegeOrUniversity',
			'name'  => 'University of Central Florida',
			'url'   => 'https://www.ucf.edu/',
		),
	);
}

/**
 * A breadcrumb trail, from the site's home down to the current page.
 *
 * @param array<int, array{name: string, url: string}> $trail Items, outermost first.
 * @return array<string, mixed>|null Node, or null for a trail too short to be one.
 */
function ucf_theme_breadcrumb_node( array $trail ) {
	if ( count( $trail ) < 2 ) {
		return null;
	}

	$items = array();

	foreach ( array_values( $trail ) as $i => $crumb ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $crumb['name'],
			'item'     => $crumb['url'],
		);
	}

	return array(
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);
}

/**
 * Print the graph, with the site and breadcrumb nodes every page carries.
 *
 * @return void
 */
function ucf_theme_print_structured_data() {
	$graph = ucf_theme_structured_data();

	$graph[] = ucf_theme_organization_node( get_bloginfo( 'name' ), home_url( '/' ) );

	if ( is_page() && ! is_front_page() ) {
		$trail = array(
			array(
				'name' => get_bloginfo( 'name' ),
				'url'  => home_url( '/' ),
			),
		);

		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = array(
				'name' => get_the_title( $ancestor ),
				'url'  => get_permalink( $ancestor ),
			);
		}

		$trail[] = array(
			'name' => get_the_title( get_queried_object_id() ),
			'url'  => get_permalink( get_queried_object_id() ),
		);

		$crumbs = ucf_theme_breadcrumb_node( $trail );

		if ( $crumbs ) {
			$graph[] = $crumbs;
		}
	}

	echo ucf_theme_structured_data_script( $graph ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with JSON_HEX_TAG above.
}
add_action( 'wp_footer', 'ucf_theme_print_structured_data' );
