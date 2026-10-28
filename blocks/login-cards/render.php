<?php
/**
 * Block template for Login cards.
 *
 * Two or three gradient cards side by side (one per portal): a screenshot
 * that rises above the card's top edge, a title and a white button. The
 * button's text takes the card's colour.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_colours = array(
	'blue'   => 'var(--col-blue-400)',
	'pink'   => 'var(--col-pink-400)',
	'orange' => 'var(--col-orange-400)',
);
$cb_cards   = array();

foreach ( isset( $attributes['cards'] ) ? (array) $attributes['cards'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['title'] ) && ! empty( $cb_row['link'] ) ) {
		$cb_cards[] = $cb_row;
	}
}

if ( ! $cb_cards ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'login-cards login-cards--' . count( $cb_cards ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="login-cards__grid" data-animate="fade-up">
			<?php
			foreach ( $cb_cards as $cb_card ) {
				$cb_colour = isset( $cb_card['colour'], $cb_colours[ $cb_card['colour'] ] ) ? $cb_card['colour'] : 'blue';
				$cb_image  = ! empty( $cb_card['image'] ) ? (int) $cb_card['image'] : 0;
				?>
				<article class="login-cards__card login-cards__card--<?php echo esc_attr( $cb_colour ); ?>">
					<?php if ( $cb_image ) { ?>
						<div class="login-cards__image"><?php echo wp_get_attachment_image( $cb_image, 'large', false, array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php } ?>
					<h2 class="login-cards__title"><?php echo esc_html( $cb_card['title'] ); ?></h2>
					<?php
					cb_peoplesafe2027_cta_buttons(
						array(
							array(
								'linkText'   => ! empty( $cb_card['buttonLabel'] ) ? $cb_card['buttonLabel'] : __( 'Login', 'cb-peoplesafe2027' ),
								'link'       => $cb_card['link'],
								'linkTarget' => ! empty( $cb_card['linkTarget'] ),
							),
						),
						array(
							'class'    => 'login-cards__buttons',
							'defaults' => array( 'white' ),
							'accent'   => $cb_colours[ $cb_colour ],
						)
					);
					?>
				</article>
			<?php } ?>
		</div>
	</div>
</section>
