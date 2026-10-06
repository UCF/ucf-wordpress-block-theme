<?php
/**
 * Title: Stats band
 * Slug: ucf-wordpress-block-theme/stats-band
 * Categories: featured
 * Description: Up to three stat statements on a dark band, each with its source and date.
 * Keywords: stats, facts, numbers, band
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ucf-section is-style-dark","layout":{"type":"constrained","contentSize":"1240px"}} -->
<section class="wp-block-group alignfull ucf-section is-style-dark"><!-- wp:heading -->
<h2 class="wp-block-heading">Why UCF?</h2>
<!-- /wp:heading -->

<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|70"}},"layout":{"type":"grid","columnCount":3,"minimumColumnWidth":"16rem"}} -->
<div class="wp-block-group">
<!-- wp:group {"className":"ucf-stat","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-stat"><!-- wp:paragraph -->
<p>Florida’s largest university, with 70,674 students</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>One sentence saying why the number matters.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Source: <a href="https://www.ucf.edu/about-ucf/facts/">UCF Facts</a>, fall 2025</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ucf-stat","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-stat"><!-- wp:paragraph -->
<p>A second statement, written out in full</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Support it with one sentence.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Source: name the publisher and the year</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ucf-stat","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-stat"><!-- wp:paragraph -->
<p>A third statement, three at most in a row</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Support it with one sentence.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Source: name the publisher and the year</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
