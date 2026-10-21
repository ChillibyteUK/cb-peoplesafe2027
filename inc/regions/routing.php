<?php
/**
 * Routing: URL paths that Polylang does not produce on its own (DEC-026).
 *
 * Every current path stays, with the region in front:
 *
 *   /uk/guides/{slug}/        resource, type Guide        (also whitepapers, brochures, infographics, video)
 *   /uk/news/{slug}/          post in the News category
 *   /uk/blogs/{slug}/         any other post
 *   /uk/{slug}/               landing page, at the same level as pages
 *   /uk/                      the front page, not /uk/home/
 *
 * Polylang puts the region in front of every rewrite rule, so the rules are
 * written without it. Post types and taxonomies are registered
 * elsewhere (inc/posttypes.php, inc/taxonomies.php). Anything that is not
 * registered is skipped.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * Path base for each resource type, keyed by the type's slug in the default
 * region.
 *
 * @return string[]
 */
function cb_peoplesafe2027_resource_bases() {
	return (array) apply_filters(
		'cb_peoplesafe2027_resource_bases',
		array(
			'guide'       => 'guides',
			'whitepaper'  => 'whitepapers',
			'brochure'    => 'brochures',
			'infographic' => 'infographics',
			'video'       => 'video',
		)
	);
}

/**
 * Slug of the News category in the default region.
 *
 * @return string
 */
function cb_peoplesafe2027_news_category_slug() {
	return (string) apply_filters( 'cb_peoplesafe2027_news_category_slug', 'news' );
}

/**
 * Register the query variables the rules below set.
 *
 * @param string[] $vars Public query variables.
 * @return string[]
 */
function cb_peoplesafe2027_routing_query_vars( $vars ) {
	$vars[] = 'cb_resource_base';
	$vars[] = 'cb_post_stream';

	return $vars;
}
add_filter( 'query_vars', 'cb_peoplesafe2027_routing_query_vars' );

/**
 * Rewrite rules, written out for every region.
 *
 * @return void
 */
function cb_peoplesafe2027_routing_rules() {
	// Polylang adds the region to the front of every rule registered here
	// (the rule becomes (uk|us|au)/guides/... with lang=$matches[1]), so the
	// rules are written without it.
	if ( post_type_exists( 'resource' ) ) {
		$bases = array_merge( array_values( cb_peoplesafe2027_resource_bases() ), array( 'resources' ) );
		$bases = implode( '|', array_map( 'preg_quote', $bases ) );
		$head  = 'index.php?post_type=resource';

		add_rewrite_rule( "^({$bases})/page/([0-9]+)/?$", "{$head}&cb_resource_base=\$matches[1]&paged=\$matches[2]", 'top' );
		add_rewrite_rule( "^({$bases})/?$", "{$head}&cb_resource_base=\$matches[1]", 'top' );
		add_rewrite_rule( "^({$bases})/([^/]+)/?$", "{$head}&resource=\$matches[2]&cb_resource_base=\$matches[1]", 'top' );
	}

	// Polylang does not prefix rules that target the built-in post type, so
	// the news and blogs rules carry the region themselves. Test G3 checks
	// both behaviours; if a Polylang update changes either, it fails there.
	foreach ( cb_peoplesafe2027_region_slugs() as $region ) {
		foreach ( array( 'news', 'blogs' ) as $stream ) {
			$head = "index.php?lang={$region}&post_type=post&cb_post_stream={$stream}";

			add_rewrite_rule( "^{$region}/{$stream}/page/([0-9]+)/?$", "{$head}&paged=\$matches[1]", 'top' );
			add_rewrite_rule( "^{$region}/{$stream}/?$", $head, 'top' );
			add_rewrite_rule( "^{$region}/{$stream}/([^/]+)/?$", "{$head}&name=\$matches[1]", 'top' );
		}
	}
}
add_action( 'init', 'cb_peoplesafe2027_routing_rules', 20 );

/**
 * Whether a post is in the News category (the category in any region counts).
 *
 * @param WP_Post $post Post.
 * @return bool
 */
function cb_peoplesafe2027_is_news_post( $post ) {
	foreach ( wp_get_post_categories( $post->ID ) as $term_id ) {
		if ( cb_peoplesafe2027_default_term_slug( $term_id ) === cb_peoplesafe2027_news_category_slug() ) {
			return true;
		}
	}

	return false;
}

/**
 * Permalink for the post types routed above.
 *
 * @param string  $link Permalink as WordPress built it.
 * @param WP_Post $post Post.
 * @return string
 */
function cb_peoplesafe2027_routed_permalink( $link, $post ) {
	if ( empty( $post->post_name ) || ! in_array( $post->post_status, array( 'publish', 'private' ), true ) ) {
		return $link;
	}

	$region = pll_get_post_language( $post->ID );

	if ( ! $region ) {
		return $link;
	}

	$home = cb_peoplesafe2027_region_url( $region );

	if ( 'post' === $post->post_type ) {
		$base = cb_peoplesafe2027_is_news_post( $post ) ? 'news' : 'blogs';

		return $home . $base . '/' . $post->post_name . '/';
	}

	if ( 'resource' === $post->post_type ) {
		$bases = cb_peoplesafe2027_resource_bases();
		$base  = 'resources';
		$terms = wp_get_object_terms( $post->ID, 'resource_type', array( 'fields' => 'ids' ) );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$slug = cb_peoplesafe2027_default_term_slug( $terms[0] );
			$base = isset( $bases[ $slug ] ) ? $bases[ $slug ] : $base;
		}

		return $home . $base . '/' . $post->post_name . '/';
	}

	if ( 'landing_page' === $post->post_type ) {
		return $home . $post->post_name . '/';
	}

	return $link;
}
add_filter( 'post_link', 'cb_peoplesafe2027_routed_permalink', 30, 2 );
add_filter( 'post_type_link', 'cb_peoplesafe2027_routed_permalink', 30, 2 );

/**
 * The front page links to the region home, not to /uk/home/.
 *
 * @param string $link    Permalink.
 * @param int    $post_id Page ID.
 * @return string
 */
function cb_peoplesafe2027_front_page_link( $link, $post_id ) {
	$region = pll_get_post_language( $post_id );
	$front  = (int) get_option( 'page_on_front' );

	if ( $region && $front && (int) pll_get_post( $front, $region ) === (int) $post_id ) {
		return cb_peoplesafe2027_region_url( $region );
	}

	return $link;
}
add_filter( 'page_link', 'cb_peoplesafe2027_front_page_link', 30, 2 );

/**
 * Archives: filter the main query for resource types and post streams.
 *
 * @param WP_Query $query The query.
 * @return void
 */
function cb_peoplesafe2027_routing_archives( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$region = cb_peoplesafe2027_request_region();

	$resource_base = $query->get( 'cb_resource_base' );
	if ( $resource_base && ! $query->get( 'resource' ) && $region ) {
		$slug = array_search( $resource_base, cb_peoplesafe2027_resource_bases(), true );
		$term = $slug ? cb_peoplesafe2027_region_term_id( $slug, 'resource_type', $region ) : 0;

		if ( $term ) {
			$query->set(
				'tax_query',
				array(
					array(
						'taxonomy' => 'resource_type',
						'field'    => 'term_id',
						'terms'    => array( $term ),
					),
				)
			);
		}

		$query->is_home              = false;
		$query->is_archive           = true;
		$query->is_post_type_archive = true;
	}

	$stream = $query->get( 'cb_post_stream' );
	if ( $stream && ! $query->get( 'name' ) && $region ) {
		$news = cb_peoplesafe2027_region_term_id( cb_peoplesafe2027_news_category_slug(), 'category', $region );

		if ( $news ) {
			$query->set( 'news' === $stream ? 'category__in' : 'category__not_in', array( $news ) );
		}

		$query->is_home    = false;
		$query->is_archive = true;
	}
}
add_action( 'pre_get_posts', 'cb_peoplesafe2027_routing_archives' );

/**
 * Landing pages sit at the same level as pages: /uk/{slug}/. A page with the
 * same slug wins.
 *
 * @param array $vars Query variables parsed from the URL.
 * @return array
 */
function cb_peoplesafe2027_landing_page_request( $vars ) {
	if ( is_admin() || empty( $vars['lang'] ) || ! post_type_exists( 'landing_page' ) || ! empty( $vars['post_type'] ) ) {
		return $vars;
	}

	// A one-segment URL arrives as a page path or, with /%postname%/
	// permalinks, as a post name.
	$slug = ! empty( $vars['pagename'] ) ? $vars['pagename'] : ( ! empty( $vars['name'] ) ? $vars['name'] : '' );

	if ( '' === $slug || false !== strpos( $slug, '/' ) ) {
		return $vars;
	}

	$common = array(
		'lang'           => $vars['lang'],
		'fields'         => 'ids',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
	);

	$page = new WP_Query( array_merge( $common, array( 'pagename' => $slug ) ) );
	$post = new WP_Query(
		array_merge(
			$common,
			array(
				'post_type' => 'post',
				'name'      => $slug,
			)
		)
	);

	if ( $page->have_posts() || $post->have_posts() ) {
		return $vars;
	}

	$landing = new WP_Query(
		array_merge(
			$common,
			array(
				'post_type' => 'landing_page',
				'name'      => $slug,
			)
		)
	);

	if ( $landing->have_posts() ) {
		unset( $vars['pagename'] );
		$vars['post_type'] = 'landing_page';
		$vars['name']      = $slug;
	}

	return $vars;
}
add_filter( 'request', 'cb_peoplesafe2027_landing_page_request', 20 );

/**
 * Redirect any URL for a routed item to its one right URL (301).
 *
 * Covers a News post asked for under /blogs/ and the other way round, a
 * resource under the wrong type, an old root-level post URL, and the front
 * page at /uk/home/.
 *
 * @return void
 */
function cb_peoplesafe2027_redirect_to_permalink() {
	if ( is_admin() || is_404() || is_preview() || ! is_singular() ) {
		return;
	}

	$object = get_queried_object();

	if ( ! $object instanceof WP_Post || 'publish' !== $object->post_status ) {
		return;
	}

	$front         = (int) get_option( 'page_on_front' );
	$is_front_page = 'page' === $object->post_type && $front && (int) pll_get_post( $front, pll_get_post_language( $object->ID ) ) === (int) $object->ID;

	if ( ! $is_front_page && ! in_array( $object->post_type, array( 'post', 'resource', 'landing_page' ), true ) ) {
		return;
	}

	$expected = get_permalink( $object );
	$uri      = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$request  = trailingslashit( (string) wp_parse_url( $uri, PHP_URL_PATH ) );
	$wanted   = trailingslashit( (string) wp_parse_url( $expected, PHP_URL_PATH ) );

	if ( $expected && $request !== $wanted ) {
		$query = (string) wp_parse_url( $uri, PHP_URL_QUERY );
		wp_safe_redirect( $expected . ( $query ? '?' . $query : '' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'cb_peoplesafe2027_redirect_to_permalink', 2 );

/**
 * Polylang builds the language home URL, the hreflang alternates and the
 * switcher links for a static front page from the page's own permalink,
 * before this theme loads (so /uk/home/, not /uk/). Correct them here.
 *
 * @param string $url    URL.
 * @param string $region Region slug.
 * @return string
 */
function cb_peoplesafe2027_normalise_front_url( $url, $region ) {
	$front = (int) get_option( 'page_on_front' );
	$page  = $front ? pll_get_post( $front, $region ) : 0;

	if ( ! $page ) {
		return $url;
	}

	$home = cb_peoplesafe2027_region_url( $region );
	$bad  = $home . get_post_field( 'post_name', $page ) . '/';

	return untrailingslashit( $url ) === untrailingslashit( $bad ) ? $home : $url;
}

/**
 * Front page URL in the hreflang alternates.
 *
 * @param string[] $hreflangs URLs keyed by hreflang value.
 * @return string[]
 */
function cb_peoplesafe2027_hreflang_front_page( $hreflangs ) {
	foreach ( cb_peoplesafe2027_region_slugs() as $region ) {
		$language = PLL()->model->get_language( $region );
		$key      = $language ? $language->w3c : '';

		if ( $key && isset( $hreflangs[ $key ] ) ) {
			$hreflangs[ $key ] = cb_peoplesafe2027_normalise_front_url( $hreflangs[ $key ], $region );
		}
	}

	return $hreflangs;
}
add_filter( 'pll_rel_hreflang_attributes', 'cb_peoplesafe2027_hreflang_front_page', 5 );

/**
 * Front page URL in the language switcher.
 *
 * @param string $url    Link.
 * @param string $region Region slug.
 * @return string
 */
function cb_peoplesafe2027_switcher_front_page( $url, $region ) {
	return cb_peoplesafe2027_normalise_front_url( $url, $region );
}
add_filter( 'pll_the_language_link', 'cb_peoplesafe2027_switcher_front_page', 10, 2 );

/**
 * Front page canonical (Yoast SEO).
 *
 * @param string $canonical Canonical URL.
 * @return string
 */
function cb_peoplesafe2027_front_page_canonical( $canonical ) {
	return is_front_page() ? cb_peoplesafe2027_normalise_front_url( $canonical, pll_current_language() ) : $canonical;
}
add_filter( 'wpseo_canonical', 'cb_peoplesafe2027_front_page_canonical' );
