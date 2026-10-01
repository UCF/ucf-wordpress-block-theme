<?php
/**
 * Degree pages rendered from the Search Service.
 *
 * Covers the pure half of includes/degree.php: how a program becomes a bound value and how
 * each resource becomes markup. The render callback and the bindings callback are thin glue
 * over real WordPress (`WP_Block`, block context, `get_block_wrapper_attributes()`) and
 * belong to an integration tier, not a mock of one.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_degree_post_type_args
 * @covers ::ucf_theme_degree_format_tuition
 * @covers ::ucf_theme_degree_field
 * @covers ::ucf_theme_degree_description_markup
 * @covers ::ucf_theme_degree_tracks_markup
 * @covers ::ucf_theme_degree_deadlines_markup
 * @covers ::ucf_theme_degree_careers_markup
 * @covers ::ucf_theme_degree_outcomes_markup
 * @covers ::ucf_theme_degree_projections_markup
 */
final class DegreeTest extends TestCase {

	/**
	 * A program shaped like the service's, trimmed to the fields under test.
	 *
	 * @var array
	 */
	private $program = array(
		'id'                  => 1004,
		'name'                => 'Computer Science (BS)',
		'credit_hours'        => 120,
		'level'               => 'Bachelors',
		'resident_tuition'    => '212.28',
		'nonresident_tuition' => '748.89',
		'tuition_type'        => 'SCH',
		'primary_profile_url' => 'https://www.ucf.edu/degree/computer-science-bs/',
		'colleges'            => array(
			array(
				'name'        => 'College of Engineering and Computer Science',
				'profile_url' => 'https://www.ucf.edu/college/engineering-computer-science/',
			),
			array( 'name' => 'College of Sciences' ),
		),
		'departments'         => array( array( 'name' => 'Computer Science' ) ),
	);

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Functions\stubEscapeFunctions();
		$this->loadInclude( 'degree' );
	}

	/**
	 * Without it a degree opens in the classic editor, which cannot offer a block template.
	 *
	 * @return void
	 */
	public function test_degrees_are_exposed_to_the_block_editor() {
		$this->assertTrue( ucf_theme_degree_post_type_args( array( 'public' => true ) )['show_in_rest'] );
	}

	/**
	 * @return void
	 */
	public function test_tuition_matches_the_importers_wording() {
		$this->assertSame( '$212.28 per credit hour', ucf_theme_degree_format_tuition( '212.28', 'SCH' ) );
		$this->assertSame( '$1,500.00 per term', ucf_theme_degree_format_tuition( '1500', 'TRM' ) );
		$this->assertSame( '$640.00 per course', ucf_theme_degree_format_tuition( 640, 'CRS' ) );
		$this->assertSame( '$9,000.00 per year', ucf_theme_degree_format_tuition( '9000.00', 'ANN' ) );
	}

	/**
	 * A tuition with nothing to say says nothing, rather than "$0.00 per credit hour".
	 *
	 * @return void
	 */
	public function test_tuition_is_empty_without_an_amount_or_a_known_type() {
		$this->assertSame( '', ucf_theme_degree_format_tuition( null, 'SCH' ) );
		$this->assertSame( '', ucf_theme_degree_format_tuition( '0.00', 'SCH' ) );
		$this->assertSame( '', ucf_theme_degree_format_tuition( '212.28', null ) );
		$this->assertSame( '', ucf_theme_degree_format_tuition( '212.28', 'XYZ' ) );
	}

	/**
	 * @return void
	 */
	public function test_fields_read_through_to_the_program() {
		$this->assertSame( 'Computer Science (BS)', ucf_theme_degree_field( $this->program, 'name' ) );
		$this->assertSame( '120', ucf_theme_degree_field( $this->program, 'credit_hours' ) );
		$this->assertSame( '$748.89 per credit hour', ucf_theme_degree_field( $this->program, 'tuition_nonresident' ) );
		$this->assertSame( 'https://www.ucf.edu/degree/computer-science-bs/', ucf_theme_degree_field( $this->program, 'profile_url' ) );
		$this->assertSame( 'https://www.ucf.edu/college/engineering-computer-science/', ucf_theme_degree_field( $this->program, 'college_url' ) );
		$this->assertSame( 'Computer Science', ucf_theme_degree_field( $this->program, 'department' ) );
	}

	/**
	 * A program can belong to more than one college; all of them are named.
	 *
	 * @return void
	 */
	public function test_college_names_every_college() {
		$this->assertSame(
			'College of Engineering and Computer Science, College of Sciences',
			ucf_theme_degree_field( $this->program, 'college' )
		);
	}

	/**
	 * Null, not empty: core leaves the block's own content for null, so a mistyped key shows
	 * its placeholder instead of silently rendering an empty element.
	 *
	 * @return void
	 */
	public function test_an_unknown_field_is_null_and_a_missing_one_is_empty() {
		$this->assertNull( ucf_theme_degree_field( $this->program, 'nmae' ) );
		$this->assertSame( '', ucf_theme_degree_field( $this->program, 'excerpt' ) );
		$this->assertSame( '', ucf_theme_degree_field( array(), 'credit_hours' ) );
	}

	/**
	 * @return void
	 */
	public function test_description_prefers_custom_and_falls_back_to_catalog() {
		Functions\when( 'wp_kses_post' )->returnArg();

		$custom  = array(
			'description_type' => array( 'name' => 'Custom Description' ),
			'description'      => '<p>Custom</p>',
		);
		$catalog = array(
			'description_type' => array( 'name' => 'Catalog Description' ),
			'description'      => '<p>Catalog</p>',
		);

		$this->assertSame( '<p>Custom</p>', ucf_theme_degree_description_markup( array( 'descriptions' => array( $catalog, $custom ) ), array( 'source' => 'custom' ) ) );
		$this->assertSame( '<p>Catalog</p>', ucf_theme_degree_description_markup( array( 'descriptions' => array( $catalog ) ), array( 'source' => 'custom' ) ) );
		$this->assertSame( '<p>Catalog</p>', ucf_theme_degree_description_markup( array( 'descriptions' => array( $catalog, $custom ) ), array( 'source' => 'catalog' ) ) );
	}

	/**
	 * Asking for the full catalog text means that text; the short one is not a substitute.
	 *
	 * @return void
	 */
	public function test_an_explicit_description_type_does_not_fall_back() {
		Functions\when( 'wp_kses_post' )->returnArg();

		$catalog = array(
			'description_type' => array( 'name' => 'Catalog Description' ),
			'description'      => '<p>Catalog</p>',
		);

		$this->assertSame( '', ucf_theme_degree_description_markup( array( 'descriptions' => array( $catalog ) ), array( 'source' => 'full' ) ) );
	}

	/**
	 * @return void
	 */
	public function test_tracks_drop_the_parent_prefix_and_link_only_where_a_post_exists() {
		Functions\when( 'get_posts' )->justReturn( array( 55 ) );
		Functions\when( 'get_post_meta' )->justReturn( '1005' );
		Functions\when( 'get_permalink' )->justReturn( 'http://localhost/ucf/degree/computer-science-bs/accelerated/' );

		$markup = ucf_theme_degree_tracks_markup(
			array(
				'name'     => 'Computer Science (BS)',
				'subplans' => array(
					array(
						'id'   => 1005,
						'name' => 'Computer Science (BS) - Accelerated BS to MS',
					),
					array(
						'id'   => 2074,
						'name' => 'Computer Science (BS) - Cyber Security Track',
					),
				),
			)
		);

		$this->assertSame(
			'<ul class="ucf-degree-tracks">'
			. '<li><a href="http://localhost/ucf/degree/computer-science-bs/accelerated/">Accelerated BS to MS</a></li>'
			. '<li>Cyber Security Track</li>'
			. '</ul>',
			$markup
		);
	}

	/**
	 * @return void
	 */
	public function test_no_subplans_is_no_markup() {
		$this->assertSame( '', ucf_theme_degree_tracks_markup( array( 'subplans' => array() ) ) );
	}

	/**
	 * @return void
	 */
	public function test_deadlines_group_by_applicant_type_in_service_order() {
		$markup = ucf_theme_degree_deadlines_markup(
			array(
				'application_deadlines' => array(
					array(
						'deadline_type'  => 'Freshmen',
						'admission_term' => 'Fall',
						'display'        => 'May 1',
					),
					array(
						'deadline_type'  => 'Transfers',
						'admission_term' => 'Fall',
						'display'        => 'June 1',
					),
					array(
						'deadline_type'  => 'Freshmen',
						'admission_term' => 'Spring',
						'display'        => 'November 1',
					),
					array(
						'deadline_type'  => 'Freshmen',
						'admission_term' => 'Summer',
						'display'        => '',
					),
				),
			)
		);

		$this->assertSame(
			'<div class="ucf-degree-deadlines">'
			. '<div class="ucf-degree-deadlines__group"><h3 class="wp-block-heading">Freshmen</h3><dl><dt>Fall</dt><dd>May 1</dd><dt>Spring</dt><dd>November 1</dd></dl></div>'
			. '<div class="ucf-degree-deadlines__group"><h3 class="wp-block-heading">Transfers</h3><dl><dt>Fall</dt><dd>June 1</dd></dl></div>'
			. '</div>',
			$markup
		);
	}

	/**
	 * In the service's order: it ranks them, and a list that reshuffles cannot be cached.
	 *
	 * @return void
	 */
	public function test_careers_keep_service_order_up_to_the_limit() {
		$this->assertSame(
			'<ul class="ucf-degree-careers"><li>Software Developer</li><li>Network Architect</li></ul>',
			ucf_theme_degree_careers_markup( array( 'Software Developer', 'Network Architect', 'Database Administrator' ), array( 'limit' => 2 ) )
		);
	}

	/**
	 * @return void
	 */
	public function test_outcomes_show_the_latest_year_and_skip_missing_figures() {
		$markup = ucf_theme_degree_outcomes_markup(
			array(
				'latest' => array(
					'academic_year_display' => '2019-20',
					'employed_full_time'    => '32.00000000',
					'continuing_education'  => null,
					'avg_annual_earnings'   => '100576.00',
				),
			)
		);

		$this->assertStringContainsString( '<dd>$100,576</dd>', $markup );
		$this->assertStringContainsString( '<dd>32%</dd>', $markup );
		$this->assertStringNotContainsString( 'continuing their education', $markup );
		$this->assertStringContainsString( 'Graduates of the 2019-20 academic year.', $markup );
	}

	/**
	 * @return void
	 */
	public function test_outcomes_without_figures_are_no_markup() {
		$this->assertSame( '', ucf_theme_degree_outcomes_markup( array( 'latest' => null ) ) );
	}

	/**
	 * Zero or negative growth is not presented as a selling point.
	 *
	 * @return void
	 */
	public function test_projections_omit_growth_that_is_not_growth() {
		$markup = ucf_theme_degree_projections_markup(
			array(
				'begin_year'        => 2021,
				'end_year'          => 2031,
				'openings'          => 213000,
				'change_percentage' => '-1.20',
			)
		);

		$this->assertStringContainsString( '<dd>213,000</dd>', $markup );
		$this->assertStringNotContainsString( 'projected growth', $markup );
		$this->assertStringContainsString( 'Projected 2021–2031.', $markup );
	}
}
