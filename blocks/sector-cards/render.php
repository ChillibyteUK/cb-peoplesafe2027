<?php
/**
 * Block template for Sector cards.
 *
 * A card for every page directly under the current page (the Sectors page), in
 * menu order: the page's featured image cropped to 3:2 with a curved bottom edge,
 * then its title. The Public / Private buttons (src/js/sector-filter.js) show
 * the cards whose sector group (the page's cb_sector_group setting) matches.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_pages = get_pages(
	array(
		'parent'      => get_the_ID(),
		'sort_column' => 'menu_order, post_title',
		'post_status' => 'publish',
	)
);

if ( ! $cb_pages ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'sector-cards' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="sector-cards__filters" role="group" aria-label="<?php esc_attr_e( 'Filter the sectors', 'cb-peoplesafe2027' ); ?>" hidden>
			<span class="sector-cards__filters-label"><?php esc_html_e( 'Sector type', 'cb-peoplesafe2027' ); ?></span>
			<button type="button" class="sector-cards__filter is-active" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All', 'cb-peoplesafe2027' ); ?></button>
			<button type="button" class="sector-cards__filter" data-filter="private" aria-pressed="false"><?php esc_html_e( 'Private sector', 'cb-peoplesafe2027' ); ?></button>
			<button type="button" class="sector-cards__filter" data-filter="public" aria-pressed="false"><?php esc_html_e( 'Public sector', 'cb-peoplesafe2027' ); ?></button>
		</div>
		<div class="sector-cards__grid" data-animate="fade-up">
			<?php
			foreach ( $cb_pages as $cb_page ) {
				$cb_group  = cb_peoplesafe2027_sanitize_sector_group( get_post_meta( $cb_page->ID, 'cb_sector_group', true ) );
				$cb_filter = 'both' === $cb_group ? 'private public' : $cb_group;
				?>
				<a class="sector-cards__card" href="<?php echo esc_url( get_permalink( $cb_page ) ); ?>" data-groups="<?php echo esc_attr( $cb_filter ); ?>">
					<span class="sector-cards__image">
						<?php
						if ( has_post_thumbnail( $cb_page ) ) {
							echo get_the_post_thumbnail(
								$cb_page,
								'large',
								array(
									'alt'     => '',
									'loading' => 'lazy',
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</span>
					<span class="sector-cards__title"><?php echo esc_html( get_the_title( $cb_page ) ); ?></span>
				</a>
			<?php } ?>
		</div>
	</div>
</section>
