<?php
/**
 * Site navigation.
 *
 * Each item of the header navigation is a post of the private "Navigation"
 * type, edited in its own wp-admin area with the Navigation section block
 * (blocks/nav-section). The post title is the name shown. Posts are ordered
 * by drag and drop (menu_order).
 *
 * Regions (Polylang): the navigation is built once, from the default region's
 * sections. In another region each link to a page is swapped for that page's
 * translation, and a link with no published translation is hidden, along with
 * a panel or section left empty. A section can also be translated like any
 * post: when a region has its own copy it is used instead. A link written as a
 * path (/products/) follows the visitor's region.
 *
 * The rendered HTML is cached per region and cleared whenever a post changes.
 * Markup is in inc/nav-render.php.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Navigation post type.
 *
 * @return void
 */
function cb_peoplesafe2027_register_nav_post_type() {
	register_post_type(
		'nav_section',
		array(
			'labels'            => array(
				'name'               => 'Navigation',
				'singular_name'      => 'Navigation section',
				'add_new'            => 'Add section',
				'add_new_item'       => 'Add navigation section',
				'edit_item'          => 'Edit navigation section',
				'new_item'           => 'New navigation section',
				'search_items'       => 'Search navigation',
				'not_found'          => 'No navigation sections found',
				'not_found_in_trash' => 'No navigation sections in trash',
				'all_items'          => 'All sections',
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_rest'      => true,
			'show_in_nav_menus' => false,
			'menu_position'     => 20,
			'menu_icon'         => 'dashicons-menu',
			'supports'          => array( 'title', 'editor', 'page-attributes' ),
			'hierarchical'      => false,
			'rewrite'           => false,
			'query_var'         => false,
			'can_export'        => true,
			'template'          => array( array( 'cb-peoplesafe2027/nav-section' ) ),
			'template_lock'     => 'all',
		)
	);
}
add_action( 'init', 'cb_peoplesafe2027_register_nav_post_type' );

/**
 * Make the Navigation type translatable in Polylang, with no setting to turn off.
 *
 * @param string[] $types       Translated post types.
 * @param bool     $is_settings True on the Polylang settings screen.
 * @return string[]
 */
function cb_peoplesafe2027_nav_translatable( $types, $is_settings ) {
	$types['nav_section'] = 'nav_section';

	return $types;
}
add_filter( 'pll_get_post_types', 'cb_peoplesafe2027_nav_translatable', 10, 2 );

/**
 * The settings of a section post: its Navigation section block's attributes,
 * with defaults filled in.
 *
 * @param WP_Post $post Navigation post.
 * @return array
 */
function cb_peoplesafe2027_nav_section_attributes( $post ) {
	foreach ( parse_blocks( $post->post_content ) as $block ) {
		if ( 'cb-peoplesafe2027/nav-section' === $block['blockName'] ) {
			$type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

			return $type ? $type->prepare_attributes_for_render( $block['attrs'] ) : $block['attrs'];
		}
	}

	return array();
}

/**
 * The sections to show in a region, in order.
 *
 * Starts from the default region's sections. A section the region has its own
 * translation of is replaced by that translation (an unpublished translation
 * hides the section).
 *
 * @param string $region Region slug, or an empty string without Polylang.
 * @return WP_Post[]
 */
function cb_peoplesafe2027_nav_sections( $region ) {
	$args = array(
		'post_type'      => 'nav_section',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	);

	if ( '' === $region || ! function_exists( 'pll_default_language' ) ) {
		return get_posts( $args );
	}

	$default  = pll_default_language();
	$sections = array();

	foreach ( get_posts( $args + array( 'lang' => $default ) ) as $post ) {
		if ( $region === $default ) {
			$sections[] = $post;
			continue;
		}

		$own = (int) pll_get_post( $post->ID, $region );

		if ( $own ) {
			if ( 'publish' === get_post_status( $own ) ) {
				$sections[] = get_post( $own );
			}
			continue;
		}

		$sections[] = $post;
	}

	return $sections;
}

/**
 * Resolve a stored link for a region.
 *
 * @param array  $link   { post, url, target } as saved by the block.
 * @param string $region Region slug, or an empty string.
 * @return array|null { url, target, post } or null when the link has nowhere to go
 *                    in this region. `post` is the ID of the page linked to, or 0.
 */
function cb_peoplesafe2027_nav_resolve_link( $link, $region ) {
	$link   = is_array( $link ) ? $link : array();
	$target = ! empty( $link['target'] );

	if ( ! empty( $link['post'] ) ) {
		$id = (int) $link['post'];

		if ( '' !== $region && function_exists( 'pll_get_post' ) ) {
			$id = (int) pll_get_post( $id, $region );
		}

		if ( ! $id || 'publish' !== get_post_status( $id ) ) {
			return null;
		}

		return array(
			'url'    => get_permalink( $id ),
			'target' => $target,
			'post'   => $id,
		);
	}

	$url = isset( $link['url'] ) ? trim( (string) $link['url'] ) : '';

	if ( '' === $url ) {
		return null;
	}

	// A path: same page in the visitor's region.
	if ( '/' === $url[0] && 0 !== strpos( $url, '//' ) ) {
		$base = ( '' !== $region && function_exists( 'cb_peoplesafe2027_region_url' ) ) ? cb_peoplesafe2027_region_url( $region ) : cb_peoplesafe2027_site_root_url();
		$url  = trailingslashit( $base ) . ltrim( $url, '/' );
	}

	return array(
		'url'    => $url,
		'target' => $target,
		'post'   => 0,
	);
}

/**
 * Resolve one link row (item) for a region: label, description, image and URL.
 * A blank label is the linked page's title; a blank image, its featured image.
 *
 * @param array  $item   Row from the block.
 * @param string $region Region slug.
 * @return array|null Null when it should not be shown.
 */
function cb_peoplesafe2027_nav_resolve_item( $item, $region ) {
	$link = cb_peoplesafe2027_nav_resolve_link( isset( $item['link'] ) ? $item['link'] : array(), $region );

	if ( ! $link ) {
		return null;
	}

	$label = isset( $item['label'] ) ? trim( (string) $item['label'] ) : '';

	if ( '' === $label && $link['post'] ) {
		$label = get_the_title( $link['post'] );
	}

	if ( '' === $label ) {
		return null;
	}

	$image = ! empty( $item['image'] ) ? (int) $item['image'] : 0;

	if ( ! $image && $link['post'] ) {
		$image = (int) get_post_thumbnail_id( $link['post'] );
	}

	return array(
		'label'  => $label,
		'desc'   => isset( $item['desc'] ) ? trim( (string) $item['desc'] ) : '',
		'image'  => $image,
		'url'    => $link['url'],
		'target' => $link['target'],
	);
}

/**
 * Resolve a list of rows, dropping those that cannot be shown.
 *
 * @param array  $rows   Rows from the block.
 * @param string $region Region slug.
 * @return array[]
 */
function cb_peoplesafe2027_nav_resolve_items( $rows, $region ) {
	$items = array();

	foreach ( (array) $rows as $row ) {
		$item = cb_peoplesafe2027_nav_resolve_item( $row, $region );

		if ( $item ) {
			$items[] = $item;
		}
	}

	return $items;
}

/**
 * Resolve a "View all" link: text and URL, or null.
 *
 * @param array  $more   { text, post, url, target }.
 * @param string $region Region slug.
 * @return array|null
 */
function cb_peoplesafe2027_nav_resolve_more( $more, $region ) {
	$text = is_array( $more ) && ! empty( $more['text'] ) ? trim( (string) $more['text'] ) : '';
	$link = '' !== $text ? cb_peoplesafe2027_nav_resolve_link( $more, $region ) : null;

	return $link ? array(
		'text'   => $text,
		'url'    => $link['url'],
		'target' => $link['target'],
	) : null;
}

/**
 * Everything the header needs for a region, resolved and ready to print.
 * A section left with nothing to show is left out.
 *
 * @param string $region Region slug, or an empty string.
 * @return array[] Sections, each with id, title, layout, position, width, url, target, dialog, items, more, panels.
 */
function cb_peoplesafe2027_nav_data( $region ) {
	$data = array();

	foreach ( cb_peoplesafe2027_nav_sections( $region ) as $post ) {
		$attrs  = cb_peoplesafe2027_nav_section_attributes( $post );
		$layout = isset( $attrs['layout'] ) ? $attrs['layout'] : 'link';
		$link   = cb_peoplesafe2027_nav_resolve_link( isset( $attrs['link'] ) ? $attrs['link'] : array(), $region );

		$section = array(
			'id'       => $post->ID,
			'title'    => get_the_title( $post ),
			'layout'   => $layout,
			'position' => ( isset( $attrs['position'] ) && 'extras' === $attrs['position'] ) ? 'extras' : 'main',
			'width'    => isset( $attrs['width'] ) ? (int) $attrs['width'] : 0,
			'url'      => $link ? $link['url'] : '',
			'target'   => $link ? $link['target'] : false,
			'dialog'   => isset( $attrs['dialog'] ) ? (string) $attrs['dialog'] : '',
			'icon'     => isset( $attrs['icon'] ) ? (string) $attrs['icon'] : '',
			'items'    => array(),
			'more'     => null,
			'panels'   => array(),
		);

		if ( in_array( $layout, array( 'list', 'tiles' ), true ) ) {
			$section['items'] = cb_peoplesafe2027_nav_resolve_items( isset( $attrs['items'] ) ? $attrs['items'] : array(), $region );
			$section['more']  = cb_peoplesafe2027_nav_resolve_more( isset( $attrs['more'] ) ? $attrs['more'] : array(), $region );

			if ( ! $section['items'] ) {
				continue;
			}
		} elseif ( 'panels' === $layout ) {
			foreach ( (array) $attrs['panels'] as $panel ) {
				$items = cb_peoplesafe2027_nav_resolve_items( isset( $panel['items'] ) ? $panel['items'] : array(), $region );

				if ( ! $items ) {
					continue;
				}

				$section['panels'][] = array(
					'title'   => isset( $panel['title'] ) ? (string) $panel['title'] : '',
					'heading' => isset( $panel['heading'] ) ? (string) $panel['heading'] : '',
					'intro'   => isset( $panel['intro'] ) ? (string) $panel['intro'] : '',
					'display' => isset( $panel['display'] ) ? (string) $panel['display'] : 'products',
					'items'   => $items,
					'more'    => cb_peoplesafe2027_nav_resolve_more( isset( $panel['more'] ) ? $panel['more'] : array(), $region ),
				);
			}

			if ( ! $section['panels'] ) {
				continue;
			}
		} elseif ( ! $link ) {
			continue; // A link or button with nowhere to go in this region.
		}

		$data[] = $section;
	}

	return $data;
}

/**
 * Version of the cached navigation. Changing it makes every cached copy stale.
 *
 * @return int
 */
function cb_peoplesafe2027_nav_cache_version() {
	return (int) get_option( 'cb_peoplesafe2027_nav_version', 1 );
}

/**
 * Clear the cached navigation. Runs when any post changes, since a section can
 * link to any page, and the menu shows that page's title, image and translation.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_nav_flush( $post_id = 0 ) {
	if ( $post_id && ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) ) {
		return;
	}

	update_option( 'cb_peoplesafe2027_nav_version', cb_peoplesafe2027_nav_cache_version() + 1, false );
}
add_action( 'save_post', 'cb_peoplesafe2027_nav_flush' );
add_action( 'deleted_post', 'cb_peoplesafe2027_nav_flush' );
add_action( 'trashed_post', 'cb_peoplesafe2027_nav_flush' );
add_action( 'untrashed_post', 'cb_peoplesafe2027_nav_flush' );

/**
 * The header navigation for the current region, as three pieces of HTML: the
 * links on the main row, the links on the right, and the panels they open.
 * Cached per region. The region switcher is not part of it, as it differs per page.
 *
 * @return string[] Keys main, extras and menus.
 */
function cb_peoplesafe2027_nav_parts() {
	$region = function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : '';
	// The file's time is in the key, so a change to how the nav is drawn (inc/nav-render.php) shows at once on deploy.
	$key    = 'cb_ps27_nav_' . cb_peoplesafe2027_nav_cache_version() . '_' . filemtime( __DIR__ . '/nav-render.php' ) . '_' . $region;
	$parts  = get_transient( $key );

	if ( ! is_array( $parts ) ) {
		$parts = cb_peoplesafe2027_nav_html( cb_peoplesafe2027_nav_data( $region ) );
		set_transient( $key, $parts, DAY_IN_SECONDS );
	}

	return $parts;
}
