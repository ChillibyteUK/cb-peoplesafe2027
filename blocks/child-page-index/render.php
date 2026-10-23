<?php
/**
 * Block template for Child page index.
 *
 * The published pages directly under the current page, in menu order then
 * title. Built on every request, so it needs no editing when a child page is
 * added, moved or renamed.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

$cb_children = get_pages(
	array(
		'parent'      => get_the_ID(),
		'sort_column' => 'menu_order, post_title',
		'post_status' => 'publish',
	)
);

if ( ! $cb_children ) {
	return;
}

$cb_wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'child-page-index' ) );
?>
<nav <?php echo $cb_wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() already escapes. ?> aria-label="<?php echo esc_attr( get_the_title() ); ?>">
	<div class="container">
		<ul class="child-page-index__list">
			<?php foreach ( $cb_children as $cb_child ) { ?>
				<li class="child-page-index__item">
					<a class="child-page-index__link" href="<?php echo esc_url( get_permalink( $cb_child ) ); ?>"><?php echo esc_html( get_the_title( $cb_child ) ); ?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
</nav>
