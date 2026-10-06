<?php
/**
 * Title: Section header with link
 * Slug: ucf-wordpress-block-theme/section-header-split
 * Categories: ucf
 * Description: A section heading and introduction with a See all link on the right. Use above a grid of cards that continues on another page.
 * Keywords: heading, see all, section
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-section-head","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|70"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group ucf-section-head" style="margin-bottom:var(--wp--preset--spacing--70)"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"constrained","contentSize":"46rem","justifyContent":"left"}} -->
<div class="wp-block-group"><!-- wp:heading -->
<h2 class="wp-block-heading">News from the college</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"lead"} -->
<p class="has-lead-font-size">One sentence saying what this section holds.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-text"} -->
<div class="wp-block-button is-style-text"><a class="wp-block-button__link wp-element-button" href="#">See all college news</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->
