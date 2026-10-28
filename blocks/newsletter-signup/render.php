<?php
/**
 * Block template for Newsletter signup.
 *
 * A panel with an email box. Submitting it opens the newsletter signup page with the address in the query string
 * (?email=), which the page's form picks up. The page defaults to /newsletter-signup/ in the visitor's region.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_title    = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_text     = isset( $attributes['content'] ) ? $attributes['content'] : '';
$cb_button   = ! empty( $attributes['buttonLabel'] ) ? $attributes['buttonLabel'] : __( 'Sign Up', 'cb-peoplesafe2027' );
$cb_image_id = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$cb_image_url = ! $cb_image_id && ! empty( $attributes['imageUrl'] ) ? $attributes['imageUrl'] : '';
$cb_action   = ! empty( $attributes['signupUrl'] ) ? $attributes['signupUrl'] : trailingslashit( cb_peoplesafe2027_region_url( function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : 'uk' ) ) . 'newsletter-signup/';
$cb_field_id = wp_unique_id( 'newsletter-email-' );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'newsletter-signup' . ( $cb_image_id || $cb_image_url ? ' newsletter-signup--has-image' : '' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="newsletter-signup__box">
			<div class="newsletter-signup__copy">
				<?php if ( $cb_title ) { ?>
					<h2 class="newsletter-signup__title"><?php echo esc_html( $cb_title ); ?></h2>
				<?php } ?>
				<?php if ( $cb_text ) { ?>
					<div class="newsletter-signup__text"><?php echo wp_kses_post( $cb_text ); ?></div>
				<?php } ?>
				<form class="newsletter-signup__form" method="get" action="<?php echo esc_url( $cb_action ); ?>">
					<label class="visually-hidden" for="<?php echo esc_attr( $cb_field_id ); ?>"><?php esc_html_e( 'Email address', 'cb-peoplesafe2027' ); ?></label>
					<input id="<?php echo esc_attr( $cb_field_id ); ?>" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email address', 'cb-peoplesafe2027' ); ?>" required>
					<button class="button button--primary" type="submit"><?php echo esc_html( $cb_button ); ?></button>
				</form>
			</div>
			<?php if ( $cb_image_id || $cb_image_url ) { ?>
				<div class="newsletter-signup__media">
					<?php if ( $cb_image_id ) { ?>
						<?php echo wp_get_attachment_image( $cb_image_id, 'large', false, array( 'class' => 'newsletter-signup__image', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php } else { ?>
						<img class="newsletter-signup__image" src="<?php echo esc_url( $cb_image_url ); ?>" alt="" loading="lazy">
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
