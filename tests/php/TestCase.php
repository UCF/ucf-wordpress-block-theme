<?php
/**
 * Base case for the WordPress-free unit suite.
 *
 * @package ucf-wordpress-block-theme
 */

namespace UCF\Theme\Tests;

use Brain\Monkey;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Sets up Brain Monkey and loads the theme's includes on demand.
 */
abstract class TestCase extends PHPUnitTestCase {

	/**
	 * Include files already loaded, so the theme's add_action() calls run once.
	 *
	 * @var array<string, bool>
	 */
	private static $loaded = array();

	/**
	 * Start Brain Monkey.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Load one of the theme's include files.
	 *
	 * WHY: must happen after `Monkey\setUp()`. Every include calls `add_action()` or
	 * `add_filter()` at include time, and Brain Monkey is what defines those — requiring a
	 * file before it is running is a fatal error, not a skipped hook.
	 *
	 * Loading is idempotent: PHP function definitions persist for the whole process, so only
	 * the first test that asks for a file actually includes it. A consequence worth knowing —
	 * the hooks registered at include time are registered during exactly one test's Brain
	 * Monkey session. Assert on a hook in the same test that first loads the file, or stub the
	 * hooked function's dependencies and call it directly, which is what the tests here do.
	 *
	 * @param string ...$names Include basenames, e.g. 'university-header'.
	 * @return void
	 */
	protected function loadInclude( ...$names ) {
		foreach ( $names as $name ) {
			if ( isset( self::$loaded[ $name ] ) ) {
				continue;
			}

			require_once UCF_THEME_DIR . "/includes/{$name}.php";
			self::$loaded[ $name ] = true;
		}
	}
}
