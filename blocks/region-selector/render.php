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

$heading = isset( $attributes['heading'] ) ? $attributes['heading'] : '';
$regions = function_exists( 'cb_peoplesafe2027_region_selector_items' ) ? cb_peoplesafe2027_region_selector_items() : array();

if ( empty( $regions ) ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'region-selector' ) );
?>
<section <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?>>
	<div class="container">
		<?php if ( $heading ) : ?>
			<h2 class="region-selector__heading"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<ul class="region-selector__list">
			<?php foreach ( $regions as $region ) : ?>
				<li class="region-selector__item">
					<a class="region-selector__link" href="<?php echo esc_url( $region['url'] ); ?>" hreflang="<?php echo esc_attr( $region['locale'] ); ?>" lang="<?php echo esc_attr( $region['locale'] ); ?>">
						<span class="region-selector__code"><?php echo esc_html( strtoupper( $region['slug'] ) ); ?></span>
						<span class="region-selector__name"><?php echo esc_html( $region['name'] ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
