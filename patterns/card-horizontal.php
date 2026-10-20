<?php
/**
 * Title: Card (horizontal)
 * Slug: ucf-wordpress-block-theme/card-horizontal
 * Categories: ucf
 * Description: A card with its image beside the text, for a list of stories. The whole card links through its title.
 * Keywords: card, horizontal, story, news
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-card ucf-card--horizontal","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-card ucf-card--horizontal"><!-- wp:image {"aspectRatio":"3/2","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:3/2;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Research · Sept. 11, 2026</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"className":"is-style-sans","fontSize":"heading-4"} -->
<h3 class="wp-block-heading is-style-sans has-heading-4-font-size"><a href="#">A story title under about eight words</a></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"data-lg"} -->
<p class="has-data-lg-font-size">One or two sentences that help someone decide whether to read it.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
