<?php
/**
 * Block template for Principle tiles.
 *
 * A centred title and introduction over a row of colour tiles, each with a
 * heart and a short line of text. Six across from lg, three from md, one on a
 * phone.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_colours = array( 'yellow', 'teal', 'blue', 'pink', 'orange' );
$cb_tiles   = array();

foreach ( isset( $attributes['tiles'] ) ? (array) $attributes['tiles'] : array() as $cb_row ) {
	$cb_text = isset( $cb_row['text'] ) ? trim( wp_strip_all_tags( $cb_row['text'] ) ) : '';

	if ( '' !== $cb_text ) {
		$cb_tiles[] = array(
			'text'   => $cb_text,
			'colour' => isset( $cb_row['colour'] ) && in_array( $cb_row['colour'], $cb_colours, true ) ? $cb_row['colour'] : 'yellow',
		);
	}
}

if ( ! $cb_tiles ) {
	return;
}

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'principle-tiles' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="principle-tiles__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<?php if ( '' !== wp_strip_all_tags( $cb_intro ) ) { ?>
			<div class="principle-tiles__intro"><?php echo wp_kses_post( cb_peoplesafe2027_rich_paragraphs( $cb_intro ) ); ?></div>
		<?php } ?>
		<div class="principle-tiles__grid" data-animate="fade-up">
			<?php foreach ( $cb_tiles as $cb_tile ) { ?>
				<div class="principle-tiles__tile principle-tiles__tile--<?php echo esc_attr( $cb_tile['colour'] ); ?>">
					<svg class="principle-tiles__icon" aria-hidden="true" focusable="false" viewBox="0 0 23.67 21.32"><path fill="currentColor" d="M17.25,0c-1.84,0-3.42,.99-4.47,1.86-.37,.3-.69,.61-.95,.88-.26-.27-.58-.58-.95-.88-1.04-.87-2.62-1.86-4.46-1.86C2.88,0,0,2.84,0,6.37c0,1.34,.27,3.53,1.77,6.14,1.51,2.61,4.21,5.57,8.98,8.51h.01c.32,.2,.69,.3,1.07,.3s.75-.1,1.08-.3c4.78-2.94,7.48-5.9,8.98-8.51,1.51-2.61,1.78-4.8,1.78-6.14,0-3.53-2.89-6.37-6.42-6.37Zm2.91,11.51c-1.31,2.27-3.74,5.01-8.29,7.8-.01,.01-.02,.01-.04,.01-.01,0-.02,0-.03-.01h0c-4.55-2.79-6.99-5.53-8.29-7.8-1.31-2.26-1.51-4.09-1.51-5.14,0-2.4,1.97-4.37,4.42-4.37,1.14,0,2.27,.64,3.19,1.4,.44,.37,.8,.74,1.05,1.01,.13,.14,.22,.26,.29,.33,.03,.04,.05,.07,.07,.09,0,.01,.01,.02,.01,.02,.19,.25,.49,.4,.8,.4s.62-.15,.8-.4h.01s0-.01,.01-.02c.02-.02,.04-.05,.07-.09,.06-.07,.16-.19,.28-.33,.25-.27,.61-.64,1.06-1.01,.91-.76,2.04-1.4,3.19-1.4,2.45,0,4.42,1.97,4.42,4.37,0,1.05-.21,2.88-1.51,5.14Z"/></svg>
					<p><?php echo esc_html( $cb_tile['text'] ); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
