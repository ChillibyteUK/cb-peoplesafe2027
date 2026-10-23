<?php
/**
 * Block template for Stats counter.
 *
 * Big numbers with a label and a description, and an optional image. Each number
 * is printed at its final value; src/js/stats-counter.js counts it up from 0 when
 * the block scrolls into view (and leaves it alone for visitors who prefer
 * reduced motion). Screen readers get the final value once.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_stats = array();

foreach ( isset( $attributes['stats'] ) ? (array) $attributes['stats'] : array() as $cb_row ) {
	if ( isset( $cb_row['value'] ) && '' !== trim( (string) $cb_row['value'] ) ) {
		$cb_stats[] = $cb_row;
	}
}

if ( ! $cb_stats ) {
	return;
}

$cb_heading    = ! empty( $attributes['title'] ) ? $attributes['title'] : __( 'Protection you can count on', 'cb-peoplesafe2027' );
$cb_image_id   = isset( $attributes['imageId'] ) ? (int) $attributes['imageId'] : 0;
$cb_show_image = empty( $attributes['hideImage'] );

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'stats-counter' . ( $cb_show_image ? '' : ' stats-counter--no-image' ) ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<div class="stats-counter__grid">
			<?php if ( $cb_show_image ) { ?>
				<div class="stats-counter__media" data-animate="fade-right">
					<?php
					if ( $cb_image_id ) {
						echo wp_get_attachment_image( $cb_image_id, 'large', false, array( 'class' => 'stats-counter__image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '<img class="stats-counter__image" src="' . esc_url( get_template_directory_uri() . '/img/stats-default.png' ) . '" alt="" width="348" height="226" loading="lazy">';
					}
					?>
				</div>
			<?php } ?>
			<div class="stats-counter__content" data-animate="fade-up">
				<h2 class="stats-counter__title"><?php echo esc_html( $cb_heading ); ?></h2>
				<div class="stats-counter__stats">
					<?php
					foreach ( $cb_stats as $cb_stat ) {
						$cb_value    = preg_replace( '/[^0-9.]/', '', str_replace( ',', '.', (string) $cb_stat['value'] ) );
						$cb_decimals = false !== strpos( $cb_value, '.' ) ? strlen( substr( strrchr( $cb_value, '.' ), 1 ) ) : 0;
						$cb_prefix   = isset( $cb_stat['prefix'] ) ? $cb_stat['prefix'] : '';
						$cb_suffix   = isset( $cb_stat['suffix'] ) ? $cb_stat['suffix'] : '';
						?>
						<div class="stats-counter__stat">
							<div class="stats-counter__value">
								<span class="visually-hidden"><?php echo esc_html( $cb_prefix . $cb_value . $cb_suffix ); ?></span>
								<span aria-hidden="true"><?php echo esc_html( $cb_prefix ); ?><span class="stats-counter__number" data-count="<?php echo esc_attr( $cb_value ); ?>" data-decimals="<?php echo esc_attr( $cb_decimals ); ?>"><?php echo esc_html( $cb_value ); ?></span><?php echo esc_html( $cb_suffix ); ?></span>
							</div>
							<?php if ( ! empty( $cb_stat['label'] ) ) { ?>
								<div class="stats-counter__label"><?php echo esc_html( $cb_stat['label'] ); ?></div>
							<?php } ?>
							<?php if ( ! empty( $cb_stat['description'] ) ) { ?>
								<div class="stats-counter__description"><?php echo esc_html( $cb_stat['description'] ); ?></div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</section>
