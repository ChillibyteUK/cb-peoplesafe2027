<?php
/**
 * Block template for Case studies and stories.
 *
 * Chosen case studies as cards (the customer's logo, a "Case study" badge, the
 * title and the card text) beside a navy panel that moves through chosen user
 * stories in the shared slider, one story in full at a time. Two columns from
 * lg, stacked below.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The posts chosen in one list, in order, published only.
 *
 * @param array $rows Rows of { id }.
 * @return WP_Post[]
 */
$cb_pick = static function ( $rows ) {
	$posts = array();

	foreach ( (array) $rows as $cb_row ) {
		$cb_post = ! empty( $cb_row['id'] ) ? get_post( (int) $cb_row['id'] ) : null;

		if ( $cb_post && 'publish' === $cb_post->post_status && 'case_study' === $cb_post->post_type ) {
			$posts[] = $cb_post;
		}
	}

	return $posts;
};

$cb_cases   = $cb_pick( isset( $attributes['caseStudies'] ) ? $attributes['caseStudies'] : array() );
$cb_stories = $cb_pick( isset( $attributes['stories'] ) ? $attributes['stories'] : array() );

if ( ! $cb_cases && ! $cb_stories ) {
	return;
}

cb_peoplesafe2027_enqueue_slider();

$cb_case_title  = isset( $attributes['caseTitle'] ) ? $attributes['caseTitle'] : '';
$cb_story_title = isset( $attributes['storyTitle'] ) ? $attributes['storyTitle'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'case-story-pair' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container case-story-pair__grid">
		<?php if ( $cb_cases ) { ?>
			<div class="case-story-pair__cases">
				<?php if ( $cb_case_title ) { ?>
					<h2 class="case-story-pair__title"><?php echo esc_html( $cb_case_title ); ?></h2>
				<?php } ?>
				<?php
				foreach ( $cb_cases as $cb_case ) {
					$cb_text = has_excerpt( $cb_case ) ? $cb_case->post_excerpt : wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $cb_case->post_content ) ) );
					?>
					<article class="related-card case-story-pair__card" data-animate="fade-up">
						<div class="related-card__inner">
							<span class="case-story-pair__badge"><?php esc_html_e( 'Case study', 'cb-peoplesafe2027' ); ?></span>
							<?php if ( has_post_thumbnail( $cb_case ) ) { ?>
								<?php echo get_the_post_thumbnail( $cb_case, 'medium', array( 'class' => 'case-story-pair__logo', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php } ?>
							<h3 class="related-card__title"><a class="related-card__link" href="<?php echo esc_url( get_permalink( $cb_case ) ); ?>"><?php echo esc_html( get_the_title( $cb_case ) ); ?></a></h3>
							<?php if ( $cb_text ) { ?>
								<p class="related-card__excerpt"><?php echo esc_html( wp_trim_words( $cb_text, 25 ) ); ?></p>
							<?php } ?>
						</div>
					</article>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if ( $cb_stories ) { ?>
			<div class="case-story-pair__stories">
				<?php if ( $cb_story_title ) { ?>
					<h2 class="case-story-pair__title"><?php echo esc_html( $cb_story_title ); ?></h2>
				<?php } ?>
				<div class="slider" data-slider data-slider-label="<?php echo esc_attr( $cb_story_title ? $cb_story_title : __( 'Stories', 'cb-peoplesafe2027' ) ); ?>" data-animate="fade-up">
					<div class="slider__viewport">
						<div class="slider__track">
							<?php foreach ( $cb_stories as $cb_story ) { ?>
								<div class="slider__slide">
									<article class="case-story-pair__story">
										<h3><?php echo esc_html( get_the_title( $cb_story ) ); ?></h3>
										<?php echo wp_kses( cb_peoplesafe2027_story_text( $cb_story ), array( 'p' => array(), 'a' => array( 'href' => true ), 'strong' => array(), 'em' => array(), 'br' => array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by wp_kses(). ?>
									</article>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</section>
