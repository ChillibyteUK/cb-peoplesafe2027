<?php
/**
 * hreflang: add x-default to every page that has alternates.
 *
 * Polylang prints x-default on the front page only. Search engines want it
 * on every page of the set, pointing at the page that lets a visitor choose
 * a region. That is the site root (see root.php).
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add x-default to the hreflang set.
 *
 * @param string[] $hreflangs URLs keyed by hreflang value.
 * @return string[]
 */
function cb_peoplesafe2027_hreflang_x_default( $hreflangs ) {
	if ( ! empty( $hreflangs ) && ! isset( $hreflangs['x-default'] ) ) {
		$hreflangs['x-default'] = cb_peoplesafe2027_site_root_url();
	}

	return $hreflangs;
}
add_filter( 'pll_rel_hreflang_attributes', 'cb_peoplesafe2027_hreflang_x_default' );
