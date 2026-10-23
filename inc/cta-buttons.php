<?php
/**
 * CTA button group: the one place a block's row of buttons is drawn.
 *
 * Hero, Home hero, Product header and CTA banner all take the same button
 * fields (see blocks/_shared/CtaButtonsField.js) and print them with
 * cb_peoplesafe2027_cta_buttons(), so the markup and variants are identical.
 *
 * Each button row has: linkText (label), link (URL), linkTarget (new tab),
 * dialog (id of a <dialog> to open, used when there is no URL) and variant
 * (primary, outline, outline-white or white; empty = by position).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Button style variants an editor can choose.
 *
 * @return string[] Variant slug => label.
 */
function cb_peoplesafe2027_button_variants() {
	return array(
		'primary'       => 'Primary',
		'outline'       => 'Outline',
		'outline-white' => 'Outline white',
		'white'         => 'White',
	);
}

/**
 * Print a group of buttons.
 *
 * @param array[] $buttons Button rows from the block attribute.
 * @param array   $args    {
 *     Optional.
 *
 *     @type string   $class    Extra class for the group.
 *     @type string[] $defaults Variant by position when a row has none. Default: primary, then outline.
 *     @type string   $accent   Text colour for the white variant (CSS value or var()).
 * }
 * @return void
 */
function cb_peoplesafe2027_cta_buttons( $buttons, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'class'    => '',
			'defaults' => array( 'primary', 'outline' ),
			'accent'   => '',
		)
	);

	$variants = array_keys( cb_peoplesafe2027_button_variants() );
	$rows     = array();

	foreach ( (array) $buttons as $button ) {
		$label  = isset( $button['linkText'] ) ? trim( (string) $button['linkText'] ) : '';
		$url    = isset( $button['link'] ) ? trim( (string) $button['link'] ) : '';
		$dialog = isset( $button['dialog'] ) ? sanitize_html_class( (string) $button['dialog'] ) : '';

		if ( '' === $label || ( '' === $url && '' === $dialog ) ) {
			continue;
		}

		$rows[] = array(
			'label'   => $label,
			'url'     => $url,
			'dialog'  => $dialog,
			'new_tab' => ! empty( $button['linkTarget'] ),
			'variant' => isset( $button['variant'] ) ? (string) $button['variant'] : '',
		);
	}

	if ( empty( $rows ) ) {
		return;
	}

	$style = $args['accent'] ? ' style="--button-accent: ' . esc_attr( $args['accent'] ) . '"' : '';
	?>
	<div class="button-group<?php echo $args['class'] ? ' ' . esc_attr( $args['class'] ) : ''; ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
		<?php
		foreach ( $rows as $position => $row ) {
			$variant = in_array( $row['variant'], $variants, true ) ? $row['variant'] : '';

			if ( '' === $variant ) {
				$variant = isset( $args['defaults'][ $position ] ) ? $args['defaults'][ $position ] : end( $args['defaults'] );
			}

			$classes = 'button button--' . $variant;

			if ( '' !== $row['url'] ) {
				printf(
					'<a class="%s" href="%s"%s>%s</a>',
					esc_attr( $classes ),
					esc_url( $row['url'] ),
					$row['new_tab'] ? ' target="_blank" rel="noopener"' : '',
					esc_html( $row['label'] )
				);
			} else {
				printf(
					'<button type="button" class="%s" data-dialog-target="%s">%s</button>',
					esc_attr( $classes ),
					esc_attr( $row['dialog'] ),
					esc_html( $row['label'] )
				);
			}
		}
		?>
	</div>
	<?php
}
