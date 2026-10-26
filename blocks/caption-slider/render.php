<?php
/**
 * Block template for Caption slider.
 *
 * White cards (an icon over a caption, the whole card a link) in the shared
 * slider: two across on a phone up to five on a wide screen. When they all fit
 * it is a still row; otherwise it loops, with dots and arrows.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_cards = array();

foreach ( isset( $attributes['cards'] ) ? (array) $attributes['cards'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['caption'] ) ) {
		$cb_cards[] = $cb_row;
	}
}

if ( ! $cb_cards ) {
	return;
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';

cb_peoplesafe2027_enqueue_slider();

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'caption-slider' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="caption-slider__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<div class="slider" data-slider data-slider-loop="true" data-slider-step="1" data-slider-label="<?php echo esc_attr( $cb_heading ? $cb_heading : __( 'Related pages', 'cb-peoplesafe2027' ) ); ?>" data-animate="fade-up">
			<div class="slider__viewport">
				<div class="slider__track">
					<?php foreach ( $cb_cards as $cb_card ) { ?>
						<?php
						$cb_image_id = isset( $cb_card['image'] ) ? (int) $cb_card['image'] : 0;
						$cb_link     = ! empty( $cb_card['link'] ) ? $cb_card['link'] : '';
						$cb_tag      = $cb_link ? 'a' : 'div';
						$cb_attrs    = $cb_link ? ' href="' . esc_url( $cb_link ) . '"' . ( ! empty( $cb_card['linkTarget'] ) ? ' target="_blank" rel="noopener"' : '' ) : '';
						?>
						<div class="slider__slide">
							<<?php echo $cb_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> class="caption-slider__card"<?php echo $cb_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php if ( $cb_image_id ) { ?>
									<span class="caption-slider__image">
										<?php echo wp_get_attachment_image( $cb_image_id, 'medium', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</span>
								<?php } ?>
								<span class="caption-slider__caption"><?php echo esc_html( $cb_card['caption'] ); ?></span>
							</<?php echo $cb_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>
