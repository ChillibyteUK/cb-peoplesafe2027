<?php
/**
 * Resource listings: /uk/guides/, /uk/whitepapers/, /uk/brochures/ and so on.
 *
 * A Hero over a grid of cards, the same card as the case studies hub (the
 * image over a yellow title bar). The title comes from the path base.
 *
 * @package cb-peoplesafe2027
 */

get_header();

$cb_titles = array(
	'guides'       => __( 'Guides', 'cb-peoplesafe2027' ),
	'whitepapers'  => __( 'Whitepapers', 'cb-peoplesafe2027' ),
	'brochures'    => __( 'Brochures', 'cb-peoplesafe2027' ),
	'infographics' => __( 'Infographics', 'cb-peoplesafe2027' ),
	'video'        => __( 'Videos', 'cb-peoplesafe2027' ),
);
$cb_base   = (string) get_query_var( 'cb_term_base' );
$cb_hub    = cb_peoplesafe2027_is_knowledge_hub();
$cb_title  = $cb_hub ? __( 'Knowledge Hub', 'cb-peoplesafe2027' ) : ( isset( $cb_titles[ $cb_base ] ) ? $cb_titles[ $cb_base ] : __( 'Resources', 'cb-peoplesafe2027' ) );

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
echo do_blocks( '<!-- wp:cb-peoplesafe2027/hero ' . wp_json_encode( array( 'title' => $cb_title, 'intro' => $cb_hub ? cb_peoplesafe2027_rich_paragraphs( cb_peoplesafe2027_get_setting( 'knowledge_hub_intro' ) ) : ( 'infographics' === $cb_base ? cb_peoplesafe2027_get_setting( 'infographics_intro' ) : '' ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
$GLOBALS['post'] = $cb_first; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
if ( $cb_hub ) {
	cb_peoplesafe2027_render_knowledge_hub( cb_peoplesafe2027_request_region() );
	get_footer();
	return;
}

$cb_list = in_array( $cb_base, array( 'whitepapers', 'brochures' ), true ); // These two are a list, the rest a grid of cards.
?>
<section class="case-hub case-hub--resources case-hub--<?php echo esc_attr( $cb_base ? $cb_base : 'all' ); ?>">
	<div class="container">
		<?php if ( $cb_list ) { ?>
			<div class="resource-list">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article class="resource-list__item">
						<?php if ( has_post_thumbnail() ) { ?>
							<a class="resource-list__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'medium_large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></a>
						<?php } ?>
						<div class="resource-list__text">
							<h2 class="resource-list__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_content() ), 30 ) ); ?></p>
							<a class="resource-list__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'cb-peoplesafe2027' ); ?> <span class="visually-hidden"><?php echo esc_html( sprintf( /* translators: %s: title */ __( 'about %s', 'cb-peoplesafe2027' ), wp_strip_all_tags( get_the_title() ) ) ); ?></span></a>
						</div>
					</article>
				<?php } ?>
			</div>
		<?php } else { ?>
			<div class="case-hub__grid">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article class="case-hub__card">
						<a class="case-hub__card-link" href="<?php the_permalink(); ?>">
							<div class="case-hub__image">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								<?php } ?>
								<?php if ( 'video' === $cb_base ) { ?>
									<span class="case-hub__play" aria-hidden="true"></span>
								<?php } ?>
							</div>
							<div class="case-hub__card-title"><?php the_title(); ?></div>
						</a>
					</article>
				<?php } ?>
			</div>
		<?php } ?>
		<?php
		the_posts_pagination(
			array(
				'mid_size'           => 2,
				'prev_text'          => '&larr; ' . __( 'Previous', 'cb-peoplesafe2027' ),
				'next_text'          => __( 'Next', 'cb-peoplesafe2027' ) . ' &rarr;',
				'screen_reader_text' => __( 'Resources pagination', 'cb-peoplesafe2027' ),
			)
		);
		?>
	</div>
</section>
<?php
get_footer();
