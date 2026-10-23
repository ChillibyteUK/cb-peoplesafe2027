<?php
/**
 * Block template for Top tabs.
 *
 * Buttons (the pills) along the top and one panel per tab. src/js/top-tabs.js
 * shows the open panel. Without JavaScript the first panel shows. Below lg the
 * pills stack with each panel directly under its pill.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_tabs = array();

foreach ( isset( $attributes['tabs'] ) ? (array) $attributes['tabs'] : array() as $cb_row ) {
	if ( ! empty( $cb_row['title'] ) ) {
		$cb_tabs[] = $cb_row;
	}
}

if ( count( $cb_tabs ) < 2 ) {
	return;
}

$cb_tabs  = array_slice( $cb_tabs, 0, 5 );
$cb_group = wp_unique_id( 'top-tabs-' );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'top-tabs' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="top-tabs__grid" style="--tabs: <?php echo (int) count( $cb_tabs ); ?>" data-top-tabs data-animate="fade-up">
			<?php foreach ( $cb_tabs as $cb_index => $cb_tab ) { ?>
				<?php
				$cb_active  = 0 === $cb_index;
				$cb_id      = $cb_group . '-' . ( $cb_index + 1 );
				$cb_icon    = isset( $cb_tab['icon'] ) ? (int) $cb_tab['icon'] : 0;
				$cb_image   = isset( $cb_tab['image'] ) ? (int) $cb_tab['image'] : 0;
				$cb_pre     = isset( $cb_tab['preTitle'] ) ? $cb_tab['preTitle'] : '';
				$cb_heading = isset( $cb_tab['heading'] ) ? $cb_tab['heading'] : '';
				$cb_text    = ! empty( $cb_tab['content'] ) ? cb_peoplesafe2027_rich_paragraphs( $cb_tab['content'] ) : '';
				?>
				<button type="button" class="top-tabs__pill<?php echo $cb_active ? ' is-active' : ''; ?>" style="--i: <?php echo (int) ( $cb_index + 1 ); ?>" aria-expanded="<?php echo $cb_active ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $cb_id ); ?>">
					<?php if ( $cb_icon ) { ?>
						<span class="top-tabs__icon"><?php echo wp_get_attachment_image( $cb_icon, 'medium', false, array( 'alt' => '', 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php } ?>
					<span class="top-tabs__pill-title"><?php echo esc_html( $cb_tab['title'] ); ?></span>
					<svg class="top-tabs__chevron" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
				</button>
				<div class="top-tabs__panel<?php echo $cb_active ? ' is-active' : ''; ?>" id="<?php echo esc_attr( $cb_id ); ?>" role="region" aria-label="<?php echo esc_attr( $cb_tab['title'] ); ?>"<?php echo $cb_active ? '' : ' hidden'; ?>>
					<?php if ( $cb_image ) { ?>
						<div class="top-tabs__image"><?php echo wp_get_attachment_image( $cb_image, 'large', false, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php } ?>
					<div class="top-tabs__content">
						<?php if ( $cb_pre ) { ?>
							<div class="top-tabs__pretitle"><?php echo esc_html( $cb_pre ); ?></div>
						<?php } ?>
						<?php if ( $cb_heading ) { ?>
							<h2 class="top-tabs__title"><?php echo esc_html( $cb_heading ); ?></h2>
						<?php } ?>
						<?php echo wp_kses_post( $cb_text ); ?>
						<?php
						if ( ! empty( $cb_tab['link'] ) && ! empty( $cb_tab['linkText'] ) ) {
							cb_peoplesafe2027_cta_buttons(
								array(
									array(
										'linkText'   => $cb_tab['linkText'],
										'link'       => $cb_tab['link'],
										'linkTarget' => ! empty( $cb_tab['linkTarget'] ),
										'variant'    => 'outline',
									),
								)
							);
						}
						?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
