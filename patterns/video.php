<?php
/**
 * Title: Video
 * Slug: ucf-wordpress-block-theme/video
 * Categories: ucf
 * Description: A video that loads nothing until someone asks for it: a 16:9 poster, a play link with the title and length, and a transcript link. Link the play text to the YouTube video.
 * Keywords: video, youtube, play, facade
 *
 * @package ucf-wordpress-block-theme
 */

?>
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
