<?php
/**
 * Block template for Logo slider.
 *
 * Client logos in the shared slider: two across on a phone up to five on a wide
 * screen. When they all fit it is a still row; when they do not it loops, and
 * rotates by itself unless the block turns that off. An optional button goes to
 * the case studies, all of them or one sector's.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_logo_ids = array();

foreach ( isset( $attributes['logos'] ) ? (array) $attributes['logos'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['logo'] ) ) {
		$cb_logo_ids[] = (int) $cb_row['logo'];
	}
}

if ( ! $cb_logo_ids ) {
	return;
}

$cb_heading  = ! empty( $attributes['title'] ) ? $attributes['title'] : __( 'Some of our clients', 'cb-peoplesafe2027' );
$cb_autoplay = ! isset( $attributes['autoplay'] ) || $attributes['autoplay'];
$cb_button   = '';

if ( ! empty( $attributes['caseStudies'] ) ) {
	$cb_region = function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : 'uk';
	$cb_button = home_url( '/' . $cb_region . '/case-studies/' );

	if ( 'all' !== $attributes['caseStudies'] ) {
		$cb_button = add_query_arg( 'sector', sanitize_title( $attributes['caseStudies'] ), $cb_button );
	}
}

cb_peoplesafe2027_enqueue_slider();

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'logo-slider' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<h2 class="logo-slider__title"><?php echo wp_kses( $cb_heading, array( 'br' => array() ) ); ?></h2>
		<div class="slider" data-slider data-slider-loop="true" data-slider-controls="none"<?php echo $cb_autoplay ? ' data-slider-autoplay="3000"' : ''; ?> data-slider-label="<?php echo esc_attr( wp_strip_all_tags( $cb_heading ) ); ?>" data-animate="fade-up">
			<div class="slider__viewport">
				<div class="slider__track">
					<?php foreach ( $cb_logo_ids as $cb_logo_id ) { ?>
						<div class="slider__slide">
							<div class="logo-slider__logo">
								<?php
								echo wp_get_attachment_image(
									$cb_logo_id,
									'large',
									false,
									array(
										'loading' => 'lazy',
										'sizes'   => '(min-width: 768px) 180px, 35vw',
									)
								); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
								?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php if ( $cb_button ) { ?>
			<div class="logo-slider__more">
				<a class="button button--primary" href="<?php echo esc_url( $cb_button ); ?>"><?php esc_html_e( 'View our case studies', 'cb-peoplesafe2027' ); ?></a>
			</div>
		<?php } ?>
	</div>
</section>
