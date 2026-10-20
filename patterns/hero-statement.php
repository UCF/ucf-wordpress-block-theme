<?php
/**
 * Title: Hero statement
 * Slug: ucf-wordpress-block-theme/hero-statement
 * Categories: ucf
 * Description: The type-led page opener: one short statement as the H1, one sentence, one primary action, one photograph. The layout is locked; change the words and the photo.
 * Keywords: hero, opener, homepage, h1
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ucf-section is-style-light","templateLock":"contentOnly","layout":{"type":"constrained","contentSize":"1240px"}} -->
<section class="wp-block-group alignfull ucf-section is-style-light"><!-- wp:columns {"verticalAlignment":"center"} -->
<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%"><!-- wp:heading {"level":1,"fontSize":"display-1"} -->
<h1 class="wp-block-heading has-display-1-font-size">A statement under ten words</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"lead"} -->
<p class="has-lead-font-size">One sentence that supports the statement and says what this place is.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#">Find your degree</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#">Visit campus</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/5","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:4/5;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
