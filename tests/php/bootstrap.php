<?php
/**
 * Bootstrap for the PHP unit suite.
 *
 * These tests run WITHOUT WordPress — no database, no wp-env, no Docker. That is a deliberate
 * trade: the functions covered here are string transforms and registration calls, and a suite
 * that runs in under a second is one that actually gets run before a commit.
 *
 * Anything that genuinely needs WordPress — a meta query against real posts, a
 * `WP_HTML_Tag_Processor` walk, the `render_block` filter — does NOT belong here. Mocking
 * those into this suite would only test the mock. That work belongs in an integration tier
 * running against real WordPress under wp-env, which this theme does not have yet.
 *
 * The theme's own includes are deliberately NOT required here. Each one calls `add_action()`
 * or `add_filter()` at include time, and those only exist while Brain Monkey is running, which
 * is per-test. The base TestCase requires them inside `setUp()` for that reason — see
 * tests/php/TestCase.php.
 *
 * @package ucf-wordpress-block-theme
 */

// Every include guards on this. The value is irrelevant; only its presence is checked.
define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

define( 'UCF_THEME_DIR', dirname( __DIR__, 2 ) );

require_once UCF_THEME_DIR . '/vendor/autoload.php';

// The WordPress functions with no behavior worth varying per test, as real definitions rather
// than per-test Brain Monkey aliases — defining them once keeps every test reading the same
// implementation. Anything a test needs to control is stubbed in that test instead.
require_once __DIR__ . '/stubs/wp-functions.php';
