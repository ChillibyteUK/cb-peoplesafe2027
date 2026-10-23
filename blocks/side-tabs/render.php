<?php
/**
 * Block template for Side tabs.
 *
 * A panel of images beside a stack of native <details> tabs (one open at a time,
 * the first open to begin with). src/js/side-tabs.js shows the image of the open
 * tab and keeps one tab open. Without JavaScript the first image shows and the
 * tabs still open and close. Below md the images are hidden.
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

$cb_heading = isset( $attributes['title'] ) ? $attributes['title'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_group   = wp_unique_id( 'side-tabs-' );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'side-tabs' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="side-tabs__title"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<?php if ( $cb_intro ) { ?>
			<div class="side-tabs__intro"><?php echo wp_kses_post( $cb_intro ); ?></div>
		<?php } ?>
		<div class="side-tabs__grid" data-animate="fade-up" data-side-tabs>
			<div class="side-tabs__images" aria-hidden="true">
				<?php foreach ( $cb_tabs as $cb_index => $cb_tab ) { ?>
					<?php $cb_image_id = isset( $cb_tab['image'] ) ? (int) $cb_tab['image'] : 0; ?>
					<div class="side-tabs__image<?php echo 0 === $cb_index ? ' is-active' : ''; ?>">
						<?php if ( $cb_image_id ) { ?>
							<?php
							echo wp_get_attachment_image(
								$cb_image_id,
								'large',
								false,
								array(
									'alt'     => '',
									'loading' => 0 === $cb_index ? 'eager' : 'lazy',
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
							?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
			<div class="side-tabs__tabs">
				<?php foreach ( $cb_tabs as $cb_index => $cb_tab ) { ?>
					<details class="side-tabs__tab" name="<?php echo esc_attr( $cb_group ); ?>"<?php echo 0 === $cb_index ? ' open' : ''; ?>>
						<summary class="side-tabs__summary">
							<h3 class="side-tabs__tab-title"><?php echo esc_html( $cb_tab['title'] ); ?></h3>
							<svg class="side-tabs__chevron" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
						</summary>
						<?php
						$cb_body = ! empty( $cb_tab['content'] ) ? cb_peoplesafe2027_rich_paragraphs( $cb_tab['content'] ) : '';

						$cb_items = ! empty( $cb_tab['list'] ) ? cb_peoplesafe2027_lines_to_items( $cb_tab['list'] ) : '';

						if ( $cb_items ) {
							$cb_body .= '<ul>' . $cb_items . '</ul>';
						}

						if ( ! empty( $cb_tab['link'] ) && ! empty( $cb_tab['linkText'] ) ) {
							ob_start();
							cb_peoplesafe2027_cta_buttons(
								array(
									array(
										'linkText'   => $cb_tab['linkText'],
										'link'       => $cb_tab['link'],
										'linkTarget' => ! empty( $cb_tab['linkTarget'] ),
										'variant'    => 'outline-white',
									),
								)
							);
							$cb_body .= trim( ob_get_clean() );
						}
						?>
						<div class="side-tabs__content"><?php echo $cb_body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post() and the button helper escape it. ?></div>
					</details>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
