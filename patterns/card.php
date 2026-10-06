<?php
/**
 * Title: Card
 * Slug: ucf-wordpress-block-theme/card
 * Categories: ucf
 * Description: One linked card: image, category, title and a sentence. The whole card is clickable through the title link.
 * Keywords: card, link, news, program
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-card","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-card"><!-- wp:image {"aspectRatio":"3/2","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Category · Sept. 18, 2026</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-sans","fontSize":"heading-4"} -->
<h3 class="wp-block-heading is-style-sans has-heading-4-font-size"><a href="#">A title under about eight words</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"data-lg"} -->
<p class="has-data-lg-font-size">One or two sentences that help someone decide whether to follow the link.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
