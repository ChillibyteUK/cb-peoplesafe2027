<?php
/**
 * Block template for Embed.
 *
 * A Turtl document, from its embed code, or a PDF in a frame. Only the Turtl
 * link in the pasted code is kept (a link with its data attributes); any
 * other markup, scripts included, is dropped. The Turtl script is loaded by
 * the theme, once, in the footer.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_kind  = isset( $attributes['kind'] ) ? $attributes['kind'] : 'turtl';
$cb_title = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_embed = '';

if ( 'pdf' === $cb_kind ) {
	$cb_url = ! empty( $attributes['file'] ) ? wp_get_attachment_url( (int) $attributes['file'] ) : '';

	if ( $cb_url ) {
		$cb_embed = '<iframe class="embed-block__pdf" src="' . esc_url( $cb_url ) . '" title="' . esc_attr( $cb_title ? $cb_title : __( 'PDF document', 'cb-peoplesafe2027' ) ) . '" loading="lazy"></iframe>';
	}
} elseif ( ! empty( $attributes['code'] ) ) {
	$cb_embed = wp_kses(
		$attributes['code'],
		array(
			'a' => array(
				'class'  => true,
				'href'   => true,
				'target' => true,
				'rel'    => true,
				'title'  => true,
				'data-*' => true,
			),
		)
	);

	if ( $cb_embed ) {
		cb_peoplesafe2027_enqueue_turtl();
	}
}

if ( ! $cb_embed ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'embed-block embed-block--' . sanitize_html_class( $cb_kind ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_title ) { ?>
			<h2 class="embed-block__title"><?php echo esc_html( $cb_title ); ?></h2>
		<?php } ?>
		<div class="embed-block__frame"><?php echo $cb_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_url()/esc_attr() or wp_kses() above. ?></div>
	</div>
</section>
