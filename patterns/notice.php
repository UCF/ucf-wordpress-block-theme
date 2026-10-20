<?php
/**
 * Title: Notice
 * Slug: ucf-wordpress-block-theme/notice
 * Categories: ucf
 * Description: A short notice with an icon and a word. Interrupt only when something changes what the reader should do.
 * Keywords: notice, alert, message
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-notice","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
<div class="wp-block-group ucf-notice"><!-- wp:icon {"icon":"ucf/info"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":3,"className":"is-style-sans","fontSize":"body"} -->
<h3 class="wp-block-heading is-style-sans has-body-font-size">Note</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>One or two sentences. Remove the notice the day it stops being true.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
