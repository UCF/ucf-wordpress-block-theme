<?php
/**
 * Title: Feature Row
 * Slug: ucf/feature-row
 * Categories: ucf-blocks
 * Description: Three centered columns — each a logo / display stat, a short thick rule, and supporting text. The rules stay aligned because each logo sits in a fixed-height band (core Group "min height"). Black band by default; for a light version, change the outer group's background to default and its text color to a dark color — the rule follows the text color automatically. Swap a heading for an image to use a Font Awesome / Noun Project icon.
 * Keywords: feature, stats, columns, three, highlights, facts
 *
 * @package ucf-wordpress-block-theme
 */

$items = array(
	array(
		'logo' => esc_html__( '60+', 'ucf-wordpress-block-theme' ),
		'text' => esc_html__( 'For 60+ years, UCF has partnered with NASA to power space exploration and technology', 'ucf-wordpress-block-theme' ),
		'cite' => '',
	),
	array(
		'logo' => esc_html__( 'No. 1', 'ucf-wordpress-block-theme' ),
		'text' => esc_html__( 'Supplier of talent to the nation\'s aerospace and defense industries for six years in a row', 'ucf-wordpress-block-theme' ),
		'cite' => esc_html__( 'Aviation Week Network', 'ucf-wordpress-block-theme' ),
	),
	array(
		'logo' => esc_html__( 'Top 10', 'ucf-wordpress-block-theme' ),
		'text' => esc_html__( 'UCF is a top ten most innovative public university in the nation', 'ucf-wordpress-block-theme' ),
		'cite' => esc_html__( 'U.S. News & World Report', 'ucf-wordpress-block-theme' ),
	),
);

$columns = '';
foreach ( $items as $item ) {
	$logo = $item['logo'];
	$text = $item['text'];

	$cite = '';
	if ( '' !== $item['cite'] ) {
		$cite_text = $item['cite'];
		$cite      = <<<HTML
		<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
		<p class="has-text-align-center has-sm-font-size"><em>$cite_text</em></p>
		<!-- /wp:paragraph -->
HTML;
	}

	$columns .= <<<HTML
	<!-- wp:column -->
	<div class="wp-block-column">
		<!-- wp:group {"style":{"dimensions":{"minHeight":"6.5rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
		<div class="wp-block-group is-layout-flex wp-block-group-is-layout-flex" style="min-height:6.5rem">
			<!-- wp:heading {"textAlign":"center","className":"display-3"} -->
			<h2 class="wp-block-heading has-text-align-center display-3">$logo</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:separator {"className":"is-style-thick"} -->
		<hr class="wp-block-separator has-alpha-channel-opacity is-style-thick"/>
		<!-- /wp:separator -->

		<!-- wp:paragraph {"align":"center"} -->
		<p class="has-text-align-center">$text</p>
		<!-- /wp:paragraph -->
$cite
	</div>
	<!-- /wp:column -->
HTML;
}

?>
<!-- wp:group {"align":"full","backgroundColor":"secondary","textColor":"inverse","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-group alignfull has-secondary-background-color has-inverse-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<?php echo $columns; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composed from escaped strings above. ?>
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->