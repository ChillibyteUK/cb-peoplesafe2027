<?php
/**
 * Block template for Home hero.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_title      = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_word_one   = isset( $attributes['wordOne'] ) ? $attributes['wordOne'] : '';
$cb_word_two   = isset( $attributes['wordTwo'] ) ? $attributes['wordTwo'] : '';
$cb_intro      = isset( $attributes['content'] ) ? $attributes['content'] : '';
$cb_buttons    = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_mobile_id  = isset( $attributes['mobileImageId'] ) ? (int) $attributes['mobileImageId'] : 0;
$cb_mobile_url = isset( $attributes['mobileImageUrl'] ) ? $attributes['mobileImageUrl'] : '';
$cb_mobile_alt = isset( $attributes['mobileImageAlt'] ) ? $attributes['mobileImageAlt'] : '';

// The animation layers, back to front. Decorative: the headline carries the message.
$cb_layers = array( 'circle', 'dots', 'gradient', 'shakira', 'laptop', 'phone1', 'phone2', 'phone3' );
$cb_img    = get_template_directory_uri() . '/img/home-hero/';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'home-hero' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container home-hero__inner">
		<div class="home-hero__content">
			<h1 class="home-hero__title">
				<?php if ( $cb_title ) { ?>
					<span class="home-hero__title-text"><?php echo esc_html( $cb_title ); ?></span>
				<?php } ?>
				<?php if ( $cb_word_one || $cb_word_two ) { ?>
					<span class="home-hero__words">
						<?php if ( $cb_word_one ) { ?>
							<span><?php echo esc_html( $cb_word_one ); ?></span>
						<?php } ?>
						<?php if ( $cb_word_two ) { ?>
							<span><?php echo esc_html( $cb_word_two ); ?></span>
						<?php } ?>
					</span>
				<?php } ?>
			</h1>
			<?php if ( $cb_intro ) { ?>
				<div class="home-hero__text"><?php echo wp_kses_post( $cb_intro ); ?></div>
			<?php } ?>
			<?php cb_peoplesafe2027_cta_buttons( $cb_buttons ); ?>
		</div>
		<div class="home-hero__media">
			<div class="home-hero__anim" aria-hidden="true">
				<?php foreach ( $cb_layers as $cb_layer ) { ?>
					<img class="home-hero__layer home-hero__layer--<?php echo esc_attr( $cb_layer ); ?>" src="<?php echo esc_url( $cb_img . $cb_layer . '.png' ); ?>" width="480" height="480" alt="" loading="eager" decoding="async">
				<?php } ?>
			</div>
			<?php if ( $cb_mobile_id ) { ?>
				<?php
				echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$cb_mobile_id,
					'large',
					false,
					array(
						'class'         => 'home-hero__still',
						'alt'           => $cb_mobile_alt,
						'fetchpriority' => 'high',
					)
				);
				?>
			<?php } elseif ( $cb_mobile_url ) { ?>
				<img class="home-hero__still" src="<?php echo esc_url( $cb_mobile_url ); ?>" alt="<?php echo esc_attr( $cb_mobile_alt ); ?>" fetchpriority="high">
			<?php } ?>
		</div>
	</div>
</section>
