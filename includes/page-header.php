<?php
/**
 * Page header (masthead) dynamic block.
 *
 * Renders a full-width header: an art-directed <picture> background driven by
 * the three breakpoint images, a gradient overlay for legibility, the primary
 * navigation spread across the top, and the overlaid title/subtitle.
 *
 * Implemented as a server-rendered block (no inner blocks) so it can be dropped
 * into a block template — see templates/page.html — while reading the page-header
 * post meta defined in includes/page-fields.php at render time.
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the "primary" nav menu location used in the masthead bar.
 */
function ucf_block_theme_register_menus() {
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'ucf-wordpress-block-theme' ),
		)
	);
}
add_action( 'after_setup_theme', 'ucf_block_theme_register_menus' );

/**
 * Register the ucf/page-header dynamic block + its editor preview script.
 */
function ucf_block_theme_register_page_header_block() {
	$script_rel  = 'assets/js/page-header-block.js';
	$script_path = get_theme_file_path( $script_rel );

	wp_register_script(
		'ucf-page-header-block',
		get_theme_file_uri( $script_rel ),
		array( 'wp-blocks', 'wp-element', 'wp-server-side-render', 'wp-i18n' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : false,
		true
	);

	register_block_type(
		'ucf/page-header',
		array(
			'api_version'     => 3,
			'render_callback' => 'ucf_block_theme_render_page_header',
			'editor_script'   => 'ucf-page-header-block',
		)
	);
}
add_action( 'init', 'ucf_block_theme_register_page_header_block' );

/**
 * Read a page-header meta value (see includes/page-fields.php).
 *
 * @param string $name    Meta key.
 * @param int    $post_id Post ID.
 * @return mixed Meta value, or '' when empty.
 */
function ucf_block_theme_field( $name, $post_id ) {
	$value = get_post_meta( $post_id, $name, true );
	return ( false === $value || null === $value ) ? '' : $value;
}

/**
 * Build the art-directed <picture> for the masthead background.
 *
 * Breakpoints match WordPress's editor device previews: Mobile <= 360px,
 * Tablet <= 780px, Desktop otherwise. Each smaller image falls back to the
 * next size up so only the desktop image is required.
 *
 * @param int $post_id Post ID.
 * @return string Picture HTML, or '' when no image is set.
 */
function ucf_block_theme_header_picture( $post_id ) {
	$lg = ucf_block_theme_field( 'hero_image_lg', $post_id );
	$md = ucf_block_theme_field( 'hero_image_md', $post_id );
	$sm = ucf_block_theme_field( 'hero_image_sm', $post_id );

	// Cascade: mobile -> tablet -> desktop.
	$md = $md ? $md : $lg;
	$sm = $sm ? $sm : $md;

	if ( ! $lg ) {
		return '';
	}

	$lg_url = wp_get_attachment_image_url( $lg, 'full' );
	$md_url = wp_get_attachment_image_url( $md, 'full' );
	$sm_url = wp_get_attachment_image_url( $sm, 'full' );
	$alt    = get_post_meta( $lg, '_wp_attachment_image_alt', true );

	$sources = '';
	if ( $sm_url ) {
		$sources .= sprintf(
			'<source media="(max-width: 360px)" srcset="%s">',
			esc_url( $sm_url )
		);
	}
	if ( $md_url ) {
		$sources .= sprintf(
			'<source media="(max-width: 780px)" srcset="%s">',
			esc_url( $md_url )
		);
	}

	return sprintf(
		'<picture class="ucf-page-header__bg">%s<img src="%s" alt="%s" loading="eager" decoding="async"></picture>',
		$sources,
		esc_url( $lg_url ),
		esc_attr( $alt )
	);
}

/**
 * Render the masthead.
 *
 * @param array  $attributes Block attributes (unused).
 * @param string $content    Inner content (unused).
 * @param object $block      Block instance (unused).
 * @return string Masthead HTML.
 */
function ucf_block_theme_render_page_header( $attributes = array(), $content = '', $block = null ) {
	$post_id = get_the_ID();

	// ServerSideRender previews (Site Editor) hit this over REST with no queried
	// page. Show a structural placeholder instead of an empty block.
	$is_preview = defined( 'REST_REQUEST' ) && REST_REQUEST;
	if ( ! $post_id && ! $is_preview ) {
		return '';
	}

	// Title: meta override, falling back to the post/page title (or a placeholder
	// in the editor when there's no page context).
	$title = $post_id ? ucf_block_theme_field( 'hero_title', $post_id ) : '';
	if ( '' === $title ) {
		$title = $post_id ? get_the_title( $post_id ) : __( 'Page title', 'ucf-wordpress-block-theme' );
	}
	$subtitle = $post_id ? ucf_block_theme_field( 'hero_subtitle', $post_id ) : '';

	$picture = $post_id ? ucf_block_theme_header_picture( $post_id ) : '';

	// Primary navigation, output as a list-unstyled bar.
	$nav = '';
	if ( has_nav_menu( 'primary' ) ) {
		$nav = wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'list-unstyled ucf-page-header__menu',
				'echo'           => false,
				'depth'          => 1,
			)
		);
	}

	ob_start();
	?>
	<header class="ucf-page-header<?php echo $picture ? ' has-image' : ''; ?>">
		<?php echo $picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts. ?>
		<div class="ucf-page-header__overlay"></div>

		<div class="ucf-page-header__bar">
			<nav class="ucf-page-header__nav" aria-label="<?php esc_attr_e( 'Primary', 'ucf-wordpress-block-theme' ); ?>">
				<?php echo $nav; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nav_menu output. ?>
			</nav>
		</div>

		<div class="ucf-page-header__content">
			<div class="ucf-page-header__inner">
				<h1 class="ucf-page-header__title"><?php echo esc_html( $title ); ?></h1>
				<?php if ( '' !== $subtitle ) : ?>
					<p class="ucf-page-header__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<?php
	return ob_get_clean();
}