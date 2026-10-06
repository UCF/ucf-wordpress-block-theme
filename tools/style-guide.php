<?php
/**
 * Build the "Style guide" page: every block style, data block and pattern the theme ships.
 *
 * Run through WP-CLI against a site with the theme active:
 *
 *     wp eval-file wp-content/themes/ucf-wordpress-block-theme/tools/style-guide.php
 *
 * The WordPress Playground blueprint runs it too (blueprint.json). Re-running is safe: the page
 * is found by its slug and replaced, not duplicated.
 *
 * WHY patterns are inserted by reference (`core/pattern`), not copied. The page then always
 * shows the patterns as they are in patterns/ — edit one and the style guide changes with it —
 * and a pattern added later appears the next time this runs, read out of the registry.
 *
 * WHY the photographs are hotlinked from ucf.edu: the page has to work on a fresh Playground
 * site with an empty media library.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'WP_CLI' ) && ! defined( 'UCF_THEME_STYLE_GUIDE' ) ) {
	exit; // CLI or the Playground blueprint only.
}

/*
 * UPSTREAM: KSES strips HTML comments for a user without `unfiltered_html`, and a block is an
 * HTML comment. WP-CLI runs with no user, so the page would be saved with its blocks gone —
 * same reason as in tests/a11y/seed.php.
 */
kses_remove_filters();

const UCF_THEME_STYLE_GUIDE_SLUG = 'style-guide';

/** A ucf.edu photograph, for the image samples. */
const UCF_THEME_STYLE_GUIDE_PHOTO = 'https://www.ucf.edu/wp-content/blogs.dir/16/files/2020/10/1310MCA579-Main-site-development-academics-excellence-800x500.jpg';

/**
 * A heading with an anchor, for the jump list.
 *
 * @param string $text   Heading text.
 * @param string $anchor Its id.
 * @return string Block markup.
 */
function ucf_theme_style_guide_heading( $text, $anchor ) {
	return sprintf(
		'<!-- wp:heading {"anchor":"%1$s"} --><h2 class="wp-block-heading" id="%1$s">%2$s</h2><!-- /wp:heading -->',
		esc_attr( $anchor ),
		esc_html( $text )
	);
}

/**
 * A mono label naming what follows, so every sample says what it is.
 *
 * @param string $text Label.
 * @return string Block markup.
 */
function ucf_theme_style_guide_label( $text ) {
	return '<!-- wp:paragraph {"className":"ucf-marker"} --><p class="ucf-marker">' . esc_html( $text ) . '</p><!-- /wp:paragraph -->';
}

/**
 * A band of page, holding a section of the guide.
 *
 * @param string $composition Composition slug.
 * @param string $inner       Block markup.
 * @return string Block markup.
 */
function ucf_theme_style_guide_band( $composition, $inner ) {
	$class = 'ucf-section is-style-' . $composition;

	return '<!-- wp:group {"tagName":"section","align":"full","className":"' . $class . '","layout":{"type":"constrained","contentSize":"1240px"}} -->'
		. '<section class="wp-block-group alignfull ' . $class . '">' . $inner . '</section><!-- /wp:group -->';
}

/**
 * An image block at a ratio.
 *
 * @param string $ratio   Aspect ratio, as theme.json names it.
 * @param string $caption Caption, may hold the credit format.
 * @return string Block markup.
 */
function ucf_theme_style_guide_image( $ratio, $caption ) {
	return '<!-- wp:image {"aspectRatio":"' . $ratio . '","scale":"cover"} --><figure class="wp-block-image">'
		. '<img src="' . esc_url( UCF_THEME_STYLE_GUIDE_PHOTO ) . '" alt="A student and a researcher examining a field instrument beside a lake on campus." style="aspect-ratio:' . $ratio . ';object-fit:cover"/>'
		. '<figcaption class="wp-element-caption">' . $caption . '</figcaption></figure><!-- /wp:image -->';
}

/**
 * The compositions: the same content on each of the four fields.
 *
 * @return string Block markup.
 */
function ucf_theme_style_guide_compositions() {
	$sample = '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">A heading on this field</h3><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>Body copy, with <a href="#compositions">a link</a> in it, and <mark>a highlighted phrase</mark>.</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph {"className":"is-style-data"} --><p class="is-style-data">Source: a data line, Oct. 1, 2026</p><!-- /wp:paragraph -->'
		. '<!-- wp:buttons --><div class="wp-block-buttons">'
		. '<!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#compositions">Primary</a></div><!-- /wp:button -->'
		. '<!-- wp:button {"className":"is-style-outline"} --><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#compositions">Outline</a></div><!-- /wp:button -->'
		. '<!-- wp:button {"className":"is-style-text"} --><div class="wp-block-button is-style-text"><a class="wp-block-button__link wp-element-button" href="#compositions">Text</a></div><!-- /wp:button -->'
		. '</div><!-- /wp:buttons -->';

	$html = '';

	foreach ( array(
		'light' => 'Light',
		'alt'   => 'Light Gray',
		'dark'  => 'Dark',
		'gold'  => 'Bold Gold',
	) as $slug => $name ) {
		$html .= ucf_theme_style_guide_band( $slug, ucf_theme_style_guide_label( 'Composition · ' . $name . ' · is-style-' . $slug ) . $sample );
	}

	return $html;
}

/**
 * Type, lists, buttons, quotes, table, disclosures, media and icons.
 *
 * @return string Block markup.
 */
function ucf_theme_style_guide_elements() {
	$render_items = static function ( array $items ) {
		$html = '';
		foreach ( $items as $item ) {
			$html .= '<!-- wp:list-item --><li>' . $item . '</li><!-- /wp:list-item -->';
		}
		return $html;
	};

	$list = static function ( $classes, array $items, $ordered = false ) use ( $render_items ) {
		$tag   = $ordered ? 'ol' : 'ul';
		$attrs = array_filter(
			array(
				'ordered'   => $ordered ? true : null,
				'className' => $classes ? $classes : null,
			)
		);
		return '<!-- wp:list' . ( $attrs ? ' ' . wp_json_encode( $attrs ) : '' ) . ' --><' . $tag . ' class="wp-block-list' . ( $classes ? ' ' . $classes : '' ) . '">'
			. $render_items( $items ) . '</' . $tag . '><!-- /wp:list -->';
	};

	$html = ucf_theme_style_guide_heading( 'Type', 'type' )
		. ucf_theme_style_guide_label( 'Heading levels' )
		. '<!-- wp:heading --><h2 class="wp-block-heading">H2 · What do graduates do?</h2><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">H3 · Research experience</h3><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":4} --><h4 class="wp-block-heading">H4 · How long does the degree take?</h4><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":5} --><h5 class="wp-block-heading">H5 · Step 2: Send your transcripts</h5><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":6} --><h6 class="wp-block-heading">H6 · Related programs</h6><!-- /wp:heading -->'
		. ucf_theme_style_guide_label( 'Heading style · Sans, on an H3' )
		. '<!-- wp:heading {"level":3,"className":"is-style-sans","fontSize":"heading-4"} --><h3 class="wp-block-heading is-style-sans has-heading-4-font-size">A card title: an H3 that looks like an H4</h3><!-- /wp:heading -->'
		. ucf_theme_style_guide_label( 'Font sizes · Display, Lead, Body, Small, Data' )
		. '<!-- wp:paragraph {"fontSize":"display-1"} --><p class="has-display-1-font-size">Display</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph {"fontSize":"lead"} --><p class="has-lead-font-size">Lead: the first paragraph under a title, once per section.</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph --><p>Body: answer the question in the first sentence, then give the detail. Links are always <a href="#type">underlined</a>.</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph {"fontSize":"small"} --><p class="has-small-font-size">Small: help text and footnotes, never below 14px.</p><!-- /wp:paragraph -->'
		. ucf_theme_style_guide_label( 'Paragraph styles · Data, Sample data note' )
		. '<!-- wp:paragraph {"className":"is-style-data"} --><p class="is-style-data">Source: <a href="https://www.ucf.edu/about-ucf/facts/">UCF Facts</a>, fall 2025</p><!-- /wp:paragraph -->'
		. '<!-- wp:paragraph {"className":"is-style-sample"} --><p class="is-style-sample">Sample data for review. Replace with verified data before publishing.</p><!-- /wp:paragraph -->'
		. ucf_theme_style_guide_label( 'Badge formats' )
		. '<!-- wp:paragraph --><p><span class="badge">Default</span> <span class="badge-gold">Gold</span> <span class="badge-blue">Blue</span> <span class="badge-success">Success</span> <span class="badge-danger">Danger</span> <span class="badge-dark">Dark</span> <span class="badge-inverse">Inverse</span></p><!-- /wp:paragraph -->'
		. '<!-- wp:separator --><hr class="wp-block-separator has-alpha-channel-opacity"/><!-- /wp:separator -->';

	$html .= ucf_theme_style_guide_heading( 'Lists', 'lists' )
		. ucf_theme_style_guide_label( 'Default' ) . $list( '', array( 'A gold square marks each item.', 'Short items, parallel in grammar.' ) )
		. ucf_theme_style_guide_label( 'Ordered' ) . $list( '', array( 'Numbers only when order matters.', 'Oswald numerals.' ), true )
		. ucf_theme_style_guide_label( 'Checklist · is-style-check' ) . $list( 'is-style-check', array( 'A requirement the reader must meet.', 'Another one.' ) )
		. ucf_theme_style_guide_label( 'Divided · is-style-divided' ) . $list( 'is-style-divided', array( '<a href="#lists">A link list item</a>', '<a href="#lists">Another</a>' ) )
		. ucf_theme_style_guide_label( 'Tags · is-style-tags' ) . $list( 'is-style-tags', array( 'Optics', 'Imaging', 'Sensors' ) )
		. ucf_theme_style_guide_label( 'Definitions · is-style-definitions' ) . $list( 'is-style-definitions', array( '<strong>Office hours</strong> Weekdays, 8 a.m. to 5 p.m.', '<strong>Phone</strong> 407-823-2000' ) );

	$button = static function ( $label, $classes = '', $size = '' ) {
		$attrs = array_filter(
			array(
				'className' => $classes ? $classes : null,
				'fontSize'  => $size ? $size : null,
			)
		);
		return '<!-- wp:button' . ( $attrs ? ' ' . wp_json_encode( $attrs ) : '' ) . ' --><div class="wp-block-button' . ( $classes ? ' ' . $classes : '' ) . '">'
			. '<a class="wp-block-button__link' . ( $size ? ' has-' . $size . '-font-size has-custom-font-size' : '' ) . ' wp-element-button" href="#buttons">' . esc_html( $label ) . '</a></div><!-- /wp:button -->';
	};

	$html .= ucf_theme_style_guide_heading( 'Buttons', 'buttons' )
		. ucf_theme_style_guide_label( 'Primary · Outline · Text' )
		. '<!-- wp:buttons --><div class="wp-block-buttons">' . $button( 'Start your application' ) . $button( 'Request information', 'is-style-outline' ) . $button( 'See all programs', 'is-style-text' ) . '</div><!-- /wp:buttons -->'
		. ucf_theme_style_guide_label( 'Sizes · Small (font size Small) · Default · Large (font size Body)' )
		. '<!-- wp:buttons --><div class="wp-block-buttons">' . $button( 'Small', '', 'small' ) . $button( 'Default' ) . $button( 'Large', '', 'body' ) . '</div><!-- /wp:buttons -->';

	$html .= ucf_theme_style_guide_heading( 'Quotes', 'quotes' )
		. ucf_theme_style_guide_label( 'Quote' )
		. '<!-- wp:quote --><blockquote class="wp-block-quote"><!-- wp:paragraph --><p>A testimonial inside running text, in a person’s own words.</p><!-- /wp:paragraph --><cite>Sample Name, Class of 2026</cite></blockquote><!-- /wp:quote -->'
		. ucf_theme_style_guide_label( 'Pullquote' )
		. '<!-- wp:pullquote --><figure class="wp-block-pullquote"><blockquote><p>If your seal survives a week in our tray, it might survive a year up there.</p><cite>Sample Name, Ph.D., Professor</cite></blockquote></figure><!-- /wp:pullquote -->';

	$html .= ucf_theme_style_guide_heading( 'Table', 'table' )
		. '<!-- wp:table --><figure class="wp-block-table"><table><thead><tr><th>Milestone</th><th class="has-text-align-right" data-align="right">Date</th></tr></thead><tbody>'
		. '<tr><td>Application opens</td><td class="has-text-align-right" data-align="right">Aug. 1, 2026</td></tr>'
		. '<tr><td>Priority deadline</td><td class="has-text-align-right" data-align="right">Nov. 1, 2026</td></tr>'
		. '</tbody></table><figcaption class="wp-element-caption">Caption on top · right-aligned columns turn mono</figcaption></figure><!-- /wp:table -->';

	$html .= ucf_theme_style_guide_heading( 'Disclosures', 'disclosures' )
		. ucf_theme_style_guide_label( 'Details' )
		. '<!-- wp:details --><details class="wp-block-details"><summary>A reference panel</summary><!-- wp:paragraph --><p>Content that makes sense on its own. It stays in the page when closed.</p><!-- /wp:paragraph --></details><!-- /wp:details -->'
		. '<!-- wp:details --><details class="wp-block-details"><summary>A second panel</summary><!-- wp:paragraph --><p>Stacked panels share one rule.</p><!-- /wp:paragraph --></details><!-- /wp:details -->'
		. ucf_theme_style_guide_label( 'Accordion · see the FAQ pattern below' );

	$html .= ucf_theme_style_guide_heading( 'Media', 'media' )
		. ucf_theme_style_guide_label( 'Aspect ratios · 3:2 · 1:1 · 4:5 · 16:9 · 21:9' )
		. '<!-- wp:group {"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"12rem"}} --><div class="wp-block-group">'
		. ucf_theme_style_guide_image( '3/2', '3:2' ) . ucf_theme_style_guide_image( '1', '1:1' ) . ucf_theme_style_guide_image( '4/5', '4:5' )
		. '</div><!-- /wp:group -->'
		. ucf_theme_style_guide_image( '16/9', '16:9, with a credit<span class="ucf-credit">Photo: University of Central Florida</span>' )
		. ucf_theme_style_guide_image( '21/9', '21:9' )
		. ucf_theme_style_guide_label( 'Media & Text' )
		. '<!-- wp:media-text {"mediaType":"image"} --><div class="wp-block-media-text is-stacked-on-mobile"><figure class="wp-block-media-text__media"><img src="' . esc_url( UCF_THEME_STYLE_GUIDE_PHOTO ) . '" alt="A student and a researcher examining a field instrument beside a lake on campus."/></figure><div class="wp-block-media-text__content">'
		. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">One idea, one photo</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Stacks below 1024px with the text first.</p><!-- /wp:paragraph --></div></div><!-- /wp:media-text -->';

	$icons = '';
	foreach ( array_keys( function_exists( 'ucf_theme_icons' ) ? ucf_theme_icons() : array() ) as $name ) {
		$icons .= '<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} --><div class="wp-block-group">'
			. '<!-- wp:icon {"icon":"ucf/' . $name . '"} /-->'
			. '<!-- wp:paragraph {"className":"is-style-data"} --><p class="is-style-data">' . esc_html( $name ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->';
	}

	$html .= ucf_theme_style_guide_heading( 'Icons', 'icons' )
		. ucf_theme_style_guide_label( 'The ucf collection, in the core Icon block' )
		. '<!-- wp:group {"layout":{"type":"grid","minimumColumnWidth":"7rem"}} --><div class="wp-block-group">' . $icons . '</div><!-- /wp:group -->';

	return ucf_theme_style_guide_band( 'light', $html );
}

/**
 * The data blocks, each with what it reads.
 *
 * @return string Block markup.
 */
function ucf_theme_style_guide_data_blocks() {
	$blocks = array(
		'Alert banner · notice'    => '<!-- wp:ucf/alert-banner {"type":"notice"} /-->',
		'Alert banner · emergency' => '<!-- wp:ucf/alert-banner {"type":"emergency"} /-->',
		'Breadcrumbs (core)'       => '<!-- wp:breadcrumbs /-->',
		'Facts'                    => '<!-- wp:ucf/facts {"program":"computer-science-bs"} /-->',
		'Profile'                  => '<!-- wp:ucf/profile {"person":"sample-cs-professor"} /-->',
		'Profile · feature'        => '<!-- wp:ucf/profile {"person":"sample-researcher","feature":true} /-->',
		'Events'                   => '<!-- wp:ucf/events {"count":3} /-->',
		'Provenance'               => '<!-- wp:ucf/provenance /-->',
		'Program finder'           => '<!-- wp:ucf/program-finder /-->',
	);

	$html = ucf_theme_style_guide_heading( 'Data blocks', 'data-blocks' )
		. '<!-- wp:paragraph --><p>Server-rendered from data the theme does not own. Today that is mock data in <code>data/mock/</code>, marked on the page as sample.</p><!-- /wp:paragraph -->';

	foreach ( $blocks as $label => $markup ) {
		$html .= ucf_theme_style_guide_label( $label ) . $markup;
	}

	return ucf_theme_style_guide_band( 'alt', $html );
}

/**
 * Every theme pattern, by reference, read from the registry.
 *
 * WHY page-level patterns are linked, not inserted: each is a whole page with its own H1, and
 * three more H1s would turn this page's outline into nonsense.
 *
 * @return string Block markup.
 */
function ucf_theme_style_guide_patterns() {
	$prefix   = 'ucf-wordpress-block-theme/';
	$patterns = array();
	$pages    = array();

	foreach ( WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $pattern ) {
		if ( 0 !== strpos( $pattern['name'], $prefix ) ) {
			continue;
		}

		if ( ! empty( $pattern['blockTypes'] ) && in_array( 'core/post-content', (array) $pattern['blockTypes'], true ) ) {
			$pages[] = $pattern;
			continue;
		}

		$patterns[] = $pattern;
	}

	usort(
		$patterns,
		static function ( $a, $b ) {
			return strcmp( $a['title'], $b['title'] );
		}
	);

	$list = '';
	foreach ( $pages as $page ) {
		$list .= '<!-- wp:list-item --><li><strong>' . esc_html( $page['title'] ) . '</strong> ' . esc_html( $page['description'] ?? '' ) . '</li><!-- /wp:list-item -->';
	}

	$html = ucf_theme_style_guide_band(
		'light',
		ucf_theme_style_guide_heading( 'Patterns', 'patterns' )
			. '<!-- wp:paragraph --><p>Every pattern in the UCF category, inserted live — each is exactly what an author gets from the inserter. ' . count( $patterns ) . ' patterns, alphabetical.</p><!-- /wp:paragraph -->'
			. ucf_theme_style_guide_label( 'Page patterns, offered when a new page or post is created' )
			. '<!-- wp:list {"className":"is-style-divided"} --><ul class="wp-block-list is-style-divided">' . $list . '</ul><!-- /wp:list -->'
	);

	foreach ( $patterns as $pattern ) {
		// WHY a thin labeled strip before each: many patterns are full-width bands themselves, and
		// without a label two adjacent ones read as one. Thin, not a Section band — 28 bands of
		// section padding around one line each would bury the patterns.
		$html .= '<!-- wp:group {"align":"full","className":"is-style-alt","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var(--wp--custom--gutter)","right":"var(--wp--custom--gutter)"}}},"layout":{"type":"constrained","contentSize":"1240px"}} -->'
			. '<div class="wp-block-group alignfull is-style-alt" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--custom--gutter);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--custom--gutter)">'
			. ucf_theme_style_guide_label( 'Pattern · ' . $pattern['title'] . ' · ' . substr( $pattern['name'], strlen( $prefix ) ) )
			. '</div><!-- /wp:group -->';
		$html .= '<!-- wp:pattern {"slug":"' . esc_attr( $pattern['name'] ) . '"} /-->';
	}

	return $html;
}

/**
 * The whole page.
 *
 * @return string Block markup.
 */
function ucf_theme_style_guide_content() {
	$jump = '';
	foreach ( array(
		'compositions' => 'Compositions',
		'type'         => 'Type',
		'lists'        => 'Lists',
		'buttons'      => 'Buttons',
		'quotes'       => 'Quotes',
		'table'        => 'Table',
		'disclosures'  => 'Disclosures',
		'media'        => 'Media',
		'icons'        => 'Icons',
		'data-blocks'  => 'Data blocks',
		'patterns'     => 'Patterns',
	) as $anchor => $title ) {
		$jump .= '<!-- wp:list-item --><li><a href="#' . $anchor . '">' . $title . '</a></li><!-- /wp:list-item -->';
	}

	$header = '<!-- wp:group {"tagName":"header","align":"full","className":"ucf-section ucf-section--tight is-style-light ucf-page-header","layout":{"type":"constrained","contentSize":"1240px"}} -->'
		. '<header class="wp-block-group alignfull ucf-section ucf-section--tight is-style-light ucf-page-header">'
		. '<!-- wp:heading {"level":1} --><h1 class="wp-block-heading">Style guide</h1><!-- /wp:heading -->'
		. '<!-- wp:paragraph {"fontSize":"lead"} --><p class="has-lead-font-size">Every block style, data block and pattern in the theme, on one page. Built by tools/style-guide.php.</p><!-- /wp:paragraph -->'
		. '<!-- wp:group {"className":"ucf-toc","layout":{"type":"default"}} --><div class="wp-block-group ucf-toc"><!-- wp:heading {"className":"is-style-sans","fontSize":"heading-6"} --><h2 class="wp-block-heading is-style-sans has-heading-6-font-size">On this page</h2><!-- /wp:heading -->'
		. '<!-- wp:list --><ul class="wp-block-list">' . $jump . '</ul><!-- /wp:list --></div><!-- /wp:group -->'
		. '</header><!-- /wp:group -->';

	return $header
		. ucf_theme_style_guide_band( 'light', ucf_theme_style_guide_heading( 'Compositions', 'compositions' ) . '<!-- wp:paragraph --><p>The same content on each of the four fields. Every role re-resolves; nothing in the content names a color.</p><!-- /wp:paragraph -->' )
		. ucf_theme_style_guide_compositions()
		. ucf_theme_style_guide_elements()
		. ucf_theme_style_guide_data_blocks()
		. ucf_theme_style_guide_patterns();
}

/**
 * Create or replace the page.
 *
 * @return int The page ID.
 */
function ucf_theme_style_guide_upsert() {
	$existing = get_page_by_path( UCF_THEME_STYLE_GUIDE_SLUG );

	$id = wp_insert_post(
		array(
			'ID'           => $existing ? $existing->ID : 0,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Style guide',
			'post_name'    => UCF_THEME_STYLE_GUIDE_SLUG,
			// UPSTREAM: wp_insert_post() unslashes; slashing keeps escaped quotes in attributes.
			'post_content' => wp_slash( ucf_theme_style_guide_content() ),
		),
		true
	);

	if ( is_wp_error( $id ) ) {
		if ( defined( 'WP_CLI' ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		return 0;
	}

	update_post_meta( $id, '_wp_page_template', 'page-landing' );

	if ( defined( 'WP_CLI' ) ) {
		WP_CLI::success( 'Style guide: ' . get_permalink( $id ) );
	}

	return $id;
}

ucf_theme_style_guide_upsert();
