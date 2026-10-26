<?php
/**
 * Block template for Cards.
 *
 * Two to four cards: an image, a title, text or a list, an optional link and a
 * colour (white, yellow, green, red or blue). Equal height in a row.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_colours = array( 'white', 'yellow', 'green', 'red', 'blue' );
$cb_cards   = array();

foreach ( isset( $attributes['cards'] ) ? (array) $attributes['cards'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['title'] ) || ! empty( $cb_row['content'] ) || ! empty( $cb_row['image'] ) ) {
		$cb_cards[] = $cb_row;
	}
}

if ( count( $cb_cards ) < 2 ) {
	return;
}

$cb_title = isset( $attributes['title'] ) ? $attributes['title'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'cards cards--' . count( $cb_cards ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_title ) { ?>
			<h2 class="cards__title"><?php echo esc_html( $cb_title ); ?></h2>
		<?php } ?>
		<div class="cards__grid" data-animate="fade-up">
			<?php
			foreach ( $cb_cards as $cb_card ) {
				$cb_colour = isset( $cb_card['colour'] ) && in_array( $cb_card['colour'], $cb_colours, true ) ? $cb_card['colour'] : 'white';
				$cb_image  = ! empty( $cb_card['image'] ) ? (int) $cb_card['image'] : 0;
				?>
				<article class="cards__card cards__card--<?php echo esc_attr( $cb_colour ); ?>">
					<?php if ( $cb_image ) { ?>
						<div class="cards__image"><?php echo wp_get_attachment_image( $cb_image, 'large', false, array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php } ?>
					<div class="cards__content">
						<?php if ( ! empty( $cb_card['title'] ) ) { ?>
							<h2 class="cards__card-title"><?php echo esc_html( $cb_card['title'] ); ?></h2>
						<?php } ?>
						<?php if ( ! empty( $cb_card['content'] ) ) { ?>
							<div class="cards__text"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_card['content'] ) ); ?></div>
						<?php } ?>
						<?php if ( ! empty( $cb_card['link'] ) && ! empty( $cb_card['linkText'] ) ) { ?>
							<a class="cards__link" href="<?php echo esc_url( $cb_card['link'] ); ?>"<?php echo ! empty( $cb_card['linkTarget'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $cb_card['linkText'] ); ?></a>
						<?php } ?>
					</div>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
