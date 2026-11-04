<?php
/**
 * Block template for Region selector.
 *
 * Lists the regions as cards (flag, country, language), each linking to that region's
 * home page. Meant for the Root hub, so the links go to the region homes, not
 * to an equivalent page (the hub has no equivalents).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$cb_intro   = isset( $attributes['intro'] ) ? $attributes['intro'] : '';
$cb_countries = array(
	'uk' => __( 'United Kingdom', 'cb-peoplesafe2027' ),
	'us' => __( 'United States', 'cb-peoplesafe2027' ),
	'au' => __( 'Australia', 'cb-peoplesafe2027' ),
	'ca' => __( 'Canada', 'cb-peoplesafe2027' ),
);
$cb_regions = function_exists( 'cb_peoplesafe2027_region_selector_items' ) ? cb_peoplesafe2027_region_selector_items() : array();

if ( empty( $cb_regions ) ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'region-selector' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h1 class="region-selector__heading"><?php echo esc_html( $cb_heading ); ?></h1>
		<?php } ?>
		<?php if ( $cb_intro ) { ?>
			<p class="region-selector__intro"><?php echo esc_html( $cb_intro ); ?></p>
		<?php } ?>
		<ul class="region-selector__list">
			<?php foreach ( $cb_regions as $cb_region ) { ?>
				<li class="region-selector__item">
					<?php
					$cb_available = ! isset( $cb_region['available'] ) || $cb_region['available'];
					$cb_flag      = function_exists( 'cb_peoplesafe2027_flag_url' ) ? cb_peoplesafe2027_flag_url( $cb_region['locale'] ) : '';
					$cb_country   = isset( $cb_countries[ $cb_region['slug'] ] ) ? $cb_countries[ $cb_region['slug'] ] : $cb_region['name'];
					$cb_inner     = '';

					ob_start();

					if ( $cb_flag ) {
						printf( '<img class="region-selector__flag" src="%s" width="48" height="36" alt="" aria-hidden="true">', esc_url( $cb_flag ) );
					}
					?>
					<span class="region-selector__text">
						<span class="region-selector__country"><?php echo esc_html( $cb_country ); ?></span>
						<span class="region-selector__name"><?php echo esc_html( $cb_region['name'] ); ?></span>
					</span>
					<?php
					if ( $cb_available ) {
						echo '<span class="region-selector__arrow" aria-hidden="true">&rarr;</span>';
					} else {
						echo '<span class="region-selector__soon">' . esc_html__( 'Coming soon', 'cb-peoplesafe2027' ) . '</span>';
					}

					$cb_inner = ob_get_clean();

					if ( $cb_available ) {
						?>
						<a class="region-selector__link" href="<?php echo esc_url( $cb_region['url'] ); ?>" hreflang="<?php echo esc_attr( $cb_region['locale'] ); ?>" lang="<?php echo esc_attr( $cb_region['locale'] ); ?>"><?php echo $cb_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts. ?></a>
					<?php } else { ?>
						<span class="region-selector__link region-selector__link--disabled" aria-disabled="true"><?php echo $cb_inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above from escaped parts. ?></span>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
