<?php
/**
 * Block template for Product bento.
 *
 * A title, intro and buttons over a bento grid of cards. Each card is a short
 * statement (a bold lead, then lighter text) over an image, and can link to a
 * page. From lg the cards share rows on a six-column grid: a half card takes
 * three columns, a third card two. Below lg they stack, one per row.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_allowed_title = array(
	'strong' => array(),
	'b'      => array(),
	'em'     => array(),
	'i'      => array(),
	'br'     => array(),
);

$cb_cards = array();

foreach ( isset( $attributes['cards'] ) ? (array) $attributes['cards'] : array() as $cb_position => $cb_row ) {
	$cb_card_title = isset( $cb_row['title'] ) ? trim( wp_kses( $cb_row['title'], $cb_allowed_title ) ) : '';
	$cb_card_image = isset( $cb_row['image'] ) ? (int) $cb_row['image'] : 0;

	if ( '' === wp_strip_all_tags( $cb_card_title ) && ! $cb_card_image ) {
		continue;
	}

	// Half or third of the row. With no choice made, the first two cards are halves and the rest thirds.
	$cb_card_size = isset( $cb_row['size'] ) && in_array( $cb_row['size'], array( 'half', 'third' ), true ) ? $cb_row['size'] : ( $cb_position < 2 ? 'half' : 'third' );

	$cb_cards[] = array(
		'title' => $cb_card_title,
		'image' => $cb_card_image,
		'size'  => $cb_card_size,
		'link'  => isset( $cb_row['link'] ) ? trim( (string) $cb_row['link'] ) : '',
	);
}

if ( ! $cb_cards ) {
	return;
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_buttons = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'product-bento' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading || '' !== wp_strip_all_tags( $cb_intro ) || $cb_buttons ) { ?>
			<div class="product-bento__header">
				<?php if ( $cb_heading ) { ?>
					<h2 class="product-bento__title"><?php echo esc_html( $cb_heading ); ?></h2>
				<?php } ?>
				<?php if ( '' !== wp_strip_all_tags( $cb_intro ) ) { ?>
					<div class="product-bento__lead"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_intro ) ); ?></div>
				<?php } ?>
				<?php cb_peoplesafe2027_cta_buttons( $cb_buttons ); ?>
			</div>
		<?php } ?>
		<ul class="product-bento__grid" role="list">
			<?php foreach ( $cb_cards as $cb_card ) { ?>
				<li class="product-bento__card product-bento__card--<?php echo esc_attr( $cb_card['size'] ); ?><?php echo '' !== $cb_card['link'] ? ' has-link' : ''; ?>">
					<div class="product-bento__card-header">
						<?php if ( '' !== $cb_card['title'] ) { ?>
							<h3 class="product-bento__card-title">
								<?php if ( '' !== $cb_card['link'] ) { ?>
									<a class="product-bento__card-link" href="<?php echo esc_url( $cb_card['link'] ); ?>"><?php echo $cb_card['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered with wp_kses() above. ?></a>
								<?php } else { ?>
									<?php echo $cb_card['title']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered with wp_kses() above. ?>
								<?php } ?>
							</h3>
						<?php } ?>
						<?php if ( '' !== $cb_card['link'] ) { ?>
							<span class="product-bento__arrow" aria-hidden="true">
								<svg viewBox="0 0 16 16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" focusable="false"><path d="M4 12L12 4M5.5 4H12v6.5"/></svg>
							</span>
						<?php } ?>
					</div>
					<?php if ( $cb_card['image'] ) { ?>
						<div class="product-bento__visual">
							<?php
							echo wp_get_attachment_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$cb_card['image'],
								'full',
								false,
								array(
									'class' => 'product-bento__image',
									'sizes' => 'half' === $cb_card['size'] ? '(min-width: 992px) 50vw, 100vw' : '(min-width: 992px) 33vw, 100vw',
								)
							);
							?>
						</div>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
