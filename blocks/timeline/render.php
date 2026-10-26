<?php
/**
 * Block template for Timeline.
 *
 * A vertical line with dated entries alternating left and right (stacked on a
 * phone), each with an optional icon and one of five colours.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_colours = array( 'yellow', 'pink', 'orange', 'green', 'grey' );
$cb_entries = array();

foreach ( isset( $attributes['entries'] ) ? (array) $attributes['entries'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['content'] ) || ! empty( $cb_row['year'] ) ) {
		$cb_entries[] = $cb_row;
	}
}

if ( ! $cb_entries ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'timeline' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<ol class="timeline__list">
			<?php
			foreach ( $cb_entries as $cb_entry ) {
				$cb_colour = isset( $cb_entry['colour'] ) && in_array( $cb_entry['colour'], $cb_colours, true ) ? $cb_entry['colour'] : 'yellow';
				$cb_icon   = ! empty( $cb_entry['icon'] ) ? (int) $cb_entry['icon'] : 0;
				?>
				<li class="timeline__item timeline__item--<?php echo esc_attr( $cb_colour ); ?>" data-animate="fade-up">
					<div class="timeline__card">
						<?php if ( ! empty( $cb_entry['year'] ) ) { ?>
							<h2 class="timeline__year"><?php echo esc_html( $cb_entry['year'] ); ?></h2>
						<?php } ?>
						<div class="timeline__body">
							<?php if ( $cb_icon ) { ?>
								<span class="timeline__icon"><?php echo wp_get_attachment_image( $cb_icon, 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<?php } ?>
							<div class="timeline__text"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs_inline( isset( $cb_entry['content'] ) ? $cb_entry['content'] : '' ) ); ?></div>
						</div>
					</div>
				</li>
			<?php } ?>
		</ol>
	</div>
</section>
