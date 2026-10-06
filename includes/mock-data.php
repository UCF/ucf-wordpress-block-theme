<?php
/**
 * Mock data — the stand-in for the systems the data blocks will eventually read.
 *
 * CONTEXT: the program, people, events and alert blocks render from data a theme does not
 * own: UCF-Degree-CPT-Plugin, UCF-People-CPT, UCF-Events-Plugin, the UCF Alert feed. Until
 * they are wired up, each block reads a JSON file in data/mock/ through this one function,
 * and nothing else in the theme knows the data is fake.
 *
 * WHY a filter per set. Replacing a mock with the real source is then a filter in the
 * plugin's integration — `ucf_theme_mock_data_programs` returning program posts in the same
 * shape — with no change to any block. The shape each block expects is documented beside the
 * block, in the file that owns it.
 *
 * WHY mock data is marked on the page. Every record carries `sample: true` or sits in a set
 * whose source says "Sample", and the blocks print the design system's sample-data note
 * beside it. A mock number must never look like a verified one.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * One mock data set, decoded.
 *
 * @param string $set The set name: the basename of a file in data/mock/.
 * @return array<string, mixed> The decoded data, or an empty array if the file is missing.
 */
function ucf_theme_mock_data( $set ) {
	static $cache = array();

	if ( ! isset( $cache[ $set ] ) ) {
		$path = get_theme_file_path( "data/mock/{$set}.json" );
		$data = is_readable( $path ) ? wp_json_file_decode( $path, array( 'associative' => true ) ) : null;

		$cache[ $set ] = is_array( $data ) ? $data : array();
	}

	/**
	 * Filters one mock data set — the seam where a real source replaces the mock.
	 *
	 * @param array<string, mixed> $data The data set.
	 */
	return apply_filters( "ucf_theme_mock_data_{$set}", $cache[ $set ] );
}

/**
 * The design system's sample-data note, printed beside anything rendered from mock data.
 *
 * @return string Markup.
 */
function ucf_theme_sample_note() {
	return '<p class="ucf-sample">'
		. esc_html__( 'Sample data for review. Replace with verified data before publishing.', 'ucf-wordpress-block-theme' )
		. '</p>';
}
