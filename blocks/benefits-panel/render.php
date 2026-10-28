<?php
/**
 * Block template for Benefits panel.
 *
 * A tinted panel: a title and introduction over a 12-column mosaic. A tile is
 * a benefit card (icon, title, text) or a photo, and is 3, 4 or 6 columns
 * wide, optionally starting 3 columns in. A card can be stacked under the card
 * before it, so two small cards share one tile. One column on a phone.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_spans = array( 3, 4, 6 );
$cb_cells = array();

foreach ( isset( $attributes['items'] ) ? (array) $attributes['items'] : array() as $cb_row ) {
	$cb_type  = isset( $cb_row['type'] ) && 'photo' === $cb_row['type'] ? 'photo' : 'card';
	$cb_image = ! empty( $cb_row['image'] ) ? (int) $cb_row['image'] : 0;
	$cb_head  = isset( $cb_row['title'] ) ? trim( wp_strip_all_tags( $cb_row['title'] ) ) : '';

	if ( ( 'photo' === $cb_type && ! $cb_image ) || ( 'card' === $cb_type && '' === $cb_head && ! $cb_image ) ) {
		continue;
	}

	$cb_item = array(
		'type'    => $cb_type,
		'image'   => $cb_image,
		'title'   => $cb_head,
		'content' => isset( $cb_row['content'] ) ? trim( wp_strip_all_tags( $cb_row['content'] ) ) : '',
		'span'    => isset( $cb_row['span'] ) && in_array( (int) $cb_row['span'], $cb_spans, true ) ? (int) $cb_row['span'] : 3,
		'offset'  => ! empty( $cb_row['offset'] ) ? 3 : 0,
	);

	$cb_last = count( $cb_cells ) - 1;

	if ( 'card' === $cb_type && ! empty( $cb_row['stack'] ) && $cb_last >= 0 && 'card' === $cb_cells[ $cb_last ]['type'] ) {
		$cb_cells[ $cb_last ]['items'][] = $cb_item;
		continue;
	}

	$cb_cells[] = array(
		'type'   => $cb_type,
		'span'   => $cb_item['span'],
		'offset' => $cb_item['offset'],
		'items'  => array( $cb_item ),
	);
}

if ( ! $cb_cells ) {
	return;
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_note    = isset( $attributes['footnote'] ) ? $attributes['footnote'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'benefits-panel' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="benefits-panel__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<?php if ( '' !== wp_strip_all_tags( $cb_intro ) ) { ?>
			<div class="benefits-panel__intro"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_intro ) ); ?></div>
		<?php } ?>
		<div class="benefits-panel__grid" data-animate="fade-up">
			<?php foreach ( $cb_cells as $cb_cell ) { ?>
				<div class="benefits-panel__cell benefits-panel__cell--<?php echo esc_attr( $cb_cell['type'] ); ?> benefits-panel__cell--span-<?php echo (int) $cb_cell['span']; ?><?php echo $cb_cell['offset'] ? ' benefits-panel__cell--offset' : ''; ?>">
					<?php foreach ( $cb_cell['items'] as $cb_item ) { ?>
						<?php if ( 'photo' === $cb_item['type'] ) { ?>
							<?php echo wp_get_attachment_image( $cb_item['image'], 'large', false, array( 'class' => 'benefits-panel__photo', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php } else { ?>
							<div class="benefits-panel__card">
								<?php if ( $cb_item['image'] ) { ?>
									<?php echo wp_get_attachment_image( $cb_item['image'], 'thumbnail', false, array( 'class' => 'benefits-panel__icon', 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php } ?>
								<?php if ( $cb_item['title'] ) { ?>
									<h3 class="benefits-panel__card-title"><?php echo esc_html( $cb_item['title'] ); ?></h3>
								<?php } ?>
								<?php if ( $cb_item['content'] ) { ?>
									<p class="benefits-panel__card-text"><?php echo esc_html( $cb_item['content'] ); ?></p>
								<?php } ?>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
		<?php if ( '' !== wp_strip_all_tags( $cb_note ) ) { ?>
			<div class="benefits-panel__footnote"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_note ) ); ?></div>
		<?php } ?>
	</div>
</section>
