<?php
/**
 * Block template for Image columns.
 *
 * Two to five images side by side, each with an optional title and text centred below it. The title
 * and introduction above are ordinary heading and paragraph blocks.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_items = array();

foreach ( isset( $attributes['items'] ) ? (array) $attributes['items'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['image'] ) || ! empty( $cb_row['title'] ) || ! empty( $cb_row['text'] ) ) {
		$cb_items[] = $cb_row;
	}
}

if ( ! $cb_items ) {
	return;
}

$cb_allowed = array(
	'a'      => array(
		'href'   => true,
		'target' => true,
		'rel'    => true,
	),
	'strong' => array(),
	'b'      => array(),
	'em'     => array(),
	'i'      => array(),
	'mark'   => array(
		'class' => true,
		'style' => true,
	),
);

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'image-columns image-columns--' . count( $cb_items ) . ( 'cards' === ( isset( $attributes['style'] ) ? $attributes['style'] : '' ) ? ' image-columns--cards' : '' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="image-columns__grid" data-animate="fade-up">
			<?php
			foreach ( $cb_items as $cb_item ) {
				$cb_image_id = isset( $cb_item['image'] ) ? (int) $cb_item['image'] : 0;
				$cb_title    = isset( $cb_item['title'] ) ? trim( wp_strip_all_tags( $cb_item['title'] ) ) : '';
				$cb_text     = isset( $cb_item['text'] ) ? cb_peoplesafe2027_rich_paragraphs( $cb_item['text'] ) : '';
				?>
				<div class="image-columns__item">
					<?php
					if ( $cb_image_id ) {
						echo '<div class="image-columns__media">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo wp_get_attachment_image(
							$cb_image_id,
							'large',
							false,
							array(
								'class'   => 'image-columns__image',
								'loading' => 'lazy',
							)
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						echo '</div>';
					}

					if ( '' !== $cb_title ) {
						echo '<h3 class="image-columns__title">' . esc_html( $cb_title ) . '</h3>';
					}

					if ( $cb_text ) {
						echo '<div class="image-columns__text">' . wp_kses( $cb_text, array_merge( $cb_allowed, array( 'p' => array() ) ) ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by wp_kses().
					}
					?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
