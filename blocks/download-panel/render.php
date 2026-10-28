<?php
/**
 * Block template for Download panel.
 *
 * One primary button that opens a file from the media library in a new tab. The
 * address comes from the file itself. Nothing is shown without a file.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_file_id = ! empty( $attributes['file'] ) ? (int) $attributes['file'] : 0;
$cb_url     = $cb_file_id ? wp_get_attachment_url( $cb_file_id ) : '';

if ( ! $cb_url ) {
	return;
}

$cb_label = ! empty( $attributes['buttonLabel'] ) ? $attributes['buttonLabel'] : __( 'Download', 'cb-peoplesafe2027' );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'download-panel' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="button-group">
			<a class="button button--primary" href="<?php echo esc_url( $cb_url ); ?>" target="_blank" rel="noopener"><svg class="download-panel__icon" aria-hidden="true" focusable="false" viewBox="0 0 384 512"><path fill="currentColor" d="M64 0C28.7 0 0 28.7 0 64v384c0 35.3 28.7 64 64 64h256c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zm192 0v128h128L256 0zM96 240h30c21.6 0 38 16.4 38 38s-16.4 38-38 38h-14v32H96V240zm30 56c8.8 0 14-5.2 14-18s-5.2-14-14-14h-14v32h14zm58-56h32c26.5 0 44 17.5 44 44v32c0 26.5-17.5 44-44 44h-32V240zm28 24v76c11 0 20-6.5 20-20v-36c0-11-8-20-20-20zm62-24h60v24h-36v24h28v24h-28v32h-24V240z"/></svg><span><?php echo esc_html( $cb_label ); ?></span></a>
		</div>
	</div>
</section>
