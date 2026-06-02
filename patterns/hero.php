<?php
/**
 * Title: Hero
 * Slug: ucf/hero
 * Categories: ucf-headers
 * Description: Full-width hero with a background image, gradient overlay, page title (H1), and subtitle. Auto-inserted at the top of new pages.
 * Keywords: hero, masthead, header, banner, cover
 * Block Types: core/post-content
 * Post Types: page
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:cover {"customGradient":"linear-gradient(180deg,rgba(0,0,0,0.25) 0%,rgba(0,0,0,0.55) 100%)","minHeight":500,"minHeightUnit":"px","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="min-height:500px"><span aria-hidden="true" class="wp-block-cover__background has-background-gradient" style="background:linear-gradient(180deg,rgba(0,0,0,0.25) 0%,rgba(0,0,0,0.55) 100%)"></span><div class="wp-block-cover__inner-container">
<!-- wp:heading {"level":1,"textColor":"inverse","className":"display-2"} -->
<h1 class="wp-block-heading has-inverse-color has-text-color display-2"><?php esc_html_e( 'Page Title', 'ucf-wordpress-block-theme' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"inverse","className":"lead"} -->
<p class="has-inverse-color has-text-color lead"><?php esc_html_e( 'A short supporting subtitle goes here.', 'ucf-wordpress-block-theme' ); ?></p>
<!-- /wp:paragraph -->
</div></div>
<!-- /wp:cover -->