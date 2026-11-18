<?php
/**
 * Structured data: the collector and the script element.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

/**
 * @covers ::ucf_theme_structured_data
 * @covers ::ucf_theme_structured_data_script
 * @covers ::ucf_theme_breadcrumb_node
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
