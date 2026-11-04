<?php
/**
 * Partnerships and accreditations listings: /uk/partnerships/ and /uk/accreditations/.
 *
 * Partnerships are logo cards (the case studies' card) titled "Partnership with ..."; accreditations are the logo, the
 * name and a line about it, three across, ending on a call to action. The introductions are Listing intros.
 *
 * @package cb-peoplesafe2027
 */

get_header();

$cb_accreditations = 'accreditations' === get_query_var( 'cb_term_base' );
$cb_title          = $cb_accreditations ? __( 'Accreditations and Certifications', 'cb-peoplesafe2027' ) : __( 'Partnerships', 'cb-peoplesafe2027' );

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

echo cb_peoplesafe2027_listing_intro_html( $cb_accreditations ? 'accreditations' : 'partnerships' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
?>
<section class="case-hub partners">
	<div class="container">
		<?php if ( $cb_accreditations ) { ?>
			<h2 class="partners__heading"><?php esc_html_e( 'Our Accreditations and Certifications', 'cb-peoplesafe2027' ); ?></h2>
			<p><?php esc_html_e( 'Our business has achieved the following standards, accreditations and certifications:', 'cb-peoplesafe2027' ); ?></p>
			<ul class="partners__accreditations">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<li class="accreditation">
						<?php if ( has_post_thumbnail() ) { ?>
							<div class="accreditation__image"><?php the_post_thumbnail( 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></div>
						<?php } ?>
						<h3 class="accreditation__title"><?php the_title(); ?></h3>
						<?php if ( has_excerpt() ) { ?>
							<p class="accreditation__intro"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<?php } ?>
					</li>
				<?php } ?>
			</ul>
		<?php } else { ?>
			<div class="case-hub__grid">
				<?php
				while ( have_posts() ) {
					the_post();
					$cb_url = cb_peoplesafe2027_partner_url( get_the_ID() );
					?>
					<article class="case-hub__card">
						<a class="case-hub__card-link" href="<?php echo esc_url( $cb_url ? $cb_url : get_permalink() ); ?>">
							<div class="case-hub__image">
								<?php if ( has_post_thumbnail() ) { ?>
									<?php the_post_thumbnail( 'large', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
								<?php } ?>
							</div>
							<div class="case-hub__card-title">
								<?php
								/* translators: %s: partner name */
								echo esc_html( sprintf( __( 'Partnership with %s', 'cb-peoplesafe2027' ), wp_strip_all_tags( get_the_title() ) ) );
								?>
							</div>
						</a>
					</article>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
<?php
if ( $cb_accreditations ) {
	echo do_blocks( '<!-- wp:cb-peoplesafe2027/cta-banner ' . wp_json_encode( array( 'title' => __( 'Contact us', 'cb-peoplesafe2027' ), 'content' => '<p>' . __( 'Our service is accredited to the highest industry standards, including all five parts of BS 8484:2016. Speak to one of our experts to understand how this benefits your business.', 'cb-peoplesafe2027' ) . '</p>', 'buttons' => array( array( 'linkText' => __( 'Get in touch', 'cb-peoplesafe2027' ), 'link' => trailingslashit( cb_peoplesafe2027_region_url( cb_peoplesafe2027_request_region() ) ) . 'contact-us/' ) ) ) ) . ' /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block output.
}

get_footer();
