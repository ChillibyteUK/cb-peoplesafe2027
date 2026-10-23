<?php
/**
 * Block template for Feature list.
 *
 * A title over a checklist: each item is a term with a short definition, two
 * columns from lg up. The ticks are the Ticks list style.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_items = array();

foreach ( isset( $attributes['items'] ) ? (array) $attributes['items'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['term'] ) ) {
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
);

$cb_pre_title = isset( $attributes['preTitle'] ) ? $attributes['preTitle'] : '';
$cb_heading   = isset( $attributes['title'] ) ? $attributes['title'] : '';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'feature-list' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_pre_title ) { ?>
			<div class="feature-list__pretitle"><?php echo esc_html( $cb_pre_title ); ?></div>
		<?php } ?>
		<?php if ( $cb_heading ) { ?>
			<h2 class="feature-list__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<ul class="wp-block-list <?php echo ( isset( $attributes['tickColour'] ) && 'orange' === $attributes['tickColour'] ) ? 'is-style-check-orange' : 'is-style-check'; ?> feature-list__items" data-animate="fade-up">
			<?php
			foreach ( $cb_items as $cb_item ) {
				$cb_definition = ! empty( $cb_item['definition'] ) ? cb_peoplesafe2027_rich_paragraphs_inline( $cb_item['definition'] ) : '';
				$cb_line       = '<strong class="feature-list__term">' . esc_html( $cb_item['term'] ) . '</strong>';

				if ( '' !== $cb_definition ) {
					$cb_line .= '<span class="feature-list__definition">' . wp_kses( $cb_definition, $cb_allowed ) . '</span>';
				}

				echo '<li class="feature-list__item">' . $cb_line . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html() and wp_kses() above.
			}
			?>
		</ul>
	</div>
</section>
