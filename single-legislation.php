<?php
/**
 * A legislation article: a Hero (the title, breadcrumb, image and a meta row of date, reading time and share), then
 * the article's own blocks with the Quick links sidebar beside them from lg up when it has two or more h2s, and the
 * newsletter signup.
 *
 * @package cb-peoplesafe2027
 */

get_header();

while ( have_posts() ) {
	the_post();

	ob_start();
	?>
	<div class="post-meta">
		<ul class="post-meta__list">
			<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time></li>
			<li class="post-meta__item"><?php echo cb_peoplesafe2027_meta_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed SVG. ?><span>
				<?php
				/* translators: %d: minutes */
				echo esc_html( sprintf( _n( '%d min read', '%d min read', cb_peoplesafe2027_post_reading_minutes(), 'cb-peoplesafe2027' ), cb_peoplesafe2027_post_reading_minutes() ) );
				?>
			</span></li>
			<li class="post-meta__item post-meta__item--share"><?php echo cb_peoplesafe2027_share_button(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the function. ?></li>
		</ul>
	</div>
	<?php
	$cb_meta_html = ob_get_clean();

	$cb_meta_hook = static function () use ( $cb_meta_html ) {
		echo $cb_meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts.
	};
	add_action( 'cb_peoplesafe2027_hero_after_intro', $cb_meta_hook );
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
	remove_action( 'cb_peoplesafe2027_hero_after_intro', $cb_meta_hook );

	cb_peoplesafe2027_share_dialog( get_permalink(), wp_strip_all_tags( get_the_title() ) );

	// Related products and resources sit under the article at the full width, not inside its nine columns.
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

	echo do_blocks( '<!-- wp:cb-peoplesafe2027/newsletter-signup ' . wp_json_encode( array( 'imageUrl' => get_theme_file_uri( 'img/newsletter-signup.png' ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
}

get_footer();
