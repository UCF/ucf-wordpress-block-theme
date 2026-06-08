<?php
/**
 * Title: Alert Callout
 * Slug: ucf/alert-callout
 * Categories: ucf-blocks
 * Description: A static, non-dismissible alert box with a colored accent. Swap the block style (Alert: Gold/Success/Info/Warning/Danger) to change the tone.
 * Keywords: alert, callout, notice, message, banner
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"is-style-alert-info","layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-alert-info">
	<!-- wp:heading {"level":3,"fontSize":"lg"} -->
	<h3 class="wp-block-heading has-lg-font-size"><?php esc_html_e( 'Heads up', 'ucf-wordpress-block-theme' ); ?></h3>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p><?php esc_html_e( 'Use this callout to draw attention to an important message. Choose a block style to match the tone of the notice.', 'ucf-wordpress-block-theme' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->