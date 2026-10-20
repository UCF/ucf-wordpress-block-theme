<?php
/**
 * Title: Notice: error
 * Slug: ucf-wordpress-block-theme/notice-danger
 * Categories: ucf
 * Description: Says what went wrong and how to fix it. Pair with a form; never use for marketing.
 * Keywords: notice, alert, message
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-notice ucf-notice--danger","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group ucf-notice ucf-notice--danger"><!-- wp:icon {"icon":"ucf/error"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":3,"className":"is-style-sans","fontSize":"body"} -->
<h3 class="wp-block-heading is-style-sans has-body-font-size">Error</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Two fields need attention. Each one is marked below with what to change.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
