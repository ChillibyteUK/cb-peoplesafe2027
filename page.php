<?php
/**
 * Page template.
 *
 * No wrapping container: every block draws its own .container (the core text
 * blocks are wrapped in one by inc/blocks.php), so a block such as the CTA
 * banner can run edge to edge.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();
	the_content();
}

get_footer();
