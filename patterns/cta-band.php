<?php
/**
 * Title: Call to Action Band
 * Slug: ucf/cta-band
 * Categories: ucf-blocks
 * Description: A full-width black band with a centered heading, supporting text, and a button. Use it to drive a single action.
 * Keywords: cta, call to action, band, banner, button
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"secondary","textColor":"inverse","layout":{"type":"constrained"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->
<div class="wp-block-group alignfull has-secondary-background-color has-inverse-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:heading {"textAlign":"center","textColor":"inverse"} -->
	<h2 class="wp-block-heading has-text-align-center has-inverse-color has-text-color"><?php esc_html_e( 'Ready to take the next step?', 'ucf-wordpress-block-theme' ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center","textColor":"inverse"} -->
	<p class="has-text-align-center has-inverse-color has-text-color"><?php esc_html_e( 'Add a sentence of supporting context, then point visitors to the action you want them to take.', 'ucf-wordpress-block-theme' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#"><?php esc_html_e( 'Get Started', 'ucf-wordpress-block-theme' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->