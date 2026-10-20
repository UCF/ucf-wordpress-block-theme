<?php
/**
 * Title: Photo pair
 * Slug: ucf-wordpress-block-theme/media-pair
 * Categories: ucf
 * Description: Two photographs at different ratios — 4:5 and 1:1 — the second dropped 64px. Shoot both with the same light direction.
 * Keywords: photos, pair, gallery
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-media-pair","layout":{"type":"default"}} -->
<div class="wp-block-group ucf-media-pair"><!-- wp:image {"aspectRatio":"4/5","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:4/5;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:image {"aspectRatio":"1","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->
