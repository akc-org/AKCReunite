<?php

/**
 * NA-80: disable Yoast automatic redirects for posts, pages, and taxonomies
 */

add_filter('Yoast\WP\SEO\post_redirect_slug_change', '__return_true' );  // posts and pages
add_filter('Yoast\WP\SEO\term_redirect_slug_change', '__return_true' );  // taxonomies