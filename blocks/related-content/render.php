<?php
/**
 * Block template for Related content.
 *
 * Cards for products, case studies, resources or posts, picked by hand or the
 * latest of one type (case studies can be limited to a sector). The cards sit
 * in the shared slider, which shows plain cards when they all fit and arrows
 * and dots when they do not.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_allowed_types = array(
	'product'    => __( 'Related products', 'cb-peoplesafe2027' ),
	'case_study' => __( 'Related case studies', 'cb-peoplesafe2027' ),
	'resource'   => __( 'Related resources', 'cb-peoplesafe2027' ),
	'post'       => __( 'Latest posts', 'cb-peoplesafe2027' ),
);

$cb_source = isset( $attributes['source'] ) ? $attributes['source'] : 'curated';
$cb_items  = array();
$cb_types  = array();

if ( 'latest' === $cb_source ) {
	$cb_type = isset( $attributes['postType'], $cb_allowed_types[ $attributes['postType'] ] ) ? $attributes['postType'] : 'product';
	$cb_args = array(
		'post_type'   => $cb_type,
		'post_status' => 'publish',
		'numberposts' => isset( $attributes['count'] ) ? max( 1, min( 24, (int) $attributes['count'] ) ) : 6,
	);

	if ( 'case_study' === $cb_type && ! empty( $attributes['sector'] ) ) {
		$cb_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- a small, cached, editor-chosen filter.
			array(
				'taxonomy' => 'sector',
				'terms'    => (int) $attributes['sector'],
			),
		);
	}

	$cb_items = get_posts( $cb_args );
	$cb_types = array( $cb_type );
} else {
	foreach ( isset( $attributes['items'] ) ? (array) $attributes['items'] : array() as $cb_row ) {
		$cb_post = ! empty( $cb_row['id'] ) ? get_post( (int) $cb_row['id'] ) : null;

		if ( $cb_post && 'publish' === $cb_post->post_status ) {
			$cb_items[] = $cb_post;
			$cb_types[] = $cb_post->post_type;
		}
	}

	$cb_types = array_unique( $cb_types );
}

if ( ! $cb_items ) {
	return;
}

$cb_default_title = ( 1 === count( $cb_types ) && isset( $cb_allowed_types[ $cb_types[0] ] ) ) ? $cb_allowed_types[ $cb_types[0] ] : __( 'Related content', 'cb-peoplesafe2027' );
$cb_heading       = ! empty( $attributes['title'] ) ? $attributes['title'] : $cb_default_title;
$cb_four_up       = array( 'product' ) === array_values( $cb_types );

cb_peoplesafe2027_enqueue_slider();

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'related-content' . ( $cb_four_up ? ' related-content--four-up' : '' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( empty( $attributes['hideTitle'] ) ) { ?>
			<h2 class="related-content__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<div class="slider" data-slider data-slider-label="<?php echo esc_attr( $cb_heading ); ?>" data-animate="fade-up">
			<div class="slider__viewport">
				<div class="slider__track">
					<?php foreach ( $cb_items as $cb_item ) { ?>
						<?php
						$cb_excerpt = '';

						if ( 'product' === $cb_item->post_type ) {
							$cb_excerpt = cb_peoplesafe2027_product_intro( $cb_item );
						}

						if ( '' === $cb_excerpt ) {
							$cb_excerpt = wp_trim_words( has_excerpt( $cb_item ) ? $cb_item->post_excerpt : wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $cb_item->post_content ) ) ), 20 );
						}
						?>
						<div class="slider__slide">
							<?php $cb_is_product = 'product' === $cb_item->post_type; ?>
							<article class="related-card<?php echo $cb_is_product ? ' product-cards__card' : ''; ?>">
								<?php if ( has_post_thumbnail( $cb_item ) ) { ?>
									<?php echo $cb_is_product ? '<div class="product-cards__media">' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php
									echo get_the_post_thumbnail(
										$cb_item,
										'large',
										array(
											'class'   => 'related-card__image',
											'alt'     => '',
											'loading' => 'lazy',
										)
									); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
									?>
									<?php echo $cb_is_product ? '</div>' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php } ?>
								<div class="related-card__inner">
									<h3 class="related-card__title"><a class="related-card__link" href="<?php echo esc_url( get_permalink( $cb_item ) ); ?>"><?php echo esc_html( get_the_title( $cb_item ) ); ?></a></h3>
									<?php if ( $cb_excerpt ) { ?>
										<p class="related-card__excerpt"><?php echo esc_html( $cb_excerpt ); ?></p>
									<?php } ?>
								</div>
							</article>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>
