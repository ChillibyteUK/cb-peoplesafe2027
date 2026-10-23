<?php
/**
 * Block template for Quote.
 *
 * One quote is a static panel; two or more are a slider (the shared slider,
 * src/js/slider.js). The panel's colour cycles blue, orange, pink by position.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_quotes = array();

foreach ( isset( $attributes['quotes'] ) ? (array) $attributes['quotes'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['quote'] ) ) {
		$cb_quotes[] = $cb_row;
	}
}

if ( ! $cb_quotes ) {
	return;
}

$cb_colours   = array( 'blue', 'orange', 'pink' );
$cb_is_slider = count( $cb_quotes ) > 1;

if ( $cb_is_slider ) {
	cb_peoplesafe2027_enqueue_slider();
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'quote' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="slider"<?php echo $cb_is_slider ? ' data-slider data-slider-label="' . esc_attr__( 'Customer quotes', 'cb-peoplesafe2027' ) . '"' : ''; ?> data-animate="fade-up">
			<div class="slider__viewport">
				<div class="slider__track">
					<?php foreach ( $cb_quotes as $cb_index => $cb_quote ) { ?>
						<?php
						$cb_colour   = $cb_colours[ $cb_index % count( $cb_colours ) ];
						$cb_image_id = isset( $cb_quote['image'] ) ? (int) $cb_quote['image'] : 0;
						$cb_logo_id  = isset( $cb_quote['logo'] ) ? (int) $cb_quote['logo'] : 0;
						$cb_cite     = isset( $cb_quote['attribution'] ) ? $cb_quote['attribution'] : '';
						?>
						<div class="slider__slide">
							<figure class="quote__card quote__card--<?php echo esc_attr( $cb_colour ); ?><?php echo $cb_image_id ? ' quote__card--has-image' : ''; ?>">
								<?php if ( $cb_image_id ) { ?>
									<?php
									echo wp_get_attachment_image(
										$cb_image_id,
										'large',
										false,
										array(
											'class' => 'quote__image',
										)
									); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
									?>
								<?php } ?>
								<div class="quote__inner">
									<blockquote class="quote__text"><?php echo wp_kses_post( wpautop( $cb_quote['quote'] ) ); ?></blockquote>
									<?php if ( $cb_cite ) { ?>
										<figcaption class="quote__cite"><?php echo esc_html( $cb_cite ); ?></figcaption>
									<?php } ?>
									<?php if ( $cb_logo_id ) { ?>
										<?php echo wp_get_attachment_image( $cb_logo_id, 'medium', false, array( 'class' => 'quote__logo' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php } ?>
								</div>
							</figure>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>
