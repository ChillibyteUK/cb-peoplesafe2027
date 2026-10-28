<?php
/**
 * Resource template (a guide, whitepaper, brochure, infographic or video).
 *
 * A Hero (the title, breadcrumbs and the featured image: the customer's logo)
 * over the post's own blocks. No wrapping container, like page.php.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();
	// The featured image is the card's cover on the listing: the Hero shows the title and breadcrumb only.
	add_filter( 'post_thumbnail_id', '__return_zero' );
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	remove_filter( 'post_thumbnail_id', '__return_zero' );
	the_content();
}

get_footer();
