<?php
/**
 * Custom Post Types Registration
 *
 * The project's post types are registered from the $types table in
 * cb_peoplesafe2027_register_post_types(). Add a row there for a new type, or
 * duplicate the commented register_post_type() call for one that needs more.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register custom post types for the theme.
 *
 * @return void
 */
function cb_peoplesafe2027_register_post_types() {

	/*
	register_post_type(
		'case_study',
		array(
			'labels'          => array(
				'name'               => 'Case Studies',
				'singular_name'      => 'Case Study',
				'add_new_item'       => 'Add New Case Study',
				'edit_item'          => 'Edit Case Study',
				'new_item'           => 'New Case Study',
				'view_item'          => 'View Case Study',
				'search_items'       => 'Search Case Studies',
				'not_found'          => 'No case studies found',
				'not_found_in_trash' => 'No case studies in trash',
			),
			'has_archive'     => false,
			'public'          => true,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'show_in_rest'    => true,
			'menu_position'   => 26,
			'menu_icon'       => 'dashicons-portfolio',
			'supports'        => array( 'title', 'editor', 'thumbnail' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => array(
				'slug'       => 'case-studies',
				'with_front' => false,
			),
		)
	);
	*/

	/*
	 * Project post types (DEC-012). Paths keep each current type's base with the
	 * region in front (DEC-026): the region prefix comes from Polylang, the base
	 * from the rewrite slug here, or from inc/regions/routing.php where rewrite
	 * is false. Types that routing.php builds paths for: resource, case_study, partner, landing_page.
	 */
	$types = array(
		'product'      => array(
			'plural'  => 'Products',
			'single'  => 'Product',
			'icon'    => 'dashicons-products',
			'rewrite' => array(
				'slug'       => 'products',
				'with_front' => false,
			),
			'archive' => false, // /products/ is an ordinary page holding the Product cards block.
			'taxes'   => array( 'product_type' ),
		),
		'case_study'   => array(
			'plural'  => 'Case studies',
			'single'  => 'Case study',
			'icon'    => 'dashicons-awards',
			'rewrite' => false, // Paths by type term: routing.php.
			'archive' => false,
			'taxes'   => array( 'sector', 'case_study_type' ),
		),
		'resource'     => array(
			'plural'   => 'Resources',
			'single'   => 'Resource',
			'icon'     => 'dashicons-media-document',
			'rewrite'  => false, // Paths by type: routing.php.
			'archive'  => false,
			'taxes'    => array( 'resource_type' ),
			'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
		),
		'legislation'  => array(
			'plural'  => 'Legislation',
			'single'  => 'Legislation',
			'icon'    => 'dashicons-book-alt',
			'rewrite' => array(
				'slug'       => 'legislation',
				'with_front' => false,
			),
			'archive' => true,
			'taxes'   => array( 'jurisdiction' ),
		),
		'partner'      => array(
			'plural'  => 'Partners',
			'single'  => 'Partner',
			'icon'    => 'dashicons-groups',
			'rewrite' => false, // Paths by type term: routing.php.
			'archive' => false,
			'taxes'   => array( 'partner_type' ),
		),
		'career'       => array(
			'plural'  => 'Careers',
			'single'  => 'Career',
			'icon'    => 'dashicons-id',
			'rewrite' => array(
				'slug'       => 'careers',
				'with_front' => false,
			),
			'archive' => false, // /careers/ is an ordinary page with a job list.
			'taxes'   => array( 'career_type' ),
		),
		'landing_page' => array(
			'plural'   => 'Landing pages',
			'single'   => 'Landing page',
			'icon'     => 'dashicons-megaphone',
			'rewrite'  => false, // Same level as pages: routing.php.
			'archive'  => false,
			'taxes'    => array( 'landing_type' ),
			'supports' => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		),
		'person'       => array(
			'plural'   => 'People',
			'single'   => 'Person',
			'icon'     => 'dashicons-admin-users',
			'rewrite'  => false,
			'archive'  => false,
			'public'   => false, // Attribution only: no URLs, no archive (DEC-014).
			'taxes'    => array( 'team' ),
			'supports' => array( 'title', 'editor', 'thumbnail' ), // The editor holds the bio; the role is a field (inc/people.php).
		),
	);

	$position = 21;

	foreach ( $types as $name => $type ) {
		$public = isset( $type['public'] ) ? $type['public'] : true;

		register_post_type(
			$name,
			array(
				'labels'        => array(
					'name'               => $type['plural'],
					'singular_name'      => $type['single'],
					'add_new_item'       => 'Add ' . $type['single'],
					'edit_item'          => 'Edit ' . $type['single'],
					'new_item'           => 'New ' . $type['single'],
					'view_item'          => 'View ' . $type['single'],
					'search_items'       => 'Search ' . strtolower( $type['plural'] ),
					'not_found'          => 'No ' . strtolower( $type['plural'] ) . ' found',
					'not_found_in_trash' => 'No ' . strtolower( $type['plural'] ) . ' in trash',
				),
				'public'        => $public,
				'show_ui'       => true,
				'show_in_menu'  => true,
				'show_in_rest'  => true,
				'has_archive'   => $type['archive'],
				'rewrite'       => $type['rewrite'],
				'query_var'     => $public,
				'menu_position' => $position++,
				'menu_icon'     => $type['icon'],
				'supports'      => isset( $type['supports'] ) ? $type['supports'] : array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'taxonomies'    => $type['taxes'],
			)
		);
	}
}
add_action( 'init', 'cb_peoplesafe2027_register_post_types' );

/**
 * Serve page.php for singular views of any custom post type that has no
 * single-{post_type}.php of its own, instead of falling back to the
 * generic single.php. CPT content in this theme is built from the same
 * blocks as pages — single.php's auto-printed <h1>/date and
 * .container/<article> wrapper are built for classic blog posts, not
 * block-built layouts, and would clash with a block's own heading (e.g.
 * CB Hero).
 *
 * Only steps in when WordPress's own template hierarchy has already fallen
 * through to the generic single.php ($template's basename) — a project can
 * still add single-{post_type}.php for a CPT that genuinely needs its own
 * markup and this filter won't touch it. Regular WP posts are untouched
 * too (is_singular( 'post' ) is excluded), so blog posts keep using
 * single.php as normal.
 *
 * @param string $template Template path WordPress would otherwise use.
 * @return string
 */
function cb_peoplesafe2027_use_page_template_for_cpts( $template ) {
	if ( is_singular() && ! is_page() && ! is_singular( 'post' ) && 'single.php' === basename( $template ) ) {
		$page_template = get_query_template( 'page' );

		if ( $page_template ) {
			return $page_template;
		}
	}

	return $template;
}
add_filter( 'template_include', 'cb_peoplesafe2027_use_page_template_for_cpts' );

/**
 * A "Hide from listing" tick on a resource: it stays published and reachable by its address, but is left off
 * the listings (/uk/guides/, /uk/video/ and so on).
 *
 * @return void
 */
function cb_peoplesafe2027_resource_listing_box() {
	add_meta_box(
		'cb-resource-listing',
		'Listing',
		function ( $post ) {
			wp_nonce_field( 'cb_resource_listing', 'cb_resource_listing_nonce' );
			?>
			<label>
				<input type="checkbox" name="cb_hide_from_listing" value="1" <?php checked( get_post_meta( $post->ID, 'cb_hide_from_listing', true ), '1' ); ?>>
				Hide from the listing
			</label>
			<?php
		},
		'resource',
		'side'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_resource_listing_box' );

/**
 * Save the "Hide from listing" tick.
 *
 * @param int $post_id Resource being saved.
 * @return void
 */
function cb_peoplesafe2027_save_resource_listing( $post_id ) {
	if ( ! isset( $_POST['cb_resource_listing_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_resource_listing_nonce'] ) ), 'cb_resource_listing' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( ! empty( $_POST['cb_hide_from_listing'] ) ) {
		update_post_meta( $post_id, 'cb_hide_from_listing', '1' );
	} else {
		delete_post_meta( $post_id, 'cb_hide_from_listing' );
	}
}
add_action( 'save_post_resource', 'cb_peoplesafe2027_save_resource_listing' );

/**
 * A body class for the type of a single resource (single-resource-video, single-resource-guide and so on), so a
 * type can be styled on its own.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function cb_peoplesafe2027_resource_type_body_class( $classes ) {
	if ( is_singular( 'resource' ) ) {
		$terms = get_the_terms( get_queried_object_id(), 'resource_type' );

		if ( $terms && ! is_wp_error( $terms ) ) {
			$classes[] = 'single-resource-' . sanitize_html_class( $terms[0]->slug );
		}
	}

	return $classes;
}
add_filter( 'body_class', 'cb_peoplesafe2027_resource_type_body_class' );
