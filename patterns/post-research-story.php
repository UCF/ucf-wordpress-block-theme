<?php
/**
 * Title: Research story
 * Slug: ucf-wordpress-block-theme/post-research-story
 * Categories: ucf
 * Description: A research story written to be quoted: key takeaways first, sourced claims, a named researcher, a video and numbered sources. The template adds the byline and review dates.
 * Keywords: research, story, news, article
 * Block Types: core/post-content
 * Post Types: post
 *
 * @package ucf-wordpress-block-theme
 */

?>
<!-- wp:group {"className":"ucf-answer","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-answer"><!-- wp:paragraph -->
<p>Key takeaways</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>A UCF team has built a lunar soil simulant that labs use to test hardware before it flies.</strong> Seals, bearings and optics that fail in the tray fail here instead of on the surface.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>The simulant is made to match samples returned from the Moon.</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Labs and companies use it to test parts before launch.</li>
<!-- /wp:list-item --><!-- wp:list-item -->
<li>Undergraduates work on the project from their second year.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">Sample story for review · Reviewed Oct. 1, 2026</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:paragraph -->
<p>Dust is the problem nobody plans for. On the Moon it is sharp, electrically charged and everywhere, and it gets into anything with a moving part.<a class="ucf-ref" href="#source-1">1</a></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How do you test for dust you cannot get?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>You make it. The team grinds and blends Earth minerals to match the size, shape and chemistry of returned samples, then ships it to labs that need to break their hardware early.<a class="ucf-ref" href="#source-2">2</a></p>
<!-- /wp:paragraph -->

<!-- wp:pullquote -->
<figure class="wp-block-pullquote"><blockquote><p>If your seal survives a week in our tray, it might survive a year up there.</p><cite>Sample Name, Ph.D., Professor</cite></blockquote></figure>
<!-- /wp:pullquote -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Who does the work?</h2>
<!-- /wp:heading -->

<!-- wp:ucf/profile {"person":"sample-researcher","feature":true} /-->

<!-- wp:group {"className":"ucf-video","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"default"}} -->
<div class="wp-block-group ucf-video"><!-- wp:image {"aspectRatio":"16/9","scale":"cover"} -->
<figure class="wp-block-image"><img alt="" style="aspect-ratio:16/9;object-fit:cover"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"ucf-video__play"} -->
<p class="ucf-video__play"><a href="https://www.youtube.com/watch?v=aqz-KE-bpKQ">Play: The video's title</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-data"} -->
<p class="is-style-data">2:14 · Captions on · <a href="#">Read the transcript</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"ucf-sources","layout":{"type":"default"}} -->
<div class="wp-block-group ucf-sources"><!-- wp:heading {"className":"is-style-sans","fontSize":"heading-5"} -->
<h2 class="wp-block-heading is-style-sans has-heading-5-font-size">Sources</h2>
<!-- /wp:heading -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list"><!-- wp:list-item -->
<li id="source-1">Publisher. <a href="#">Title of the source</a>. 2026.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li id="source-2">Publisher. <a href="#">A second source</a>. 2025.</li>
<!-- /wp:list-item --></ol>
<!-- /wp:list --></div>
<!-- /wp:group -->
