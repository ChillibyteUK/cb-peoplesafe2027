<?php
/**
 * Block template for Home hero.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$title      = isset( $attributes['title'] ) ? $attributes['title'] : '';
$word_one   = isset( $attributes['wordOne'] ) ? $attributes['wordOne'] : '';
$word_two   = isset( $attributes['wordTwo'] ) ? $attributes['wordTwo'] : '';
$content    = isset( $attributes['content'] ) ? $attributes['content'] : '';
$buttons    = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$mobile_id  = isset( $attributes['mobileImageId'] ) ? (int) $attributes['mobileImageId'] : 0;
$mobile_url = isset( $attributes['mobileImageUrl'] ) ? $attributes['mobileImageUrl'] : '';
$mobile_alt = isset( $attributes['mobileImageAlt'] ) ? $attributes['mobileImageAlt'] : '';

// The animation layers, back to front. Decorative: the headline carries the message.
$layers = array( 'circle', 'dots', 'gradient', 'shakira', 'laptop', 'phone1', 'phone2', 'phone3' );
$img    = get_template_directory_uri() . '/img/home-hero/';

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'home-hero' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container home-hero__inner">
		<div class="home-hero__content">
			<h1 class="home-hero__title">
				<?php if ( $title ) : ?>
					<span class="home-hero__title-text"><?php echo esc_html( $title ); ?></span>
				<?php endif; ?>
				<?php if ( $word_one || $word_two ) : ?>
					<span class="home-hero__words">
						<?php if ( $word_one ) : ?>
							<span><?php echo esc_html( $word_one ); ?></span>
						<?php endif; ?>
						<?php if ( $word_two ) : ?>
							<span><?php echo esc_html( $word_two ); ?></span>
						<?php endif; ?>
					</span>
				<?php endif; ?>
			</h1>
			<?php if ( $content ) : ?>
				<div class="home-hero__text"><?php echo wp_kses_post( $content ); ?></div>
			<?php endif; ?>
			<?php cb_peoplesafe2027_cta_buttons( $buttons ); ?>
		</div>
		<div class="home-hero__media">
			<div class="home-hero__anim" aria-hidden="true">
				<?php foreach ( $layers as $layer ) : ?>
					<img class="home-hero__layer home-hero__layer--<?php echo esc_attr( $layer ); ?>" src="<?php echo esc_url( $img . $layer . '.png' ); ?>" width="480" height="480" alt="" loading="eager" decoding="async">
				<?php endforeach; ?>
			</div>
			<?php if ( $mobile_id ) : ?>
				<?php echo wp_get_attachment_image( $mobile_id, 'large', false, array( 'class' => 'home-hero__still', 'alt' => $mobile_alt, 'fetchpriority' => 'high' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php elseif ( $mobile_url ) : ?>
				<img class="home-hero__still" src="<?php echo esc_url( $mobile_url ); ?>" alt="<?php echo esc_attr( $mobile_alt ); ?>" fetchpriority="high">
			<?php endif; ?>
		</div>
	</div>
</section>
