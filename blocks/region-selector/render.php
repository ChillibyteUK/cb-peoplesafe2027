<?php
/**
 * Block template for Region selector.
 *
 * Skeleton: unstyled markup. Lists the regions, each linking to that region's
 * home page. Meant for the Root hub, so the links go to the region homes, not
 * to an equivalent page (the hub has no equivalents).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$cb_regions = function_exists( 'cb_peoplesafe2027_region_selector_items' ) ? cb_peoplesafe2027_region_selector_items() : array();

if ( empty( $cb_regions ) ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'region-selector' ) );
?>
<section <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $cb_heading ) { ?>
			<h2 class="region-selector__heading"><?php echo esc_html( $cb_heading ); ?></h2>
		<?php } ?>
		<ul class="region-selector__list">
			<?php foreach ( $cb_regions as $cb_region ) { ?>
				<li class="region-selector__item">
					<a class="region-selector__link" href="<?php echo esc_url( $cb_region['url'] ); ?>" hreflang="<?php echo esc_attr( $cb_region['locale'] ); ?>" lang="<?php echo esc_attr( $cb_region['locale'] ); ?>">
						<span class="region-selector__code"><?php echo esc_html( strtoupper( $cb_region['slug'] ) ); ?></span>
						<span class="region-selector__name"><?php echo esc_html( $cb_region['name'] ); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
	</div>
</section>
