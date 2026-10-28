<?php
/**
 * Block template for Product cards.
 *
 * A card for every product in the current region (image, name, intro), in
 * menu order, with a filter button for each product type in use
 * (src/js/product-filter.js). A page can open with one type chosen, as
 * ?q=type-slug. Without JavaScript every card shows.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_products = get_posts(
	array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);

if ( ! $cb_products ) {
	return;
}

$cb_types = array();

foreach ( $cb_products as $cb_product ) {
	$cb_terms = get_the_terms( $cb_product, 'product_type' );

	foreach ( is_array( $cb_terms ) ? $cb_terms : array() as $cb_term ) {
		$cb_types[ $cb_term->slug ] = $cb_term->name;
	}
}

asort( $cb_types );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'product-cards' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_types ) { ?>
			<div class="product-cards__filters" role="group" aria-label="<?php esc_attr_e( 'Filter the products', 'cb-peoplesafe2027' ); ?>" hidden>
				<span class="product-cards__filters-label"><?php esc_html_e( 'Type', 'cb-peoplesafe2027' ); ?></span>
				<button type="button" class="product-cards__filter is-active" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All', 'cb-peoplesafe2027' ); ?></button>
				<?php foreach ( $cb_types as $cb_slug => $cb_name ) { ?>
					<button type="button" class="product-cards__filter" data-filter="<?php echo esc_attr( $cb_slug ); ?>" aria-pressed="false"><?php echo esc_html( $cb_name ); ?></button>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="product-cards__grid" data-animate="fade-up">
			<?php
			foreach ( $cb_products as $cb_product ) {
				$cb_slugs = wp_list_pluck( (array) get_the_terms( $cb_product, 'product_type' ), 'slug' );
				$cb_intro = cb_peoplesafe2027_product_intro( $cb_product );
				?>
				<article class="related-card product-cards__card" data-types="<?php echo esc_attr( implode( ' ', $cb_slugs ) ); ?>">
					<div class="product-cards__media">
					<?php
					if ( has_post_thumbnail( $cb_product ) ) {
						echo get_the_post_thumbnail(
							$cb_product,
							'large',
							array(
								'class'   => 'related-card__image',
								'alt'     => '',
								'loading' => 'lazy',
							)
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
					</div>
					<div class="related-card__inner">
						<h3 class="related-card__title"><a class="related-card__link" href="<?php echo esc_url( get_permalink( $cb_product ) ); ?>"><?php echo esc_html( get_the_title( $cb_product ) ); ?></a></h3>
						<?php if ( $cb_intro ) { ?>
							<p class="related-card__excerpt"><?php echo esc_html( $cb_intro ); ?></p>
						<?php } ?>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
