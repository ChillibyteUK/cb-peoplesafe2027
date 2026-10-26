<?php
/**
 * Block template for CTA banner.
 *
 * A call to action on a gradient: title, text and buttons, with an optional
 * image on the left from the large breakpoint up.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_themes = array(
	'orange' => 'var(--col-orange-400)',
	'pink'   => 'var(--col-pink-400)',
	'blue'   => 'var(--col-blue-400)',
	'plain'  => '',
);

$cb_theme    = isset( $attributes['theme'], $cb_themes[ $attributes['theme'] ] ) ? $attributes['theme'] : 'orange';
$cb_heading  = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_text     = isset( $attributes['content'] ) ? $attributes['content'] : '';
$cb_buttons  = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_image_id = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cta-banner cta-banner--' . $cb_theme . ( $cb_image_id ? ' cta-banner--has-image' : '' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container cta-banner__grid">
		<?php if ( $cb_image_id ) { ?>
			<div class="cta-banner__media">
				<?php
				echo wp_get_attachment_image(
					$cb_image_id,
					'large',
					false,
					array(
						'class' => 'cta-banner__image',
					)
				); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
				?>
			</div>
		<?php } ?>
		<div class="cta-banner__content" data-animate="fade-left">
			<?php if ( $cb_heading ) { ?>
				<h2 class="cta-banner__title"><?php echo esc_html( $cb_heading ); ?></h2>
			<?php } ?>
			<?php if ( $cb_text ) { ?>
				<div class="cta-banner__text"><?php echo wp_kses_post( $cb_text ); ?></div>
			<?php } ?>
			<?php
			cb_peoplesafe2027_cta_buttons(
				$cb_buttons,
				'plain' === $cb_theme ? array( 'defaults' => array( 'primary', 'outline' ) ) : array(
					'defaults' => array( 'white' ),
					'accent'   => $cb_themes[ $cb_theme ],
				)
			);
			?>
			<?php if ( ! empty( $attributes['formUrl'] ) ) { ?>
				<iframe class="cta-banner__form" data-form-src="<?php echo esc_url( $attributes['formUrl'] ); ?>" title="<?php echo esc_attr( $cb_heading ? $cb_heading : __( 'Form', 'cb-peoplesafe2027' ) ); ?>" loading="lazy" width="100%" height="<?php echo esc_attr( ! empty( $attributes['formHeight'] ) ? (int) $attributes['formHeight'] : 550 ); ?>" style="border:0"></iframe>
			<?php } ?>
		</div>
	</div>
</section>
