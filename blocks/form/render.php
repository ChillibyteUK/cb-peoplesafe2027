<?php
/**
 * Block template for Form.
 *
 * A Pardot form in an iframe, loaded when it nears the screen. The page's
 * campaign parameters (utm_*, gclid and so on) are passed on to the form by
 * src/js/form-frame.js so the lead keeps its source. An empty address uses the
 * Book a Demo form from Site-Wide Settings.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_url = ! empty( $attributes['formUrl'] ) ? $attributes['formUrl'] : cb_peoplesafe2027_demo_form_url();

if ( ! $cb_url ) {
	return;
}

$cb_title  = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro  = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_height = ! empty( $attributes['height'] ) ? (int) $attributes['height'] : 1200;

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'form-embed' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_title ) { ?>
			<h2 class="form-embed__title"><?php echo esc_html( $cb_title ); ?></h2>
		<?php } ?>
		<?php if ( $cb_intro ) { ?>
			<p class="form-embed__intro"><?php echo esc_html( $cb_intro ); ?></p>
		<?php } ?>
		<iframe class="form-embed__frame" data-form-src="<?php echo esc_url( $cb_url ); ?>" title="<?php echo esc_attr( $cb_title ? $cb_title : __( 'Form', 'cb-peoplesafe2027' ) ); ?>" loading="lazy" width="100%" height="<?php echo esc_attr( $cb_height ); ?>" style="border:0"></iframe>
	</div>
</section>
