<?php
/**
 * Block template for Page list (temporary).
 *
 * A stand-in for the real navigation while the build is under way: every page
 * in this region as a nested list, then the other content types. Logged-in
 * editors only, so it never shows to visitors. Remove it from the home pages
 * (and delete this block) before launch.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

if ( ! current_user_can( 'edit_pages' ) ) {
	return;
}

$cb_front_id = (int) get_option( 'page_on_front' );

if ( function_exists( 'pll_get_post' ) && pll_current_language() ) {
	$cb_front_id = (int) pll_get_post( $cb_front_id, pll_current_language() );
}

$cb_page_list = wp_list_pages(
	array(
		'title_li'    => '',
		'echo'        => false,
		'sort_column' => 'menu_order, post_title',
		'post_status' => array( 'publish', 'private' ),
		'exclude'     => $cb_front_id ? (string) $cb_front_id : '',
	)
);

$cb_types = get_post_types(
	array(
		'public'   => true,
		'_builtin' => false,
	),
	'objects'
);

unset( $cb_types['root_hub'], $cb_types['landing_page'] );
$cb_types = array( 'post' => get_post_type_object( 'post' ) ) + $cb_types;
?>
<section class="temp-page-list">
	<div class="container">
		<p class="temp-page-list__note">Temporary page list. Shown to logged-in editors only. Remove before launch.</p>

		<div class="temp-page-list__columns">
			<div class="temp-page-list__group">
				<h2 class="temp-page-list__heading">Pages</h2>
				<?php if ( $cb_page_list ) { ?>
					<ul class="temp-page-list__list"><?php echo $cb_page_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped by wp_list_pages(). ?></ul>
				<?php } else { ?>
					<p>No other pages yet.</p>
				<?php } ?>
			</div>

			<?php
			foreach ( $cb_types as $cb_type ) {
				if ( ! $cb_type ) {
					continue;
				}

				$cb_items = get_posts(
					array(
						'post_type'      => $cb_type->name,
						'post_status'    => array( 'publish', 'private' ),
						'posts_per_page' => 40,
						'orderby'        => 'title',
						'order'          => 'ASC',
					)
				);

				if ( ! $cb_items ) {
					continue;
				}
				?>
				<div class="temp-page-list__group">
					<h2 class="temp-page-list__heading"><?php echo esc_html( $cb_type->labels->name ); ?></h2>
					<ul class="temp-page-list__list">
						<?php foreach ( $cb_items as $cb_item ) { ?>
							<li><a href="<?php echo esc_url( get_permalink( $cb_item ) ); ?>"><?php echo esc_html( get_the_title( $cb_item ) ); ?></a></li>
						<?php } ?>
					</ul>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
