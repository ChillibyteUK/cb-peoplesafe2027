<?php
/**
 * Block template for Trustpilot widget.
 *
 * The widget definitions come from Site-Wide Settings (inc/trustpilot.php).
 * Trustpilot's own script fills the div in, so it is only loaded on a page
 * that has one.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_widgets       = cb_peoplesafe2027_trustpilot_widgets();
$cb_business_unit = cb_peoplesafe2027_get_setting( 'trustpilot_business_unit' );
$cb_review_url    = cb_peoplesafe2027_get_setting( 'trustpilot_review_url' );
$cb_chosen        = isset( $attributes['widget'] ) ? $attributes['widget'] : '';
$cb_widget        = ( $cb_chosen && isset( $cb_widgets[ $cb_chosen ] ) ) ? $cb_widgets[ $cb_chosen ] : reset( $cb_widgets );

if ( ! $cb_widget || ! $cb_business_unit ) {
	if ( current_user_can( 'edit_posts' ) ) {
		echo '<!-- Trustpilot widget: set a business unit ID and a widget under Site-Wide Settings, Trustpilot. -->';
	}

	return;
}

wp_enqueue_script(
	'trustpilot-widget',
	'https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js',
	array(),
	null, // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- vendor-versioned URL.
	array(
		'in_footer' => true,
		'strategy'  => 'async',
	)
);

// Trustpilot locales are en-GB style, which is what WordPress's locale becomes
// once Polylang has switched it to the region's.
$cb_widget_locale = str_replace( '_', '-', get_locale() );
$cb_height        = $cb_widget['height'] ? $cb_widget['height'] : '28px';

// On a card: the block's own choice, or else the widget's setting.
$cb_card_choice = isset( $attributes['card'] ) ? $attributes['card'] : '';
$cb_use_card    = ( 'yes' === $cb_card_choice ) || ( 'no' !== $cb_card_choice && $cb_widget['card'] );
$cb_card_class  = $cb_use_card ? 'trustpilot__card' : 'trustpilot__plain';

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'trustpilot' ) );
?>
<div <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="<?php echo esc_attr( $cb_card_class ); ?>">
		<div
			class="trustpilot-widget"
			data-locale="<?php echo esc_attr( $cb_widget_locale ); ?>"
			data-template-id="<?php echo esc_attr( $cb_widget['template_id'] ); ?>"
			data-businessunit-id="<?php echo esc_attr( $cb_business_unit ); ?>"
			data-style-height="<?php echo esc_attr( $cb_height ); ?>"
			data-style-width="100%"
			data-token="<?php echo esc_attr( $cb_widget['token'] ); ?>"
			<?php if ( $cb_widget['stars'] ) { ?>
				data-stars="<?php echo esc_attr( $cb_widget['stars'] ); ?>"
			<?php } ?>
			<?php if ( $cb_widget['review_languages'] ) { ?>
				data-review-languages="<?php echo esc_attr( $cb_widget['review_languages'] ); ?>"
			<?php } ?>
			style="min-height: <?php echo esc_attr( $cb_height ); ?>"
		>
			<a href="<?php echo esc_url( $cb_review_url ? $cb_review_url : 'https://www.trustpilot.com' ); ?>" target="_blank" rel="noopener">Trustpilot</a>
		</div>
		</div>
	</div>
</div>
