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
