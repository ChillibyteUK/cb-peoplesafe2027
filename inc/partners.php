<?php
/**
 * Partners: partnerships and accreditations.
 *
 * Both are Partner posts (the type picks the listing: /uk/partnerships/ or /uk/accreditations/). A partner can link
 * to another page instead of having its own (SureCam to its product page, say); an accreditation has no page of its
 * own, so its address goes back to the listing.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The "Link instead of its own page" box on a partner.
 *
 * @return void
 */
function cb_peoplesafe2027_partner_link_box() {
	add_meta_box(
		'cb-partner-link',
		'Link',
		function ( $post ) {
			wp_nonce_field( 'cb_partner_link', 'cb_partner_link_nonce' );
			printf(
				'<p><label for="cb_partner_link" class="screen-reader-text">Link instead of its own page</label><input type="text" id="cb_partner_link" name="cb_partner_link" value="%s" style="width:100%%" placeholder="/uk/products/surecam/"></p><p class="description">Optional. The listing links here instead of to this page, and this page sends visitors there.</p>',
				esc_attr( (string) get_post_meta( $post->ID, 'cb_partner_link', true ) )
			);
		},
		'partner',
		'side'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_partner_link_box' );

/**
 * Save the partner's link.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_save_partner_link( $post_id ) {
	if ( ! isset( $_POST['cb_partner_link_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_partner_link_nonce'] ) ), 'cb_partner_link' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$link = isset( $_POST['cb_partner_link'] ) ? esc_url_raw( wp_unslash( $_POST['cb_partner_link'] ) ) : '';

	if ( $link ) {
		update_post_meta( $post_id, 'cb_partner_link', $link );
	} else {
		delete_post_meta( $post_id, 'cb_partner_link' );
	}
}
add_action( 'save_post_partner', 'cb_peoplesafe2027_save_partner_link' );

/**
 * Where a listing should send visitors for a partner: its own page, or the page it links to, or nowhere (an
 * accreditation without a page).
 *
 * @param int $post_id Partner ID.
 * @return string URL, or an empty string.
 */
function cb_peoplesafe2027_partner_url( $post_id ) {
	$link = (string) get_post_meta( $post_id, 'cb_partner_link', true );

	if ( $link ) {
		return $link;
	}

	return '' !== trim( (string) get_post_field( 'post_content', $post_id ) ) ? get_permalink( $post_id ) : '';
}

/**
 * A partner with a link, or with no content, has no page of its own: send its address to the link, or back to the
 * listing.
 *
 * @return void
 */
function cb_peoplesafe2027_partner_redirect() {
	if ( ! is_singular( 'partner' ) ) {
		return;
	}

	$post_id = get_queried_object_id();
	$link    = (string) get_post_meta( $post_id, 'cb_partner_link', true );

	if ( $link ) {
		wp_safe_redirect( $link, 301 );
		exit;
	}

	if ( '' === trim( (string) get_post_field( 'post_content', $post_id ) ) ) {
		wp_safe_redirect( trailingslashit( dirname( untrailingslashit( get_permalink( $post_id ) ) ) ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'cb_peoplesafe2027_partner_redirect' );
