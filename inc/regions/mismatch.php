<?php
/**
 * Region mismatch: a slug that exists only in another region is a 404.
 *
 * Polylang redirects such a request to the other region, and WordPress
 * independently guesses the permalink of a 404 and redirects to that. Both
 * are wrong here: /uk/legislation/california-sb-553/ must not exist, because
 * the California law is a US-only page. Removing only the redirects serves
 * the US item at the UK URL, which is worse, so a mismatch is turned into a
 * 404 first.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the requested item belongs to a different region from the one in
 * the URL.
 *
 * @return bool
 */
function cb_peoplesafe2027_region_mismatch() {
	$object = get_queried_object();

	if ( $object instanceof WP_Post ) {
		$object_region = pll_get_post_language( $object->ID );
	} elseif ( $object instanceof WP_Term ) {
		$object_region = pll_get_term_language( $object->term_id );
	} else {
		return false;
	}

	$requested = cb_peoplesafe2027_request_region();

	return $object_region && $requested && $object_region !== $requested;
}

/**
 * Turn a region mismatch into a 404.
 *
 * @return void
 */
function cb_peoplesafe2027_mismatch_404() {
	if ( cb_peoplesafe2027_region_mismatch() ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();
	}
}
add_action( 'template_redirect', 'cb_peoplesafe2027_mismatch_404', 1 );

/**
 * Cancel Polylang's redirect to the other region's URL.
 *
 * @param string|false $redirect_url Where Polylang would redirect to.
 * @return string|false
 */
function cb_peoplesafe2027_cancel_region_redirect( $redirect_url ) {
	return ( cb_peoplesafe2027_region_mismatch() || is_404() ) ? false : $redirect_url;
}
add_filter( 'pll_check_canonical_url', 'cb_peoplesafe2027_cancel_region_redirect' );

// WordPress redirects a 404 to the permalink it guesses. Never wanted here.
add_filter( 'do_redirect_guess_404_permalink', '__return_false' );
