<?php
/**
 * Title: Card Grid
 * Slug: ucf/card-grid
 * Categories: ucf-blocks
 * Description: A responsive three-column row of cards, each with an image, heading, supporting text, and a link. Columns stack on mobile.
 * Keywords: cards, grid, columns, features, links
 *
 * @package ucf-wordpress-block-theme
 */

$placeholder = esc_url( get_theme_file_uri( 'assets/img/placeholder.svg' ) );
$card_title  = esc_html__( 'Card Title', 'ucf-wordpress-block-theme' );
$card_body   = esc_html__( 'A short description of this card. Replace it with your own content.', 'ucf-wordpress-block-theme' );
$card_link   = esc_html__( 'Learn more', 'ucf-wordpress-block-theme' );

$card = <<<HTML
<!-- wp:column -->
<div class="wp-block-column">
	<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
	<figure class="wp-block-image size-large"><img src="$placeholder" alt=""/></figure>
	<!-- /wp:image -->

	<!-- wp:heading {"level":3,"fontSize":"lg"} -->
	<h3 class="wp-block-heading has-lg-font-size">$card_title</h3>
	<!-- /wp:heading -->

	<!-- wp:paragraph -->
	<p>$card_body</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph -->
	<p><a href="#">$card_link</a></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
HTML;

?>
<!-- wp:columns {"align":"wide"} -->
<div class="wp-block-columns alignwide">
	<?php echo $card . $card . $card; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- composed from escaped vars above. ?>
</div>
<!-- /wp:columns -->