<?php
/**
 * Block template for Hero.
 *
 * The title and the image fall back to the page's own title and featured
 * image, so most pages need only the intro and the buttons.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_source_id = ! empty( $block->context['postId'] ) ? (int) $block->context['postId'] : get_the_ID();

$cb_heading    = ! empty( $attributes['title'] ) ? $attributes['title'] : get_the_title( $cb_source_id );
$cb_label      = isset( $attributes['label'] ) ? trim( $attributes['label'] ) : '';
$cb_hide_title = ! empty( $attributes['hideTitle'] );
$cb_intro      = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_buttons    = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_image_id   = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;

if ( ! $cb_image_id && has_post_thumbnail( $cb_source_id ) ) {
	$cb_image_id = (int) get_post_thumbnail_id( $cb_source_id );
}

$cb_image_args = array(
	'class'         => 'hero__image',
	'loading'       => 'eager',
	'fetchpriority' => 'high',
);

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'hero' . ( $cb_image_id ? '' : ' hero--no-image' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php
		if ( empty( $attributes['hideBreadcrumbs'] ) ) {
			cb_peoplesafe2027_render_breadcrumbs( cb_peoplesafe2027_get_breadcrumbs( $cb_source_id ), 'cb-breadcrumbs hero__breadcrumbs' );
		}
		?>
		<div class="hero__grid">
			<?php if ( $cb_image_id ) { ?>
				<div class="hero__media">
					<?php echo wp_get_attachment_image( $cb_image_id, 'large', false, $cb_image_args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php } ?>
			<div class="hero__content">
				<?php
				// With a label, the label is the page's H1 and the title becomes an H2 with the same large look.
				if ( $cb_label ) {
					?>
					<h1 class="hero__label"><?php echo esc_html( $cb_label ); ?></h1>
					<?php
				}
				if ( ! $cb_hide_title && $cb_heading ) {
					$cb_title_tag = $cb_label ? 'h2' : 'h1';
					?>
					<<?php echo esc_attr( $cb_title_tag ); ?> class="hero__title"><?php echo esc_html( $cb_heading ); ?></<?php echo esc_attr( $cb_title_tag ); ?>>
				<?php } ?>
				<?php if ( $cb_intro ) { ?>
					<div class="hero__intro"><?php echo wp_kses_post( $cb_intro ); ?></div>
				<?php } ?>
				<?php cb_peoplesafe2027_cta_buttons( $cb_buttons ); ?>
			</div>
		</div>
	</div>
</section>
