<?php
/**
 * People: the role and LinkedIn fields and the way a person's details are read.
 *
 * A person is a post of the People type (non-public): name = title, photo =
 * featured image, bio = the editor, role and LinkedIn address = the fields below. The Team block
 * (blocks/team) shows them.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the Role and LinkedIn box to the Person editing screen.
 *
 * @return void
 */
function cb_peoplesafe2027_people_meta_box() {
	add_meta_box(
		'cb-person-role',
		'Role and LinkedIn',
		function ( $post ) {
			wp_nonce_field( 'cb_person_role', 'cb_person_role_nonce' );
			printf(
				'<p><label for="cb_role"><strong>Role</strong></label><br><input type="text" id="cb_role" name="cb_role" value="%s" style="width:100%%" placeholder="For example Chairman"></p>',
				esc_attr( (string) get_post_meta( $post->ID, 'cb_role', true ) )
			);
			printf(
				'<p><label for="cb_linkedin"><strong>LinkedIn</strong></label><br><input type="url" id="cb_linkedin" name="cb_linkedin" value="%s" style="width:100%%" placeholder="https://www.linkedin.com/in/..."></p>',
				esc_attr( (string) get_post_meta( $post->ID, 'cb_linkedin', true ) )
			);
		},
		'person',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_people_meta_box' );

/**
 * Save the role and the LinkedIn address.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_people_save_fields( $post_id ) {
	if ( ! isset( $_POST['cb_person_role_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_person_role_nonce'] ) ), 'cb_person_role' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'cb_role', isset( $_POST['cb_role'] ) ? sanitize_text_field( wp_unslash( $_POST['cb_role'] ) ) : '' );
	update_post_meta( $post_id, 'cb_linkedin', isset( $_POST['cb_linkedin'] ) ? esc_url_raw( wp_unslash( $_POST['cb_linkedin'] ) ) : '' );
}
add_action( 'save_post_person', 'cb_peoplesafe2027_people_save_fields' );

/**
 * The people who have written at least one post: only they have an author page.
 *
 * @return int[]
 */
function cb_peoplesafe2027_author_ids() {
	global $wpdb;

	return array_map( 'intval', (array) $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = 'cb_author' AND meta_value <> '' AND meta_value <> '0'" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- a short list, read on a person's page and for the sitemap.
}

/**
 * A person who has written nothing has no page.
 *
 * @return void
 */
function cb_peoplesafe2027_people_no_empty_pages() {
	if ( is_singular( 'person' ) && ! in_array( get_queried_object_id(), cb_peoplesafe2027_author_ids(), true ) ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'cb_peoplesafe2027_people_no_empty_pages' );

/**
 * Keep people without posts out of the Yoast sitemap.
 *
 * @return int[]
 */
function cb_peoplesafe2027_people_sitemap_exclude() {
	$exclude = array();

	foreach ( get_posts(
		array(
			'post_type'      => 'person',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'post_status'    => 'publish',
		)
	) as $person_id ) {
		if ( ! in_array( (int) $person_id, cb_peoplesafe2027_author_ids(), true ) ) {
			$exclude[] = (int) $person_id;
		}
	}

	return $exclude;
}
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'cb_peoplesafe2027_people_sitemap_exclude' );
