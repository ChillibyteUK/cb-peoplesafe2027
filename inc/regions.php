<?php
/**
 * Regions: multi-region support on top of Polylang Pro.
 *
 * One WordPress install, one language per region (en_GB at /uk/, en_US at
 * /us/, en_AU at /au/). Polylang does the language plumbing; the modules
 * below add what it does not do for this project. Each one is safe to load
 * when Polylang is missing: the whole file does nothing without it.
 *
 *   regions/hreflang.php         x-default on every page.
 *   regions/mismatch.php         A slug that exists in another region is a 404.
 *   regions/routing.php          Merged post type paths, landing pages, front page.
 *   regions/menu.php             One menu, resolved to the visitor's region.
 *   regions/root.php             Language-neutral hub at the site root.
 *   regions/language-switcher.php  The switcher shown in the header.
 *
 * Decisions: DEC-003, DEC-004, DEC-009, DEC-010, DEC-026 (project tracker).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether Polylang is active and the functions this module needs exist.
 *
 * @return bool
 */
function cb_peoplesafe2027_regions_active() {
	return function_exists( 'pll_current_language' )
		&& function_exists( 'pll_languages_list' )
		&& function_exists( 'pll_default_language' )
		&& function_exists( 'pll_home_url' );
}

if ( ! cb_peoplesafe2027_regions_active() ) {
	return;
}

/**
 * Region slugs, for example array( 'uk', 'us', 'au' ).
 *
 * @return string[]
 */
function cb_peoplesafe2027_region_slugs() {
	return (array) pll_languages_list();
}

/**
 * The site root URL, with a trailing slash and no region.
 *
 * The function home_url() cannot be used for this: Polylang filters it, and on the
 * front end it returns the current region's home.
 *
 * @return string
 */
function cb_peoplesafe2027_site_root_url() {
	return trailingslashit( get_option( 'home' ) );
}

/**
 * The region named in the first segment of the requested URL, or an empty
 * string. Polylang switches its own current language to the language of a
 * requested item, so this reads the URL instead, which is what the visitor
 * asked for.
 *
 * @return string
 */
function cb_peoplesafe2027_request_region() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$base = (string) wp_parse_url( cb_peoplesafe2027_site_root_url(), PHP_URL_PATH );

	if ( '' !== $base && 0 === strpos( $path, $base ) ) {
		$path = substr( $path, strlen( $base ) );
	}

	$first = explode( '/', trim( $path, '/' ) )[0];

	return in_array( $first, cb_peoplesafe2027_region_slugs(), true ) ? $first : '';
}

/**
 * The URL of a region's root, for example http://example.com/uk/.
 *
 * The function pll_home_url() cannot be used to build paths: with a static front page it
 * returns the front page's own permalink.
 *
 * @param string $region Region slug.
 * @return string With a trailing slash.
 */
function cb_peoplesafe2027_region_url( $region ) {
	$language = PLL()->model->get_language( $region );

	return $language ? trailingslashit( $language->get_search_url() ) : cb_peoplesafe2027_site_root_url();
}

/**
 * Slug of a term in the default region. Term slugs differ by region when
 * slugs are not shared, so rules are written against the default region's
 * term.
 *
 * @param int $term_id Term ID in any region.
 * @return string Empty string when there is no default-region term.
 */
function cb_peoplesafe2027_default_term_slug( $term_id ) {
	$default_id = pll_get_term( (int) $term_id, pll_default_language() );
	$term       = $default_id ? get_term( $default_id ) : null;

	return ( $term && ! is_wp_error( $term ) ) ? $term->slug : '';
}

/**
 * The term for a default-region slug, in the given region.
 *
 * @param string $slug     Slug of the term in the default region.
 * @param string $taxonomy Taxonomy name.
 * @param string $region   Region slug.
 * @return int Term ID, or 0 when there is none.
 */
function cb_peoplesafe2027_region_term_id( $slug, $taxonomy, $region ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return 0;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'slug'       => $slug,
			'hide_empty' => false,
			'lang'       => '',
			'number'     => 0,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return 0;
	}

	foreach ( $terms as $term ) {
		if ( pll_get_term_language( $term->term_id ) === pll_default_language() ) {
			return (int) pll_get_term( $term->term_id, $region );
		}
	}

	return 0;
}

require_once __DIR__ . '/regions/hreflang.php';
require_once __DIR__ . '/regions/mismatch.php';
require_once __DIR__ . '/regions/routing.php';
require_once __DIR__ . '/regions/menu.php';
require_once __DIR__ . '/regions/root.php';
require_once __DIR__ . '/regions/language-switcher.php';
