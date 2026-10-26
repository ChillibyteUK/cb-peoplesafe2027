<?php
/**
 * People: the role field and the way a person's details are read.
 *
 * A person is a post of the People type (non-public): name = title, photo =
 * featured image, bio = the editor, role = the field below. The Team block
 * (blocks/team) shows them.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add the Role box to the Person editing screen.
 *
 * @return void
 */
function cb_peoplesafe2027_people_meta_box() {
	add_meta_box(
		'cb-person-role',
		'Role',
		function ( $post ) {
			wp_nonce_field( 'cb_person_role', 'cb_person_role_nonce' );
			printf(
				'<p><label for="cb_role" class="screen-reader-text">Role</label><input type="text" id="cb_role" name="cb_role" value="%s" style="width:100%%" placeholder="For example Chairman"></p>',
				esc_attr( (string) get_post_meta( $post->ID, 'cb_role', true ) )
			);
		},
		'person',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_people_meta_box' );

/**
 * Save the role.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_people_save_role( $post_id ) {
	if ( ! isset( $_POST['cb_person_role_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_person_role_nonce'] ) ), 'cb_person_role' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	update_post_meta( $post_id, 'cb_role', isset( $_POST['cb_role'] ) ? sanitize_text_field( wp_unslash( $_POST['cb_role'] ) ) : '' );
}
add_action( 'save_post_person', 'cb_peoplesafe2027_people_save_role' );
