<?php
/**
 * Alerts: the university-wide Alert banner.
 *
 * CONTEXT: the design system's emergency banner comes only from the UCF Alert feed, which a
 * site never authors; the notice banner is for closures and deadline changes. Both render
 * from data — today mock data (data/mock/alerts.json), eventually UCF-Alert-Plugin — so the
 * block an editor places shows whatever the feed holds, and nothing when it holds nothing.
 *
 * The shape `ucf_theme_mock_data( 'alerts' )` returns:
 *
 *     alerts  map of type ("emergency" | "notice") => { title, text, link: { label, url } }
 *
 * @package ucf-wordpress-block-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

/**
 * Register the Alert banner block.
 *
 * @return void
 */
function ucf_theme_register_alert_block() {
	register_block_type(
		'ucf/alert-banner',
		array(
			'api_version'     => 3,
			'title'           => __( 'Alert banner', 'ucf-wordpress-block-theme' ),
			'description'     => __( 'The university alert from the UCF Alert feed: emergency (red) or notice (gold). Shows nothing when there is no alert.', 'ucf-wordpress-block-theme' ),
			'category'        => 'ucf', // SYNC: UCF_THEME_BLOCK_CATEGORY in includes/blocks.php.
			'icon'            => 'warning',
			'keywords'        => array( 'emergency', 'notice', 'ucf alert' ),
			'attributes'      => array(
				'type' => array(
					'type'    => 'string',
					'enum'    => array( 'notice', 'emergency' ),
					'default' => 'notice',
				),
			),
			'supports'        => array(
				'html'  => false,
				'align' => array( 'full' ),
			),
			'render_callback' => 'ucf_theme_render_alert_banner',
		)
	);
}
add_action( 'init', 'ucf_theme_register_alert_block' );

/**
 * Render the Alert banner.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string Markup.
 */
function ucf_theme_render_alert_banner( $attributes ) {
	$type  = 'emergency' === ( $attributes['type'] ?? '' ) ? 'emergency' : 'notice';
	$alert = ucf_theme_mock_data( 'alerts' )['alerts'][ $type ] ?? null;

	if ( ! $alert ) {
		return '';
	}

	$icon = function_exists( 'wp_get_icon' ) ? wp_get_icon( 'ucf/' . ( 'emergency' === $type ? 'alert' : 'alert-bell' ) ) : '';

	return sprintf(
		'<div %s>%s</div>',
		get_block_wrapper_attributes( array( 'class' => 'ucf-alert-banner ucf-alert-banner--' . $type ) ),
		ucf_theme_alert_markup( $alert, $type, $icon )
	);
}

/**
 * The inside of a banner.
 *
 * A11Y: an emergency is `role="alert"`, announced on load; a notice is a plain region, since
 * announcing every closure on every page would train people to ignore the announcement.
 *
 * @param array<string, mixed> $alert One alert.
 * @param string               $type  "emergency" or "notice".
 * @param string               $icon  SVG markup from the icon registry, or empty.
 * @return string Markup.
 */
function ucf_theme_alert_markup( array $alert, $type, $icon ) {
	$label = 'emergency' === $type ? __( 'Emergency', 'ucf-wordpress-block-theme' ) : __( 'Notice', 'ucf-wordpress-block-theme' );

	return sprintf(
		'<div class="ucf-alert-banner__inner"%s>%s<div><h2 class="ucf-alert-banner__title">%s: %s</h2><p>%s <a href="%s">%s</a></p></div></div>',
		'emergency' === $type ? ' role="alert"' : '',
		$icon,
		esc_html( $label ),
		esc_html( $alert['title'] ),
		esc_html( $alert['text'] ),
		esc_url( $alert['link']['url'] ),
		esc_html( $alert['link']['label'] )
	);
}
