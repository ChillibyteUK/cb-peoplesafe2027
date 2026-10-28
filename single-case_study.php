<?php
/**
 * Case study and user story template.
 *
 * A Hero (the title, breadcrumbs and the featured image: the customer's logo)
 * over the post's own blocks. No wrapping container, like page.php.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	the_content();
}

get_footer();
