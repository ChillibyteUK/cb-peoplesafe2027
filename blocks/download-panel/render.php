<?php
/**
 * Block template for Download panel.
 *
 * A preview image, a title and a button for one file. The type and size
 * shown under the title are worked out from the file itself.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_file_id = ! empty( $attributes['file'] ) ? (int) $attributes['file'] : 0;
$cb_url     = $cb_file_id ? wp_get_attachment_url( $cb_file_id ) : '';

if ( ! $cb_url ) {
	return;
}

$cb_path  = get_attached_file( $cb_file_id );
$cb_size  = ( $cb_path && file_exists( $cb_path ) ) ? size_format( filesize( $cb_path ) ) : '';
$cb_type  = strtoupper( pathinfo( $cb_url, PATHINFO_EXTENSION ) );
$cb_title = ! empty( $attributes['title'] ) ? $attributes['title'] : get_the_title( $cb_file_id );
$cb_label = ! empty( $attributes['buttonLabel'] ) ? $attributes['buttonLabel'] : __( 'Download', 'cb-peoplesafe2027' );
$cb_image = ! empty( $attributes['image'] ) ? (int) $attributes['image'] : 0;
$cb_meta  = implode( ', ', array_filter( array( $cb_type, $cb_size ) ) );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'download-panel' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="download-panel__card">
			<?php if ( $cb_image ) { ?>
				<div class="download-panel__image">
					<?php echo wp_get_attachment_image( $cb_image, 'medium_large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php } ?>
			<div class="download-panel__body">
				<h2 class="download-panel__title"><?php echo esc_html( $cb_title ); ?></h2>
				<a class="button button--primary" href="<?php echo esc_url( $cb_url ); ?>" download><span><?php echo esc_html( $cb_label ); ?></span></a>
				<?php if ( $cb_meta ) { ?>
					<p class="download-panel__meta"><?php echo esc_html( $cb_meta ); ?></p>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
