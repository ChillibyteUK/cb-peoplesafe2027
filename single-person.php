<?php
/**
 * An author's page: a Hero (their name, role, photo and bio) over a grid of the posts they wrote.
 *
 * The posts are those whose Author box (cb_author) points at this person, in the visitor's region.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();

	$cb_person   = get_post();
	$cb_role     = (string) get_post_meta( $cb_person->ID, 'cb_role', true );
	$cb_linkedin = (string) get_post_meta( $cb_person->ID, 'cb_linkedin', true );
	$cb_bio      = trim( (string) apply_filters( 'the_content', $cb_person->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
	$cb_intro    = '';

	if ( $cb_role ) {
		$cb_intro .= '<p><strong>' . esc_html( $cb_role ) . '</strong></p>';
	}

	$cb_intro .= $cb_bio;

	if ( $cb_linkedin ) {
		$cb_intro .= '<p><a href="' . esc_url( $cb_linkedin ) . '" rel="noopener" target="_blank">' . esc_html__( 'LinkedIn', 'cb-peoplesafe2027' ) . '</a></p>';
	}

	echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero ' . wp_json_encode( array( 'intro' => $cb_intro ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.

	$cb_page  = isset( $_GET['pg'] ) ? max( 1, (int) $_GET['pg'] ) : 1; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public page number.
	$cb_query = new WP_Query(
		array(
			'post_type'        => 'post',
			'post_status'      => 'publish',
			'posts_per_page'   => 24,
			'paged'            => $cb_page,
			'no_found_rows'    => false,
			'fields'           => 'ids',
			'meta_key'         => 'cb_author', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- a handful of authors.
			'meta_value'       => $cb_person->ID, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'lang'             => function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : 'uk',
		)
	);
	$cb_posts = $cb_query->posts;

	if ( $cb_posts ) {
		?>
		<section class="case-hub posts-index">
			<div class="container">
				<h2><?php echo esc_html( sprintf( /* translators: %s: author's name */ __( 'Posts by %s', 'cb-peoplesafe2027' ), get_the_title( $cb_person ) ) ); ?></h2>
				<div class="posts-index__grid">
					<?php echo cb_peoplesafe2027_post_cards( $cb_posts, 1 === $cb_page, 'blogs' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in cb_peoplesafe2027_post_card(). ?>
				</div>
				<?php
				$cb_links = paginate_links(
						array(
							'base'      => add_query_arg( 'pg', '%#%', get_permalink( $cb_person ) ),
							'format'    => '',
							'current'   => $cb_page,
							'total'     => (int) $cb_query->max_num_pages,
							'prev_text' => '&larr; ' . __( 'Previous', 'cb-peoplesafe2027' ),
							'next_text' => __( 'Next', 'cb-peoplesafe2027' ) . ' &rarr;',
							'type'      => 'plain',
						)
				);

				if ( $cb_links ) {
					echo '<nav class="navigation pagination" aria-label="' . esc_attr__( 'Posts pagination', 'cb-peoplesafe2027' ) . '"><div class="nav-links">' . wp_kses_post( $cb_links ) . '</div></nav>';
				}
				?>
			</div>
		</section>
		<?php
	}
}

get_footer();
