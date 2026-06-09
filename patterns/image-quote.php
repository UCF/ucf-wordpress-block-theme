<?php
/**
 * Title: Image + Quotation
 * Slug: ucf/image-quote
 * Categories: ucf-blocks
 * Description: A 33/66 two-column row — an image on the left and a stylized "quotation" blockquote with citation on the right, vertically centered. Replace the placeholder image with your own.
 * Keywords: quote, quotation, testimonial, image, columns
 *
 * @package ucf-wordpress-block-theme
 */

$placeholder = esc_url( get_theme_file_uri( 'assets/img/placeholder.svg' ) );

?>
<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center">
	<!-- wp:column {"verticalAlignment":"center","width":"33.33%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $placeholder; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>" alt=""/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:column -->

	<!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
	<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%">
		<!-- wp:quote {"className":"is-style-quote-quotation"} -->
		<blockquote class="wp-block-quote is-style-quote-quotation"><!-- wp:paragraph -->
		<p><?php esc_html_e( 'UCF prepares students for the careers of the future and powers the economy of the region and the state.', 'ucf-wordpress-block-theme' ); ?></p>
		<!-- /wp:paragraph --><cite><?php esc_html_e( 'President, University of Central Florida', 'ucf-wordpress-block-theme' ); ?></cite></blockquote>
		<!-- /wp:quote -->
	</div>
	<!-- /wp:column -->
</div>
<!-- /wp:columns -->