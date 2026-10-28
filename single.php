<?php
/**
 * Single post template: a blog or a news article.
 *
 * A Hero (the title, a meta row of category, date, reading time and author, the breadcrumb and the featured image), then the post's own blocks
 * with a sticky Quick links sidebar of its h2s beside them on desktop, any related products or resources at the full
 * width, the next and previous posts of the same kind and related posts.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();

	$cb_news_post = cb_peoplesafe2027_is_news_post( get_post() );
	$cb_news_term = cb_peoplesafe2027_region_term_id( cb_peoplesafe2027_news_category_slug(), 'category', cb_peoplesafe2027_request_region() );
	$cb_excluded  = ( ! $cb_news_post && $cb_news_term ) ? array( $cb_news_term ) : array();
	$cb_region    = trailingslashit( cb_peoplesafe2027_region_url( function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : 'uk' ) );
	$cb_author    = cb_peoplesafe2027_post_author( get_the_ID() );

	// The post's categories as links (blogs) or News itself.
	$cb_category_links = array();

	if ( $cb_news_post ) {
		$cb_category_links[] = '<a href="' . esc_url( $cb_region . 'news/' ) . '">' . esc_html__( 'News', 'cb-peoplesafe2027' ) . '</a>';
	} else {
		foreach ( get_the_category() as $cb_category ) {
			if ( 'uncategorized' !== cb_peoplesafe2027_default_term_slug( $cb_category->term_id ) ) {
				$cb_category_links[] = '<a href="' . esc_url( add_query_arg( 'category', $cb_category->slug, $cb_region . 'blogs/' ) ) . '">' . esc_html( html_entity_decode( $cb_category->name ) ) . '</a>';
			}
		}
	}

	// Related products and resources sit under the article at the full width, not inside its nine columns. The rest
	// is rendered once: for the page, its h2 ids and the Quick links.
	$cb_body  = array();
	$cb_after = array();

	foreach ( parse_blocks( get_the_content() ) as $cb_block ) {
		if ( 'cb-peoplesafe2027/related-content' === $cb_block['blockName'] ) {
			$cb_after[] = $cb_block;
		} else {
			$cb_body[] = $cb_block;
		}
	}

	$cb_content  = apply_filters( 'the_content', serialize_blocks( $cb_body ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- core filter.
	$cb_headings = cb_peoplesafe2027_collect_h2s( $cb_content );
	$cb_has_toc  = count( $cb_headings ) >= 2;
	?>
	<?php
	ob_start();
	?>
	<div class="post-meta">
		<ul class="post-meta__list">
			<?php if ( $cb_category_links ) { ?>
				<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span><?php echo implode( ', ', $cb_category_links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_html() above. ?></span></li>
			<?php } ?>
			<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time></li>
			<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span>
				<?php
				/* translators: %d: minutes */
				echo esc_html( sprintf( _n( '%d min read', '%d min read', cb_peoplesafe2027_post_reading_minutes(), 'cb-peoplesafe2027' ), cb_peoplesafe2027_post_reading_minutes() ) );
				?>
			</span></li>
			<?php if ( $cb_author ) { ?>
				<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span><?php echo esc_html( $cb_author->post_title ); ?></span></li>
			<?php } ?>
			<li class="post-meta__item post-meta__item--share"><?php echo cb_peoplesafe2027_share_button(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function. ?></li>
		</ul>
	</div>
	<?php
	$cb_meta_html = ob_get_clean();

	// The meta row sits in the hero, under the title.
	$cb_meta_hook = static function () use ( $cb_meta_html ) {
		echo $cb_meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
	};
	add_action( 'cb_peoplesafe2027_hero_after_intro', $cb_meta_hook );
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	remove_action( 'cb_peoplesafe2027_hero_after_intro', $cb_meta_hook );
	?>
	<?php cb_peoplesafe2027_share_dialog( get_permalink(), wp_strip_all_tags( get_the_title() ) ); ?>

	<div class="container post-layout<?php echo $cb_has_toc ? ' post-layout--with-toc' : ''; ?>">
		<article <?php post_class( 'post-layout__article' ); ?>>
			<?php echo $cb_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- the_content filter output. ?>
		</article>
		<?php if ( $cb_has_toc ) { ?>
			<nav class="post-toc" aria-label="<?php esc_attr_e( 'Quick links', 'cb-peoplesafe2027' ); ?>">
				<p class="post-toc__title"><?php esc_html_e( 'Quick links', 'cb-peoplesafe2027' ); ?></p>
				<ul class="post-toc__list">
					<?php foreach ( $cb_headings as $cb_heading ) { ?>
						<li><a class="post-toc__link" href="#<?php echo esc_attr( $cb_heading['id'] ); ?>"><?php echo esc_html( $cb_heading['text'] ); ?></a></li>
					<?php } ?>
				</ul>
			</nav>
		<?php } ?>
	</div>
	<?php
	foreach ( $cb_after as $cb_block ) {
		echo render_block( $cb_block ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	}

	$cb_previous = $cb_news_post ? get_previous_post( true ) : get_previous_post( false, $cb_excluded );
	$cb_next     = $cb_news_post ? get_next_post( true ) : get_next_post( false, $cb_excluded );
	?>
	<?php if ( $cb_previous || $cb_next ) { ?>
		<nav class="container post-nav" aria-label="<?php esc_attr_e( 'More posts', 'cb-peoplesafe2027' ); ?>">
			<?php if ( $cb_previous ) { ?>
				<a class="post-nav__link post-nav__link--previous" href="<?php echo esc_url( get_permalink( $cb_previous ) ); ?>" rel="prev">
					<span class="post-nav__label">&larr; <?php esc_html_e( 'Previous', 'cb-peoplesafe2027' ); ?></span>
					<span class="post-nav__title"><?php echo esc_html( get_the_title( $cb_previous ) ); ?></span>
				</a>
			<?php } ?>
			<?php if ( $cb_next ) { ?>
				<a class="post-nav__link post-nav__link--next" href="<?php echo esc_url( get_permalink( $cb_next ) ); ?>" rel="next">
					<span class="post-nav__label"><?php esc_html_e( 'Next', 'cb-peoplesafe2027' ); ?> &rarr;</span>
					<span class="post-nav__title"><?php echo esc_html( get_the_title( $cb_next ) ); ?></span>
				</a>
			<?php } ?>
		</nav>
	<?php } ?>
	<?php
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/newsletter-signup ' . wp_json_encode( array( 'imageUrl' => get_theme_file_uri( 'img/newsletter-signup.png' ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/related-content ' . wp_json_encode( array( 'source' => 'curated', 'title' => __( 'Related posts', 'cb-peoplesafe2027' ), 'items' => array_map( function ( $cb_id ) { return array( 'type' => 'post', 'id' => $cb_id ); }, cb_peoplesafe2027_related_post_ids( get_post(), 3 ) ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
}

get_footer();
