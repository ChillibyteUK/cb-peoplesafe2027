<?php
/**
 * Block template for Pushthrough.
 *
 * A call to action that sits inside an article, at the width of the text. "Panel": an optional label, a title, text,
 * a line of short facts and the buttons, stacked on a coloured panel. "Strip": the title and text on the left, the
 * buttons on the right. The title is an h3, so it never joins an article's quick links.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_layout  = isset( $attributes['layout'] ) && 'strip' === $attributes['layout'] ? 'strip' : 'panel';
$cb_themes  = array( 'navy', 'teal', 'orange', 'light' );
$cb_theme   = isset( $attributes['theme'] ) && in_array( $attributes['theme'], $cb_themes, true ) ? $attributes['theme'] : 'navy';
$cb_label   = isset( $attributes['label'] ) ? trim( $attributes['label'] ) : '';
$cb_title   = isset( $attributes['title'] ) ? trim( $attributes['title'] ) : '';
$cb_text    = isset( $attributes['content'] ) ? trim( $attributes['content'] ) : '';
$cb_buttons = isset( $attributes['buttons'] ) ? $attributes['buttons'] : array();
$cb_facts   = isset( $attributes['facts'] ) ? array_filter( array_map( 'trim', preg_split( '#\R#', (string) $attributes['facts'] ) ) ) : array();

if ( ! $cb_title && ! $cb_text && ! $cb_buttons ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'pushthrough pushthrough--' . $cb_layout . ' pushthrough--' . $cb_theme ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="pushthrough__box">
			<div class="pushthrough__copy">
				<?php if ( 'panel' === $cb_layout && $cb_label ) { ?>
					<p class="pushthrough__label"><?php echo esc_html( $cb_label ); ?></p>
				<?php } ?>
				<?php if ( $cb_title ) { ?>
					<h3 class="pushthrough__title"><?php echo esc_html( $cb_title ); ?></h3>
				<?php } ?>
				<?php if ( $cb_text ) { ?>
					<div class="pushthrough__text"><?php echo wp_kses_post( $cb_text ); ?></div>
				<?php } ?>
				<?php if ( 'panel' === $cb_layout && $cb_facts ) { ?>
					<ul class="pushthrough__facts">
						<?php foreach ( $cb_facts as $cb_fact ) { ?>
							<li><?php echo esc_html( $cb_fact ); ?></li>
						<?php } ?>
					</ul>
				<?php } ?>
			</div>
			<?php
			cb_peoplesafe2027_cta_buttons(
				$cb_buttons,
				array(
					'class'    => 'pushthrough__buttons',
					'defaults' => array( 'primary' ),
				)
			);
			?>
		</div>
	</div>
</section>
