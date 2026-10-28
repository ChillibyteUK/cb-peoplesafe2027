<?php
/**
 * Case studies and real life stories.
 *
 * One template for both paths (routing.php): /case-studies/ lists the case
 * studies as cards with a sector filter; /stories/ shows an intro, a video and
 * the incident types, and ?incident= lists those stories in full. The intro
 * text and the video are in Site-Wide Settings.
 *
 * @package cb-peoplesafe2027
 */

get_header();

$cb_stories  = 'stories' === get_query_var( 'cb_term_base' );
$cb_incident = $cb_stories && isset( $_GET['incident'] ) ? sanitize_title( wp_unslash( $_GET['incident'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
$cb_sector   = ! $cb_stories && isset( $_GET['sector'] ) ? sanitize_title( wp_unslash( $_GET['sector'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
$cb_here     = trailingslashit( strtok( ( is_ssl() ? 'https://' : 'http://' ) . ( isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '' ) . ( isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '' ), '?' ) );
$cb_here     = preg_replace( '#/page/\d+/$#', '/', $cb_here );

if ( $cb_stories ) {
	$cb_title = __( 'Real life stories', 'cb-peoplesafe2027' );
	$cb_intro = cb_peoplesafe2027_get_setting( 'stories_intro' );
} else {
	$cb_title = __( 'Case studies', 'cb-peoplesafe2027' );
	$cb_intro = cb_peoplesafe2027_get_setting( 'case_studies_intro' );
}

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
echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero ' . wp_json_encode( array( 'title' => $cb_title, 'intro' => $cb_intro ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
$GLOBALS['post'] = $cb_first; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

$cb_video = $cb_stories && ! $cb_incident ? cb_peoplesafe2027_get_setting( 'stories_video_url' ) : '';

if ( $cb_video ) {
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/text-and-media ' . wp_json_encode( array( 'mediaType' => 'video', 'videoUrl' => $cb_video, 'mediaPosition' => 'below' ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
}
?>
<section class="case-hub">
	<div class="container">
		<?php if ( $cb_stories && ! $cb_incident ) { ?>
			<?php $cb_prompt = cb_peoplesafe2027_get_setting( 'stories_prompt' ); ?>
			<?php if ( $cb_prompt ) { ?>
				<p class="case-hub__prompt"><?php echo esc_html( $cb_prompt ); ?></p>
			<?php } ?>
			<ul class="case-hub__tiles">
				<?php foreach ( get_terms( array( 'taxonomy' => 'incident', 'hide_empty' => true, 'lang' => function_exists( 'pll_current_language' ) ? pll_current_language() : '' ) ) as $cb_term ) { ?>
					<li><a class="case-hub__tile" href="<?php echo esc_url( add_query_arg( 'incident', $cb_term->slug, $cb_here ) ); ?>"><?php echo esc_html( $cb_term->name ); ?></a></li>
				<?php } ?>
			</ul>
		<?php } elseif ( $cb_stories ) { ?>
			<p class="case-hub__back"><a href="<?php echo esc_url( $cb_here ); ?>">&larr; <?php esc_html_e( 'All incident types', 'cb-peoplesafe2027' ); ?></a></p>
			<div class="case-hub__stories">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article class="case-hub__story">
						<h2><?php the_title(); ?></h2>
						<?php the_content(); ?>
					</article>
				<?php } ?>
			</div>
		<?php } else { ?>
			<nav class="case-hub__filters" aria-label="<?php esc_attr_e( 'Filter by sector', 'cb-peoplesafe2027' ); ?>">
				<span class="case-hub__filters-label"><?php esc_html_e( 'Sector', 'cb-peoplesafe2027' ); ?></span>
				<a class="case-hub__filter<?php echo $cb_sector ? '' : ' is-active'; ?>" href="<?php echo esc_url( $cb_here ); ?>"><?php esc_html_e( 'All', 'cb-peoplesafe2027' ); ?></a>
				<?php foreach ( get_terms( array( 'taxonomy' => 'sector', 'hide_empty' => true, 'lang' => function_exists( 'pll_current_language' ) ? pll_current_language() : '' ) ) as $cb_term ) { ?>
					<a class="case-hub__filter<?php echo $cb_sector === $cb_term->slug ? ' is-active' : ''; ?>" href="<?php echo esc_url( add_query_arg( 'sector', $cb_term->slug, $cb_here ) ); ?>"><?php echo esc_html( html_entity_decode( $cb_term->name ) ); ?></a>
				<?php } ?>
			</nav>
			<div class="case-hub__grid">
				<?php
				while ( have_posts() ) {
					the_post();
					$cb_excerpt = has_excerpt() ? get_the_excerpt() : '';
					?>
					<article class="case-hub__card">
						<a class="case-hub__card-link" href="<?php the_permalink(); ?>">
							<div class="case-hub__image">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								<?php } ?>
							</div>
							<div class="case-hub__card-title"><?php the_title(); ?></div>
						</a>
						<?php if ( $cb_excerpt ) { ?>
							<div class="case-hub__desc"><?php echo esc_html( $cb_excerpt ); ?></div>
						<?php } ?>
					</article>
				<?php } ?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'           => 2,
					'prev_text'          => '&larr; ' . __( 'Previous', 'cb-peoplesafe2027' ),
					'next_text'          => __( 'Next', 'cb-peoplesafe2027' ) . ' &rarr;',
					'screen_reader_text' => __( 'Case studies pagination', 'cb-peoplesafe2027' ),
				)
			);
			?>
		<?php } ?>
	</div>
</section>
<?php
get_footer();
