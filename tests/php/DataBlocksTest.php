<?php
/**
 * The data blocks' markup builders and structured-data nodes: Facts, Program finder, Profile,
 * Events, Provenance, Alert banner.
 *
 * WHY one file for six includes. Each include's testable part is the same kind of thing — a
 * pure builder from a record to markup and a node — and the record shapes are what tie them
 * together; MockDataTest checks the data, this checks what is made from it. The registration
 * and render callbacks need real WordPress (`register_block_type`, `get_block_wrapper_attributes`)
 * and belong to an integration tier.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_facts_markup
 * @covers ::ucf_theme_program_node
 * @covers ::ucf_theme_finder_markup
 * @covers ::ucf_theme_profile_markup
 * @covers ::ucf_theme_person_node
 * @covers ::ucf_theme_upcoming_events
 * @covers ::ucf_theme_events_markup
 * @covers ::ucf_theme_event_node
 * @covers ::ucf_theme_provenance_markup
 * @covers ::ucf_theme_article_node
 * @covers ::ucf_theme_alert_markup
 * @covers ::ucf_theme_degree_record
 * @covers ::ucf_theme_degree_level
 */
final class DataBlocksTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'format', 'mock-data', 'programs', 'people', 'events', 'provenance', 'alerts' );
	}

	/**
	 * A facts record in the documented shape.
	 *
	 * @param bool $sample Whether it is sample data.
	 * @return array<string, mixed>
	 */
	private function program( $sample = true ) {
		return array(
			'name'   => 'Computer Science B.S.',
			'url'    => 'https://www.ucf.edu/degree/computer-science-bs/',
			'sample' => $sample,
			'source' => array(
				'label' => 'Sample catalog',
				'url'   => 'https://www.ucf.edu/',
				'asOf'  => '2026-10-01',
			),
			'facts'  => array(
				array(
					'label' => 'Credit hours',
					'value' => '120',
					'data'  => true,
				),
				array(
					'label' => 'Format',
					'value' => 'In person & online',
				),
			),
		);
	}

	/**
	 * Facts: a real `<dl>`, a dated source, a citation, and the sample note only when sample.
	 *
	 * @return void
	 */
	public function test_facts_markup() {
		$html = ucf_theme_facts_markup( $this->program(), 'cite-1' );

		$this->assertStringContainsString( '<dl class="ucf-facts__list">', $html );
		$this->assertStringContainsString( '<dd class="is-data">120</dd>', $html );
		$this->assertStringContainsString( 'In person &amp; online', $html );
		$this->assertStringContainsString( '<time datetime="2026-10-01">Oct. 1, 2026</time>', $html );
		$this->assertStringContainsString( 'id="cite-1"', $html );
		$this->assertStringContainsString( 'data-ucf-copy="cite-1" hidden', $html );
		$this->assertStringContainsString( 'ucf-sample', $html );

		$this->assertStringNotContainsString( 'ucf-sample', ucf_theme_facts_markup( $this->program( false ), 'cite-2' ) );
	}

	/**
	 * The program node carries the credit hours as a number.
	 *
	 * @return void
	 */
	public function test_program_node() {
		$node = ucf_theme_program_node( $this->program() );

		$this->assertSame( 'EducationalOccupationalProgram', $node['@type'] );
		$this->assertSame( 120, $node['numberOfCredits'] );
	}

	/**
	 * Finder: the full list is in the markup, controls start hidden, one chip per level.
	 *
	 * @return void
	 */
	public function test_finder_markup() {
		$programs = array(
			array(
				'name'    => 'Art (BA)',
				'url'     => 'https://www.ucf.edu/degree/art-ba/',
				'college' => 'Arts and Humanities',
				'level'   => 'Bachelor’s',
			),
			array(
				'name'    => 'History MA',
				'url'     => 'https://www.ucf.edu/degree/history-ma/',
				'college' => 'Arts and Humanities',
				'level'   => 'Master’s',
			),
		);

		$html = ucf_theme_finder_markup( $programs, 'f' );

		$this->assertSame( 2, substr_count( $html, '<li class="ucf-result"' ) );
		$this->assertSame( 2, substr_count( $html, 'class="ucf-chip"' ) );
		$this->assertStringContainsString( 'class="ucf-finder__controls" hidden', $html );
		$this->assertStringContainsString( 'data-search="art (ba) arts and humanities"', $html );
		$this->assertStringContainsString( '2 programs', $html );
	}

	/**
	 * Profile: no image of a stand-in face, the expertise as tags.
	 *
	 * @return void
	 */
	public function test_profile_markup() {
		$person = $this->person();
		$html   = ucf_theme_profile_markup( $person );

		$this->assertStringNotContainsString( '<img', $html );
		$this->assertStringContainsString( 'is-style-tags', $html );
		$this->assertSame( 'Person', ucf_theme_person_node( $person )['@type'] );
	}

	/**
	 * Events: past ones drop off, the rest come soonest first, and the count is honored.
	 *
	 * @return void
	 */
	public function test_upcoming_events() {
		$events = array(
			array(
				'title'  => 'Later',
				'offset' => 10,
			),
			array(
				'title'  => 'Past',
				'offset' => -1,
			),
			array(
				'title'  => 'Today',
				'offset' => 0,
			),
			array(
				'title' => 'Dated',
				'date'  => '2026-10-03',
			),
		);

		$upcoming = ucf_theme_upcoming_events( $events, '2026-10-01', 5 );

		$this->assertSame( array( 'Today', 'Dated', 'Later' ), array_column( $upcoming, 'title' ) );
		$this->assertSame( '2026-10-11', $upcoming[2]['date'] );
		$this->assertCount( 1, ucf_theme_upcoming_events( $events, '2026-10-01', 1 ) );
	}

	/**
	 * Events markup: a machine-readable date, and an online event is a virtual location.
	 *
	 * @return void
	 */
	public function test_events_markup_and_node() {
		$event = array(
			'title'    => 'Info session',
			'date'     => '2026-09-15',
			'time'     => '6 p.m.',
			'location' => 'Online',
			'url'      => '#',
		);

		$html = ucf_theme_events_markup( array( $event ) );

		$this->assertStringContainsString( '<time datetime="2026-09-15">Sept. 15, 2026</time>', $html );
		$this->assertStringContainsString( 'aria-hidden="true"', $html );
		$this->assertSame( 'VirtualLocation', ucf_theme_event_node( $event )['location']['@type'] );
	}

	/**
	 * Provenance: a sample reviewer is labeled on the page and left out of the structured data.
	 *
	 * @return void
	 */
	public function test_provenance_keeps_a_sample_reviewer_out_of_the_node() {
		$record = array(
			'author'    => 'Writer',
			'reviewer'  => 'Sample Name, Ph.D.',
			'sample'    => true,
			'published' => '2026-09-01',
			'modified'  => '2026-10-01',
			'owner'     => 'Office of Research',
		);

		$this->assertStringContainsString( 'ucf-sample', ucf_theme_provenance_markup( $record ) );
		$this->assertArrayNotHasKey( 'reviewedBy', ucf_theme_article_node( $record, 'Title' ) );

		$record['sample'] = false;

		$this->assertStringNotContainsString( 'ucf-sample', ucf_theme_provenance_markup( $record ) );

		$record['author'] = '';
		$this->assertStringNotContainsString( 'Written by', ucf_theme_provenance_markup( $record ) );
		$this->assertSame( 'Sample Name, Ph.D.', ucf_theme_article_node( $record, 'Title' )['reviewedBy']['name'] );
	}

	/**
	 * Alert: only an emergency is announced on load.
	 *
	 * @return void
	 */
	public function test_only_an_emergency_is_announced() {
		$alert = array(
			'title' => 'Closed',
			'text'  => 'Campus is closed.',
			'link'  => array(
				'label' => 'Updates',
				'url'   => 'https://www.ucf.edu/alert/',
			),
		);

		$this->assertStringContainsString( 'role="alert"', ucf_theme_alert_markup( $alert, 'emergency', '' ) );
		$this->assertStringNotContainsString( 'role="alert"', ucf_theme_alert_markup( $alert, 'notice', '' ) );
	}

	/**
	 * A degree's level is read from the abbreviation in its title.
	 *
	 * @dataProvider degree_titles
	 * @param string $title    Title.
	 * @param string $expected Level.
	 * @return void
	 */
	public function test_degree_level( $title, $expected ) {
		$this->assertSame( $expected, ucf_theme_degree_level( $title ) );
	}

	/**
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function degree_titles() {
		return array(
			'bachelor'    => array( 'Chemistry (BS) - Biochemistry', 'Bachelor’s' ),
			'bsba'        => array( 'Finance BSBA', 'Bachelor’s' ),
			'master'      => array( 'Hospitality and Tourism Management MS', 'Master’s' ),
			'mba'         => array( 'Business Administration MBA', 'Master’s' ),
			'doctoral'    => array( 'Business Administration (PhD) - Accounting', 'Doctoral' ),
			'certificate' => array( 'Accounting Graduate Certificate', 'Certificate' ),
			'none'        => array( 'Pre-medical', 'Degree' ),
		);
	}

	/**
	 * A track's record names its program and track, and is sample until the catalog arrives;
	 * a mock record for the same degree wins.
	 *
	 * @return void
	 */
	public function test_degree_record() {
		$degree = array(
			'title'    => 'Chemistry (BS) - Biochemistry',
			'url'      => 'https://example.test/degree/chemistry-bs/biochemistry/',
			'parent'   => 'Chemistry (BS)',
			'modified' => '2026-10-01',
		);

		$record = ucf_theme_degree_record( $degree, null );
		$facts  = array_column( $record['facts'], 'value', 'label' );

		$this->assertTrue( $record['sample'] );
		$this->assertSame( 'Bachelor’s', $facts['Level'] );
		$this->assertSame( 'Chemistry (BS)', $facts['Program'] );
		$this->assertSame( 'Biochemistry', $facts['Track'] );
		$this->assertSame( '2026-10-01', $record['source']['asOf'] );

		$this->assertArrayNotHasKey(
			'Track',
			array_column(
				ucf_theme_degree_record(
					array_merge(
						$degree,
						array(
							'title'  => 'Chemistry (BS)',
							'parent' => '',
						)
					),
					null
				)['facts'],
				'value',
				'label'
			)
		);

		$mock = $this->program();
		$this->assertSame( $mock, ucf_theme_degree_record( $degree, $mock ) );
	}

	/**
	 * One person in the documented shape.
	 *
	 * @return array<string, mixed>
	 */
	private function person() {
		return array(
			'name'        => 'Sample Name, Ph.D.',
			'role'        => 'Professor',
			'credentials' => 'Ph.D. 2009',
			'expertise'   => array( 'Optics' ),
			'links'       => array(
				array(
					'label' => 'Profile',
					'url'   => '#',
				),
			),
			'contact'     => array(
				'label' => 'Email',
				'url'   => '#',
			),
		);
	}
}
