<?php
/**
 * Title: Hero image
 * Slug: ucf-wordpress-block-theme/hero-image
 * Categories: ucf
 * Description: A full-width photograph with the headline on a solid black panel over its lower corner — never on the photo itself. Crop for a phone first. Layout locked.
 * Keywords: hero, photo, banner, h1
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"tagName":"section","align":"full","className":"ucf-hero-image","templateLock":"contentOnly","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull ucf-hero-image"><!-- wp:image {"aspectRatio":"21/9","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:21/9;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:group {"className":"is-style-dark ucf-keep-dark","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
<div class="wp-block-group is-style-dark ucf-keep-dark"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">A headline in real HTML</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"lead"} -->
<p class="has-lead-font-size">One sentence. The panel keeps this at 21:1 whatever the photograph does.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
