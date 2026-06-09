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

?>
<!-- wp:group {"align":"full","backgroundColor":"secondary","textColor":"inverse","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-group alignfull has-secondary-background-color has-inverse-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"dimensions":{"minHeight":"6.5rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
			<div class="wp-block-group is-layout-flex wp-block-group-is-layout-flex" style="min-height:6.5rem">
				<!-- wp:heading {"textAlign":"center","className":"display-3"} -->
				<h2 class="wp-block-heading has-text-align-center display-3"><?php esc_html_e( '60+', 'ucf-wordpress-block-theme' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->

			<!-- wp:separator {"className":"is-style-thick mx-auto"} -->
			<hr class="wp-block-separator has-alpha-channel-opacity is-style-thick mx-auto"/>
			<!-- /wp:separator -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><?php esc_html_e( 'For 60+ years, UCF has partnered with NASA to power space exploration and technology', 'ucf-wordpress-block-theme' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"dimensions":{"minHeight":"6.5rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
			<div class="wp-block-group is-layout-flex wp-block-group-is-layout-flex" style="min-height:6.5rem">
				<!-- wp:heading {"textAlign":"center","className":"display-3"} -->
				<h2 class="wp-block-heading has-text-align-center display-3"><?php esc_html_e( 'No. 1', 'ucf-wordpress-block-theme' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->

			<!-- wp:separator {"className":"is-style-thick mx-auto"} -->
			<hr class="wp-block-separator has-alpha-channel-opacity is-style-thick mx-auto"/>
			<!-- /wp:separator -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><?php esc_html_e( 'Supplier of talent to the nation\'s aerospace and defense industries for six years in a row', 'ucf-wordpress-block-theme' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
			<p class="has-text-align-center has-sm-font-size"><em><?php esc_html_e( 'Aviation Week Network', 'ucf-wordpress-block-theme' ); ?></em></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"style":{"dimensions":{"minHeight":"6.5rem"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
			<div class="wp-block-group is-layout-flex wp-block-group-is-layout-flex" style="min-height:6.5rem">
				<!-- wp:heading {"textAlign":"center","className":"display-3"} -->
				<h2 class="wp-block-heading has-text-align-center display-3"><?php esc_html_e( 'Top 10', 'ucf-wordpress-block-theme' ); ?></h2>
				<!-- /wp:heading -->
			</div>
			<!-- /wp:group -->

			<!-- wp:separator {"className":"is-style-thick mx-auto"} -->
			<hr class="wp-block-separator has-alpha-channel-opacity is-style-thick mx-auto"/>
			<!-- /wp:separator -->

			<!-- wp:paragraph {"align":"center"} -->
			<p class="has-text-align-center"><?php esc_html_e( 'UCF is a top ten most innovative public university in the nation', 'ucf-wordpress-block-theme' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
			<p class="has-text-align-center has-sm-font-size"><em><?php esc_html_e( 'U.S. News & World Report', 'ucf-wordpress-block-theme' ); ?></em></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->