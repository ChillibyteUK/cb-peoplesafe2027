<?php
/**
 * Blogs and news listings: /uk/blogs/ and /uk/news/.
 *
 * A Hero, then a search box and (blogs) category pills over a grid of cards: a lead card, two halves, then thirds.
 * Search, the pills and Load more fetch more cards from cb/v1/posts (src/js/posts-index.js); without JavaScript the
 * pills are links (?category=) and the pages are numbered.
 *
 * @package cb-peoplesafe2027
 */

get_header();

$cb_news     = 'news' === get_query_var( 'cb_post_stream' );
$cb_stream   = $cb_news ? 'news' : 'blogs';
$cb_title    = $cb_news ? __( 'News', 'cb-peoplesafe2027' ) : __( 'Blogs', 'cb-peoplesafe2027' );
$cb_region   = cb_peoplesafe2027_request_region();
$cb_filter   = ! $cb_news && isset( $_GET['category'] ) ? sanitize_title( wp_unslash( $_GET['category'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
$cb_here     = trailingslashit( preg_replace( '#/page/\d+/?$#', '/', strtok( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', '?' ) ) );
$cb_total    = (int) $GLOBALS['wp_query']->found_posts;
$cb_loaded   = (int) $GLOBALS['wp_query']->post_count + (int) ( max( 1, (int) get_query_var( 'paged' ) ) - 1 ) * CB_PEOPLESAFE2027_POSTS_PER_BATCH;

// On an archive the global post is the first one listed, and the Hero falls back to the current post's title,
// breadcrumb and featured image: take it away while the Hero is drawn so the page shows its own.
add_filter(
	'cb_peoplesafe2027_archive_breadcrumb',
	static function () use ( $cb_title ) {
		return $cb_title;
	}
);
$cb_first        = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
$GLOBALS['post'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- put back below.
echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero ' . wp_json_encode( array( 'title' => $cb_title ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
$GLOBALS['post'] = $cb_first; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>
<section
	class="case-hub posts-index"
	data-posts-index
	data-endpoint="<?php echo esc_url( rest_url( 'cb/v1/posts' ) ); ?>"
	data-stream="<?php echo esc_attr( $cb_stream ); ?>"
	data-region="<?php echo esc_attr( $cb_region ); ?>"
	data-category="<?php echo esc_attr( $cb_filter ); ?>"
	data-loaded="<?php echo esc_attr( (string) $cb_loaded ); ?>"
	data-total="<?php echo esc_attr( (string) $cb_total ); ?>"
>
	<div class="container">
		<div class="posts-index__search">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7.5"/><path d="m16.5 16.5 4.5 4.5"/></svg>
			<label class="visually-hidden" for="posts-index-search"><?php echo esc_html( $cb_news ? __( 'Search news', 'cb-peoplesafe2027' ) : __( 'Search blogs', 'cb-peoplesafe2027' ) ); ?></label>
			<input id="posts-index-search" type="search" placeholder="<?php esc_attr_e( 'Search by keyword', 'cb-peoplesafe2027' ); ?>" autocomplete="off" data-posts-search>
		</div>
		<?php if ( ! $cb_news ) { ?>
			<nav class="case-hub__filters" aria-label="<?php esc_attr_e( 'Filter by category', 'cb-peoplesafe2027' ); ?>">
				<span class="case-hub__filters-label"><?php esc_html_e( 'Category', 'cb-peoplesafe2027' ); ?></span>
				<a class="case-hub__filter<?php echo $cb_filter ? '' : ' is-active'; ?>" href="<?php echo esc_url( $cb_here ); ?>" data-posts-filter data-category=""><?php esc_html_e( 'All', 'cb-peoplesafe2027' ); ?></a>
				<?php
				// Every used category but News (its own listing) and Uncategorized.
				$cb_skip = array_filter(
					array(
						cb_peoplesafe2027_region_term_id( cb_peoplesafe2027_news_category_slug(), 'category', $cb_region ),
						cb_peoplesafe2027_region_term_id( 'uncategorized', 'category', $cb_region ),
					)
				);
				foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true, 'exclude' => $cb_skip, 'lang' => function_exists( 'pll_current_language' ) ? pll_current_language() : '' ) ) as $cb_term ) {
					?>
					<a class="case-hub__filter<?php echo $cb_filter === $cb_term->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'category', $cb_term->slug, $cb_here ) ); ?>" data-posts-filter data-category="<?php echo esc_attr( $cb_term->slug ); ?>"><?php echo esc_html( html_entity_decode( $cb_term->name ) ); ?></a>
				<?php } ?>
			</nav>
		<?php } ?>
		<p class="visually-hidden" role="status" data-posts-status></p>
		<div class="posts-index__grid" data-posts-grid>
			<?php echo cb_peoplesafe2027_post_cards( wp_list_pluck( $GLOBALS['wp_query']->posts, 'ID' ), ! is_paged(), $cb_stream ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built in cb_peoplesafe2027_post_card(). ?>
		</div>
		<p class="posts-index__empty" data-posts-empty hidden><?php esc_html_e( 'No articles found. Try different keywords.', 'cb-peoplesafe2027' ); ?></p>
		<button class="button button--outline posts-index__more" type="button" data-posts-more<?php echo $cb_loaded >= $cb_total ? ' hidden' : ''; ?>><?php esc_html_e( 'Load more', 'cb-peoplesafe2027' ); ?></button>
		<div data-posts-pagination>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => '&larr; ' . __( 'Previous', 'cb-peoplesafe2027' ),
					'next_text'          => __( 'Next', 'cb-peoplesafe2027' ) . ' &rarr;',
					'screen_reader_text' => $cb_news ? __( 'News pagination', 'cb-peoplesafe2027' ) : __( 'Blogs pagination', 'cb-peoplesafe2027' ),
				)
			);
			?>
		</div>
	</div>
</section>
<?php
get_footer();
