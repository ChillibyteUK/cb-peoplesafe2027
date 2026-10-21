<?php
/**
 * Menu: one menu for every region.
 *
 * The primary menu is built once, from the default region's pages. At render
 * time each item is swapped for its translation in the visitor's region. An
 * item with no translation there is hidden, with its children. The swap
 * happens before WordPress works out which item is current, so the current
 * item is highlighted in every region.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Always render the menu assigned to the default region, in every region.
 *
 * Polylang keeps a separate menu assignment per region. This project keeps
 * one menu, so the other regions borrow the default region's.
 *
 * @param array $args wp_nav_menu() arguments.
 * @return array
 */
function cb_peoplesafe2027_single_menu_args( $args ) {
	if ( empty( $args['theme_location'] ) ) {
		return $args;
	}

	$location = $args['theme_location'];
	$menu_id  = 0;

	// Polylang's own assignment for the default region, if there is one.
	$default = pll_default_language();
	$options = isset( PLL()->options['nav_menus'] ) ? PLL()->options['nav_menus'] : array();
	$theme   = get_option( 'stylesheet' );

	if ( ! empty( $options[ $theme ][ $location ][ $default ] ) ) {
		$menu_id = (int) $options[ $theme ][ $location ][ $default ];
	} else {
		// The plain WordPress assignment. get_nav_menu_locations() cannot be
		// used: Polylang filters it to the current region.
		$mods = get_option( 'theme_mods_' . $theme );

		if ( ! empty( $mods['nav_menu_locations'][ $location ] ) ) {
			$menu_id = (int) $mods['nav_menu_locations'][ $location ];
		}
	}

	if ( $menu_id ) {
		$args['menu'] = $menu_id;
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'cb_peoplesafe2027_single_menu_args', 100 );

/**
 * Swap each menu item for its translation in the current region.
 *
 * @param array $items Menu items.
 * @return array
 */
function cb_peoplesafe2027_resolve_menu_items( $items ) {
	if ( is_admin() ) {
		return $items;
	}

	$region = pll_current_language();

	if ( ! $region ) {
		return $items;
	}

	$default_home = cb_peoplesafe2027_region_url( pll_default_language() );
	$region_home  = cb_peoplesafe2027_region_url( $region );
	$removed      = array();
	$kept         = array();

	foreach ( $items as $item ) {
		if ( in_array( (int) $item->menu_item_parent, $removed, true ) ) {
			$removed[] = (int) $item->ID;
			continue;
		}

		if ( 'post_type' === $item->type ) {
			$translated = pll_get_post( (int) $item->object_id, $region );

			if ( ! $translated || 'publish' !== get_post_status( $translated ) ) {
				$removed[] = (int) $item->ID;
				continue;
			}

			if ( (int) $translated !== (int) $item->object_id ) {
				$item->object_id = (string) $translated;
				$item->url       = get_permalink( $translated );
			}
		} elseif ( 'taxonomy' === $item->type ) {
			$translated = pll_get_term( (int) $item->object_id, $region );

			if ( ! $translated ) {
				$removed[] = (int) $item->ID;
				continue;
			}

			if ( (int) $translated !== (int) $item->object_id ) {
				$link = get_term_link( (int) $translated, $item->object );

				if ( is_wp_error( $link ) ) {
					$removed[] = (int) $item->ID;
					continue;
				}

				$item->object_id = (string) $translated;
				$item->url       = $link;
			}
		} elseif ( 'custom' === $item->type && 0 === strpos( (string) $item->url, $default_home ) ) {
			// A link into the default region, for example an archive: same path, current region.
			$item->url = $region_home . substr( $item->url, strlen( $default_home ) );
		}

		$kept[] = $item;
	}

	return $kept;
}
add_filter( 'wp_get_nav_menu_items', 'cb_peoplesafe2027_resolve_menu_items', 20 );
