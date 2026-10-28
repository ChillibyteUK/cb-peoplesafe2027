<?php
/**
 * Routing: URL paths that Polylang does not produce on its own (DEC-026).
 *
 * Every current path stays, with the region in front:
 *
 *   /uk/guides/{slug}/        resource, type Guide        (also whitepapers, brochures, infographics, video)
 *   /uk/case-studies/{slug}/  case study                  (a User story is at /uk/stories/{slug}/)
 *   /uk/partnerships/{slug}/  partner                     (an Accreditation is at /uk/accreditations/{slug}/)
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
 * Post types whose path base depends on a term, so one type can live at
 * several paths. For each type: the taxonomy, the base used when no term has
 * its own, the term slugs (default region) that do have one, and whether the
 * default base lists everything (true) or only items without such a term.
 *
 * @return array[]
 */
function cb_peoplesafe2027_term_routes() {
	return (array) apply_filters(
		'cb_peoplesafe2027_term_routes',
		array(
			'resource'   => array(
				'taxonomy'    => 'resource_type',
				'default'     => 'resources',
				'bases'       => cb_peoplesafe2027_resource_bases(),
				'default_all' => true,
			),
			'case_study' => array(
				'taxonomy'    => 'case_study_type',
				'default'     => 'case-studies',
				'bases'       => array( 'user-story' => 'stories' ),
				'default_all' => false,
			),
			'partner'    => array(
				'taxonomy'    => 'partner_type',
				'default'     => 'partnerships',
				'bases'       => array( 'accreditation' => 'accreditations' ),
				'default_all' => false,
			),
		)
	);
}

/**
 * The routed types that are registered.
 *
 * @return array[] Routes keyed by post type.
 */
function cb_peoplesafe2027_active_term_routes() {
	return array_filter(
		cb_peoplesafe2027_term_routes(),
		function ( $route, $type ) {
			return post_type_exists( $type ) && taxonomy_exists( $route['taxonomy'] );
		},
		ARRAY_FILTER_USE_BOTH
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
	$vars[] = 'cb_term_base';
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
	foreach ( cb_peoplesafe2027_active_term_routes() as $type => $route ) {
		$bases = array_merge( array_values( $route['bases'] ), array( $route['default'] ) );
		$bases = implode( '|', array_map( 'preg_quote', array_unique( $bases ) ) );
		$head  = "index.php?post_type={$type}";

		add_rewrite_rule( "^({$bases})/page/([0-9]+)/?$", "{$head}&cb_term_base=\$matches[1]&paged=\$matches[2]", 'top' );
		add_rewrite_rule( "^({$bases})/?$", "{$head}&cb_term_base=\$matches[1]", 'top' );
		add_rewrite_rule( "^({$bases})/([^/]+)/?$", "{$head}&{$type}=\$matches[2]&cb_term_base=\$matches[1]", 'top' );
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

	$routes = cb_peoplesafe2027_active_term_routes();

	if ( isset( $routes[ $post->post_type ] ) ) {
		$route = $routes[ $post->post_type ];
		$base  = $route['default'];
		$terms = wp_get_object_terms( $post->ID, $route['taxonomy'], array( 'fields' => 'ids' ) );

		foreach ( is_wp_error( $terms ) ? array() : $terms as $term_id ) {
			$slug = cb_peoplesafe2027_default_term_slug( $term_id );

			if ( isset( $route['bases'][ $slug ] ) ) {
				$base = $route['bases'][ $slug ];
				break;
			}
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
 * Archives: filter the main query for term-based type paths and post streams.
 *
 * @param WP_Query $query The query.
 * @return void
 */
function cb_peoplesafe2027_routing_archives( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$region = cb_peoplesafe2027_request_region();

	$term_base = $query->get( 'cb_term_base' );
	$type      = $query->get( 'post_type' );
	$routes    = cb_peoplesafe2027_active_term_routes();

	if ( $term_base && is_string( $type ) && isset( $routes[ $type ] ) && ! $query->get( $type ) && $region ) {
		$route = $routes[ $type ];
		$slug  = array_search( $term_base, $route['bases'], true );

		if ( false !== $slug ) {
			// A base with its own term: list that term only.
			$term = cb_peoplesafe2027_region_term_id( $slug, $route['taxonomy'], $region );

			if ( $term ) {
				$query->set(
					'tax_query',
					array(
						array(
							'taxonomy' => $route['taxonomy'],
							'field'    => 'term_id',
							'terms'    => array( $term ),
						),
					)
				);
			}
		} elseif ( ! $route['default_all'] ) {
			// The default base: everything except the terms that have their own.
			$exclude = array();

			foreach ( array_keys( $route['bases'] ) as $other ) {
				$term = cb_peoplesafe2027_region_term_id( $other, $route['taxonomy'], $region );

				if ( $term ) {
					$exclude[] = $term;
				}
			}

			if ( $exclude ) {
				$query->set(
					'tax_query',
					array(
						array(
							'taxonomy' => $route['taxonomy'],
							'field'    => 'term_id',
							'terms'    => $exclude,
							'operator' => 'NOT IN',
						),
					)
				);
			}
		}

		$query->is_home              = false;
		$query->is_archive           = true;
		$query->is_post_type_archive = true;
	}

	// ?sector=slug narrows the case study archive to one sector (the Logo slider's case studies button links here).
	if ( $term_base && 'case_study' === $type && ! $query->get( 'case_study' ) && isset( $_GET['sector'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
		$tax_query   = (array) $query->get( 'tax_query' );
		$tax_query[] = array(
			'taxonomy' => 'sector',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( wp_unslash( $_GET['sector'] ) ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		);

		$query->set( 'tax_query', $tax_query );
	}

	// ?incident=slug lists the stories of one incident type (the Real life stories page's tiles link here).
	if ( $term_base && 'case_study' === $type && ! $query->get( 'case_study' ) && isset( $_GET['incident'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
		$tax_query   = (array) $query->get( 'tax_query' );
		$tax_query[] = array(
			'taxonomy' => 'incident',
			'field'    => 'slug',
			'terms'    => array( sanitize_title( wp_unslash( $_GET['incident'] ) ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		);

		$query->set( 'tax_query', $tax_query );
		$query->set( 'posts_per_page', -1 );
	}

	if ( $term_base && 'case_study' === $type && ! $query->get( 'case_study' ) && 'stories' !== $term_base ) {
		$query->set( 'posts_per_page', 12 );
	}

	if ( $term_base && 'resource' === $type && ! $query->get( 'resource' ) ) {
		$query->set( 'posts_per_page', 12 );
		$query->set(
			'meta_query',
			array(
				'relation' => 'OR',
				array(
					'key'     => 'cb_hide_from_listing',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => 'cb_hide_from_listing',
					'value'   => '1',
					'compare' => '!=',
				),
			)
		);
	}

	$stream = $query->get( 'cb_post_stream' );

	// ?category=slug narrows the blogs listing to one category (its pills link here).
	if ( 'blogs' === $stream && ! $query->get( 'name' ) && isset( $_GET['category'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- a public filter, no state changes.
		$filtered = get_term_by( 'slug', sanitize_title( wp_unslash( $_GET['category'] ) ), 'category' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( $filtered ) {
			$query->set( 'category__in', array( $filtered->term_id ) );
		}
	}

	// The listings load more nine at a time (inc/posts-index.php); without JavaScript the pages are the same size.
	if ( $stream && ! $query->get( 'name' ) ) {
		$query->set( 'posts_per_page', CB_PEOPLESAFE2027_POSTS_PER_BATCH );
	}

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
 * A region root (/uk/) serves that region's front page.
 *
 * With directory URLs and the default region not hidden, Polylang leaves
 * /uk/ as a bare language request, which WordPress treats as the blog index.
 * Point it at the region's translation of the static front page instead.
 *
 * @param array $vars Query variables parsed from the URL.
 * @return array
 */
function cb_peoplesafe2027_region_front_page_request( $vars ) {
	if ( is_admin() || empty( $vars['lang'] ) || 'page' !== get_option( 'show_on_front' ) ) {
		return $vars;
	}

	if ( array_diff( array_keys( $vars ), array( 'lang', 'page' ) ) ) {
		return $vars;
	}

	$front = (int) get_option( 'page_on_front' );
	$page  = $front ? (int) pll_get_post( $front, $vars['lang'] ) : 0;

	if ( $page ) {
		$vars['page_id'] = $page;
	}

	return $vars;
}
add_filter( 'request', 'cb_peoplesafe2027_region_front_page_request', 10 );

/**
 * Stop WordPress and Polylang redirecting a region's front page to its page
 * permalink (/uk/home/). The front page lives at /uk/, and
 * cb_peoplesafe2027_redirect_to_permalink() sends /uk/home/ there.
 *
 * @param string|false $redirect_url Where core would redirect to.
 * @return string|false
 */
function cb_peoplesafe2027_keep_front_page_url( $redirect_url ) {
	$front = (int) get_option( 'page_on_front' );

	if ( $front && is_page() && (int) pll_get_post( $front, pll_get_post_language( get_queried_object_id() ) ) === get_queried_object_id() ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'cb_peoplesafe2027_keep_front_page_url', 20 );

/**
 * Redirect any URL for a routed item to its one right URL (301).
 *
 * Covers a News post asked for under /blogs/ and the other way round, a
 * item under the wrong type path (a resource, case study or partner), an old root-level post URL, and the front
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

	$routed = array_merge( array( 'post', 'landing_page' ), array_keys( cb_peoplesafe2027_active_term_routes() ) );

	if ( ! $is_front_page && ! in_array( $object->post_type, $routed, true ) ) {
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
