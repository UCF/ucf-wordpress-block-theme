#!/usr/bin/env bash
#
# Seed a wp-env instance with deterministic content so the route list in
# tests/routes.js resolves. Run after `npx wp-env start`.
#
# Creates: postname permalinks, a static "Home" front page, a hero page
# (slug name-of-page) built from the theme's Hero pattern, a basic page
# (sample-page), and a single post (hello-world). The default "Uncategorized"
# category already exists.
set -euo pipefail

cli() { npx wp-env run cli wp "$@"; }

echo "Activating theme + permalinks..."
cli theme activate ucf-wordpress-block-theme
cli rewrite structure '/%postname%/' --hard

echo "Static front page..."
HOME_ID=$(cli post create --post_type=page --post_status=publish \
	--post_title='Home' --post_name='home' --porcelain)
cli option update show_on_front 'page'
cli option update page_on_front "$HOME_ID"

echo "Hero page (from the ucf/hero pattern)..."
HERO_CONTENT=$(cli eval 'echo WP_Block_Patterns_Registry::get_instance()->get_registered("ucf/hero")["content"];')
cli post create --post_type=page --post_status=publish \
	--post_title='Name of Page' --post_name='name-of-page' --post_content="$HERO_CONTENT"

echo "Basic page..."
cli post create --post_type=page --post_status=publish \
	--post_title='Sample Page' --post_name='sample-page' \
	--post_content='<!-- wp:paragraph --><p>Sample page content.</p><!-- /wp:paragraph -->'

echo "Single post..."
cli post create --post_type=post --post_status=publish \
	--post_title='Hello World' --post_name='hello-world' \
	--post_content='<!-- wp:paragraph --><p>Hello world post content.</p><!-- /wp:paragraph -->'

echo "Flushing rewrite + pattern cache..."
cli rewrite flush --hard
cli eval 'wp_get_theme()->delete_pattern_cache();'

echo "Seed complete."