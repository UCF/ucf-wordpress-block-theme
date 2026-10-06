<?php
/**
 * Block style registration, and its agreement with the stylesheet.
 *
 * `includes/block-styles.php` and `src/scss/_compositions.scss` hold the same list of
 * composition names in two languages, and PHP cannot read a Sass map. Both files carry a
 * `SYNC:` comment saying so, which is a instruction to a human and catches nothing.
 *
 * The two ways that drifts are both silent:
 *
 * - A name in PHP but not in Sass is an entry in the editor's Styles panel that paints
 *   nothing. The editor applies it, the block looks unchanged, and nothing errors.
 * - A name in Sass but not in PHP is CSS no editor can reach — it ships in every byte of
 *   main.css and can only be applied by hand-writing the class.
 *
 * Neither shows up in a page capture, a build, or a lint. This test is the only thing that
 * looks.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey\Functions;

/**
 * @covers ::ucf_theme_register_composition_styles
 * @covers ::ucf_theme_register_element_styles
 * @covers ::ucf_theme_element_styles
 * @covers ::ucf_theme_register_block_styles
 */
final class BlockStylesTest extends TestCase {

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		$this->loadInclude( 'block-styles' );
	}

	/**
	 * Collect what a registration function asks for, rather than performing it.
	 *
	 * @param callable $register The registration function to run.
	 * @return array<int, array{0: string, 1: string}> Pairs of [ block type, style name ].
	 */
	private function captureRegistrations( callable $register ) {
		$calls = array();

		Functions\when( 'register_block_style' )->alias(
			static function ( $block_type, $args ) use ( &$calls ) {
				$calls[] = array( $block_type, $args['name'] );
			}
		);

		$register();

		return $calls;
	}

	/**
	 * The top-level keys of a named Sass map, read from the stylesheet source.
	 *
	 * WHY: parsed rather than duplicated here. A list restated in the test is a third copy
	 * that can drift from the other two, which is the bug this file exists to catch.
	 *
	 * Matches keys at exactly one tab of indentation, which is what makes it read the map's
	 * own entries and not the nested per-composition keys ("bg", "text", "treatment") two
	 * tabs in.
	 *
	 * @param string $map_name Sass variable name, without the dollar sign.
	 * @return string[] The map's top-level keys, in source order.
	 */
	private function sassMapKeys( $map_name ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a file from disk in a suite that has no WordPress; wp_remote_get() is for URLs.
		$source = file_get_contents( UCF_THEME_DIR . '/src/scss/_compositions.scss' );

		$this->assertIsString( $source, 'src/scss/_compositions.scss must be readable.' );

		$start = strpos( $source, "\${$map_name}: (" );

		$this->assertNotFalse( $start, "\${$map_name} was not found in _compositions.scss." );

		// The map ends at the first `);` that begins a line — the nested maps close with
		// `),` at one tab in, so they cannot terminate the scan early.
		$end = strpos( $source, "\n);", $start );

		$this->assertNotFalse( $end, "\${$map_name} has no closing `);`." );

		preg_match_all(
			'/^\t"([a-z0-9-]+)":/m',
			substr( $source, $start, $end - $start ),
			$matches
		);

		$this->assertNotEmpty( $matches[1], "\${$map_name} parsed as empty — the parser is wrong, not the map." );

		return $matches[1];
	}

	/**
	 * Every composition in the Sass map is registered, and nothing else is.
	 *
	 * @return void
	 */
	public function test_registered_compositions_match_the_stylesheet() {
		$registered = $this->captureRegistrations( 'ucf_theme_register_composition_styles' );

		$names = array();

		foreach ( $registered as $call ) {
			$names[] = $call[1];
		}

		$expected = array();

		foreach ( $this->sassMapKeys( 'compositions' ) as $composition ) {
			$expected[] = $composition;
		}

		sort( $names );
		sort( $expected );

		$this->assertSame(
			$expected,
			$names,
			'includes/block-styles.php and src/scss/_compositions.scss disagree about which compositions exist.'
		);
	}

	/**
	 * The `-accent` flavors are defined in the stylesheet and deliberately not registered.
	 *
	 * This is the theme's one intentional case of CSS with no registration behind it, so it
	 * looks exactly like the bug the test above exists to catch. Both halves are asserted
	 * here, because either one alone reads as an oversight:
	 *
	 * - The flavors must stay defined. Whatever establishes the accent applies the class, and
	 *   deleting the rules as "unreachable" would leave that class painting nothing.
	 * - They must stay unregistered. Registering them doubles the Styles panel to offer eight
	 *   variants of one decision, and makes the accent something an author picks rather than
	 *   something a component establishes.
	 *
	 * @return void
	 */
	public function test_accent_flavors_are_defined_but_not_registered() {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- As above.
		$source = file_get_contents( UCF_THEME_DIR . '/src/scss/_compositions.scss' );

		$this->assertStringContainsString(
			'.is-style-#{$name}-accent',
			$source,
			'The -accent flavors are no longer emitted by _compositions.scss. Whatever applies that class now paints nothing.'
		);

		$registered = $this->captureRegistrations( 'ucf_theme_register_block_styles' );

		foreach ( $registered as $call ) {
			$this->assertStringEndsNotWith(
				'-accent',
				$call[1],
				"`{$call[1]}` is registered as a block style. The accent flavors are established by a component, not picked from the Styles panel."
			);
		}
	}

	/**
	 * Every composition names a treatment that exists.
	 *
	 * Unlike the drift checks above, the build does catch this one: `map.get($treatments,
	 * "drak")` is null, and the mixin's next `map.get()` on it fails with `$map: null is not a
	 * map` — `sass` exits 65 and `npm run build` fails with it.
	 *
	 * WHY this test exists anyway: that error names neither the composition nor the bad
	 * treatment, so it says a map is null somewhere in a 160-line file. This says which field
	 * named which missing treatment. It also fails in `npm test`, which runs in a tenth of a
	 * second, rather than only at build time.
	 *
	 * Do not generalize the build's helpfulness here to `map.get()` at large. A miss used
	 * directly in a property value — `map.get($size, "gone")` — emits nothing, drops the
	 * declaration and exits 0. It errors here only because the null is then indexed again.
	 *
	 * @return void
	 */
	public function test_every_composition_names_a_treatment_that_exists() {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- As above.
		$source     = file_get_contents( UCF_THEME_DIR . '/src/scss/_compositions.scss' );
		$treatments = $this->sassMapKeys( 'treatments' );

		preg_match_all( '/"treatment": "([a-z0-9-]+)"/', $source, $matches );

		$this->assertNotEmpty( $matches[1], 'No "treatment" keys were found to check.' );

		foreach ( array_unique( $matches[1] ) as $treatment ) {
			$this->assertContains(
				$treatment,
				$treatments,
				"A composition names the treatment `{$treatment}`, which is not in \$treatments."
			);
		}
	}

	/**
	 * Compositions are registered on core/group, and on nothing else.
	 *
	 * They set a background and the text roles that go with it, which is a container's job.
	 * Registering one on a paragraph would offer an editor a style that paints a block
	 * background where the pattern expects a field.
	 *
	 * @return void
	 */
	public function test_compositions_are_registered_on_the_group_block() {
		$registered = $this->captureRegistrations( 'ucf_theme_register_composition_styles' );

		foreach ( $registered as $call ) {
			$this->assertSame( 'core/group', $call[0], "`{$call[1]}` is registered on the wrong block type." );
		}
	}

	/**
	 * The compositions are the only styles registered on core/group.
	 *
	 * A treatment is applied by a composition style and by nothing else — that is the whole
	 * contract, and it only holds while this is true. Any other group style would be a second
	 * route to a field, which is what the removed `on-dark` style and `.has-*-background-color`
	 * rules were: one visual result reachable several ways with different consequences.
	 *
	 * Styles on other blocks are allowed, but only from `ucf_theme_element_styles()` — see
	 * the next two tests.
	 *
	 * @return void
	 */
	public function test_the_compositions_are_the_only_group_styles() {
		$all        = $this->captureRegistrations( 'ucf_theme_register_block_styles' );
		$from_pairs = $this->captureRegistrations( 'ucf_theme_register_composition_styles' );

		$group_names = array();

		foreach ( $all as $call ) {
			if ( 'core/group' === $call[0] ) {
				$group_names[] = $call[1];
			}
		}

		$pair_names = array();

		foreach ( $from_pairs as $call ) {
			$pair_names[] = $call[1];
		}

		$this->assertSame(
			array(),
			array_values( array_diff( $group_names, $pair_names ) ),
			'A core/group style is registered that is not one of the compositions.'
		);
	}

	/**
	 * Every registered style is either a composition or on the reviewed element list.
	 *
	 * WHY: so a style cannot arrive by a third route. Adding one means adding it to
	 * `ucf_theme_element_styles()`, where its comment names the partial that paints it.
	 *
	 * @return void
	 */
	public function test_every_style_is_a_composition_or_a_listed_element_style() {
		$expected = $this->captureRegistrations( 'ucf_theme_register_composition_styles' );

		foreach ( ucf_theme_element_styles() as $block => $styles ) {
			$this->assertNotSame( 'core/group', $block, 'An element style is listed on core/group, which only compositions may style.' );

			foreach ( array_keys( $styles ) as $name ) {
				$expected[] = array( $block, $name );
			}
		}

		$this->assertEqualsCanonicalizing(
			$expected,
			$this->captureRegistrations( 'ucf_theme_register_block_styles' )
		);
	}

	/**
	 * Every element style has a rule in the stylesheet.
	 *
	 * The composition test above reads a Sass map; element styles have no map, so this looks
	 * for the `.is-style-{name}` selector in any partial. A style registered with no rule is an
	 * editor offering that paints nothing.
	 *
	 * @return void
	 */
	public function test_every_element_style_ships_with_its_css() {
		$source = '';

		foreach ( (array) glob( UCF_THEME_DIR . '/src/scss/*.scss' ) as $partial ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- As above.
			$source .= file_get_contents( $partial );
		}

		foreach ( ucf_theme_element_styles() as $block => $styles ) {
			foreach ( array_keys( $styles ) as $name ) {
				$this->assertMatchesRegularExpression(
					'/\.is-style-' . preg_quote( $name, '/' ) . '(?![a-z0-9-])/',
					$source,
					"`{$block}` registers the style `{$name}`, but no partial in src/scss/ defines `.is-style-{$name}`."
				);
			}
		}
	}
}
