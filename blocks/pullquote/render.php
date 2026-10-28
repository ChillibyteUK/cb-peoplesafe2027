<?php
/**
 * Block template for Pull quote.
 *
 * One large quote on a coloured panel (yellow, navy or light) with a big opening
 * quote mark, and the attribution on the right. For the middle of a case study.
 * The Quote block is the one with logos, photos and a slider.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_quote = isset( $attributes['quote'] ) ? trim( wp_strip_all_tags( $attributes['quote'] ) ) : '';

if ( '' === $cb_quote ) {
	return;
}

$cb_themes      = array( 'yellow', 'navy', 'light' );
$cb_theme       = isset( $attributes['theme'] ) && in_array( $attributes['theme'], $cb_themes, true ) ? $attributes['theme'] : 'yellow';
$cb_attribution = isset( $attributes['attribution'] ) ? trim( wp_strip_all_tags( $attributes['attribution'] ) ) : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'pullquote pullquote--' . $cb_theme ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<figure class="pullquote__panel" data-animate="fade-up">
			<blockquote class="pullquote__quote"><p><?php echo esc_html( $cb_quote ); ?></p></blockquote>
			<?php if ( '' !== $cb_attribution ) { ?>
				<figcaption class="pullquote__attribution"><?php echo esc_html( $cb_attribution ); ?></figcaption>
			<?php } ?>
		</figure>
	</div>
</section>
