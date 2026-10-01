<?php
/**
 * Seed a wp-env instance with the content the accessibility suite walks.
 *
 * Run through WP-CLI, not the web:
 *
 *     wp-env run cli wp eval-file wp-content/themes/ucf-wordpress-block-theme/tests/a11y/seed.php
 *
 * `tools/a11y-tests.js` does that for you; `npm run env:seed` is the standalone door.
 *
 * CONTEXT: four families of page come out of this. Ported from the UCF Brand Block Theme and
 * cut down to what this theme actually renders.
 *
 *   Routes    One page per template — page, single post, blog index, search with and without
 *             results, 404 — plus the front page. Fixed, because a template is a fixed thing.
 *   Features  Theme markup no template carries: the Section band and the badge tones, plus
 *             any custom block no pattern renders yet. Hand-written, but the badge tones and
 *             the Section's compositions are read from source, not listed here.
 *   Patterns  One page per registered `ucf-wordpress-block-theme/*` pattern, read out of
 *             `WP_Block_Patterns_Registry`. A new pattern is covered the next time this runs.
 *   Variants  One page per registered block style, read out of `WP_Block_Styles_Registry`
 *             the same way. See the note above the samples for the part that is not automatic.
 *
 * It ends by writing `seeded.json` next to this file — the manifest the Playwright specs read
 * to know what exists and what to call it. The suite refuses to run without it, so a seed that
 * failed halfway cannot be mistaken for a site with nothing wrong.
 *
 * Re-running is safe: everything it makes is tagged with UCF_THEME_A11Y_SEED_META and deleted
 * on the way in, so this is a reset rather than an append.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	die( "tests/a11y/seed.php must run under WP-CLI.\n" );
}

/*
 * UPSTREAM: KSES strips HTML comments, and a block *is* an HTML comment. Left on, every
 * `wp_insert_post()` below would store content with the block delimiters gone — the pages
 * would still render (as raw HTML), so this fails by producing plausible-looking pages that
 * are not the blocks under test. WP-CLI runs with no logged-in user, so
 * `current_user_can( 'unfiltered_html' )` is false and `kses_init_filters()` has already
 * attached them on `init`.
 */
kses_remove_filters();

/** The theme's directory name, which is also its slug and its pattern namespace. */
const UCF_THEME_A11Y_SLUG = 'ucf-wordpress-block-theme';

/** Meta key marking a post as this script's, so a re-run can clear the last one. */
const UCF_THEME_A11Y_SEED_META = '_ucf_theme_a11y_seed';

/** Search route's query, and a word every seeded route page contains. */
const UCF_THEME_A11Y_SEARCH_TERM = 'admissions';

ucf_theme_a11y_seed();

/**
 * Build the whole fixture site and write the manifest.
 *
 * @return void
 */
function ucf_theme_a11y_seed() {
	ucf_theme_a11y_assert_theme_active();
	ucf_theme_a11y_reset();

	// FIX: pretty permalinks. The manifest hands Playwright paths like `/a11y-page/`, and with
	// the default `?p=` structure every one of them would 404 into a green-looking 404 test.
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	ucf_theme_a11y_write_htaccess();

	$manifest = array(
		'routes'   => ucf_theme_a11y_seed_routes(),
		'features' => ucf_theme_a11y_seed_features(),
		'patterns' => ucf_theme_a11y_seed_patterns(),
		'variants' => ucf_theme_a11y_seed_variants(),
	);

	ucf_theme_a11y_assert_blocks_covered();

	$wp_rewrite->flush_rules( true );
	wp_get_theme()->delete_pattern_cache();

	$path  = __DIR__ . '/seeded.json';
	$bytes = file_put_contents( $path, wp_json_encode( $manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- CLI-only fixture writer; WP_Filesystem is not bootstrapped here.

	if ( false === $bytes ) {
		WP_CLI::error( "Could not write the manifest to {$path}." );
	}

	WP_CLI::success(
		sprintf(
			'Seeded %d routes, %d features, %d patterns, %d variants.',
			count( $manifest['routes'] ),
			count( $manifest['features'] ),
			count( $manifest['patterns'] ),
			count( $manifest['variants'] )
		)
	);
}

/**
 * Refuse to run against any theme but this one.
 *
 * FIX: this checks rather than fixes, and that distinction is the whole point. The brand
 * theme's version called `switch_theme()` here, which does not do what it looks like it does:
 * WordPress has already booted by the time `eval-file` runs, so `functions.php` loaded for
 * whatever theme was active and `init` has been and gone. The switch writes the option and
 * nothing else — this process still sees empty pattern and block-style registries, while the
 * *next* process gets the theme.
 *
 * So the seed failed on a clean environment and passed on every run after it: green on any
 * machine that had run it once, red on every CI runner, which is how it shipped. Failing
 * loudly is strictly better than a fix that hides itself on the second attempt.
 *
 * `tools/a11y-tests.js` activates the theme in a separate WP-CLI invocation, which is the
 * arrangement that actually works.
 *
 * @return void
 */
function ucf_theme_a11y_assert_theme_active() {
	$active = get_stylesheet();

	if ( UCF_THEME_A11Y_SLUG !== $active ) {
		WP_CLI::error(
			"The active theme is '{$active}', so this would seed and audit that theme instead.\n"
			. 'Activate it first, in its own command — switching from inside this script is too '
			. "late to matter:\n"
			. '  wp-env run cli wp theme activate ' . UCF_THEME_A11Y_SLUG
		);
	}
}

/**
 * Write the rewrite rules into `.htaccess` by hand.
 *
 * UPSTREAM: `flush_rules( true )` will not do it from here. It guards the write behind
 * `got_mod_rewrite()`, which asks the *current* server whether mod_rewrite is loaded — and the
 * current server is PHP-CLI in the `cli` container, which has no Apache and answers no. The
 * `wordpress` container serving port 8888 does have it; nothing ever asks that one.
 *
 * The symptom is worth recognizing, because it does not look like a permalink problem: every
 * seeded path returns Apache's own bare "Not Found" page, so the suite audits a page with no
 * `<html lang>` and reports a `html-has-lang` violation on a theme that sets it correctly.
 * `wp rewrite flush --hard` fails the same way for the same reason.
 *
 * `mod_rewrite_rules()` only builds the string, so calling it directly and writing the result
 * skips the check without faking it.
 *
 * @return void
 */
function ucf_theme_a11y_write_htaccess() {
	global $wp_rewrite;

	require_once ABSPATH . 'wp-admin/includes/misc.php';

	$written = insert_with_markers(
		ABSPATH . '.htaccess',
		'WordPress',
		explode( "\n", $wp_rewrite->mod_rewrite_rules() )
	);

	if ( ! $written ) {
		WP_CLI::error(
			'Could not write ' . ABSPATH . '.htaccess. Without it every pretty permalink '
			. '404s and the whole suite audits Apache error pages.'
		);
	}
}

/**
 * Delete everything a previous run created.
 *
 * WHY: by meta rather than by slug. A run that changed the naming scheme would otherwise leave
 * its predecessor's pages behind, and those stale pages are indistinguishable from real ones
 * in a report.
 *
 * @return void
 */
function ucf_theme_a11y_reset() {
	$stale = get_posts(
		array(
			'post_type'        => 'any',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'fields'           => 'ids',
			'suppress_filters' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One-off CLI teardown over a fixture site.
			'meta_key'         => UCF_THEME_A11Y_SEED_META,
		)
	);

	foreach ( $stale as $id ) {
		wp_delete_post( $id, true );
	}
}

/**
 * Create a page or post and tag it as seeded.
 *
 * @param array<string, mixed> $args Arguments for `wp_insert_post()`, minus the status.
 * @return int The new post ID.
 */
function ucf_theme_a11y_insert( array $args ) {
	// UPSTREAM: wp_insert_post() unslashes what it is given, which eats the backslashes JSON
	// uses to escape a quote inside a block comment's attributes — the attrs then parse as
	// null and the block renders with none of them, looking merely empty. Slashing on the way
	// in matches what the editor's REST save does, so a sample carrying markup inside an
	// attribute keeps it.
	if ( isset( $args['post_content'] ) ) {
		$args['post_content'] = wp_slash( $args['post_content'] );
	}

	$id = wp_insert_post(
		array_merge(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
			),
			$args
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		WP_CLI::error( 'Could not create "' . ( $args['post_title'] ?? '?' ) . '": ' . $id->get_error_message() );
	}

	update_post_meta( $id, UCF_THEME_A11Y_SEED_META, 1 );

	return $id;
}

/**
 * One page per template, plus the front page and the two routes that are queries.
 *
 * @return array<int, array<string, mixed>> Manifest entries.
 */
function ucf_theme_a11y_seed_routes() {
	$home = ucf_theme_a11y_insert(
		array(
			'post_title'   => 'University of Central Florida',
			'post_name'    => 'a11y-home',
			'post_content' => ucf_theme_a11y_body_content( 'the front page' ),
		)
	);

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $home );

	ucf_theme_a11y_insert(
		array(
			'post_title'   => 'A page, for the page template',
			'post_name'    => 'a11y-page',
			'post_content' => ucf_theme_a11y_body_content( 'a page' ),
		)
	);

	// WHY: two posts, not one. The blog index and search results are lists, and a list of one
	// renders no separation between entries for axe to measure.
	foreach ( array( 'first', 'second' ) as $which ) {
		ucf_theme_a11y_insert(
			array(
				'post_type'    => 'post',
				'post_title'   => ucfirst( $which ) . ' post, for the single and index templates',
				'post_name'    => 'a11y-post-' . $which,
				'post_excerpt' => 'The ' . $which . ' seeded post, with an excerpt so the index and search lists show one.',
				'post_content' => ucf_theme_a11y_body_content( 'the ' . $which . ' post' ),
			)
		);
	}

	return array(
		array(
			'name' => 'front-page',
			'path' => '/',
		),
		array(
			'name' => 'page',
			'path' => '/a11y-page/',
		),
		array(
			'name' => 'single-post',
			'path' => '/a11y-post-first/',
		),
		array(
			'name' => 'index-blog',
			'path' => '/?post_type=post',
		),
		array(
			'name' => 'search-results',
			'path' => '/?s=' . UCF_THEME_A11Y_SEARCH_TERM,
		),
		array(
			'name' => 'search-no-results',
			'path' => '/?s=' . rawurlencode( 'zzzz no results zzzz' ),
		),
		// WHY: the only route expected not to return 200, and declared as such. The suite treats
		// an unexpected 404 as a failure — a mistyped path would otherwise audit the 404
		// template and pass.
		array(
			'name'   => 'notfound-404',
			'path'   => '/a11y-this-page-does-not-exist/',
			'status' => 404,
		),
	);
}

/**
 * A page per piece of theme markup that no template or pattern renders.
 *
 * Neither the Section band nor the badges appear in `templates/`: both are things an author
 * adds in the editor. Without a page of their own here, nothing the suite visits would carry
 * them, and a contrast regression in either would sail through.
 *
 * @return array<int, array<string, string>> Manifest entries.
 */
function ucf_theme_a11y_seed_features() {
	/*
	 * WHY: one band per registered composition, stacked, rather than the variation's default
	 * `is-style-light` alone. Stacked bands are how a Section is actually used, and the band's
	 * own padding and full-bleed alignment are what this page adds over the variant tier — a
	 * single Light band would repeat that tier's light page and add nothing.
	 */
	$bands  = '';
	$styles = WP_Block_Styles_Registry::get_instance()->get_registered_styles_for_block( 'core/group' );

	foreach ( array_keys( $styles ) as $style ) {
		$bands .= ucf_theme_a11y_section( 'is-style-' . $style, ucfirst( $style ) . ' section' ) . "\n\n";
	}

	ucf_theme_a11y_insert(
		array(
			'post_title'   => 'Feature: Section bands',
			'post_name'    => 'a11y-feature-section',
			'post_content' => $bands,
		)
	);

	ucf_theme_a11y_insert(
		array(
			'post_title'   => 'Feature: Badges',
			'post_name'    => 'a11y-feature-badges',
			'post_content' => ucf_theme_a11y_badges(),
		)
	);

	return array(
		array(
			'name'  => 'ucf-section',
			'path'  => '/a11y-feature-section/',
			// The class is the assertion: it is only on the page if the band rendered.
			'class' => 'ucf-section',
		),
		array(
			'name'  => 'badge',
			'path'  => '/a11y-feature-badges/',
			'class' => 'badge',
		),
	);
}

/**
 * One Section band, as the variation in src/js/editor/section-variation.js inserts it.
 *
 * SYNC: the attributes mirror that variation's `attributes` — `section` tag, full alignment,
 * the `ucf-section` marker class and a constrained layout. Change one, change both.
 *
 * @param string $style_class The composition class, `is-style-*`.
 * @param string $heading     Band heading.
 * @return string Block markup.
 */
function ucf_theme_a11y_section( $style_class, $heading ) {
	return '<!-- wp:group {"tagName":"section","align":"full","className":"ucf-section ' . $style_class . '","layout":{"type":"constrained"}} -->' . "\n"
		. '<section class="wp-block-group alignfull ucf-section ' . $style_class . '">'
		. '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2><!-- /wp:heading -->'
		. ucf_theme_a11y_probe()
		. '</section>' . "\n"
		. '<!-- /wp:group -->';
}

/**
 * Every badge tone, each in running text.
 *
 * WHY: the tones are read out of src/js/editor/badge-format.js, not listed here. That file is
 * the only list of them, and a tone added there and forgotten here would be a badge nothing
 * audits. Reading the source is the same move `ucf_theme_a11y_assert_blocks_covered()` makes
 * with `block.json`, and it fails loudly if the pattern stops matching rather than seeding an
 * empty page.
 *
 * @return string Block markup.
 */
function ucf_theme_a11y_badges() {
	$source = (string) file_get_contents( get_theme_file_path( 'src/js/editor/badge-format.js' ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own files from CLI.

	preg_match_all( "/className:\s*'(badge(?:-[a-z]+)*)'/", $source, $matches );

	$tones = array_unique( $matches[1] );

	if ( ! $tones ) {
		WP_CLI::error(
			'Found no badge tones in src/js/editor/badge-format.js. Its `className` entries have '
			. 'probably changed shape; update the pattern in ucf_theme_a11y_badges().'
		);
	}

	$markup = '';

	foreach ( $tones as $class ) {
		$markup .= '<!-- wp:paragraph -->' . "\n"
			. '<p>Running copy with a <span class="' . esc_attr( $class ) . '">' . esc_html( $class ) . '</span> badge in it.</p>' . "\n"
			. '<!-- /wp:paragraph -->' . "\n";
	}

	return $markup;
}

/**
 * One page per `ucf-wordpress-block-theme/*` pattern, each holding that pattern and nothing else.
 *
 * The registry is the list, so a pattern added to `patterns/` is covered by the next seed
 * without anyone remembering to add it here. `content` is already the rendered result: core
 * includes the pattern's PHP file and buffers its output before registering it, which is why
 * translated strings and interpolated PHP come through as markup.
 *
 * @return array<int, array<string, string>> Manifest entries.
 */
function ucf_theme_a11y_seed_patterns() {
	$entries   = array();
	$namespace = UCF_THEME_A11Y_SLUG . '/';
	$patterns  = WP_Block_Patterns_Registry::get_instance()->get_all_registered();

	foreach ( $patterns as $pattern ) {
		// UPSTREAM: two spellings in one registry. Patterns discovered from a theme's
		// `patterns/` directory carry `slug`, while those core registers itself carry only
		// `name`. Reading `slug` alone warns on every core pattern it walks past.
		$slug = (string) ( $pattern['slug'] ?? $pattern['name'] ?? '' );

		if ( 0 !== strpos( $slug, $namespace ) ) {
			continue;
		}

		$content = trim( (string) ( $pattern['content'] ?? '' ) );

		if ( '' === $content ) {
			WP_CLI::error( "Pattern {$slug} registered with empty content." );
		}

		$short = sanitize_title( substr( $slug, strlen( $namespace ) ) );

		ucf_theme_a11y_insert(
			array(
				'post_title'   => 'Pattern: ' . $pattern['title'],
				'post_name'    => 'a11y-pattern-' . $short,
				'post_content' => $content,
			)
		);

		$entries[] = array(
			'name' => $slug,
			'path' => '/a11y-pattern-' . $short . '/',
		);
	}

	/*
	 * WHY: an empty list is legitimate — `patterns/` holds no pattern yet — so the manifest
	 * accepts one. Pattern files on disk with nothing registered is not, and only this side can
	 * tell the two apart: a wrong namespace in a pattern's `Slug:` header lands it outside the
	 * filter above, and the suite would audit nothing while looking finished.
	 */
	if ( ! $entries && glob( get_theme_file_path( 'patterns' ) . '/*.php' ) ) {
		WP_CLI::error(
			"patterns/ has pattern files but none registered under {$namespace}. Check each "
			. "file's `Slug:` header is namespaced to the theme."
		);
	}

	return $entries;
}

/**
 * One page per registered block style.
 *
 * `WP_Block_Styles_Registry` holds exactly this theme's styles and nothing else — core
 * registers its own (`is-style-outline`, `is-style-rounded`) on the client, so they never reach
 * the server registry. That makes the registry a clean list of what to cover.
 *
 * What is *not* automatic is the markup: a style is a class, and a class needs a block to sit
 * on. The samples supply one representative block per block type, and a style registered
 * against a type with no sample is a hard error rather than a silent skip — so adding a style
 * to a new block type breaks the seed until someone decides what that block should look like.
 *
 * @return array<int, array<string, string>> Manifest entries.
 */
function ucf_theme_a11y_seed_variants() {
	$entries  = array();
	$samples  = ucf_theme_a11y_samples();
	$registry = WP_Block_Styles_Registry::get_instance()->get_all_registered();

	foreach ( $registry as $block_name => $styles ) {
		if ( ! isset( $samples[ $block_name ] ) ) {
			WP_CLI::error(
				"Block styles are registered for {$block_name} but tests/a11y/seed.php has no sample "
				. 'markup for it. Add one to ucf_theme_a11y_samples() so the styles get covered.'
			);
		}

		foreach ( array_keys( $styles ) as $style ) {
			$class = 'is-style-' . $style;
			$slug  = 'a11y-variant-' . sanitize_title( str_replace( '/', '-', $block_name ) . '-' . $style );

			ucf_theme_a11y_insert(
				array(
					'post_title'   => 'Variant: ' . $block_name . ' ' . $class,
					'post_name'    => $slug,
					'post_content' => str_replace( '%CLASS%', $class, $samples[ $block_name ] ),
				)
			);

			$entries[] = array(
				'name'  => $block_name . ' ' . $class,
				'path'  => '/' . $slug . '/',
				// The spec asserts this class is in the DOM before it trusts the axe run. A
				// page whose variant failed to render is a page that passes every check.
				'class' => $class,
			);
		}
	}

	return $entries;
}

/**
 * Representative markup per block type, with `%CLASS%` where the style class goes.
 *
 * Each is what `save()` produces for that block, so WordPress renders it rather than
 * recovering it. They are hand-written because there is no server-side way to ask a static
 * block what its `save()` emits; the DOM assertion in `variants.spec.js` is what catches one
 * going stale.
 *
 * Only `core/group` today, because the compositions are the theme's only block styles.
 * `core/group` gets the probe rather than a paragraph, and that is the point of the whole
 * tier: a composition sets the `--ucf-*` roles its contents read, so the same link is legible
 * in one and not in another. Only a group carrying the full range of content finds that.
 *
 * @return array<string, string> Block name to markup.
 */
function ucf_theme_a11y_samples() {
	return array(
		'core/group' => '<!-- wp:group {"className":"%CLASS%","layout":{"type":"constrained"}} -->' . "\n"
			. '<div class="wp-block-group %CLASS%">'
			. '<!-- wp:heading --><h2 class="wp-block-heading">Heading inside the composition</h2><!-- /wp:heading -->'
			. ucf_theme_a11y_probe()
			. '</div>' . "\n"
			. '<!-- /wp:group -->',
	);
}

/**
 * The contrast probe dropped inside every composition.
 *
 * One of everything that reads a `--ucf-*` role — body copy, a link in it, a subheading, a
 * list, a primary and an outline button, and each role utility from src/scss/_utilities.scss
 * — so a composition that recolors its contents is measured against all of them at once. A page holding a single
 * paragraph would pass while `.accent-text` sat unreadable inside it.
 *
 * SYNC: a role utility added to _utilities.scss that colors text belongs in this probe.
 *
 * @return string Block markup.
 */
function ucf_theme_a11y_probe() {
	return '<!-- wp:paragraph --><p>Default body copy, with <a href="/a11y-page/">a link</a> in it.</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">A subheading</h3><!-- /wp:heading -->'
		. '<!-- wp:list --><ul class="wp-block-list">'
		. '<!-- wp:list-item --><li>A list item inside the composition</li><!-- /wp:list-item -->'
		. '<!-- wp:list-item --><li>A list item with <a href="/a11y-page/">a link</a></li><!-- /wp:list-item -->'
		. '</ul><!-- /wp:list -->'
		. '<!-- wp:paragraph {"className":"accent-text"} --><p class="accent-text">Copy in the accent-text role.</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph {"className":"accent-fill"} --><p class="accent-fill">Copy in the accent-fill role.</p><!-- /wp:paragraph -->'
		. '<!-- wp:buttons --><div class="wp-block-buttons">'
		. '<!-- wp:button --><div class="wp-block-button">'
		. '<a class="wp-block-button__link wp-element-button" href="/a11y-page/">A button inside it</a>'
		. '</div><!-- /wp:button -->'
		. '<!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline">'
		. '<a class="wp-block-button__link wp-element-button" href="/a11y-page/">A secondary button</a>'
		. '</div><!-- /wp:button -->'
		. '</div><!-- /wp:buttons -->';
}

/**
 * Body copy for the route pages: two H2s, a link in running text, and the search term.
 *
 * @param string $what What the page is, echoed into the copy.
 * @return string Block markup.
 */
function ucf_theme_a11y_body_content( $what ) {
	return '<!-- wp:heading -->' . "\n"
		. '<h2 class="wp-block-heading">About ' . esc_html( $what ) . '</h2>' . "\n"
		. '<!-- /wp:heading -->' . "\n"
		. '<!-- wp:paragraph -->' . "\n"
		. '<p>Body copy for ' . esc_html( $what ) . ', covering ' . esc_html( UCF_THEME_A11Y_SEARCH_TERM )
		. ' and including <a href="/a11y-page/">a link</a> so link contrast is judged in running '
		. 'text rather than in isolation.</p>' . "\n"
		. '<!-- /wp:paragraph -->' . "\n"
		. '<!-- wp:heading -->' . "\n"
		. '<h2 class="wp-block-heading">More about ' . esc_html( $what ) . '</h2>' . "\n"
		. '<!-- /wp:heading -->' . "\n"
		. '<!-- wp:paragraph -->' . "\n"
		. '<p>The cases that come up most often, and what to do instead.</p>' . "\n"
		. '<!-- /wp:paragraph -->';
}

/**
 * Fail if a custom block ships without appearing anywhere the suite will look.
 *
 * WHY: `src/blocks/` is empty today, so this passes trivially — it is here so the first block
 * cannot land unaudited. A block in a template or pattern is covered incidentally, with nothing
 * declaring it, so a block could be added with no page rendering it and the suite would stay
 * green while covering less. This is the check that says so out loud.
 *
 * Only top-level blocks are checked: a block with a `parent` cannot appear except inside it,
 * and asserting on it separately would just be asserting twice.
 *
 * @return void
 */
function ucf_theme_a11y_assert_blocks_covered() {
	$haystack = '';

	$seeded = get_posts(
		array(
			'post_type'        => 'any',
			'post_status'      => 'any',
			'numberposts'      => -1,
			'suppress_filters' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One-off CLI check over a fixture site.
			'meta_key'         => UCF_THEME_A11Y_SEED_META,
		)
	);

	foreach ( $seeded as $post ) {
		$haystack .= $post->post_content;
	}

	// Templates and parts count as coverage: a block in `templates/page.html` renders on every
	// page the suite visits, which is stronger coverage than a page of its own.
	// UPSTREAM: `glob()` returns false on error, and `(array) false` is `[ false ]` — a
	// `file_get_contents( false )` warning. ucf_theme_a11y_glob() is the empty case without it.
	foreach ( array( 'templates', 'parts' ) as $dir ) {
		foreach ( ucf_theme_a11y_glob( get_theme_file_path( $dir ) . '/*.html' ) as $file ) {
			$haystack .= (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own files from CLI.
		}
	}

	$missing = array();

	foreach ( ucf_theme_a11y_glob( get_theme_file_path( 'src/blocks' ) . '/*/block.json' ) as $file ) {
		$meta = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading the theme's own files from CLI.

		if ( ! is_array( $meta ) || ! empty( $meta['parent'] ) ) {
			continue;
		}

		if ( false === strpos( $haystack, '<!-- wp:' . $meta['name'] ) ) {
			$missing[] = $meta['name'];
		}
	}

	if ( $missing ) {
		WP_CLI::error(
			'These blocks render on no page the accessibility suite visits: ' . implode( ', ', $missing )
			. '. Add them to ucf_theme_a11y_seed_features() in tests/a11y/seed.php.'
		);
	}
}

/**
 * `glob()`, with failure folded into "no matches".
 *
 * @param string $pattern Glob pattern.
 * @return string[] Matching paths.
 */
function ucf_theme_a11y_glob( $pattern ) {
	$files = glob( $pattern );

	return is_array( $files ) ? $files : array();
}
