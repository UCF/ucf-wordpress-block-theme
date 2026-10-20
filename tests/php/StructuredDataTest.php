<?php
/**
 * Structured data: the collector, the script element, and FAQ pairs read from an accordion.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_structured_data
 * @covers ::ucf_theme_structured_data_script
 * @covers ::ucf_theme_breadcrumb_node
 * @covers ::ucf_theme_faq_pairs
 * @covers ::ucf_theme_faq_node
 * @covers ::ucf_theme_collect_faq
 */
final class StructuredDataTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'structured-data' );
		ucf_theme_structured_data( null, true );
	}

	/**
	 * A parsed accordion, as `parse_blocks()` returns one.
	 *
	 * @param string $class_name The accordion's className.
	 * @return array<string, mixed>
	 */
	private function accordion( $class_name ) {
		$item = static function ( $question, $answer ) {
			return array(
				'blockName'   => 'core/accordion-item',
				'innerBlocks' => array(
					array(
						'blockName'   => 'core/accordion-heading',
						'innerHTML'   => '<h3><button><span class="wp-block-accordion-heading__toggle-title">' . $question . '</span><span aria-hidden="true">+</span></button></h3>',
						'innerBlocks' => array(),
					),
					array(
						'blockName'   => 'core/accordion-panel',
						'innerHTML'   => '<div role="region"></div>',
						'innerBlocks' => array(
							array(
								'blockName' => 'core/paragraph',
								'innerHTML' => '<p>' . $answer . '</p>',
							),
						),
					),
				),
			);
		};

		return array(
			'blockName'   => 'core/accordion',
			'attrs'       => array( 'className' => $class_name ),
			'innerBlocks' => array(
				$item( 'Can I change my major?', 'Usually <strong>yes</strong>.' ),
				$item( 'Empty answer?', '' ),
			),
		);
	}

	/**
	 * Questions come out without core's "+" glyph, answers without markup, and an item with no
	 * answer is dropped rather than claimed.
	 *
	 * @return void
	 */
	public function test_reads_question_and_answer_pairs() {
		$this->assertSame(
			array(
				array(
					'question' => 'Can I change my major?',
					'answer'   => 'Usually yes.',
				),
			),
			ucf_theme_faq_pairs( $this->accordion( 'ucf-faq' ) )
		);
	}

	/**
	 * Only an accordion marked `ucf-faq` becomes FAQPage — reference content is not Q&A.
	 *
	 * @return void
	 */
	public function test_only_a_marked_accordion_is_an_faq() {
		ucf_theme_collect_faq( '', $this->accordion( 'ucf-colleges' ) );
		$this->assertSame( array(), ucf_theme_structured_data() );

		ucf_theme_collect_faq( '', $this->accordion( 'something ucf-faq' ) );
		$graph = ucf_theme_structured_data();

		$this->assertCount( 1, $graph );
		$this->assertSame( 'FAQPage', $graph[0]['@type'] );
	}

	/**
	 * A node without a type is not added.
	 *
	 * @return void
	 */
	public function test_ignores_a_node_without_a_type() {
		ucf_theme_structured_data( null );
		ucf_theme_structured_data( array( 'name' => 'no type' ) );

		$this->assertSame( array(), ucf_theme_structured_data() );
	}

	/**
	 * The script cannot be closed early by a value holding `</script>`.
	 *
	 * @return void
	 */
	public function test_script_escapes_a_closing_tag() {
		$script = ucf_theme_structured_data_script(
			array(
				array(
					'@type' => 'Thing',
					'name'  => '</script><b>',
				),
			)
		);

		$this->assertStringStartsWith( '<script type="application/ld+json">', $script );
		$this->assertSame( 1, substr_count( $script, '</script>' ) );
		$this->assertSame( '', ucf_theme_structured_data_script( array() ) );
	}

	/**
	 * A trail of one is not a breadcrumb; positions count from 1.
	 *
	 * @return void
	 */
	public function test_breadcrumbs() {
		$this->assertNull(
			ucf_theme_breadcrumb_node(
				array(
					array(
						'name' => 'Home',
						'url'  => '/',
					),
				)
			)
		);

		$node = ucf_theme_breadcrumb_node(
			array(
				array(
					'name' => 'Home',
					'url'  => '/',
				),
				array(
					'name' => 'Academics',
					'url'  => '/academics/',
				),
			)
		);

		$this->assertSame( 2, $node['itemListElement'][1]['position'] );
		$this->assertSame( 'Academics', $node['itemListElement'][1]['name'] );
	}
}
