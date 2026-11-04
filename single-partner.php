<?php
/**
 * A partnership's page: a Hero (the title, breadcrumb and logo) over its own blocks. Accreditations, and partners that
 * link elsewhere, have no page of their own (inc/partners.php sends their address on).
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
