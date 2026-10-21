<?php
/**
 * Root: a language-neutral hub at the site root.
 *
 * The root of peoplesafe.com shows the region switcher and the group-level
 * pages. Polylang would redirect it to a region home. That redirect is
 * decided before the theme loads, so it is switched off by the cbp-peoplesafe
 * plugin, which ships with the project. This file serves the hub page at /
 * and nowhere else.
 *
 * The hub is a Root hub item (a post type from the cbp-peoplesafe plugin, edited
 * in wp-admin like a page) with no language. A page would not do: a page with
 * a language makes Polylang redirect the root to that region. Which item is
 * shown is chosen under Settings, Regions; the option
 * cb_peoplesafe2027_root_hub_page holds its ID, and zero means the newest
 * published one. A published page set in that option still works as a fallback
 * when the plugin is missing.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The hub page ID, or 0 when none is set or it is not published.
 *
 * @return int
 */
function cb_peoplesafe2027_root_hub_id() {
	if ( function_exists( 'cbp_peoplesafe_root_hub_id' ) ) {
		return cbp_peoplesafe_root_hub_id();
	}

	$id = (int) apply_filters( 'cb_peoplesafe2027_root_hub_page', get_option( 'cb_peoplesafe2027_root_hub_page', 0 ) );

	return ( $id && 'publish' === get_post_status( $id ) ) ? $id : 0;
}

/**
 * Whether the request is for the site root, with no region in the URL.
 *
 * @return bool
 */
function cb_peoplesafe2027_is_root_request() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = trailingslashit( (string) wp_parse_url( $uri, PHP_URL_PATH ) );
	$base = trailingslashit( (string) wp_parse_url( cb_peoplesafe2027_site_root_url(), PHP_URL_PATH ) );

	return $path === $base;
}

/**
 * Serve the hub page at the root.
 *
 * @param array $vars Query variables parsed from the URL.
 * @return array
 */
function cb_peoplesafe2027_root_request( $vars ) {
	$hub = cb_peoplesafe2027_root_hub_id();

	if ( $hub && ! is_admin() && cb_peoplesafe2027_is_root_request() && empty( $vars['lang'] ) && empty( $vars['s'] ) ) {
		$vars = ( 'root_hub' === get_post_type( $hub ) )
			? array(
				'post_type' => 'root_hub',
				'p'         => $hub,
			)
			: array( 'page_id' => $hub );
	}

	return $vars;
}
add_filter( 'request', 'cb_peoplesafe2027_root_request', 30 );

/**
 * The hub's permalink is the site root.
 *
 * @param string $link    Permalink.
 * @param int|WP_Post $post_id Page ID, or the post for a post type link.
 * @return string
 */
function cb_peoplesafe2027_root_hub_link( $link, $post_id ) {
	$post_id = ( $post_id instanceof WP_Post ) ? $post_id->ID : (int) $post_id;

	return ( cb_peoplesafe2027_root_hub_id() === $post_id ) ? cb_peoplesafe2027_site_root_url() : $link;
}
add_filter( 'page_link', 'cb_peoplesafe2027_root_hub_link', 40, 2 );
add_filter( 'post_type_link', 'cb_peoplesafe2027_root_hub_link', 40, 2 );

/**
 * The hub exists at the root only: send /uk/root-hub/ and the like there.
 *
 * @return void
 */
function cb_peoplesafe2027_root_hub_redirect() {
	$hub = cb_peoplesafe2027_root_hub_id();

	if ( ! $hub || is_admin() || ! ( is_page( $hub ) || is_singular( 'root_hub' ) ) || cb_peoplesafe2027_is_root_request() ) {
		return;
	}

	wp_safe_redirect( cb_peoplesafe2027_site_root_url(), 301 );
	exit;
}
add_action( 'template_redirect', 'cb_peoplesafe2027_root_hub_redirect', 2 );
