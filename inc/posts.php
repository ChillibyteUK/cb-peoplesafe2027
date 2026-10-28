<?php
/**
 * Blogs and news: the author (a Person post, not a WordPress user) and the post's schema.
 *
 * A post's author is picked in a box on the editing screen and kept in the cb_author meta. It is not shown on the
 * page; it feeds the BlogPosting or NewsArticle schema. A post with none is credited to the organisation.
 *
 * @package cb-peoplesafe2027
 */

defined( 'ABSPATH' ) || exit;

/**
 * The Person post that wrote a post, or null.
 *
 * @param int $post_id Post ID.
 * @return WP_Post|null
 */
function cb_peoplesafe2027_post_author( $post_id ) {
	$person = (int) get_post_meta( $post_id, 'cb_author', true ) ? get_post( (int) get_post_meta( $post_id, 'cb_author', true ) ) : null;

	return ( $person && 'person' === $person->post_type && 'publish' === $person->post_status ) ? $person : null;
}

/**
 * The Author box on the post editing screen.
 *
 * @return void
 */
function cb_peoplesafe2027_post_author_box() {
	add_meta_box(
		'cb-post-author',
		'Author',
		function ( $post ) {
			wp_nonce_field( 'cb_post_author', 'cb_post_author_nonce' );
			$people = get_posts(
				array(
					'post_type'      => 'person',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
				)
			);
			$chosen = (int) get_post_meta( $post->ID, 'cb_author', true );
			?>
			<p><label for="cb_author" class="screen-reader-text">Author</label>
			<select id="cb_author" name="cb_author" style="width:100%">
				<option value="0">Peoplesafe (no named author)</option>
				<?php foreach ( $people as $person ) { ?>
					<option value="<?php echo esc_attr( $person->ID ); ?>" <?php selected( $chosen, $person->ID ); ?>><?php echo esc_html( $person->post_title ); ?></option>
				<?php } ?>
			</select></p>
			<p class="description">Not shown on the page. Used for the article's search engine markup. Add a new author under People.</p>
			<?php
		},
		'post',
		'side'
	);
}
add_action( 'add_meta_boxes', 'cb_peoplesafe2027_post_author_box' );

/**
 * Save the author.
 *
 * @param int $post_id Post ID.
 * @return void
 */
function cb_peoplesafe2027_save_post_author( $post_id ) {
	if ( ! isset( $_POST['cb_post_author_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['cb_post_author_nonce'] ) ), 'cb_post_author' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$person = isset( $_POST['cb_author'] ) ? (int) $_POST['cb_author'] : 0;

	if ( $person ) {
		update_post_meta( $post_id, 'cb_author', $person );
	} else {
		delete_post_meta( $post_id, 'cb_author' );
	}
}
add_action( 'save_post_post', 'cb_peoplesafe2027_save_post_author' );

/**
 * Yoast's Article piece as a BlogPosting (or a NewsArticle for News), with the section, the author and the
 * language.
 *
 * @param array  $data    Yoast's Article data.
 * @param object $context Yoast's context.
 * @return array
 */
function cb_peoplesafe2027_article_schema( $data, $context ) {
	if ( ! is_singular( 'post' ) ) {
		return $data;
	}

	$post_id = get_queried_object_id();
	$names   = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );

	$data['@type'] = cb_peoplesafe2027_is_news_post( get_post( $post_id ) ) ? 'NewsArticle' : 'BlogPosting';

	if ( $names ) {
		$data['articleSection'] = array_map( 'html_entity_decode', $names );
	}

	$data['timeRequired'] = 'PT' . cb_peoplesafe2027_post_reading_minutes( $post_id ) . 'M';

	$person = cb_peoplesafe2027_post_author( $post_id );

	$data['author'] = array( '@id' => $person ? trailingslashit( $context->site_url ) . '#/schema/person/author-' . $person->ID : trailingslashit( $context->site_url ) . '#organization' );

	return $data;
}
add_filter( 'wpseo_schema_article', 'cb_peoplesafe2027_article_schema', 10, 2 );

/**
 * The author's Person piece, added to the graph that the article points at.
 *
 * @param array[] $graph   Yoast's schema pieces.
 * @param object  $context Yoast's context.
 * @return array[]
 */
function cb_peoplesafe2027_author_schema( $graph, $context ) {
	if ( ! is_singular( 'post' ) ) {
		return $graph;
	}

	$person = cb_peoplesafe2027_post_author( get_queried_object_id() );

	if ( ! $person ) {
		return $graph;
	}

	$piece = array(
		'@type'    => 'Person',
		'@id'      => trailingslashit( $context->site_url ) . '#/schema/person/author-' . $person->ID,
		'name'     => $person->post_title,
		'worksFor' => array( '@id' => trailingslashit( $context->site_url ) . '#organization' ),
	);

	$role = (string) get_post_meta( $person->ID, 'cb_role', true );

	if ( $role ) {
		$piece['jobTitle'] = $role;
	}

	$graph[] = $piece;

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'cb_peoplesafe2027_author_schema', 11, 2 );

/**
 * Reading time of a post in whole minutes (at least one), from its written text. Blocks that draw their own content
 * (FAQs, related posts) are not counted, and nothing is rendered, so it is cheap and the same wherever it is used.
 *
 * @param int|WP_Post|null $post Post. Defaults to the loop post.
 * @return int
 */
function cb_peoplesafe2027_post_reading_minutes( $post = null ) {
	$post = get_post( $post );

	return $post ? max( 1, (int) estimate_reading_time_in_minutes( $post->post_content, 200 ) ) : 1;
}

/**
 * A small line icon for the post meta row (feather style, drawn in the text colour).
 *
 * @param string $name tag, calendar, clock or user.
 * @return string SVG markup.
 */
function cb_peoplesafe2027_meta_icon( $name ) {
	$paths = array(
		'tag'      => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L2 12V2h10l8.6 8.6a2 2 0 0 1 0 2.8z"/><circle cx="7" cy="7" r="1.2"/>',
		'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'clock'    => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'user'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
	);

	return isset( $paths[ $name ] ) ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[ $name ] . '</svg>' : '';
}

/**
 * The Blogs and News listings as a CollectionPage holding an ItemList of the articles on the page.
 *
 * @param array[] $graph   Yoast's schema pieces.
 * @param object  $context Yoast's context.
 * @return array[]
 */
function cb_peoplesafe2027_listing_schema( $graph, $context ) {
	if ( is_singular() || ! get_query_var( 'cb_post_stream' ) || ! have_posts() ) {
		return $graph;
	}

	$items    = array();
	$position = 0;

	foreach ( $GLOBALS['wp_query']->posts as $listed ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => ++$position,
			'url'      => get_permalink( $listed ),
			'name'     => wp_strip_all_tags( get_the_title( $listed ) ),
		);
	}

	foreach ( $graph as $key => $piece ) {
		if ( isset( $piece['@type'] ) && ( in_array( 'WebPage', (array) $piece['@type'], true ) || in_array( 'CollectionPage', (array) $piece['@type'], true ) ) ) {
			$graph[ $key ]['@type']      = 'CollectionPage';
			$graph[ $key ]['mainEntity'] = array( '@id' => $piece['@id'] . '#list' );
			$list                        = array(
				'@type'           => 'ItemList',
				'@id'             => $piece['@id'] . '#list',
				'itemListElement' => $items,
			);
		}
	}

	if ( ! isset( $list ) ) {
		$page_id = trailingslashit( $context->canonical ) . '#webpage';
		$graph[] = array(
			'@type'      => 'CollectionPage',
			'@id'        => $page_id,
			'url'        => $context->canonical,
			'name'       => wp_get_document_title(),
			'isPartOf'   => array( '@id' => trailingslashit( $context->site_url ) . '#website' ),
			'inLanguage' => get_bloginfo( 'language' ),
			'mainEntity' => array( '@id' => $page_id . '-list' ),
		);
		$list    = array(
			'@type'           => 'ItemList',
			'@id'             => $page_id . '-list',
			'itemListElement' => $items,
		);
	}

	$graph[] = $list;

	return $graph;
}
add_filter( 'wpseo_schema_graph', 'cb_peoplesafe2027_listing_schema', 12, 2 );

/**
 * Give the Blogs and News listings their own Yoast title, description and canonical address. Yoast treats /news/ as
 * the News category's archive; the address that is really listed is /uk/news/ (or /uk/blogs/), so that is the canonical.
 * The title and description come from Site-Wide Settings; a blank field keeps Yoast's.
 *
 * @param string $value  Yoast's value.
 * @param string $suffix seo_title or seo_description.
 * @return string
 */
function cb_peoplesafe2027_stream_seo( $value, $suffix ) {
	$stream = get_query_var( 'cb_post_stream' );

	if ( is_singular() || ! $stream ) {
		return $value;
	}

	$setting = cb_peoplesafe2027_get_setting( $stream . '_' . $suffix, '' );

	if ( $setting ) {
		return $setting;
	}

	// Yoast's own title for these listings is its home or category archive title; a plain one reads better.
	if ( 'seo_title' === $suffix ) {
		return ( 'news' === $stream ? __( 'News', 'cb-peoplesafe2027' ) : __( 'Blogs', 'cb-peoplesafe2027' ) ) . ' - ' . get_bloginfo( 'name' );
	}

	return $value;
}
add_filter(
	'wpseo_title',
	function ( $title ) {
		return cb_peoplesafe2027_stream_seo( $title, 'seo_title' );
	}
);
add_filter(
	'wpseo_opengraph_title',
	function ( $title ) {
		return cb_peoplesafe2027_stream_seo( $title, 'seo_title' );
	}
);
add_filter(
	'wpseo_metadesc',
	function ( $desc ) {
		return cb_peoplesafe2027_stream_seo( $desc, 'seo_description' );
	}
);
add_filter(
	'wpseo_opengraph_desc',
	function ( $desc ) {
		return cb_peoplesafe2027_stream_seo( $desc, 'seo_description' );
	}
);
add_filter(
	'wpseo_canonical',
	function ( $canonical ) {
		$stream = get_query_var( 'cb_post_stream' );

		if ( is_singular() || ! $stream ) {
			return $canonical;
		}

		$page = max( 1, (int) get_query_var( 'paged' ) );
		$url  = trailingslashit( cb_peoplesafe2027_region_url( function_exists( 'pll_current_language' ) && pll_current_language() ? pll_current_language() : 'uk' ) ) . $stream . '/';

		return $page > 1 ? $url . 'page/' . $page . '/' : $url;
	}
);

/**
 * Posts related to one: the ones of the same kind (blogs, or news) that share the most categories with it, newest
 * first among equals, then the newest of that kind to make up the number.
 *
 * @param int|WP_Post|null $post  Post. Defaults to the loop post.
 * @param int              $count How many to return.
 * @return int[] Post IDs.
 */
function cb_peoplesafe2027_related_post_ids( $post = null, $count = 3 ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return array();
	}

	$is_news   = cb_peoplesafe2027_is_news_post( $post );
	$news_term = cb_peoplesafe2027_region_term_id( cb_peoplesafe2027_news_category_slug(), 'category', cb_peoplesafe2027_request_region() );
	$kind      = array();

	if ( $news_term ) {
		$kind[ $is_news ? 'category__in' : 'category__not_in' ] = array( $news_term );
	}

	$own = $is_news ? array() : array_diff( wp_get_post_categories( $post->ID ), array( (int) $news_term ) );

	$ranked = array();

	if ( $own ) {
		$shared = get_posts(
			array_merge(
				$kind,
				array(
					'post_status'  => 'publish',
					'numberposts'  => 40,
					'post__not_in' => array( $post->ID ),
					'fields'       => 'ids',
					'category__in' => $own,
				)
			)
		);

		foreach ( $shared as $position => $id ) {
			$ranked[ $id ] = count( array_intersect( $own, wp_get_post_categories( $id ) ) ) * 100 - $position;
		}

		arsort( $ranked );
	}

	$ids = array_slice( array_keys( $ranked ), 0, $count );

	if ( count( $ids ) < $count ) {
		$ids = array_merge(
			$ids,
			get_posts(
				array_merge(
					$kind,
					array(
						'post_status'  => 'publish',
						'numberposts'  => $count - count( $ids ),
						'post__not_in' => array_merge( array( $post->ID ), $ids ),
						'fields'       => 'ids',
					)
				)
			)
		);
	}

	return array_map( 'intval', $ids );
}

/**
 * The News listing is the News category to Yoast, and Yoast's setting keeps category archives out of search results.
 * The listing is a real page people should find, so it is indexable like the Blogs one.
 */
add_filter(
	'wpseo_robots_array',
	function ( $robots ) {
		if ( get_query_var( 'cb_post_stream' ) && ! is_singular() ) {
			$robots['index']  = 'index';
			$robots['follow'] = 'follow';
		}

		return $robots;
	}
);
